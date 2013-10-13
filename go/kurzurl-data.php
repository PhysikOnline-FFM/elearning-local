<?php
/*
    Ein Trac-Wikiseiten-Extraktor, der MySQl-Verbindungsdaten aus der trac.ini
    ausliest und eine Wikiseite rausholt.
*/

header("Content-Type: text/plain");

$trac_config_file = "/home/elearning-www/trac-data/conf/trac.ini";
$kurzlink_wikititle = "Kurzlinks";

$trac_config = file_get_contents($trac_config_file);

preg_match("#mysql://([^:]+):([^@]+)@([^:]+):(\d+)/([^\s]+)#", $trac_config, $connection_data)
	or die("Error: Could not find out mysql connection data.");
list(,$user,$pwd,$host,$port,$db) = $connection_data;

mysql_connect("$host:$port", $user, $pwd) or die("Could not connect do mysql server");
mysql_select_db($db) or die("Could not sel db");
$res = mysql_query("SELECT  `text` FROM `wiki` WHERE `name` = '$kurzlink_wikititle' ORDER BY `version` DESC LIMIT 1");
$wikitext = ($row = mysql_fetch_assoc($res)) ? $row["text"] : "No Kurzlinks found";

print $wikitext;
