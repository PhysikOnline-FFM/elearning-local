<?php
/* Ausschreibungsmagic und Seitenheader */

// Lese Ausschreibungen aus Verzeichnis "ausschreibungen" ein.

$ausschreibungen = glob("../ausschreibungen/*.htm");
$ausschreibungen_data = array_map("get_meta_tags", $ausschreibungen);
$ausschreibungen_text = array_map("file_get_contents", $ausschreibungen);

function print_ausschreibung($id) {
	global $ausschreibungen_data, $ausschreibungen_text;

	?><div>
	   <div class="heading"><span class="triangle"></span> <?=$ausschreibungen_data[$id]["titel"]; ?></div>
	   <div class="content">
		<?php
			$text = $ausschreibungen_text[$id];
			print preg_replace("/^(.+)<body>/s", "", $text);

		?>
	   </div>
	</div><?php
}

function aktive_ausschreibungen() {
	global $ausschreibungen_data;
	return array_filter($ausschreibungen_data, function($x) {
			return isset($x["titel"]) && isset($x["aktiv"]) && preg_match("/Ja|Yes|True/i", $x["aktiv"]);
		});
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Jobs bei PhysikOnline: Stellenangebote und Bewerben!</title>
	
	<link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic|Yanone+Kaffeesatz:400,300,200,700|Oswald:400,300,700' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Bitter:400,700,400italic' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic|Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Karla:400,400italic,700,700italic' rel='stylesheet' type='text/css'>

	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/bewerbung.css" rel="stylesheet" type="text/css" />
	
	<script src="js/html5shiv.js"></script>
	<script src="js/jquery.js"></script>
	<script src="js/jquery.collapse.js"></script>
	
	<script type="text/javascript">
		$(function(){
			$(".positionen").filter(function(){
				// 2014 hinzugekommen:
				// nur wenn mehr als eine Position offen ist, Aufklappmenue aktivieren.
				show_collapse = $(this).find("div div.heading").length > 1;
				if(!show_collapse) {
					// uebelst haesslich: Aufklappmenue simulieren.
					$(this).find('div div.heading').addClass("open").wrapInner("<a/>");
				}
				return show_collapse;
			}).collapse({
				query: 'div div.heading',
				show: function() { this.slideDown(300); },
				hide: function() { this.slideUp(300);  },
			});
		});
	</script>
</head>
<body lang="de">
	<div id="wikitools"></div>

	<div id="physik-online-bar">
		<a id="po-logo" href="/" title="Zur Physik Online Startseite"><img src="img/po-tv-logo.png" alt="" /></a>
		<h1 id="logo-title">
			<span>RiedbergTV</span>
		</h1>
	</div>

	<div id="main">
		<header class="main-bodys">
			<nav id="site-navigation">
				<ul>
				</ul>
			</nav>
		</header>

		<div id="the-grid" class="main-bodys">
