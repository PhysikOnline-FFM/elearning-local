<?php
/**
 * PHYSIK ONLINE 3.0 skin - Server Side LESS compiler, gemaess
 * POTT Ticket #641.
 * Diese PHP-File gibt eine CSS-Datei aus.
 **/
 header('Content-type: text/css');
 
 // Parameter:

 // Temporaere nur interne Cache-Datei auf dem Server
 $cssfile = "/tmp/physikonline-ilias-generated.css";
 // Einstiegs-LESS-File, die kompiliert wird. Sie kann per @include
 // andere CSS/LESS-Files einbinden, diese Einbindungen werden aufgeloest.
 $lessfile = "less.d/po-v3.0.less";
 // Alle Dateien, die auf diese Globbing-Pattern passen, werden bei jedem
 // PHP-Aufruf auf Aenderungen ueberprueft.
 $watch_files = array_merge( glob("css.d/*"), glob("less.d/*") );
 // Damit @imports aufgeloest werden koennen, muessen die lokalen Dateisystem-
 // Pfade klar sein. Da Philip /Customizing/... verwendet, der volle Pfad.
 $less_import_dir = array(
     // Customizing ist relativ dazu 
    "/home/elearning-www/public_html/elearning/local/",
    "css.d/",
    "less.d/",
     );

 // Soll Browser-Cache-Information verwendet werden (spart Bandbreite) oder
 // nicht? Default: false.
 $ignore_http_caching = false;

 // Ab hier das Programm
 $mtime_cache_file = @filemtime($cssfile);
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
    // LESS-Files erzeugen
    require "lib/lessc.inc.php";
    $less = new lessc;

    $less->setImportDir($less_import_dir);
	if (!$debug) {
		$less->setFormatter("compressed");
	}
    $out = $less->compileFile($lessfile);
    file_put_contents($cssfile, $out);
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
 * PhysikOnline (PO3) CSS Code - https://elearning.physik.uni-frankfurt.de/
 * Generated from LESS files
 * --- DIESE DATEI NICHT VON HAND BEARBEITEN ---
 * Design by Intsar Bangwi, Philip Arnold, Sven Koeppel
 *
 * Packed: <?php echo implode(' ', $watch_files); ?> 
 * Arguments:  ?debug=true  - regenerate cache file
 * Generation Date: <?php print date('r', $mtime_cache_file); ?><?php #print $regenerate ? "Just regenerated" : date('r', $mtime_cache_file); ?> 
 **/
<?php if($regenerate) print $out; else readfile($cssfile); ?>
