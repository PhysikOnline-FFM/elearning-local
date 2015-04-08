<html>
<head>
  <meta charset="utf-8">
  <title>PhysikOnline Talks und Präsentationen - Goethe-Universität Frankfurt</title>
</head>
<body>
<h1>Talkdatenbank im POTT</h1>
<p>Hier werden <a href="https://elearning.physik.uni-frankfurt.de/projekt/report/17">alle Talks</a>
dynamisch aus dem <a href="https://elearning.physik.uni-frankfurt.de/projekt/">POTT</a> geladen, mithilfe
des <a href="https://elearning.physik.uni-frankfurt.de/projekt/ticket/1068">Vortragsdatenbank</a>-Programmes.
Dies ist eine Referenzimplementierung in PHP, die serverseitig die benötigten Daten lädt, sodass die Ergebnisse
suchmaschinendurchsuchbar sind. Vergleiche auch die hübschere, aber nicht barrierefreie
<a href="jquery-talks.htm">javascriptbasierte Seite: Talkdatenbank-Visualisierung</a>.

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
