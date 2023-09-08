<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: getimage.php,v 1.35 2018-08-27 14:45:29 ngantier Exp $

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

require_once("$class_path/curl.class.php");
require_once($base_path."/includes/isbn.inc.php");
require_once($base_path."/admin/connecteurs/in/amazon/amazon.class.php");

session_write_close();

$poids_fichier_max=1024*1024;//Limite la taille de l'image à 1 Mo

if(!isset($notice_id)){
	$notice_id = 0;
}

if(!isset($etagere_id)){
	$etagere_id = 0;
}

if(!isset($authority_id)){
	$authority_id = 0;
}

$img_disk="";

$manag_cache=getimage_cache($notice_id, $etagere_id, $authority_id, $vigurl, $noticecode, $url_image);
if($manag_cache["location"]){
    $img_disk=$manag_cache["location"];
    if($manag_cache["hash_location"]){
        copy($img_disk,$manag_cache["hash_location"]);
    }
    send_img_disk($img_disk);
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
	$aCurl->timeout=15;
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
	if ($image == '' || file_get_contents($base_path.'/images/white_pixel.jpg') == $image) {	    
	    $amazon = new amazon();
	    $data = $amazon->get_images_by_code($noticecode);
	    if(isset($data['MediumImage'])) {	        
	        $content = $aCurl->get($data['MediumImage']);
	        $image = $content->body;
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
	if($manag_cache["hash_location"]){
		$copy_ok=imagepng($dest, $manag_cache["hash_location"]);
	}
	if($copy_ok){
		send_img_disk($manag_cache["hash_location"]);
	}else{
		header('Content-Type: image/png');
		imagepng($dest);
		imagedestroy($dest);
		imagedestroy($img);
	}
}else{
	$img_disk = get_url_icon('no_image.png');
	if($manag_cache["hash_location_empty"]){
		copy($img_disk,$manag_cache["hash_location_empty"]);
	}elseif($manag_cache["hash_location"]){
		copy($img_disk,$manag_cache["hash_location"]);
	}
	send_img_disk($img_disk);
}

function send_img_disk($img_disk){
	if($img_disk){
		header('Content-Type: image/png');
		$fp=@fopen($img_disk, "rb");
		if($fp){
			fpassthru($fp);
			fclose($fp) ;
		}
	}
	die();
}