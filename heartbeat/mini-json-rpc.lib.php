<?php
/**
 * A minimal JSON-RPC like library. Usage:
	class YourClass {
		public $some_method_signature = array('the-a', 'some-b', 'c');
		function some_method($a, $b, $c) {
			return array('a'=>$a, 'b'=>$b, 'c'=>$c);
		}
	}
	run_json_rpc(new YourClass());
 * Then calling ?query=some_method&the-a=1&some-b=2&c=foo will return a JSON document
	{'a':1,'b':2,'c':'foo'}
 * So this is like very simple RPC with exposing all public methods of a PHP class.
 * The signature workaround is for mapping unnamed PHP function arguments to named
 * GET arguments (this would be so much nicer in Python).
 * There is not much more to say :D
 *
 * Public Domain, Sven Koeppel Feburary 2014 for PhysikOnline
 *
 **/


# error_reporting(E_ALL);
# ini_set("display_errors", 1);

if(__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
	print '<h1>Mini RPC Library</h1><pre>';
	highlight_file(__FILE__); exit();
}

function err($msg) {
	print '{"error":"'.$msg.'"}';
	exit;
}

function run_json_rpc($class_instance) {
	header("Content-Type: application/json");
	// do not cache results!
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

	$queries = $class_instance; //new HostQueries();
	$allowed_queries = get_class_methods($queries);
	$query_signatures = array_map(function($x) use ($queries) {
		return $queries->{$x.'_signature'}; }, $allowed_queries);

	if(!isset($_GET['query']) or !in_array($_GET['query'], $allowed_queries))
		err("Need Query ?query=..., available are ".implode(', ',$allowed_queries));

	// collect arguments
	$args = array();
	$query = $_GET['query'];
	$query_index = array_search($query, $allowed_queries);
	$signature = $query_signatures[$query_index];
	foreach($signature as $v) {
		if(!isset($_GET[$v]))
			err("Query '$query' needs argument $v");
		$args[] = $_GET[$v];
	}

	//err("Calling HostQueries->$query(".implode(", ",$args).")");
	$ret = call_user_func_array(array($queries, $query), $args);
	print json_encode($ret);
}

