<?php
        $dir_blahtex_cache = "/var/www/elearning/local/latex/blahtex/cache";
 
        function processBlahTex($tex, $tempdir)
        {
                $cmd_dvipng = "/usr/bin/dvipng";
                $cmd_blahtex = "/usr/bin/blahtexml";
                
                $descriptorspec = 
                        array(
                                0 => array( "pipe", "r" ),
                                1 => array( "pipe", "w" )
                        );
				if(!isset($options)) $options = ""; # fix
                $options .= " --png --texvc-compatible-commands --use-ucs-package --temp-directory $tempdir --png-directory $tempdir --shell-dvipng " . $cmd_dvipng;
                $process = proc_open($cmd_blahtex . " " . $options, $descriptorspec, $pipes);
                if ( !$process ) 
                {
                        return array(
                                false, 
                                "math_unknown_error #1"
                        );
                }
                #fwrite( $pipes[0], "displaystyle" ); # wtf?
                fwrite( $pipes[0], $tex );
                fclose( $pipes[0] );
                
                $contents = '';
                while ( !feof($pipes[1] ) ) {
                        $contents .= fgets( $pipes[1], 4096 );
                }
                fclose( $pipes[1] );
                if ( proc_close( $process ) != 0 ) {
                        // exit code of blahtex is not zero; this shouldn't happen
                        return array( 
                                false, 
                                "math_unknown_error #2"
                        );
                }
                return array( true, $contents );
        }
 
        // SCRIPT STARTS HERE
        $filename = $dir_blahtex_cache . "/" . md5($_SERVER["QUERY_STRING"]) . ".png";
        if (file_exists($filename))
        {
                header("Content-Type: image/png");
                header('Content-Length: '.filesize($filename));
                readfile($filename);
        }
        else
        {
                $tempdir = dirname(tempnam("",""));
                $result = processBlahTex(" " . urldecode($_SERVER["QUERY_STRING"]), $tempdir);
                if ($result[0] == TRUE)
                { 
                        if (preg_match("/<md5>(.*?)<\/md5>/is", $result[1], $matches))
                        {
                                $result = @rename("$tempdir/" . $matches[1] . ".png", $filename);
                                if (!$result)
                                {
                                        $filename = "$tempdir/" . $matches[1] . ".png";
                                }
                                header("Content-Type: image/png");
                                header('Content-Length: '.filesize($filename));
                                readfile($filename);
                        }
                        else
                        {
                                // error, no png file found
                        }
                }
                else
                {
                        // error, blahtex call failed
                }
        }
?>