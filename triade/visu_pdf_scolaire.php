<?php
session_start();
if (empty($_SESSION["nom"]))  {
	header("Location: ./acces_refuse.php");
	exit;
}
include_once('./common/config.inc.php');
include_once('./librairie_php/db_triade.php');
include_once("./common/config2.inc.php");
$cnx=cnx();
$fic=$_GET["id"];
if (preg_match('/pdf_bull\/commentaire_/',$fic)) {
	validerequete("3");
}else{
	validerequete("2");
}
if (!file_exists($fic)) {
	header("Location: ./err404.php");
	exit;
}
$fic=preg_replace('/\.\./',"x",$fic);
$fic=preg_replace('/^\//',"x",$fic);
$filename = stripslashes(basename($fic));
switch(strrchr(basename($filename), ".")) {
	case ".pdf": $type = "application/pdf"; break;
	default: exit; break;

}
history_cmd($_SESSION["nom"],"CONSULTATION"," $filename");
PgClose();
header("Content-disposition: attachment; filename=$filename");
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: $type\n"); // Surtout ne pas enlever le \n
header("Content-Length: ".filesize($fic));
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile($fic);

?>
