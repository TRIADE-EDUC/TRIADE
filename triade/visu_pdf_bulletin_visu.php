<?php
session_start();
if (empty($_SESSION["id_pers"]))  {
	header("Location: ./acces_refuse.php");
	exit;
}

include_once('./common/config.inc.php');
include_once('./librairie_php/db_triade.php');
include_once("./common/config2.inc.php");
validerequete("6");
$cnx=cnx();
$id_pers=$_SESSION["id_pers"];
$file=$_GET["file"];
$id=$_GET["id"];

if ($_SESSION["membre"] == "menututeur") $id_pers=$_GET["ide"];

$data=recupInfoBulletinElPar($id);
$idclasse=$data[0][0];
$tri=$data[0][1];
$anneeScolaire=$data[0][2];
$anneeScolaire=preg_replace('/ /','',$anneeScolaire);

$fic="./data/archive/bulletin/$anneeScolaire/_$id_pers/$file";
if (!file_exists($fic)) exit ;


$filename = stripslashes(basename($fic));
switch(strrchr(basename($filename), ".")) {
	case ".pdf": $type = "application/pdf"; break;
	case ".xls": $type = "application/x-msexcel"; break;
	default: exit; break;

}

history_cmd($_SESSION["nom"],"CONSULTATION BULLETIN"," $filename");
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
