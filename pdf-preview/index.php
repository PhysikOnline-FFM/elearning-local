<?php
/**
 * PDF to JPG service:
 * Rendern von PDF-Seiten zu Bildern zur Einbindung in Webseiten
 * Siehe POTT #867
 *
 * Public Domain, Sven Koeppel 2014
 **/

function req_arg($name, $errmsg=Null) {
	if(!isset($_GET[$name])) die($errmsg ? $errmsg : "Required get arg: ?$name=...");
	else return $_GET[$name];
}

function def_arg($name, $default=Null) { # default arg
	return isset($_GET[$name]) ? $_GET[$name] : $default;
}

$cache_dir = 'cache';
$web_cache_path = function($jpgfile){ return "$_SERVER[SCRIPT_URI]$jpgfile"; };

$url = req_arg('url');
if(preg_match('/[\'"]/', $url))
	die("Bad url: $url, bad chars included");
# make sure this doesnt crawl the web.
if(strpos($url, 'uni-frankfurt.de') === false) {
	die("Bad url: Only uni-frankfurt.de domains allowed");
}
$eurl = escapeshellarg($url);
$size = def_arg('size');
if($size && !preg_match('/\d{0,3}x\d{0,3}/', $size))
	die("Bad size: $size, must be [INT]x[INT]");
$page = def_arg('page', 0);
if($page && !preg_match('/^\d{0,2}$/', $page))
	die("Bad page: $page, must be decimal [INT] between 0 and 99.");

$tmp = $cache_dir.'/'.md5($url);
$tmpfile = $tmp.'.pdf';
$jpgfile = $tmp.($size?"-$size":'').($page!=0?"-p$page":'').'.jpg';
$jpgurl = $web_cache_path($jpgfile);

# make use of cache
if(file_exists($jpgfile)) {
	header("Location: $jpgurl");
	exit;
}

if(!file_exists($tmpfile)) {
	$wget = "wget -q -O $tmpfile $eurl";
	system($wget, $retval);
	if($retval) die("Error downloading $url\n<br>Used: $wget");

	if(!filesize($tmpfile)) die("Error downloading $url, got empty file!\n<br>Used: $wget");

	$ispdf = `file $tmpfile | grep PDF | wc -l`;
	if($ispdf != 1) {
		unlink($tmpfile);
		// TODO: Dafuer sorgen, dass Client nicht ohne Ende Files saugen kann
		die("File from $url is not a PDF");
	}
}

// Work around black PDF pages Bug. See
// http://stackoverflow.com/questions/10934456/imagemagick-pdf-to-jpgs-sometimes-results-in-black-background
// Since at ITP they use a pretty old ImageMagick version, "-alpha remove" does not work:
// $bugwork = "-alpha remove";
$bugwork = "-flatten";

$resize = ($size ? "-resize $size" : '');
$conv_cmd = "convert $resize $bugwork ${tmpfile}[${page}] $jpgfile 2>&1";
$conv = `$conv_cmd`;

$lastline = system($conv, $retval);
if($retval) die("Converting PDF to JPG failed ($retval). \nOutput of $conv_cmd\n $lastline.");

header("Location: $jpgurl");

