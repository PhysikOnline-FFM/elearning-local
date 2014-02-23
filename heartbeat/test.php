<pre><?php

// ist netcat installiert?

function a($b, &$c, &$d) {
	$c = $b."hi";
	$d = $b."ho";
}

$a = "hallO";
a($a, $b, $c);

print "$a\n$b\n$c";
