<?php

error_reporting(E_ALL & ~E_NOTICE);

// Ausschreibungsmagic in header.php
include "header.php";

?>


<h1>Stellenausschreibungen und Bewerbungen <small> bei PhysikOnline</small></h1>

<!--
 Debugginginfo über Ausschreibungen:
<?php
 var_dump($ausschreibungen);
 var_dump($ausschreibungen_data);
?>
-->

<p>Laufende Ausschreibungen:</p>
<div class="positionen">
<?php
$found = 0;
foreach($ausschreibungen_data as $id => $data) {
	if(!isset($data["titel"]) || !isset($data["aktiv"])) {
		print "Ungültige Ausschreibung: ".$ausschreibungen[$id];
		continue;
	}

	if(preg_match("/Ja|Yes|True/i", $data["aktiv"])) {
		print_ausschreibung($id);
		$found++;
	}
}
if(!$found) {
	?><div class="no-positions">Derzeit leider <b>keine</b> offenen Positionen!
	<p>Wir haben zurzeit keine Stellen ausgeschrieben. Wenn du unsere Arbeit gut findest, kannst
	du uns trotzdem jederzeit schreiben. Es lohnt sich allemal: Wir arbeiten mit anderen
	Uni-Organen zusammen, die auch oft Verstärkung suchen.
	Siehe dazu <a href="/go/impressum">das Impressum</a> für Kontaktdaten.
	</div><?php
}
?>
</div>

<p>Ehemalige Ausschreibungen:</p>

<div class="positionen">
<?php
$found = 0;
foreach($ausschreibungen_data as $id => $data) {
	if(!isset($data["titel"]) || !isset($data["aktiv"])) {
		print "Ungültige Ausschreibung: ".$ausschreibungen[$id];
		continue;
	}

	if(preg_match("/Nein|No|False/i", $data["aktiv"])) {
		print_ausschreibung($id);
		$found++;
	}
}
if(!$found) {
	?><ul><li>Keine abgelaufenen Ausschreibungen vorhanden</ul>
	<?php
}
?>
</div>

<!--<div style="font-size: 1.5em; text-align: center;"><a href="bewerben.php">Jetzt
bewerben</a></div>-->


</body>
</html>