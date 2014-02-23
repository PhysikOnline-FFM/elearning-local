<!doctype html>
<html>
<title>Ping Onlines</title>
<meta name="viewport" content="width=300, initial-scale=1">
<style>
img { width: 64px; height: 64px; }
td, tr { border: 1px solid #aaa; }
</style>
<body>
<h2>PhysikOnline Heartbeat</h2>
<table>
<tr><th>Online?<th>Wer
<?php

#$hosts_file = "/home/koeppel/office.txt";

foreach(file($hosts_file) as $line) {
	// skip comments
	if(preg_match("/^\s*$|^\s*#/", $line)) continue;

	// format of file:
	// hostname 	description text (tab before)
	if(!preg_match("/^([a-zA-Z0-9.-]+)\s+([a-zA-Z0-9.-]+)\s+(.*)$/", $line, $m)) {
		print "Bad line: $line";
	}
	list(,$host, $user, $desc) = $m;
	$idle = "idle-status.php?host=$host&amp;user=$user";
	# todo: link only when up, therefore javascript
	print "<tr><td><img src='online-status.php?img&host=$host'><td><a href='$idle'>$desc</a>";
}
