<?php
if(!isset($_POST['data'])) {
	print "Aufruf durch feedback.js per AJAX.";
	exit;
}

$data = json_decode($_POST['data']);

// vom Benutzer eingegebener Text, fakultativ
$text = $data[0]->Issue;

// Bilddaten, als URI-File codiert (data:image/png;base64,iVBORw...)
$screenshot_encoded = $data[1];
$encodedData = str_replace(' ','+',$screenshot_encoded);
$encodedData = substr($encodedData, strlen("data:image/png;base64,"));
$screenshot = base64_decode($encodedData);
$tmpfile = tempnam("/tmp", "screenshot");
rename($tmpfile, $tmpfile.=".png"); // Add file ending
file_put_contents($tmpfile, $screenshot);

// Screenshot liegt nun als Binaerbild vor

require 'PHPMailer/class.phpmailer.php';

$mail = new PHPMailer;

$mail->CharSet = "utf-8"; # ilias, pott, diese php file, js, alles in utf-8

$mail->From = 'no-answer@elearning.physik.uni-frankfurt.de';
$mail->FromName = 'eLearning Feedback';
/* spamschutz public git */ $domain = '.physik.uni-frankfurt.de';
$mail->addAddress('elearning@th'.$domain, 'eLearning-Team');
$mail->addAddress('pott@elearning'.$domain, 'POTT');

#$mail->WordWrap = 50;
$mail->addAttachment($tmpfile);
$filename = basename($tmpfile); // /tmp/datei => datei
$date = date('r');

$mail->Subject = "PO3 Feedback";
$body = <<<BODY
Ein neuer Feedback ist durch das Feedbacksystem (#655) eingereicht worden.

=== Vom Besucher ausgefüllter Text ===
$text

=== Screenshot ===
[[Image($filename, width=100%)]]

=== Technische Daten ===

 Adresse wo Benutzer herkam::
    $_SERVER[HTTP_REFERER]
 Browser des Benutzers (User-Agent)::
    $_SERVER[HTTP_USER_AGENT]
 IP-Adresse des Besuchers::
    $_SERVER[REMOTE_ADDR]
 Datum::
    $date

@type: Designen
@component: ILIAS
@keywords: po3, feedback
@sensitive: 1

BODY;

$mail->Body = $body;

if(!$mail->send()) {
	http_response_code(420); // Enhance your calm -> daran identifiziert feedback.js, dass senden fehlschlug
	echo "Mail konnte nicht gesendet werden. Fehler: " . $mail->ErrorInfo;
} else {
	echo "Feedback per Mail übermittelt!";
}
