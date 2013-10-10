<?php
error_reporting(E_ALL & ~E_NOTICE);

if (!defined('SCRIPTNAME')) {
echo 'Unzul&auml;ssiger Scriptaufruf';
exit;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
// Templatedaten holen fuer Templates ohne Ersatzvariablen
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function get_tdata($tmplname) {
if(file_exists($tmplname)) {
$lines = implode("",file($tmplname));
return $lines;
} else {
print_scripterror("Fehler!", "Die Datei: $tmplname kann nicht ge&ouml;ffnet werden");
exit;
}
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
// Templateinhalt holen fuer Einzelausgaben und Ausgaben in
// while, for und foreach Schleifen
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function get_tpldata($templatename) {

if(file_exists($templatename)) {
$templatecontent = file($templatename);
return $templatecontent;
} else {
print_scripterror("Fehler!", "Die Datei: $templatename kann nicht ge&ouml;ffnet werden");
exit;
}
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
// Templateparser
// Aufrufbeispiel:
// echo $tp_content_out = templateparser($templatecontent, $wertearray);
// $templatecontent 	= Template HTML Code
// $wertearray 			= Zu ersetztende Platzhalterdaten
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function templateparser($templatedatei, $wertearray) { 

if(is_array($wertearray)) {
foreach($wertearray as $key => $value) { 
$suchmuster = "/<%%(".strtoupper($key).")%%>/si";

// Gefundene Platzhalter mit Werten aus $wertearray ersetzen
$templatedatei = preg_replace($suchmuster, $value, $templatedatei); 
} 
// Nicht ersetzte Platzhalter aus Template entfernen
$templatedatei = preg_replace("/((<%%)(.+?)(%%>))/si", '', $templatedatei);
}

return (implode("", $templatedatei)); 
}




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
// Scripterror
// Sofortiger Scriptabbruch bei Fehlern
// gibt einfache HTML Seite aus mit Fehlerhinweisen 
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function print_scripterror($titel = '', $fehlertext = '') {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Physik-Online Impressum</title>


  <meta content="text/html; charset=UTF-8" http-equiv="content-type">

  <link href="../Customizing/global/skin/bluesun/bluesunrise.css" type="text/css" rel="stylesheet">

  <link href="../Customizing/global/skin/bluesun/bluesunrise_cont.css" type="text/css" rel="stylesheet">

  <link href="templates/default/delos_cont.css" type="text/css" rel="stylesheet">

  <link type="image/x-icon" href="favicon.ico" rel="shortcut icon">

</head>



<body class="">

<img style="position: absolute; top: 3px; left: 6px;" src="../Customizing/global/skin/bluesun/images/bluesun/logo_small.gif" border="0">
<div class="ilMainHeader" style="min-width: 600px;">
<div class="il_header_oben">
<div class="il_Vert_Strich_oben"> <span class="il_PDTitle">Physik Online</span><br>

<span class="il_PDSubtitle">Das eLearning-Portal des
Fachbereichs Physik</span> </div>

</div>

<div class="il_MainMenu" style="clear: both;">
<div class="il_Vert_Strich_unten"> <a class="MMInactive" href="javascript:history.back();">Zur&uuml;ck</a>
</div>

</div>

<div style="clear: both;"></div>

</div>

<div class="il_HeaderInner">
<h1 class="il_Header"><img style="vertical-align: middle;" alt="ILIAS-System" src="../Customizing/global/skin/bluesun/images/icon_root_b.gif" id="headerimage" border="0">&nbsp; Stellenangebot f&uuml;r Videoproduktion</h1>

</div>

<div style="clear: both;"></div>

<h2><?php echo $titel; ?></h2>

<?php echo $fehlertext; ?>


<?php
exit;
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
?>