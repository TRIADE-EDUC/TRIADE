<?php
session_start();
if (empty($_SESSION["nom"]))  {
    print "<script language='javascript'>";
    print "location.href='./acces_refuse.php'";
    print "</script>";
    exit;
}

if (!isset($_GET["fichier"])) { exit; }
include_once('./common/config.inc.php');
include_once('./librairie_php/db_triade.php');
include_once("./common/config2.inc.php");

$fic=$_GET["fichier"];
$pass=0;


if (preg_match('/data\/recherche\//',$fic)) { validerequete("3"); $pass=1; }
if (preg_match('/data\/vacation\//',$fic)) { validerequete("2"); $pass=1; }
if (preg_match('/data\/compta\//',$fic)) { validerequete("2"); $pass=1; }
if (preg_match('/data\/pdf_bull\/contrerendustage\//',$fic)) { validerequete("3"); $pass=1; }
if (preg_match('/data\/pdf_bull\/listingstage\//',$fic)) { validerequete("3"); $pass=1; }
if (preg_match('/data\/comptaenseignant\//',$fic)) { validerequete("menuadmin"); $pass=1; }
if (preg_match('/data\/parametrage\//',$fic)) { validerequete("2"); $pass=1; }
if (preg_match('/data\/fichier_ASCII\//',$fic)) { 
	if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
		validerequete("menuprof");
	}else{
		validerequete("2"); 
	}
	$pass=1; 
}
if (preg_match('/data\/cantine\//',$fic)) { validerequete("8"); $pass=1; }
if (preg_match('/data\/pdf_certif\/listingEntreprise/',$fic)) { validerequete("5");  $pass=1;}
if (preg_match('/data\/circulaire\//',$fic)) { $pass=1;}
if (preg_match('/.\/data\/archive\/bulletin\//',$fic)) { 
	if ($_SESSION["membre"] == "menupersonnel") {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) { exit(); }
		Pgclose();
	}else{
		validerequete("3"); 
	}
	$pass=1;	
}
if ($pass == 0) { exit; }


$fic=preg_replace('/\.\./',"x",$fic);
$fic=preg_replace('/^\//',"x",$fic);
$filename = stripslashes(basename($fic));
switch(strrchr(basename($filename), ".")) {
	case ".gz": $type = "application/x-gzip"; break;
	case ".tgz": $type = "application/x-gzip"; break;
	case ".zip": $type = "application/zip"; break;
	case ".pdf": $type = "application/pdf"; break;
	case ".png": $type = "image/png"; break;
	case ".gif": $type = "image/gif"; break;
	case ".jpg": $type = "image/jpeg"; break;
	case ".txt": $type = "text/plain"; break;
	case ".php": exit; break;
	case ".htm": $type = "text/html"; break;
	case ".html": $type = "text/html"; break;
	default: $type = "application/octet-stream"; break;
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
