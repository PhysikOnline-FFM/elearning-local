<?php
/**
 * A PHP implementation of POTT #1068, the idea to use
 * Trac tickets to manage a small database of talks and publications.
 *
 * To do so, tickets contain informations in their description by
 * means of simple trac definition lists. Talks and publications can
 * be filtered by keywords.
 *
 * -- Sven, Feb 2014
 *
 */

function connect_to_pott() {
	# extract connection data and connect to trac db
	# this is from the "pott-graph" php scripts
	$trac_config_file = "/home/elearning-www/trac-data/conf/trac.ini";
	$trac_config = file_get_contents($trac_config_file);
	preg_match("#mysql://([^:]+):([^@]+)@([^:]+):(\d+)/([^\s]+)#", $trac_config, $connection_data)
		or die("Error: Could not find out mysql connection data.");
	list(,$user,$pwd,$host,$port,$db) = $connection_data;
	mysql_connect("$host:$port", $user, $pwd) or die("Could not connect do mysql server");
	mysql_set_charset('utf8');
	mysql_select_db($db) or die("Could not sel db");
}

function parse_description($desc) {
	# extracts talk information
	$num_found = preg_match_all("/^\s{2,}([a-z0-9_\-]+)::\s*(.+)$/im", $desc, $matches, PREG_SET_ORDER);
	if($num_found && $matches) {
		$ret = array();
		foreach($matches as $line) {
			$ret[$line[1]] = trim($line[2]);
		}
		return $ret;
	} else {
		return array("nothing"=>"found");
	}
}

function fix_links(&$talk_data) {
	# fix pott internal links like '[[raw-attachment:whatever.pdf]]'
	# to some URL like http://elearning.physik..../attachment/...whatever.pdf
	#
	# This works by call-by-reference, that is, $talk_data is changed in-place.
	$wikiopen = preg_quote('[['); $wikiclose = preg_quote(']]');
	$ticket = $talk_data['ticket_id'];
	foreach($talk_data as &$value) {
		// resolve links to attachments
		$value = preg_replace("/(?:$wikiopen)?(?:raw-)?attachment:(.*?)(?:$wikiclose)?/i",
			"https://elearning.physik.uni-frankfurt.de/projekt/raw-attachment/ticket/$ticket/\\1",
			$value);
		// whaa... remaining brackets
		$value = str_replace(']]', '', $value);
	}
	unset($value); // dereference
}

function crawl_talks() {
	$res = mysql_query("SELECT id,description,keywords FROM ticket WHERE keywords LIKE '%talk%'");
	$ret = array();
	while($row = mysql_fetch_assoc($res)) {
		$talk_data = parse_description($row['description']);
		$talk_data['ticket_id'] = $row['id'];
		$talk_data['keywords'] = preg_split('/\s*,\s*/', $row['keywords']);
		fix_links($talk_data);
		//print_r($talk_data);
		$ret[] = $talk_data;
	}
	return $ret;
}

if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
	connect_to_pott();
	$talks = crawl_talks();
	header("Content-Type: application/json; charset=utf-8");
	print json_encode($talks);
} else {
	// this file is included. Use it as a library
}

