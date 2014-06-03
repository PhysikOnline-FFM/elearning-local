<?php
/**
 * PHP-Library for easy generation of URLs for PDF preview service.
 * 
 * Sven Koeppel 2014 Public Domain.
 **/

$url_path_to_pdf2jpg = '/pdf-preview/';

function get_pdf_list($array, $width=200) {
	$s = '<div class="pdf-list">';
	foreach($array as $a) {
		$s .= get_pdf_link($a[0], $a[1], "${width}x", "width: ${width}px");
	}
	$s .= '</div>';
	return $s;
}

function get_pdf_link($uri, $desc="", $size=Null, $extrastyles=Null) {
	return "<a class='pdf' href='$uri' style='$extrastyles'><img src='".get_pdf_preview($uri,$size).
	"' title='PDF-Vorschau: $desc' class='preview'>
	<span class='desc'>$desc</span></a>";
}

function get_pdf_preview($uri, $size=Null, $page=Null) {
	global $url_path_to_pdf2jpg;
	$data = array();

	if(file_exists($uri)) {
		$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$url .= $_SERVER['SERVER_NAME'];
		$url .= (substr($_SERVER['REQUEST_URI'], -1) == '/') ?
			$_SERVER["REQUEST_URI"] :
			(dirname($_SERVER["REQUEST_URI"]).'/');
		$url .= $uri;
		$data['url'] = $url;
#		print $url; exit;
	} else
		$data['url'] = $uri;
	if($size) $data['size'] = $size;
	if($page != Null) $data['page'] = $page;
	$str= $url_path_to_pdf2jpg.'?'.http_build_query($data);
	return $str;
}

