<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fb.php,v 1.18 2023/08/17 09:47:54 dbellamy Exp $

$base_path = ".";

require_once "{$base_path}/includes/init.inc.php";
require_once "{$base_path}/includes/common_includes.inc.php";

global $url_location, $charset, $url, $opac_url_base;

$args = explode("&", $url);
$url_location = $args[0];

if (0 !== strpos($url_location, $opac_url_base) && 0 !== strpos($url_location, "./")) {
    print "<script>document.location='{$opac_url_base}';</script>";
    exit;
}

$allow_parameters = [
    "title",
    "desc",
    "id",
    "opac_view",
    "type",
];

for($i=1; $i<count($args);$i++) {
	$key_value = explode("=",$args[$i]);
	if (in_array($key_value[0], $allow_parameters)) {
    	${$key_value[0]} = $key_value[1];
	}
}

if (!isset($title)) {
    $title = "";
}
if (!isset($desc)) {
    $desc = "";
}
if (!isset($id)) {
    $id = 0;
}
if (!isset($opac_view)) {
    $opac_view = 0;
}
if (!isset($type)) {
    $type = 0;
}

$url = htmlentities($url_location,ENT_QUOTES,$charset);
if (!empty($id)) {
    $url .= "&id=" . htmlentities($id,ENT_QUOTES,$charset);    
}
if (!empty($opac_view)) {
    $url .= "&opac_view=" . htmlentities($opac_view,ENT_QUOTES,$charset);    
}

if (TYPE_NOTICE == $type) {
    $record_datas = new record_datas($id);
    $title = $record_datas->get_tit1();
    $url = htmlentities($record_datas->get_permalink());
    $desc = $record_datas->get_resume();
}

print "
<html xmlns='http://www.w3.org/1999/xhtm'
      xmlns:og='http://ogp.me/ns#'
      xmlns:fb='http://www.facebook.com/2008/fbml' charset='" . htmlentities(stripslashes($charset),ENT_QUOTES,$charset) . "'>
	<head>
		<meta name='title' content='" . htmlentities(stripslashes($title),ENT_QUOTES,$charset) . "' />
		<meta name='description' content='" . htmlentities(stripslashes($desc),ENT_QUOTES,$charset) . "' />
		<title>" . htmlentities(stripslashes($title),ENT_QUOTES,$charset) . "</title>
		
		<script>
			document.location='" . $url . "';
		</script>
	</head>
</html>";