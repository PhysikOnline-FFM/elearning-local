<?php
/**
 * PDF to JPG service:
 * Rendern von PDF-Seiten zu Bildern zur Einbindung in Webseiten
 * Siehe POTT #867
 *
 * Public Domain, Sven Koeppel 2014
 **/


function req_arg($name, $errmsg=Null) {
	if(!isset($_GET[$name])) die($errmsg ? $errmsg : "<html><b>Required GET arg</b>: <tt>?$name=...</tt> or <a href='constructor.php'>GUI PDF preview constructor</a>");
	else return $_GET[$name];
}

function def_arg($name, $default=Null) { # default arg
	return isset($_GET[$name]) ? $_GET[$name] : $default;
}

function exit_message($msg) {
	// prints out json(p) error message. $msg may be a string
	// or an array to jsonify. Afterwards exit.
	header("Content-Type: application/json; charset=utf-8");

	if(! is_array($msg))
		$msg = array('msg' => $msg);

	if(isset($_GET['jsonp'])) {
		$padding = $_GET['jsonp'];
		print $padding.'(';
		print json_encode($msg);
		print ');';
	} else
		print json_encode($msg);
	exit;
}

$cache_dir = 'cache';
$web_cache_path = function($jpgfile){ return "$_SERVER[SCRIPT_URI]$jpgfile"; };

$url = req_arg('url');
if(preg_match('/[\'"]/', $url)) {
	header("HTTP/1.0 400 Bad Request");
	exit_message("Bad url: $url, bad chars included");
}

# make sure this doesnt crawl the web.
$url_allowed_regexp = '/uni-frankfurt.de|physikelearning.de/';
if(!preg_match($url_allowed_regexp, $url)) {
	header("HTTP/1.0 403 Forbidden");
	exit_message("Bad url: Only uni-frankfurt.de domains allowed");
}

putenv('LANG=en_US.UTF-8'); // workaround for system, ``, ...
setlocale(LC_CTYPE, "en_US.UTF-8"); // workaround for UTF-8 in escapeshellarg
$eurl = escapeshellarg($url);

$size = def_arg('size');
if($size && !preg_match('/\d{0,3}x\d{0,3}/', $size)) {
	header("HTTP/1.0 400 Bad Request");
	exit_message("Bad size: $size, must be [INT]x[INT]");
}

$page = def_arg('page', 0);
if($page && !preg_match('/^\d{0,2}$/', $page)) {
	header("HTTP/1.0 400 Bad Request");
	exit_message("Bad page: $page, must be decimal [INT] between 0 and 99.");
}

$page_from = def_arg('page_from', 0);
if(!is_numeric($page_from)) {
	header("HTTP/1.0 400 Bad Request");
	exit_message("Bad page_from: $page_from must be decimal");
}
$page = $page - $page_from;

$tmp = $cache_dir.'/'.md5($url);
$tmpfile = $tmp.'.pdf';
$jpgfile = $tmp.($size?"-$size":'').($page!=0?"-p$page":'').'.jpg';
$jpgurl = $web_cache_path($jpgfile);

// utf8 check
//header('Content-Type: text/plain; charset=utf-8');
//var_dump($GLOBALS);
//exit;

# make use of cache
if(file_exists($jpgfile) && !isset($_GET['metadata'])) {
	header("Location: $jpgurl");
	exit;
}

if(!file_exists($tmpfile)) {
	$wget = "wget -nv -O $tmpfile $eurl 2>&1";
	ob_start();
	system($wget, $retval);
	$wget_out = ob_get_clean();
	ob_end_clean();
	if($retval) {
		// avoid collecting empty temp files
		if(is_file($tmpfile)) unlink($tmpfile);

		header("HTTP/1.0 502 Bad Gateway");
		exit_message(array(
			'msg' => "Error downloading $url",
			'retval' => $retval,
			'cmd_used' => $wget,
			'output' => $wget_out
		));
	}

	if(!filesize($tmpfile)) {
		header("HTTP/1.0 502 Bad Gateway");
		exit_message(array(
			'msg' => "Error downloading $url, got empty file!",
			'retval' => $retval,
			'cmd_used' => $wget
		));
	}

	$ispdf = `file $tmpfile | grep PDF | wc -l`;
	$type = `file $tmpfile`;
	if($ispdf != 1) {
		unlink($tmpfile);
		// TODO: Dafuer sorgen, dass Client nicht ohne Ende Files saugen kann
		header("HTTP/1.0 403 Forbidden");
		exit_message(array(
			'msg' => "File from $url is not a PDF",
			'output' => $type
		));
	}
}

if(isset($_GET['metadata'])) {
	$dump_cmd = "pdfinfo $tmpfile 2>&1";
	$dump = `$dump_cmd`;

	exit_message(array(
		'msg' => 'Here is your PDF metadata output',
		'output' => $dump
	));
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
if($retval) {
	header("HTTP/1.0 501 Not Implemented");
	exit_message(array(
		'msg' => 'Converting PDF to JPG failed',
		'retval' => $retval,
		'cmd_used' => $conv_cmd,
		'output' => $lastine
	));
}

header("Location: $jpgurl");

