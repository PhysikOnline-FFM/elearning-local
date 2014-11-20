<!DOCTYPE html>
<html>
<head>
<title>Alle WikiExtras-Trac-Icons</title>
<meta charset="utf-8">
<style>
	span { float: left; display: inline-block; margin: 5px; }
	img { width: 16px; height: 16px; }
	input { border: none; padding: 0; font-family: monospace; width: 300px; }
</style>
<script type="text/javascript" src="/local/Customizing/global/skin/po-v3.0/js/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
	$("input").hover(function() {
		$(this).select();
	});
});
</script>
</head>
<body>
<h1>Alle WikiExtras-Trac-Icons auf einen Blick</h1>

<p>Es laden nun <b>
<?php
$icondir = "/home/elearning-www/trac-data/new-trac20-plugins-deploy/htdocs/wikiextras-icons-16";
$webdir = "/projekt/chrome/wikiextras-icons-16";

$icons = glob($icondir . "/*.png");
echo count($icons);
?> </b> Bilder. Das könnte deinen Browser crashen. Chrome wäre ein Kandidat, der das packt.</p>

<p>Per Mousehover kann man schnell einen Namen auswählen und kopieren, um ihn einzubinden im POTT.</p>
<p>-- Sven, 10.10.2013</p>
<hr>

<?php

foreach($icons as $icon) {
	$name = pathinfo($icon, PATHINFO_FILENAME);
	print "<span><img src='$webdir/$name.png'> <input type='text' value='(|$name|)'></span>\n";
}
