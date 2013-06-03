<?php
/**
 * The technikum29.de refer system. adopted for physikonline
 *
 * This simple program is based on the original sveniwebserver refer
 * system. You get an usage help message by calling this document
 * without any parameters.
 *
 *
 * Written on 09.07.2008. 16:00
 **/

 $address = false;                                    # hat als False lediglich den Sinn, dass es nachher mit
 $time    = 3;                                        # Querystring gefüllt wird, falls kein GET-String dabei war.
 $help    = false;                                    # display help message?


 if(!$_SERVER["QUERY_STRING"])                        # ohne Seitenangabe bringt Seite nichts
   $help = true;
 else
 {
 if($_POST) // ursprünglich if($_POST && !$_GET)      # kann auch per POST aufgerufen werden
   $_GET = $_POST;                                    # (was bringts ...)

   if(isset($_GET["to"]))                             # Einbeziehung von get[to] und get[address] in
     $address = $_GET["to"];                          # einzelne Variable
   else if(isset($_GET["address"]))                   #  (Address ist dann die Zielweiterleitungsadresse)
     $address = $_GET["address"];

   if(isset($_GET["time"]))                           # Einbeziehung von get[time] und get[wait] in
     $time = $_GET["time"];                           # einzelne Variable
   else if(isset($_GET["wait"]))                      #  (Time ist dann Wartezeit in sec auf Weiterleitungsseite)
     $time = $_GET["wait"];

   if(isset($_GET['pre']))
     $_GET['prefix'] = $_GET['pre'];
   if(isset($_GET["prefix"]))                         # Für Massenformularaufruf:
     $address = $_GET["prefix"].$address;             # get[prefix] wird einfach vor die Adresse gestellt

   if(isset($_GET['post']))
     $_GET['postfix'] = $_GET['post'];
   if(isset($_GET['postfix']))
     $address .= $_GET['postfix'];

   if(! $address)                                     # Ach ja, und falls keine Zeit angegeben ist, kann Refer-ziel
     $address = $_SERVER["QUERY_STRING"];             # gleich in QUERYSTRING geladen werden, das geht hier
 }

 if(! preg_match("/http:/", $address))                # macht aus dem evv. relativen
 {                                                    # String einen absoluten (chk ob absolut/relativ)
  $before = "http://".$_SERVER["HTTP_HOST"];          # danach noch chk ob er evv. so "/ordner/datei.bla" oder
                                                      # so "\ordner\datei.bla" aussieht, dann wird das erste Zeichen
  if($address{0} == "\\" || $address{0} == "/")       # weggeschnitten und dann alles schön neu zusammengesetzt.
    $address = substr($address, 1);
  $address = $before . "/" . $address;
 }

 $time_js = $time / 1000 - 120;                       # Zeit für den Javascript-Seitenreplacer
 $address_print = strlen($address) > 70 ?
    substr($address, 0, 70).'...' : $address;         # URL zum Anzeigen (max. 70 Zeichen + "..." lang)

 // begin of output

?>
<html>
<? print $help ? '<pre>' : '<!--'; ?>
####################################################################
##  PHYSIK ONLINE REFERRER PROGRAM
##
##  USAGE:
##
##  - QUERY STRING:
##       -> simply the target address; the program will show
##          a short redirection page. Intended usage: To hide
##          the site you came from in the HTTP "Referer" header.
##          See also: http://anonym.to
##       => Example: [script-name]?http://www.google.de/
##
##  - GET METHOD: (all parameters can be used in combination)
##       ?to ?address
##       -> the target URL
##       => Ex: ?to=http://www.google.de/
##
##       ?prefix ?pre
##       -> for mass callings like in formulas: The prefix value
##          will prefix the adress value (as given in "to" or
##          "address".
##       => Ex: ?to=computer&pre=http://technikum29.de/en/
##       => another example: A HTML formula: <form method="get"><input type="hidden" name="pre" value="http://technikum29.de/">
##          Go to <select name="to"><option>de</option><option>en</option></select> homepage. <input type="submit"></form>
##          (see sourcecode for the simple usage)
##
##      ?postfix ?post
##      -> the opposite of prefix, with the corresponding meaning.
##      => Ex: ?to=dev&pre=http://&post=.technikum29.de/
##      => Ex: A HTML formula: <form method="get"><input type="hidden" name="pre" value="http://"><input type="hidden" name="post" value=".technikum29.de/">
##         Go to http://<select name="to"><option>dev<option>privat<option>www<option>ftp</select>.technikum29.de/ <input type="submit"></form>
##
##      ?time ?wait
##      -> how long to wait until get redirected.(in seconds)
##      -> special value: "0" or some NaN value like "no". This
##         will make a HTTP Redirect instead of displaying this
##         nice HTML redirection page.
##      => Ex: Wait one minute: ?to=/some/where&wait=60
##
##  - POST METHOD:
##      -> can be used exactly like the GET METHOD.
##      -> Advantage over GET: The visitor won't see where he will
##         be redirected.
####################################################################
<? if($help) exit; else print '-->'; ?>
<head>
  <title>PhysikOnline Referrer</title>
  <script language="JavaScript">
  <!--
      setTimeout("location.replace('<?=$address; ?>')", <?=$time_js; ?>);
  -->
  </script>
  <? printf("<meta http-equiv=\"refresh\" content=\"%d; URL=%s\">", $time, $address); ?>
  <style type="text/css">
  <!--
  body       { margin: 0px; background-color: #FAFAFA; }
  #site      { width: 50%; padding: 2px; border: 1px dashed #AAA; background-color: #FFF; }
  #h2        { font: 20px Verdana; font-weight: bold; color: #CCC; }
  #h1        { font: 56px Arial,Verdana,sans serif; font-weight: bold; color: #ACACAC; line-height: 130%; }
  #p         { font: 13px Verdana; color: #AAA; }
  a          { font: 13px Verdana; color: #AAA; text-decoration: none; }
  a:link, a:visited
             { color: #CCC; text-decoration: none; }
  a:focus, a:hover, a:active
             { color: #000; text-decoration: underline; }
  #error     { font: bold 16pt sans-serif; text-align: center; }
  #error h1  { font: 23pt sans-serif small-caps; }
  -->
  </style>
  <meta charset="utf-8">
</head>
<body scroll="no">
<?
 if(isset($error)) echo '<div id="error"><h1>Fehler: Seite wurde mit falschen Parametern aufgerufen.</h1>(Quelltext für Parameterhilfen lesen)</div>';
?>
<table width="100%" height="100%" cellspacing="0" cellpadding="0">
<tr>
 <td valign="middle" align="center">
  <div id="site">
     <div id="h2">PhysikOnline</div>
     <div id="h1">Referrer</div>
     <div id="p">You are referred to the following address: <a href="<?=$address; ?>"><?=$address_print; ?></a></div>
  </div>
 </td>
</tr>
</body>
</html>

