<?php
/**
 * PHYSIK ONLINE 5.1 JavaScripts - Server side compiling und packing
 * Vergleichbar mit physik.css.php
 * Diese PHP-File gibt eine Javascript-Datei aus.
 **/
 header('Content-type: text/javascript; charset=utf-8');
 
 // Parameter:
 // Temporaere nur interne Cache-Datei auf dem Server
 $outfile = "/tmp/po51-ilias-generated.js";
 // Alle Dateien, die auf diese Globbing-Pattern passen, werden bei jedem
 // PHP-Aufruf auf Aenderungen ueberprueft.
 $watch_files = glob("js/*.js");

 // Soll Browser-Cache-Information verwendet werden (spart Bandbreite) oder
 // nicht? Default: false.
 $ignore_http_caching = false;

 // Ab hier das Programm
 $mtime_cache_file = @filemtime($outfile);
 $mtime_test_files = array_map(
	function($x){return @filemtime($x);},
	$watch_files);
 $mtime_test_max = array_reduce($mtime_test_files, 'max');
 $cache_is_valid = $mtime_cache_file
			&& $mtime_test_max < $mtime_cache_file;
 $debug = isset($_GET["debug"]);
 $regenerate = !$cache_is_valid || $debug;

 header("Last-Modified: ".gmdate("D, d M Y H:i:s", $mtime_cache_file)." GMT");

 if($regenerate) {
    // gepackte Javascript-File erzeugen
    $javascript = "";
    foreach($watch_files as $file) {
       $javascript .= "\n\n/* -------------- $file ------------------- */\n\n";
       $javascript .= file_get_contents($file);
    }

    // load minifier
    include "lib/JavaScriptMinifier.php";
    $javascript = JavaScriptMinifier::minify($javascript);
    file_put_contents($outfile, $javascript);
 } else {
    // Cache is valid!
    // HTTP Caching verwenden, wenn Client bereits neuste Version hat, nicht nochmal
    // uebertragen

    if(!$ignore_http_caching && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $mtime_cache_file) {
        header("HTTP/1.1 304 Not Modified");
        // important - no more output!
        exit;
    } // else: Ausgaben machen, siehe unten:
 }

?>
/*!
 * PhysikOnline (PO5) JavaScript Code - https://elearning.physik.uni-frankfurt.de/
 * Generated from a bunch of javascript files
 * --- DIESE DATEI NICHT VON HAND BEARBEITEN ---
 * Coded by Philip Arnold and Sven Koeppel
 *
 * Packed: <?php echo implode(' ', $watch_files); ?> 
 * Arguments:  ?debug=true  - regenerate cache file
 * Generation Date: <?php print date('r', $mtime_cache_file); ?><?php #print $regenerate ? "Just regenerated" : date('r', $mtime_cache_file); ?> 
 **/
<?php if($regenerate) print $javascript; else readfile($outfile); ?>
