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
if ($_SESSION["membre"] == "menuprof") {
	$data=aff_enr_parametrage("autorisebulletinprof"); 
	if ($data[0][1] == "oui") {
		validerequete("menuprof");
	}else{
		verif_profp_class($_SESSION["id_pers"],$_GET["idclasse"]);
	}
	PgClose();
}elseif($_SESSION["membre"] == "menupersonnel"){
	$visu=0;
	if(verifDroit($_SESSION["id_pers"],"vatelcompta")) { $visu=1; }

	if ($visu == 0) { exit; }
}else{
	validerequete("2");
}
$fic=$_GET["id"];
$fic=preg_replace('/.\/data\/pdf_bull\//','',$fic);
$fic="./data/pdf_bull/".$fic;
if (!file_exists($fic)) {
	header("Location: ./err404.php");
	exit;
}
$filename = stripslashes(basename($fic));
switch(strrchr(basename($filename), ".")) {
	case ".pdf": $type = "application/pdf"; break;
	case ".zip": $type = "application/zip"; break;
	default: exit; break;

}
$cnx=cnx();
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
