<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: getgif.php,v 1.14.10.2 2025/05/06 13:05:04 dgoron Exp $

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

//Sessions !! Attention, ce doit être impérativement le premer include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");
require_once($base_path.'/includes/start.inc.php');

session_write_close();

global $optionnel, $nomgif, $opac_notices_depliable_plus, $opac_notices_depliable_moins;

if(!isset($optionnel) || !$optionnel){//Dans le cas ou l'image est obligatoire (si elle sert de lien cliquable par exemple)
	if($nomgif == "plus"){
		$chemin=get_url_icon("plus.gif");
	}elseif($nomgif == "moins"){
		$chemin=get_url_icon("minus.gif");
	} else {
	    $chemin='';
	}
	if($chemin){
    	$content_type_gif="Content-Type: image/gif";
    	$fp=@fopen($chemin, "rb");
	}
}else{
	$chemin="";
	$content_type_gif="Content-Type: image/png";
	$fp=@fopen(get_url_icon('vide.png'), "rb");
}

switch ($nomgif) {
	case "plus":
		if($opac_notices_depliable_plus){
			$chemin = get_url_icon($opac_notices_depliable_plus);
		}
		break;
	case "moins":
		if($opac_notices_depliable_moins){
			$chemin = get_url_icon($opac_notices_depliable_moins);
		}
		break;
	default:
		break;
}
$tmp = "";
if($chemin){
	$fp2=@fopen($chemin, "rb");
	if($fp2){
		fclose($fp) ;
		$fp=$fp2;
		if(function_exists("finfo_open") && function_exists("finfo_file") && ($tmp=finfo_file(finfo_open(FILEINFO_MIME_TYPE), $chemin))){
		    if($tmp == 'image/svg') {
		        $tmp = 'image/svg+xml';
		    }
		    $content_type_gif="Content-Type: ".$tmp;
		}elseif(function_exists("mime_content_type") && ($tmp=mime_content_type($chemin))){
			$content_type_gif="Content-Type: ".$tmp;
		}
	}
}

if (substr($tmp, 0, 6) == "image/") {
	header($content_type_gif);
	fpassthru($fp);
	fclose($fp) ;
}

?>