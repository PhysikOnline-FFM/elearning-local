<?php
/*
	JSON Host information retrieval system.
	PhysikOnline, 15.02.2014.

	Usage: ?host=...&query=...[&port=...][&url=...]
	Return: JSON data
	This is "kind of" JSON-RPC, but not JSON-RPC.

	See the source with ?s.
	Public Domain, 16.02.2014, Sven Koeppel.
*/

require "mini-json-rpc.lib.php";

class HostQueries {
	// typical layout of answer is
	/*
		online_status=[0|1],
		text=["online"|"offline"|"n.a."|...]
		label_status=[Default Primary Success Info Warning Danger]
	*/

	private $tcp_services = array('mysql' => 3306, 'gibtsnicht' => 12345,
		'lucene' => 12711, 'smtp' => 25, 'http' => 80, 'https' => 443);

	// UDP ist nicht so einfach zu testen.
	private $udp_services = array('radius' => 1812);

	// Pattern to fulfill (hier: IP-Adressen und Domaenennamen)
	private $valid_hosts = "/^[a-zA-Z0-9.-]+$/";

	private function clean_host($hostname) {
		$server = $hostname; //$_GET['host'];
		if(!preg_match($this->valid_hosts, $server)) err("Bad Hostname, forget it");
		$server = escapeshellcmd($server); // paranoia ;-)
		return $server;
	}
	private function run($cmd) {
		$trash = array();
		exec($cmd, $trash, $ret); // meist 0 == erfolg
		return $ret;
	}
	private function textit($status, &$text, &$label_status) {
		$text = $status ? 'online' : 'offline';
		$label_status = $status ? 'success' : 'danger';
	}

	public $ping_signature = array('host');
	function ping($hostname) {
		$server = $this->clean_host($hostname);
		$cmd = "ping -c1 -n -W1 $server"; // &> /dev/null"; => geht nicht, kein shell-kontext
		$online_status = !$this->run($cmd);
		$this->textit($online_status, $text, $label_status);
		return compact('online_status', 'text', 'server', 'cmd', 'label_status');
	}

	public $test_tcp_signature = array('host', 'service');
	function test_tcp($hostname, $service_name) {
		$server = $this->clean_host($hostname);
		if(!array_key_exists($service_name, $this->tcp_services)) err("TCP Service not allowed");
		$port = $this->tcp_services[$service_name];
		$cmd = "nc -z -w5 $server $port";
		$online_status = !$this->run($cmd);
		$this->textit($online_status, $text, $label_status);
		return compact('online_status', 'text', 'port', 'server', 'cmd', 'service_name', 'label_status');
	}

	public $test_udp_signature = array('host', 'service');
	function test_udp($hostname, $service_name) {
		switch($service_name) {
			case 'radius':
				return array('text'=>'nowork!');

				$server = $this->clean_host($hostname);
				# timeout 3sec for maximal 3sec runtime
				# Problem: radtest nicht auf Webserver installiert :(
				# daher test_udp_by_proxy nutzen!
				$cmd = "timeout 3 radtest dummyuser dummypasswort $server 10 dummyradpwd";
				#$cmd = "which timeout 2>&1";
				$out = shell_exec($cmd);
				# typical line in output when reachable:
				# rad_verify: Received Access-Reject packet from home server a.b.c.d port 1812 with invalid signature!  (Shared secret is incorrect.)
				# typical output when not reachable (host down):
				# just no output except the input.
				$online_status = preg_match('/rad_verify: Received/', $out);
				$this->textit($online_status, $text, $label_status);
				return compact('out','online_status', 'text', 'service_name', 'server', 'cmd', 'label_status');
			default:
				return array('text' => 'n.a.', 'online' => 0, 'label_status' => 'info',
					'info' => "UDP Protocol $service_name not implemented");
		}
	}

	public $test_http_signature = array('url', 'wantedtext');
	function test_http($url, $wanted_text) {
		if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) err("Not a valid URL: $url");
		// paranoid:
		if(strpos($url, '..') !== false || !preg_match('/^http/', $url)) err("Bad URL: $url");
		// even more paranoid 
		if(! (preg_match('#^https?://[a-z0-9.-]+.uni-frankfurt.de/#', $url) || preg_match('#^https?://riedberg.tv/#', $url))) err("Need a uni Frankfurt URL"); // simple but dirty change to allow https://riedberg.tv
		// using the HTTP PECL module (unfortunately here not present)
		/*
		$response = http_get($url, array('timeout'=>5), $infos);
		$response_code = $info['response_code'];
		$website_body = http_parse_message($response)->body; 
		*/
		// so use CURL, since it is present
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, '-'); // store cookies during session
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // HTTPS-Probleme ignorieren
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // HTTPS-Probleme ignorieren
		$response = curl_exec($ch); // response is long string with Headers, Redirections, etc
		if(isset($_GET['debug'])) {
			print "<pre>Loaded $url using CURL:\n";
			var_dump($response); // to watch the output (is nice!)
			exit;
		}
		curl_close($ch);
		// find the HTTP Status codes in the response. Since following Refers
		// was activated, there may be multiple "HTTP/1.0 123 Statustext" lines
		preg_match_all('/^HTTP.*(\d{3}).*$/im', $response, $http_stati);
		// $http_stati = { [0]=> { full strings}, [1] => {["123"],["123],..}}
		// get last status code:
		$response_code = end(end($http_stati));
		$found_wanted_text = $wanted_text ? (strpos($response, $wanted_text) !== false) : 'n.a.';
		$online_status = ($response_code < 400) && ($wanted_text ? $found_wanted_text : True);
		$this->textit($online_status, $text, $label_status);
		return compact('url', 'wanted_text', 'online_status', 'text', 'label_status', 'response_code', 'found_wanted_text');
	}
}

if(isset($_GET['s'])) {
	print '<h1>PhysikOnline: Online Status mit PHP</h1><pre>';
	highlight_file(__FILE__); exit();
}

run_json_rpc(new HostQueries());




