<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations_vign.php,v 1.2.2.1.2.1 2025/02/12 12:34:06 dbellamy Exp $

use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Orm\AnimationOrm;

global $opac_opac_view_activate, $opac_view, $pmb_opac_view_class, $opac_view_filter_class, $opac_default_style;
global $css, $class_path, $notice_id, $etagere_id, $authority_id, $entity_id, $opac_curl_available, $pmb_notice_img_pics_max_size;
global $no_caching;

require_once("./includes/apache_functions.inc.php");

//on ajoute des entêtes qui autorisent le navigateur à faire du cache...
$headers = getallheaders();
//une journée
$offset = 60 * 60 * 24 ;
if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) <= time())) {
    header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
    return;
}else{
    header('Expired: '.gmdate("D, d M Y H:i:s", time() + $offset).' GMT', true);
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
}

$base_path=".";
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');

// récupération paramètres MySQL et connection à la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/misc.inc.php");

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");
require_once($base_path.'/includes/start.inc.php');

//si les vues sont activées (à laisser après le calcul des mots vides)
// Il n'est pas possible de chagner de vue à ce niveau
if($opac_opac_view_activate){
    $current_opac_view=(isset($_SESSION["opac_view"]) ? $_SESSION["opac_view"] : '');
    if($opac_view==-1){
        $_SESSION["opac_view"]="default_opac";
    }else if($opac_view)	{
        $_SESSION["opac_view"] = intval($opac_view);
    }
    $_SESSION['opac_view_query']=0;
    if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
    require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");

    $opac_view_class= new $pmb_opac_view_class((isset($_SESSION["opac_view"]) ? $_SESSION["opac_view"] : ''),$_SESSION["id_empr_session"]);
    if($opac_view_class->id){
        $opac_view_class->set_parameters();
        $opac_view_filter_class=$opac_view_class->opac_filters;
        $_SESSION["opac_view"]=$opac_view_class->id;
        if(!$opac_view_class->opac_view_wo_query) {
            $_SESSION['opac_view_query']=1;
        }
    } else {
        $_SESSION["opac_view"]=0;
    }
    $css=$_SESSION["css"]=$opac_default_style;
}

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
