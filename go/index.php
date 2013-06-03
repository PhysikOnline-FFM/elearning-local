<?php
# kleiner URL-Shortener, genommen aus BioKemika
# 28.03.2010, Sven Koeppel

$empty_target = "https://elearning.physik.uni-frankfurt.de/projekt/wiki/Kurzlinks";
$input_list = "https://elearning.physik.uni-frankfurt.de/projekt/wiki/Kurzlinks?format=txt";

# get out target name. Simple variant (using mod_rewrite):
$target = $_SERVER['QUERY_STRING'];
if(empty($target)) go_to($empty_target);

# make some appending voodoo ($target="foo/bar" => $target=foo, $apx="/bar")
$apx = strstr($target, "/");
if($apx) $target = strstr($target, "/", true);
else     $apx = "";

# more complex variant (using ErrorDocument):
#$target = substr($_SERVER['REQUEST_URI'], 1);

$lines = file($input_list);

foreach($lines as $x) {
        if($x{0} != ' ') continue; # strip comments
        $data = preg_split('/\s+/', $x, 2, PREG_SPLIT_NO_EMPTY);
        if($data[0] == $target) go_to($data[1]);
}

# nothing found
header("HTTP/1.0 404 Not Found");
readfile('../error/404.htm');
print "<center>(powered by physikonline url-shortener, using <i>$target</i>";
print $apx ? ", Appendix <i>$apx</i>" : "";
print ")</center>";
exit;

function go_to($url) {
	global $apx;
	$url = trim($url);
        header("Location: $url$apx");
        print "<html>See <a href='$url$apx'>$url$apx</a>";
        exit;
}
