<?php
if (!isset($_GET["fichier"])) { exit; }
include_once('./common/config.inc.php');
include_once('./librairie_php/db_triade.php');
include_once("./common/config2.inc.php");

$fic="./data/fichier_ASCII/CentralSouhait".$_GET["fichier"];
if (!file_exists($fic)) { exit; } 

$fic=preg_replace("/\.\./","x",$fic);
$fic=preg_replace("/^\//","x",$fic);
$filename=stripslashes(basename($fic));
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
