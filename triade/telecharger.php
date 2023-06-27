<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
if (empty($_SESSION["nom"]))  {
	header('Location: ./acces_refuse.php');
	exit;
}
include_once("./common/config2.inc.php");
$fic=$_GET["fichier"];
$filename=$_GET["fichiername"];
if (isset($_GET["fichiername"])) {
	$filename = stripslashes(basename($_GET["fichiername"]));
	$fic=preg_replace("/\.\./","x",$fic);
	$fic=preg_replace("/^\//","x",$fic);
}else{
	$fic=preg_replace("/\.\./","x",$fic);
	$fic=preg_replace("/^\//","x",$fic);
	$filename = stripslashes(basename($fic));
}

$fic=preg_replace("/data2/","../data/",$fic);

if (preg_match('/ArchiveBulletin/',$fic))  { 
	include_once("./librairie_php/db_triade.php");
	validerequete("menuadmin"); 
}

switch(strrchr(basename($filename), ".")) {
	case ".gz": $type = "application/x-gzip"; break;
	case ".tgz": $type = "application/x-gzip"; break;
	case ".zip": $type = "application/zip"; break;
	case ".pdf": $type = "application/pdf"; break;
	case ".png": $type = "image/png"; break;
	case ".gif": $type = "image/gif"; break;
	case ".jpg": $type = "image/jpeg"; break;
	case ".txt": $type = "text/plain"; break;
	case ".doc": $type = "application/msword"; break;
	case ".xls": $type = "application/x-msexcel"; break;
	case ".rtf": $type = "application/msword"; break;
	case ".php": exit; break;
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
