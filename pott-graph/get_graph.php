<?php
/*
    Ein "Links"-Extraktor aus Trac, arbeitet aehnlich /local/go/kurzurl-data.php.
    Geschrieben zur Graph-Visualisierung am 09.01.2014.
    -- Sven Koeppel, GPL
*/

if(isset($_GET['s'])) {
	print '<h1>Generating a Graph life from Trac</h1><pre>';
	highlight_file(__FILE__); exit();
}

header("Content-Type: application/json");

# extract connection data and connect to db
$trac_config_file = "/home/elearning-www/trac-data/conf/trac.ini";
$trac_config = file_get_contents($trac_config_file);
preg_match("#mysql://([^:]+):([^@]+)@([^:]+):(\d+)/([^\s]+)#", $trac_config, $connection_data)
	or die("Error: Could not find out mysql connection data.");
list(,$user,$pwd,$host,$port,$db) = $connection_data;
mysql_connect("$host:$port", $user, $pwd) or die("Could not connect do mysql server");
mysql_set_charset('utf8'); // perhaps again invalid UTF-8...
mysql_select_db($db) or die("Could not sel db");

// Link patterns to find.
// for valid links see https://elearning.physik.uni-frankfurt.de/projekt/wiki/TracLinks
// using the "Study" pattern ...S to speed up
// Each regex has to give on match [1] the key!
$wikiopen = preg_quote('[['); $wikiclose = preg_quote(']]');
$link_regex = array(
	// find any ticket links, they are like #123 or [[ticket:123]],
	// even attachment:abc.xyz:ticket:123
	"/(?:#|ticket:)(\d+)/S",
	// simple wiki links (no one uses such ones)
	"%wiki:/?([a-z0-9/_-]+)%Si",
	// ordinary wiki links
	// at beginning skip external links, Images, Tags like BR, Macros...
	// at end skip anchors, link labels, queries, etc.
	"%$wikiopen(?!http)(?!Image)(?!BR)(?:/wiki/)?(.+?)(?:[#|(].+?)?$wikiclose%iS",
	// server relative wiki links, redundant to above
	//"/\\[\\[/wiki/([^\\]#|]+)(?:#.+?)?\\]\\]/S",
	// and even "stupid" full links
	"%(?:$wikiopen)?https?://elearning.physik.uni-frankfurt.de/projekt/wiki/([^#?\\]|\s,]+)(?:(?:#|\\?).+?)*%Si",
	// ^^ hier todo: urlencode!
	// "stupid" full ticket links
	"%(?:$wikiopen)?https?://elearning.physik.uni-frankfurt.de/projekt/ticket/(\d+)%Si",

	// Todo (schwierig!): 
	// [[TitleIndex(POKAL/,hideprefix)]] --- aufloesen!!

	// Nicht so schwer: Triviale Queries aufloesen
	// allerdings kann hier eine Liste stehen, vgl. Problembeschreibung in POTT #811
	"%${wikiopen}TicketQuery\(id=(\d+)%Si"
	
);

function extract_links($string, $key) { // $key for debugging
	global $link_regex;
	$all_matches = array();
	$debug = false;

	foreach($link_regex as $re) {
		$ret = preg_match_all($re, $string, $matches);
		if($debug && !is_numeric($key)) {
			print "key: $key\n";
			var_dump($matches);
		}
		if($ret) {
			$all_matches += $matches[1];
		}
		unset($matches);
	}

	return $all_matches;
}

function trac_link($type, $key) {
	switch($type) {
		case 'ticket':	return "/projekt/ticket/$key";
		case 'wiki':	return "/projekt/wiki/$key";
		default:	return "/notknowntype?$key";
	}
}

# setup data storages

# adjazenzliste: array(1=>[2,3,4], 2=>[...]) je ticketid=>[liste von ticketids]
$adjlist = array();
# nodedata: Identifier => array(irgendwelche Daten)
$nodedata = array();

# Read data by SQL queries to the Trac database. Each query must output at least two columns:
# +-----------------------------+
# | key       | text            |
# +-----------------------------+
# The key will be used to identify the node (like a number for tickets and a string for wiki pages).
# Any further columns will be put into $nodedata.
$queries = array(
	"SELECT 'ticket' as `type`, `id` as `key`, `description` as `text`, `status` as `status` FROM `ticket`",
	'SELECT "ticket" as `type`, `ticket` as `key`, `newvalue` as `text` FROM `ticket_change` WHERE (not newvalue ="") AND (field LIKE "%comment%" OR field = "description" OR field = "summary" )  ORDER BY ticket',

	# test: Wikiseiten. Dieses Query bekommt alle neusten Wikiseiten
	# Intern-Seiten rausgefiltert, weil Seitenname sonst publik waere
	'SELECT "wiki" as `type`, `name` as `key`, `text`, MAX(version) as `_version` FROM `wiki` WHERE not `name` LIKE "%Intern%"  GROUP by name   ORDER BY version desc'
);
foreach($queries as $q) {
	$res = mysql_query($q);
	while($row = mysql_fetch_assoc($res)) {
		# extract basic information
		$key = $row['key']; #intval($row['key']);
		$ex = extract_links($row['text'], $key);
		
		# further information goes to $nodedata
		$skip_keys = preg_grep('/^text$|^_/', array_keys($row));//, PREG_GREP_INVERT);
		foreach($skip_keys as $k) unset($row[$k]);
		$row['link'] = trac_link($row['type'], $key);
		$nodedata[$key] = isset($nodedata[$key]) ? array_merge($nodedata[$key], $row) : $row;

		#if(empty($ex)) continue;  # das geht nicht, sonst bleiben isolierte tickets unbehandelt!!

		# das hier trifft nur bei tickets als zielen zu -.-. klappte!
		#$ex = array_map('intval', $ex); # sicherstellen, dass alles Zahlen sind

		$adjlist[$key] = isset($adjlist[$key]) ? array_merge($adjlist[$key], $ex) : $ex;
	}
}

# Mehrfachkanten entfernen
foreach($adjlist as $k => $l) $adjlist[$k] = array_unique($l);


# das waer jetzt das einfache format
# {ticketid:[liste knoten zu anderen ticketids],...}
# print json_encode($adjlist);

# give an array from $associative with indexes from $indexes.
function array_by_index($associative, $indexes) {
	return array_map(function($x) use ($associative){
		return $associative[$x];
	}, $indexes);
}

# mache Format
# {"nodes": [   {"ticket":1}, {"ticket":2}, {...} ],
#  "links": [  {"source":1, "target":0 }, {"source":3,"target":7}, ... ] }
$sorted_ids = array_keys($adjlist); sort($sorted_ids);
$nodes_list = array_by_index($nodedata, $sorted_ids);
$id2nodepos = array(); # Inverse von $sorted_ids!!
foreach($sorted_ids as $i=>$id) { $id2nodepos[$id] = $i; }

#print_r($sorted_ids);
#print_r($id2nodepos);

$links_list = array();
foreach($adjlist as $from => $to_list) {
	foreach($to_list as $to) {
		if(! isset($id2nodepos[$from]) || !isset($id2nodepos[$to]) )
			# we can't find that node. So this is a broken link.
			continue;
		$links_list[] = array(
			#"from" => $from, "to" => $to,
			# mapping in the other space
			"source" => $id2nodepos[$from],
			"target" => $id2nodepos[$to],
			"value" => 1,
		);
	}
}

#print_r($links_list);

$result = array("nodes" => $nodes_list, "links" => $links_list);
print json_encode($result);




