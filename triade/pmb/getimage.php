<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: getimage.php,v 1.41 2023/02/20 13:33:14 dgoron Exp $

global $class_path, $pmb_curl_available, $empr_pics_max_size;
global $notice_id, $etagere_id, $authority_id;
global $no_caching;

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
if(isset($_GET['empr_pic'])){
	$empr_pic=$_GET['empr_pic'];
}else{
	$empr_pic="";
}

$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once ($base_path."/includes/init.inc.php");
require_once($class_path."/curl.class.php");
require_once("$base_path/includes/isbn.inc.php");
require_once($base_path."/admin/connecteurs/in/amazon/amazon.class.php");

session_write_close();

$poids_fichier_max=1024*1024;//Limite la taille de l'image à 1 Mo

$notice_id = intval($notice_id);
$etagere_id = intval($etagere_id);
$authority_id = intval($authority_id);

$img_disk="";

if(empty($no_caching)) {
	$manag_cache=getimage_cache($notice_id, $etagere_id, $authority_id, $vigurl, $noticecode, $url_image, $empr_pic);
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
if ($pmb_curl_available) {
	$aCurl = new Curl();
	$aCurl->limit=$poids_fichier_max;//Limite la taille de l'image à 1 Mo
	$aCurl->timeout=5;
	$aCurl->options["CURLOPT_SSL_VERIFYPEER"]="0";
	$aCurl->options["CURLOPT_ENCODING"]="";
	
	$need_copyright_amazon = false;
	
	if (count($list_images)) foreach ($list_images as $current_url) {
		$content = $aCurl->get($current_url);
		if(!isset($content->body)) continue;
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
	if (!empty($noticecode) && ($image == '' || file_get_contents($base_path.'/images/white_pixel.jpg') == $image || file_get_contents($base_path.'/images/white_pixel_2x2.png') == $image)) {
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
	if($empr_pic){
		if (imagesx($img) >= imagesy($img)) {
			if(imagesx($img) <= $empr_pics_max_size){
				$largeur=imagesx($img);
				$hauteur=imagesy($img);
			}else{
				$redim=true;
				$largeur=$empr_pics_max_size;
				$hauteur = ($largeur*imagesy($img))/imagesx($img);
			}
		} else {
			if(imagesy($img) <= $empr_pics_max_size){
				$hauteur=imagesy($img);
				$largeur=imagesx($img);
			}else{
				$redim=true;
				$hauteur=$empr_pics_max_size;
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
	$img_disk=get_url_icon('vide.png');
	if(empty($no_caching)) {
		if($manag_cache["hash_location_empty"]){
			copy($img_disk,$manag_cache["hash_location_empty"]);
		}elseif($manag_cache["hash_location"]){
			copy($img_disk,$manag_cache["hash_location"]);
		}
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