<html>
<h1>PDF 2 JPG tests</h1>
<?php
include "lib.php";

// Beispiel: POTT-PDFs. Achtung, passwortgeschuetzte Anhaenge koennen nicht geladen werden!
$url_a = 'https://elearning.physik.uni-frankfurt.de/projekt/raw-attachment/ticket/866/POKAL-Protokoll%2002.%20Juni%202014%20handschriftlich.pdf';
$url_b = 'https://elearning.physik.uni-frankfurt.de/projekt/raw-attachment/ticket/555/SELF-Antrag_PhysikOnline_POKAL%2C%20finanzbereinigt.pdf';
$p = array();
$p[] = get_pdf_preview($url_a, '130x');
$p[] = get_pdf_preview($url_a, '300x');
$p[] = get_pdf_preview($url_a, '600x');
$p[] = get_pdf_preview($url_a, '600x');
$p[] = get_pdf_preview($url_b);
// get specific pages:
$p[] = get_pdf_preview($url_b, '600x', 3);
$p[] = get_pdf_preview($url_b, '600x', 4);

foreach($p as $i => $a) {
?>
<p>Doc <?=$i; ?>: <a href="<?=$a; ?>"><?=$a; ?></a>
<br><img src="<?=$a; ?>">
<?php }


