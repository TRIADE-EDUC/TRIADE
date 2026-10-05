<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thumbnail.php,v 1.3.2.1.2.1 2025/03/07 08:36:42 jparis Exp $

use Pmb\Common\Helper\HelperEntities;
use Pmb\Common\Library\Image\CacheImage;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $class_path, $base_path, $base_auth, $base_title, $base_noheader, $base_nocheck, $base_nobody;
global $type, $id;
global $img_cache_type;

$base_path      = ".";
$base_auth      = ""; //"CIRCULATION_AUTH";
$base_title     = "";
$base_noheader  = 1;
//$base_nocheck  = 1;
$base_nobody    = 1;
$base_nosession = 1;

require_once ($base_path."/includes/init.inc.php");
require_once($class_path."/curl.class.php");
require_once("$base_path/includes/isbn.inc.php");
require_once($base_path."/admin/connecteurs/in/amazon/amazon.class.php");

session_write_close();

$id = intval($id);
$type = trim($type);

if(!empty($opac_img_cache_type) && in_array($opac_img_cache_type, ['png', 'webp'])) {
    global $pmb_img_cache_type;
    $pmb_img_cache_type = $opac_img_cache_type;
} else {
    // Le parametre pmb_img_cache_type n'existe pas il faut le definir
    $pmb_img_cache_type = "png";
}

$entitiesNamespaces = HelperEntities::get_entities_namespace();
$filenameCache = CacheImage::generateFilename($entitiesNamespaces[$type], $id);
$cacheFilemtime = CacheImage::filemtime($filenameCache);

if ($cacheFilemtime) {
    $headers = getallheaders();
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) >= $cacheFilemtime)) {
		header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
		exit();
	}
}

if (!empty($type) && ThumbnailSourcesHandler::checkType($type)) {

    // Duree du cache (une journe)
    $duration = 60 * 60 * 24;
    header('Expired: '.gmdate("D, d M Y H:i:s", time() + $duration).' GMT', true);
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);

    $handler = new ThumbnailSourcesHandler();
    $handler->printImage($type, $id);
} else {
    http_response_code(404);
}