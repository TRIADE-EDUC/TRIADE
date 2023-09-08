<?php
session_start();
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
if (!isset($_GET["id"])) {
	exit;
}

$id=$_GET["id"];

include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

$data=recupListFichierPartagerViaId($id);
if (count($data) == 0) { exit; }

// fichier,chemin,membreIdProprio,membreIdAutorise,idclasse,membresource,idsource,id
$fichier=$data[0][0];
$chemin=$data[0][1];
$membreIdProprio=$data[0][2];
$membreIdAutorise=$data[0][3];
$idclasse=$data[0][4];
$membresource=$data[0][5];
$idsource=$data[0][6];
$membreid=$_SESSION["membre"].$_SESSION["id_pers"];
if ($membreIdAutorise != $membreid) { exit; }

$filename=$fichier;
$fic="./data/stockage/$membresource/$idsource/$chemin";

Pgclose();


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
