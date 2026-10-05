<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+

function forbidden($image_path='../images') {
  global $charset;

print "<!DOCTYPE html>
<html>
<head>
	<meta charset=\"".$charset."\" />
  	<META HTTP-EQUIV=\"pragma\" CONTENT=\"no-cache\">
	<META HTTP-EQUIV=\"expires\" CONTENT=\"Wed, 30 Sept 2001 12:00:00 GMT\">
    <title>
      PMB. Forbidden Zone
    </title>
  </head>
  <body bgcolor=\"#ffffff\">

    <br />

    <br />

    <br />

    <div class='center'>
    	<strong>PMB. Forbidden Zone</strong><br />
      <img src=\"$image_path/forbidden.jpg\" title=\"forbidden\" alt=\"forbidden\">
    </div>
  </body>
</html>
";

die;
}

