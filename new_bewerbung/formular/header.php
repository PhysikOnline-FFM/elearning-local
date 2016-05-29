<?php
/* Ausschreibungsmagic und Seitenheader */

// Lese Ausschreibungen aus Verzeichnis "ausschreibungen" ein.

$ausschreibungen = glob("../ausschreibungen/*.htm");
$ausschreibungen_data = array_map("get_meta_tags", $ausschreibungen);
$ausschreibungen_text = array_map("file_get_contents", $ausschreibungen);

function print_ausschreibung($id) {
	global $ausschreibungen_data, $ausschreibungen_text;

	?>
	 <div class="panel-group">
	  <div class="panel panel-default">
	    <div class="panel-heading">
	      <h4 class="panel-title">
	        <a data-toggle="collapse" href="#<?=$id?>"><?=$ausschreibungen_data[$id]["titel"]; ?></a>
	      </h4>
	    </div>
	    <div id="<?=$id?>" class="panel-collapse collapse">
	    <?php
			$text = $ausschreibungen_text[$id];
			print preg_replace("/^(.+)<body>/s", "", $text);

		?>
	    </div>
	  </div>
	</div>
	<?php
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
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Jobs bei PhysikOnline: Stellenangebote und Bewerben!</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

	<link rel="stylesheet" href="css/bewerbung.css">

</head>

<body lang="de">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

