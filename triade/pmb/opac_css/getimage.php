<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: getimage.php,v 1.49.6.1 2025/02/12 12:34:06 dbellamy Exp $

global $opac_opac_view_activate, $current_opac_view, $opac_view, $pmb_opac_view_class, $opac_view_filter_class, $opac_default_style;
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

if(isset($_GET['noticecode'])){
	$noticecode=$_GET['noticecode'];
}else{
	$noticecode="";
}
if(isset($_GET['vigurl'])){
	$vigurl=$_GET['vigurl'];
}else{
	$vigurl="";
}
if(isset($_GET['url_image'])){
	$url_image=$_GET['url_image'];
}else{
	$url_image="";
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

require_once("$class_path/curl.class.php");
require_once($base_path."/includes/isbn.inc.php");
require_once($base_path."/admin/connecteurs/in/amazon/amazon.class.php");

session_write_close();

$poids_fichier_max=1024*1024;//Limite la taille de l'image à 1 Mo

$notice_id = intval($notice_id);
$etagere_id = intval($etagere_id);
$authority_id = intval($authority_id);
$entity_id = intval($entity_id);

$img_disk="";

if(empty($no_caching)) {
	$manag_cache=getimage_cache($notice_id, $etagere_id, $authority_id, $vigurl, $noticecode, $url_image);
	if (!empty($manag_cache['location'])) {
		$img_disk=$manag_cache["location"];
		if(!empty($manag_cache["hash_location"])){
			copy($img_disk,$manag_cache["hash_location"]);
		}
		send_img_disk($img_disk);
	}
}

$list_images=array();
if($vigurl){
	$list_images[]=$vigurl;
}

if (strlen($noticecode)==12) {
    // code UPC -> EAN
    $noticecode = '0' . $noticecode;
}
$url_images  = explode(";", urldecode($url_image));
foreach ($url_images as $url_image) {
    if ($noticecode) {
    	if (isEAN($noticecode)) {
    		if (isISBN($noticecode)) {
    			if (isISBN10($noticecode)) {
    				$list_images[]=str_replace("!!isbn!!", str_replace("-","",$noticecode), $url_image);
    				$list_images[]=str_replace("!!isbn!!", str_replace("-","",formatISBN($noticecode,"13")), $url_image);
    			} else {
    				$list_images[]=str_replace("!!isbn!!", str_replace("-","",EANtoISBN10($noticecode)), $url_image);
    				$list_images[]=str_replace("!!isbn!!", str_replace("-","",$noticecode), $url_image);
    			}
    		} else {
    			$list_images[]=str_replace("!!isbn!!", str_replace("-","",$noticecode), $url_image);
    		}
    	}
    	$list_images[]=str_replace("!!isbn!!", $noticecode, $url_image);

    } else {
    	$list_images[]=rawurldecode(stripslashes($url_image));
    }
}
$list_images = array_unique($list_images);
$image="";
if ($opac_curl_available) {
	$aCurl = new Curl();
	$aCurl->limit=$poids_fichier_max;//Limite la taille de l'image à 1 Mo
	$aCurl->timeout=5;
	$aCurl->options["CURLOPT_SSL_VERIFYPEER"]="0";
	$aCurl->options["CURLOPT_ENCODING"]="";

	$need_copyright_amazon = false;

	if (count($list_images)) foreach ($list_images as $current_url) {
		$content = $aCurl->get($current_url);
		$image=$content->body;

		if(!isset($content->headers['Content-Length']) && strlen($image)){
			$content->headers['Content-Length'] = strlen($image);
		}

		if(!$image || $content->headers['Status-Code'] != 200 || ($content->headers['Content-Length'] > $aCurl->limit) ||  ($content->headers['Content-Length'] < 100)){
			$image="";
		}else{
			if (strpos($current_url, 'amazon')) {
				$need_copyright_amazon = true;
			}
			break;
		}
	}
	if (!empty($noticecode) && ($image == '' || file_get_contents($base_path.'/images/white_pixel.gif') == $image || file_get_contents($base_path.'/images/white_pixel_2x2.png') == $image)) {
	    $amazon = new amazon();
	    $data = $amazon->get_images_by_code($noticecode);
	    if (isset($data['MediumImage'])) {
	        $content = $aCurl->get($data['MediumImage']);
	        $image = $content->body;
	    } else {
	    	$image = '';
	    }
	}
} else {
	// priorité à vigurl si fournie
	$fp="";
	if (count($list_images)) foreach ($list_images as $current_url) {
		if($fp=@fopen(rawurldecode(stripslashes($current_url)), "rb")){
			break;
		}
	}

	if ($fp) {
		//Lecture et vérification de l'image
		$image="";
		$size=0;
		$flag=true;
		while (!feof($fp)) {
			$image.=fread($fp,4096);
			$size=strlen($image);
			if ($size>$poids_fichier_max) {
				$flag=false;
				break;
			}
		}
		if (!$flag) {
			$image="";
		}
		fclose($fp) ;
	}
}

if ($image && ($img=imagecreatefromstring($image))) {
	$redim=false;
	if($vigurl){
		if(!($pmb_notice_img_pics_max_size*1)) $pmb_notice_img_pics_max_size=150;
		if (imagesx($img) >= imagesy($img)) {
			if(imagesx($img) <= $pmb_notice_img_pics_max_size){
				$largeur=imagesx($img);
				$hauteur=imagesy($img);
			}else{
				$redim=true;
				$largeur=$pmb_notice_img_pics_max_size;
				$hauteur = ($largeur*imagesy($img))/imagesx($img);
			}
		} else {
			if(imagesy($img) <= $pmb_notice_img_pics_max_size){
				$hauteur=imagesy($img);
				$largeur=imagesx($img);
			}else{
				$redim=true;
				$hauteur=$pmb_notice_img_pics_max_size;
				$largeur = ($hauteur*imagesx($img))/imagesy($img);
			}
		}
	}else{
		$largeur = imagesx($img);
		$hauteur = imagesy($img);
	}

	$dest = imagecreatetruecolor($largeur,$hauteur);
	$white = imagecolorallocate($dest, 255, 255, 255);
	imagefilledrectangle($dest, 0, 0, $largeur, $hauteur, $white);
	if($redim){
		imagecopyresampled($dest, $img, 0, 0, 0, 0, $largeur, $hauteur,imagesx($img),imagesy($img));
	}else{
		imagecopyresampled($dest, $img, 0, 0, 0, 0, $largeur, $hauteur, $largeur, $hauteur);
	}

	//Copyright Amazon
	if ($need_copyright_amazon) {
		imagestring($dest, 1, ($largeur/3), ($hauteur/1.1), "Copyright Amazon", $white);
	}

	$copy_ok=false;
	if(empty($no_caching) && $manag_cache["hash_location"]){
		$copy_ok=imagepng($dest, $manag_cache["hash_location"]);
	}
	if(empty($no_caching) && $copy_ok){
		if(!empty($manag_cache["hash_location"])) {
			send_img_disk($manag_cache["hash_location"]);
		}
	}else{
		header('Content-Type: image/png');
		imagepng($dest);
		imagedestroy($dest);
		imagedestroy($img);
	}
}else{
	$img_disk = get_url_icon('no_image.png');
	if(empty($entity_id) && !empty($noticecode)) {
		$entity_id = notice::get_notice_id_from_cb($noticecode);
	}
	if (!empty($entity_id)) {
		$query = "SELECT niveau_biblio, typdoc FROM notices WHERE notice_id='".$entity_id."'";
		$res = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$img_disk = notice::get_picture_url_no_image($row['niveau_biblio'], $row['typdoc']);
		}
	}
	$type = get_content_type($img_disk);
	if(empty($no_caching)) {
		if($manag_cache["hash_location_empty"]){
			copy($img_disk,$manag_cache["hash_location_empty"]);
		}elseif($manag_cache["hash_location"]){
			copy($img_disk,$manag_cache["hash_location"]);
		}
	}
	send_img_disk($img_disk, $type);
}

function send_img_disk($img_disk, $content_type = 'Content-Type: image/png') {
	if ($img_disk) {
		header($content_type);
		$fp = @fopen($img_disk, "rb");
		if ($fp) {
			fpassthru($fp);
			fclose($fp);
		}
	}
	die();
}