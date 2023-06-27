<?php
session_start();
include_once("../common/config2.inc.php");
if (empty($_SESSION["admin1"])) { 
	header('Location: acces_refuse.php');
    	exit;
}
function telechargerFichier($chemin){
 if ((!file_exists($chemin)) && (!@fclose(@fopen($chemin, "r")))) die('Erreur:fichier incorrect');
 $filename = stripslashes(basename($chemin));
 $user_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
 header("Content-type: application/force-download");
 header(((is_integer(strpos($user_agent,"msie")))&&(is_integer(strpos($user_agent, "win"))))?"Content-Disposition:filename=\"$filename\"":"Content-Disposition: attachment; filename=\"$filename\"");
 header("Content-Description: Telechargement de Fichier");
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
 @readfile($chemin);
 die();
}

if ($_GET["id"] == "base" ) { telechargerFichier("../data/dumpdist/dump".$_GET["id2"]."_.sql"); }
if ($_GET["id"] == "conf" ) { telechargerFichier("../data/dumpdist/".$_GET["id2"]."_dump_common.zip"); }
if ($_GET["id"] == "data" ) { telechargerFichier("../data/dumpdist/".$_GET["id2"]."_dump_data.zip"); }
?>
