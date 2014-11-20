<?php
/*
  This is a proxy script for ILIAS/POTT to overcome HTTPS/HTTP inclusion
  problems as well as JSONP. It overcomes actually everything.

  This script transparently loads the PWP feed. Nothing more, nothing less.

  For documentation, see POTT #982.

  - Sven, 30. oct 2014
*/

chdir("/home/elearning-www/public_html/podcast-wiki/feed/");
require "index.php";

