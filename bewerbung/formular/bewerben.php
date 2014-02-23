<?php
error_reporting(E_ALL & ~E_NOTICE);
define('SCRIPTNAME', 'index');
#include_once "global_func.php";
// Seitenheader und Layout oben
include "header.php";

// Formular
if ($_POST) {
$name = $_POST['name'];
$studiengang = $_POST['studiengang'];
$semester = $_POST['semester'];
$informationen = $_POST['informationen'];
$email = $_POST['email'];
$stelle = $_POST['stelle'];

// Empfaenger aus Ausschreibung auslesen
$offene_stellen = aktive_ausschreibungen();
if(is_numeric($stelle) && isset($offene_stellen[$stelle])) {
	$an = $offene_stellen[$stelle]["mailto"];
	// Email-Suffix dranhaengen
	$emails = preg_split('/\s*,\s*/', $an);
	foreach($emails as $id => $mail) $emails[$id] .= "@elearning.physik.uni-frankfurt.de";
	$an = implode(", ", $emails);

	$stelle_lesbar = $offene_stellen[$stelle]["titel"];
} else $an = "elearning@th.physik.uni-frankfurt.de";
 
$name_tag[0] = "Sonntag";
$name_tag[1] = "Montag";
$name_tag[2] = "Dienstag";
$name_tag[3] = "Mittwoch";
$name_tag[4] = "Donnerstag";
$name_tag[5] = "Freitag";
$name_tag[6] = "Samstag";
$num_tag   = date( "w");
$day      = $name_tag[$num_tag];
$year      = date("Y");
$day_num   = date("d");
$month      = date("m");
$time      = (date("H:i")); 


# UTF8: Sonderzeichen repariert
#$headers = "Content-Type: text/plain; charset=UTF-8\n\n";
# fehlt From header und ICH HAB AUCH KENIEN BOCK MEHR DAS ZU MACHEN


 # Diese Nachricht wird an E-Mail-Adresse gesendet
 $text = "Diese Bewerbung wurde am $day, den $day_num.$month.$year um $time Uhr via Webformular verschickt:\n\nName: $name\nE-Mail: $email\nStelle: $stelle_lesbar\nStudiengang: $studiengang\nSemester: $semester\nInformationen: \n$informationen";
 $text2 = "Hallo $name,\n\ndeine Bewerbung wurde am $day, den $day_num.$month.$year um $time Uhr via Webformular verschickt. Vielen Dank.\n\nWir wollen nun erst einmal die Bewerbungen sammeln und werden uns dann vorraussichtlich Ende Oktober bei Dir melden. Bei Fragen kannst du uns jederzeit auf diese Mail antworten.\n\nViele Gruesse,\ndas eLearning-Team der Physik\n\n\nP.S.: Folgende Angaben hast du gemacht:\n\nName: $name\nE-Mail: $email\nStudiengang: $studiengang\nSemester: $semester\nInformationen: \n$informationen";
 mail($an, "Bewerbung bei PhysikOnline", $text, "From: " . $email);
 mail($email, "Bewerbung bei PhysikOnline", $text2, "From: PhysikOnline <team@elearning.physik.uni-frankfurt.de>");
 echo "Deine Bewerbung wurde gesendet. Vielen Dank.<br><br>Wir wollen nun erst einmal die Bewerbungen sammeln und werden uns dann vorraussichtlich Ende Oktober bei Dir melden. <br>
		Wenn du weitere Fragen hast, schreib uns einfach ein Mail an <a href='mailto:elearning@th.physik.uni-frankfurt.de'>elearning (at) th.physik.uni-frankfurt.de</a>.   
		<br><br>Folgende Daten wurden verschickt: <br> <br>
		<b>Name:</b> $name <br><b>E-Mail:</b> $email<br><b>Empfänger:</b> $an<br><b>Studiengang:</b> $studiengang<br><b>Semester:</b> $semester<br><b>Informationen:</b> <br><p style='font-family:Verdana,Arial,Helvetica,sans-serif;'>$informationen</p>";
}
else {
?>
<pre style="font-family:Fixedsys,Courier,monospace;"></pre>

<script type="text/javascript">
function eingaben_ueberpruefen(){
 var mail = document.Formular.email.value;
 if (document.Formular.name.value.length < 3) {
  alert("Sie haben noch keinen Namen eingegeben!")
  document.Formular.name.focus();
  return false;
 }

 else if (mail.length < 10 || mail.indexOf ('@',0) == -1 || mail.indexOf ('.',0) == -1) {
  alert("Bitte geben Sie eine gueltige E-Mail-Adresse ein.")
  document.Formular.email.select();
  return false;
 }

 else if (document.Formular.studiengang.value.length < 3) {
  alert("Bitte geben Sie einen Studiengang ein!")
  document.Formular.studiengang.focus();
  return false;
 }
 
 else if (document.Formular.semester.value.length < 1 || isNaN(document.Formular.semester.value)) {
  alert("Bitte geben Sie eine Semesteranzahl ein!")
  document.Formular.semester.focus();
  return false;
 }

 else
 return true;
}
</script>

<h2>Bewerben bei PhysikOnline</h2>

<form name="Formular" method="post" action="bewerben.php"
 onSubmit="return eingaben_ueberpruefen();">

<p>Bitte f&uuml;lle f&uuml;r die Bewerbung folgendes Formular aus. Wenn du weitere Fragen hast, schreib uns einfach ein Mail an <a href='mailto:elearning@th.physik.uni-frankfurt.de'>elearning (at) th.physik.uni-frankfurt.de</a>.
&nbsp; &rarr; <small><a href="./">Zurück zur Ausschreibung</a></small>
</p>

<?php
if(count(aktive_ausschreibungen()) == 0) {
?><div class="no-positions">
Derzeit sind <b>keine</b> Stellen ausgeschrieben!

<p>Bitte verwende diese Bewerbungsmaske nicht, sondern schreibe uns an obige E-Mail-Adresse,
wenn du Kontakt aufnehmen willst.
</div>
<?php
}
?>

 <table  id="tbl_contact">
  <tr>
   <td class="left">Name:</td>
   <td class="right"><input type="text" class="txt_field" name="name" maxlength="40"></td>
  </tr>
  <tr>
   <td class="left">E-Mail:</td>
   <td class="right"><input type="text" class="txt_field" name="email" maxlength="40"></td>
  </tr>
  <tr>
   <td class="left">Studiengang:</td>
   <td class="right"><input type="text" class="txt_field" name="studiengang" maxlength="40"></td>
  </tr>
  <tr>
   <td class="left">Semesteranzahl:</td>
   <td class="right"><input type="text" class="txt_field_nmb" name="semester" maxlength="2"></td>
  </tr>
      <?php
		$ausschreibungen_data = aktive_ausschreibungen();
		if(count($ausschreibungen_data) > 1) {
			?>
			  <tr>
			   <td class="left">Stelle:</td>
			   <td class="right"><select name="stelle">
			   <?php foreach($ausschreibungen_data as $id => $data) {
				print "<option value='$id'>".$data["titel"]."</option>";
				}
			    ?></select></td></tr><?php
		} else print '<input type="hidden" name="stelle" value="0">';
       ?>
  <tr>
   <td class="left">Weitere Informationen:<br><small>(z.B. Vorkenntnisse, Motivation, Infos &uuml;ber dich, ...)</small></td>
   <td class="right"><textarea class="txt_field" name="informationen"></textarea></td>
  </tr>
  <tr>
   <td class="left"></td>
   <td class="right"><br>
    <input class="btn" type="submit" value="Absenden"> &nbsp;&nbsp;&nbsp;
    <input class="btn" type="reset">
   </td>
  </tr>
 </table>
</form>
<?php

}

?>
