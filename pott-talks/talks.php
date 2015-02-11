<html>
<head>
  <meta charset="utf-8">
  <title>POTT talks</title>
</head>
<body>
<h1>Talks im POTT</h1>
vgl. <a href="https://elearning.physik.uni-frankfurt.de/projekt/ticket/1068">#1068</a>.

<ul>
<?php
require "talkdb.php";

connect_to_pott();
$talks = crawl_talks();

$tmpl=<<<TPL
<li class="{css_keywords}"><em>{presenter}</em>: <a href="{slides}">{title}</a> ({date})
   <br>{place}
   <br><em>{joined_keywords}</em> <a href="http://physikelearning.de/{ticket_id}">#{ticket_id}</a></li>
TPL;

function giver($list) {
	// magic shorthand with default
	return function($key, $default) use ($list) {
		return isset($list[$key]) ? $list[$key] : $default;
	};
}

foreach($talks as $talk) {
	print preg_replace_callback("/\{([a-z_]+)\}/i", function ($m) use ($talk) {
		$key = $m[1];
		$get = giver($talk);
		switch($key) {
			case 'css_keywords':
				return implode(' ', $get('keywords', array()) );
			case 'joined_keywords':
				return implode(', ', $get('keywords', array()) ); 
			default:
				return $get($key, "[$key]");
		}
	}, $tmpl);
}
