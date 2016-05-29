<?php

error_reporting(E_ALL & ~E_NOTICE);

// Ausschreibungsmagic in header.php
include "header.php";

?>

<div id="main-headElement" class="jumbotron">
	<div id="po-headElement">
		<img src="../img/logo_big.jpg">
		<h1>PhysikOnline wants you!</h1>
	</div>
</div>

<div class="container" id="main-container">
<h1>Kreatives Mitgestalten</h1>
PhysikOnline steht f&uuml;r die Idee, als Studenten an der Goethe Universit&auml;t das Studium der Physik mitzugestalten und kreativ zu erg&auml;nzen.
Was darunter genau zu verstehen ist, ist eine zentrale Frage der wir regelm&auml;&szlig;ig in offenen Diskussionen nachgehen.
In der Vergangenheit sind daraus ganz unterschiedliche Projekte hervorgegangen, wie das <b>Elearning System</b> des Fachbereichs, <b>PhysikOnline-TV</b> (Podcast-Wiki Physik)
und die gerade ausgelaufene (bzgl. Förderzeit) kollaborative Arbeits- und Lernplatform <b>POKAL</b>.
In diesen Sparten spiegeln sich die diversen Interpretationen der oben genannten Zielsetzung wider,
die ma&szlig;geblich durch die Zusammensetzung der Gruppe von Studenten hinter PhysikOnline bestimmt wird.
Deshalb sind wir auf der Suche nach interessierten und motivierten Studenten, <b>wie Dir!</b>
Aus jedem neuen Kopf entspringen frische Ideen, die diskutiert und umgesetzt werden wollen.
PhysikOnline ist die studentische Plattform, wo genau das realisiert werden kann!

<h2>Umsetzen von Ideen</h2>
Die Herausforderung ist dabei anf&auml;ngliche, wage Ideen zu konkretisieren und nach und nach in Taten zu &uuml;bersetzen.
Hierf&uuml;r sind meist auch Gelder (z.B. f&uuml;r Kameras, Videoschnittsoftware, Server, ...), R&auml;ume und andere Hilfe notwendig.
Diese versuchen wir regelm&auml;&szlig;ig z.B. durch Vermarktung der Ideen in Vortr&auml;gen vor potentiellen Unterst&uuml;tzern
und nutzen von F&ouml;rderungsprogrammen der Universit&auml;t und des Landes Hessen zu gewinnen.
Zus&auml;tzlich k&ouml;nnen wir durch die mittlerweile langj&auml;hrige Integration in den Fachbereich auch von dort auf Unterst&uuml;zung hoffen.
Ein erfolgreiches studentisches Zusammenarbeiten bei PhysikOnline setzt also nicht nur Kreativit&auml;t sondern auch Projektorganisation voraus!

<h2>Studenten</h2>
Es liegt in der Natur des Curriculums des Physikstudiums, dass es nach planm&auml;&szlig;ig f&uuml;nf (Master) Jahren endet.
Da PhysikOnline ein ausschlie&szlig;lich studentisches Projekt ist, unterliegt es demnach zwangsl&auml;fig einem Schrumpfungsprozess,
dem es entgegenzuwirken gilt. Nicht nur aus Diversit&auml;tsgr&uuml;nden sind demnach explizit motivierte
<strong>Studenten eines jeden Semesters</strong> (und auch angrenzender Fachbereiche) herzlich zur Teilnahme eingeladen.
<b>Es bedarf nun unbedingt neuer Gesichter im Team</b>, da das von uns etablierte und gepflegte System für den reibungslosen Vorlesungsbetrieb essentiell geworden ist und wir in die Jahre gekommen sind.
<br>

<h2>Melde dich!</h2>
Wenn du Interesse hast, uns alte Hasen abzulösen, weil du alles besser und moderner gestalten willst, dann w&uuml;rden wir Dich sehr gerne kennenlernen!
Schreib uns einfach eine unverbindliche Mail an <a href="mailto:elearning@th.physik.uni-frankfurt.de">elearning@th.physik.uni-frankfurt.de</a>.
<br><br>
Dein PhysikOnline Team!
<br><br>
PS: Wenn Du dich konkret f&uuml;r PhysikOnline/RiedbergTV interessierst, dann gibt es unterhalb noch mehr Infos zu den Stellenausschreibungen.


<hr />


<h1>Stellenausschreibungen und Bewerbungen <small> bei PhysikOnline</small></h1>
<!--
 Debugginginfo über Ausschreibungen:
<?php
 ###var_dump($ausschreibungen);
 ###var_dump($ausschreibungen_data);
?>
-->

<p>Laufende Ausschreibungen:</p>

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
	?><div>Derzeit leider <b>keine</b> offenen Positionen!
	<p>Wir haben zurzeit keine Stellen ausgeschrieben. Wenn du unsere Arbeit gut findest, kannst
	du uns trotzdem jederzeit schreiben. Es lohnt sich allemal: Wir arbeiten mit anderen
	Uni-Organen zusammen, die auch oft Verstärkung suchen.
	Siehe dazu <a href="/go/impressum">das Impressum</a> für Kontaktdaten.
	</div><?php
}
?>

	</div><!-- end of #main -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</body>
</html>
