<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vig_num.php,v 1.17.4.2 2025/03/07 08:36:42 jparis Exp $

// définition du minimum nécéssaire
$base_path      = ".";
$base_auth      = ""; //"CIRCULATION_AUTH";
$base_title     = "";
$base_noheader  = 1;
$base_nocheck   = 1;
$base_nobody    = 1;
$base_nosession = 1;

require_once ("$base_path/includes/init.inc.php");
session_write_close();//Fermeture de la session dès que possible

$explnum_id = (int) $explnum_id;
$resultat = pmb_mysql_query("SELECT explnum_id, explnum_mimetype, explnum_vignette, explnum_extfichier FROM explnum WHERE explnum_id = '$explnum_id' ", $dbh);
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	exit ;
}
global $pmb_docnum_img_folder_id;
$ligne = pmb_mysql_fetch_object($resultat);
if (!empty($pmb_docnum_img_folder_id)) {
    $query = "select repertoire_path from upload_repertoire where repertoire_id ='$pmb_docnum_img_folder_id'";
    $result = pmb_mysql_query($query);
    if(pmb_mysql_num_rows($result)){
        $row=pmb_mysql_fetch_object($result);
        $filename_output=$row->repertoire_path."img_docnum_".$ligne->explnum_id;
        if (file_exists($filename_output)) {
            print file_get_contents($filename_output);
            exit;
        }
    }
}
if ($ligne->explnum_vignette) {
	print $ligne->explnum_vignette;
	exit ;
} else {
    require_once ("$include_path/explnum.inc.php");
    require_once("$class_path/curl.class.php");

	//On charge le tableau de mimetypes
	create_tableau_mimetype();
	//On cherche l'image associee
	$image_filename = "/images/mimetype/". icone_mimetype($ligne->explnum_mimetype, $ligne->explnum_extfichier);
	if ($pmb_curl_available) {
		$image_url = 'http';
		if ($_SERVER["HTTPS"] == "on") {$image_url .= "s";}
		$image_url .= "://";
		$image_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].dirname($_SERVER["SCRIPT_NAME"]).$image_filename;
		$aCurl = new Curl();
		$content = $aCurl->get($image_url);
		$contenu_vignette = $content->body;
	} else {
		$fp = fopen(".$image_filename" , "r" ) ;
		$contenu_vignette = fread ($fp, filesize(".$image_filename"));
		fclose ($fp) ;
	}
	print $contenu_vignette ;
}
