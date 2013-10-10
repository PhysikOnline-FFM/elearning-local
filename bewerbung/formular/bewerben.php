<?php
error_reporting(E_ALL & ~E_NOTICE);
define('SCRIPTNAME', 'index');
include_once "global_func.php";
// Seitenheader und Layout oben
echo get_tdata("content/header.html");

// send mail
/* function send_email($mail_adress, $name, $mail_body ){

	$mail_subject = "SeLF-Bewerbung";
	$admin_mail = "jan.uphoff@spamschutz";

	// send the mail
	include_once "./../../Services/Mail/classes/class.ilMimeMail.php";
	$mmail = new ilMimeMail();
	$mmail->autoCheck(false);
	$mmail->From($admin_mail);																		
	$mmail->Subject($mail_subject);
	$mmail->To($mail_adress);
	$mmail->Body($mail_body);

	$mmail->Send();
} */


// Formular
if ($_POST) {
$name = $_POST['name'];
$studiengang = $_POST['studiengang'];
$semester = $_POST['semester'];
$informationen = $_POST['informationen'];
$email = $_POST['email'];

$an = "elearning@th.physik.uni-frankfurt.de";
//$an = "elearning@th.physik.uni-frankfurt.de";
 
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





 # Diese Nachricht wird an E-Mail-Adresse gesendet
 $text = "Diese Bewerbung wurde am $day, den $day_num.$month.$year um $time Uhr via Webformular verschickt:\n\nName: $name\nE-Mail: $email\nStudiengang: $studiengang\nSemester: $semester\nInformationen: \n$informationen";
 $text2 = "Hallo $name,\n\ndeine Bewerbung wurde am $day, den $day_num.$month.$year um $time Uhr via Webformular verschickt. Vielen Dank.\n\nWir wollen nun erst einmal die Bewerbungen sammeln und werden uns dann vorraussichtlich Ende Oktober bei Dir melden. Bei Fragen kannst du uns jederzeit auf diese Mail antworten.\n\nViele Gruesse,\nJan und Marius\n\n\nP.S.: Folgende Angaben hast du gemacht:\n\nName: $name\nE-Mail: $email\nStudiengang: $studiengang\nSemester: $semester\nInformationen: \n$informationen";
 @mail($an, "Bewerbung auf Video-Projekt-Stelle", $text, "From: " . $email);
 @mail($email, "Bewerbung auf Video-Projekt-Stelle", $text2, "From: elearning@th.physik.uni-frankfurt.de");
 echo "Deine Bewerbung wurde gesendet. Vielen Dank.<br><br>Wir wollen nun erst einmal die Bewerbungen sammeln und werden uns dann vorraussichtlich Ende Oktober bei Dir melden. <br>
		Wenn du weitere Fragen hast, schreib uns einfach ein Mail an <a href='mailto:elearning@th.physik.uni-frankfurt.de'>elearning (at) th.physik.uni-frankfurt.de</a>.   
		<br><br>Folgende Daten wurden verschickt: <br> <br>
		<b>Name:</b> $name <br><b>E-Mail:</b> $email<br><b>Studiengang:</b> $studiengang<br><b>Semester:</b> $semester<br><b>Informationen:</b> <br><pre style='font-family:Verdana,Arial,Helvetica,sans-serif;'>$informationen</pre>";
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

<form name="Formular" method="post" action="bewerben.php"
 onSubmit="return eingaben_ueberpruefen();">
<?php
 
// Inhalt
echo get_tdata("content/bewerben.html");

?>
</form>
<?php

}


// Seitenfooter
echo get_tdata("content/footer.html");
?>
