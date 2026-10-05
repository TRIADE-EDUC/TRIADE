<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_vign.php,v 1.6.6.1.2.1 2025/03/07 08:36:42 jparis Exp $

// définition du minimum nécéssaire
$base_path      = ".";
$base_auth      = ""; //"CIRCULATION_AUTH";
$base_title     = "";
$base_noheader  = 1;
$base_nocheck   = 1;
$base_nobody    = 1;
$base_nosession = 1;

require_once $base_path . '/includes/init.inc.php';
session_write_close();

$mode = strval($_GET['mode']);
$type = strval($_GET['type']);
$id = intval($_GET['id']);

// Path du fichier de cache
global $database;
$cache_path = [$base_path, "temp", "cms_vign", $database];
if (!empty($mode)) {
    $cache_path[] = $mode;
}

$cache_path[] = $type.$id;
$cache_file_prefix = join(DIRECTORY_SEPARATOR, $cache_path);

// Si le fichier de cache existe, on considère qu'il est activé...
$cache_file = null;
if(file_exists($cache_file_prefix.'.png')){
	$cache_file = $cache_file_prefix.'.png';
} elseif(file_exists($cache_file_prefix.'.jpeg')){
	$cache_file = $cache_file_prefix.'.jpeg';
} elseif(file_exists($cache_file_prefix.'.gif')){
	$cache_file = $cache_file_prefix.'.gif';
}

if (isset($cache_file) && is_file($cache_file)) {
    $headers = getallheaders();
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) >= filemtime($cache_file))) {
		header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
		exit();
	}
}

// Duree du cache (une journe)
$duration = 60 * 60 * 24;
header('Expired: '.gmdate("D, d M Y H:i:s", time() + $duration).' GMT', true);
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);

require_once $class_path . '/cms/cms_logo.class.php';

try {
    $logo = new cms_logo($id,$type);
    $logo->show_picture($mode);
} catch (Exception $e) {
    http_response_code(404);
}