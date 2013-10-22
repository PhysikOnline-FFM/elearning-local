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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>Jobs bei PhysikOnline: Stellenangebote und Bewerben!</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="https://elearning.physik.uni-frankfurt.de/Customizing/global/skin/physik/physik.css?vers=4-3-0-Beta1--2012-09-04" />
	<link rel="stylesheet" type="text/css" href="bewerbung.css">

	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript" src="jquery.collapse.js"></script>
	<script type="text/javascript">
		$(function(){
			$(".positionen").collapse({
				query: 'div div.heading',
				show: function() { this.slideDown(300); },
				hide: function() { this.slideUp(300);  },
			});
		});
	</script>
</head>
<body class="std">
<div id="ilAll">
<div class="ilStartupFrame" id="po-startupframe">
	<div id="il_startup_logo">
		<div class="po-head">
			<a id="po-logo" href="/" title="Zur Physik Online Startseite"><img src="https://elearning.physik.uni-frankfurt.de/Customizing/global/skin/physik/src/logo_small-new.png" alt="Physik eLearning" /></a>
			<div class="ilTopTitle">
				<a href="/" title="Zur Startseite des Lernportals">PhysikOnline</a>
				<div class="po-subtitle">Das eLearning-Portal des Fachbereichs Physik</div>
			</div>
		</div>
	</div>
	<div id="il_startup_content">
		<div class="po-content po-clearafter">
