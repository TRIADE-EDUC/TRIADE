<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations_vign.php,v 1.2.2.1.2.1 2025/03/07 08:36:42 jparis Exp $

use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Orm\AnimationOrm;

global $class_path, $base_path, $base_auth, $base_title, $base_noheader, $base_nocheck, $base_nobody;
global $no_caching, $type, $id, $msg;

$base_path      = ".";
$base_auth      = ""; //"CIRCULATION_AUTH";
$base_title     = "";
$base_noheader  = 1;
//$base_nocheck  = 1;
$base_nobody    = 1;
$base_nosession = 1;

require_once($base_path."/includes/init.inc.php");
require_once($class_path."/curl.class.php");
require_once("$base_path/includes/isbn.inc.php");
require_once($base_path."/admin/connecteurs/in/amazon/amazon.class.php");

session_write_close();

global $animationId, $size;
$animationId = intval($animationId);
$size = intval($size);

// Path du fichier de cache
$cache_file_prefix = AnimationModel::getImgCachePathPrefix($animationId, $size);

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
header('Expired: ' . gmdate("D, d M Y H:i:s", time() + $duration) . ' GMT', true);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT', true, 200);

if (AnimationOrm::exist($animationId)) {
    AnimationModel::printLogo($animationId, $size);
} else {
    http_response_code(404);
    $img = imagecreatetruecolor(1,1);
    header('Content-Type: image/png');
    imagepng($img);
}
