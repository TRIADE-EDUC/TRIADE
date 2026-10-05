<?php 
session_start();
/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

/* NOTE : Dans ce contexte : Avatar => Photo de l'utilisateur sur le trombinoscope */
/* get-avatar.php, le demandeur le consulte en mode GET avec session PHP */
/* Forme URL de la requête : http://serveur/triade/api-mobile/get-avatar.php */

if(isset($_SESSION["connecte"]) and $_SESSION["connecte"] == true and $_SERVER['REQUEST_METHOD'] === 'GET'){
//if(true){
    	// Envoi direct de l'avatar au format JPEG
    	/* Ci-dessous un exemple de photo de trombinoscope */
	$profil=$_SESSION["profil"]; // 1 = étudiant, 2 = parent, 3 = enseignant
	$idpers=$_SESSION["id_pers"];
    	$idclasse=$_SESSION["idClasse"];
/*	
	$idpers=87;
	$profil=1;
	$idclasse=1;
 */	

    	$cnx=cnx();
	if (!is_numeric($idpers)) exit;
	$photoLocal=recherche_photo_eleve($idpers);
	Pgclose();

	$_img_base = realpath('../data/image_eleve');
	$_img_path = ($photoLocal && $_img_base) ? realpath('../data/image_eleve/' . $photoLocal) : false;
	if ($_img_path && strpos($_img_path, $_img_base) === 0 && file_exists($_img_path)) {
		header('Content-Type: text/plain');
	    	$avatar = file_get_contents($_img_path);
	    	$base64 = base64_encode($avatar);
		/* L'image est renvoyée sous la forme d'un encodage en base64 */
	    	echo $base64 ;
    		die();
	}else{
    		header('Content-Type: text/plain');
	    	$avatar = file_get_contents("../image/commun/photo_vide.jpg"); // la photo de trombinoscope vide
	    	$base64 = base64_encode($avatar);
	    	/* L'image est renvoyée sous la forme d'un encodage en base64 */
	    	echo $base64 ; 
    		die();
	}
} else { /* Le demandeur ne possède pas de sessison - Par défaut */
    	header('Content-Type: text/plain');
    	$avatar = file_get_contents("../image/commun/photo_vide.jpg"); // la photo de trombinoscope vide
    	$base64 = base64_encode($avatar);
    	/* L'image est renvoyée sous la forme d'un encodage en base64 */
    	echo $base64 ; 
    	die();
}
?>
