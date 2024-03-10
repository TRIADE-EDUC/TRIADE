<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
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
$fic=trim($_GET["fic"]);
$id_pers_ss=$_SESSION['id_pers'];
$id_pers=$_GET['idpers'];

if ($id_pers_ss != $id_pers) exit;

if (preg_match('/^\//',$fic)) exit;

$filename = stripslashes(basename($fic));

switch(strrchr(basename($filename), ".")) {
	case ".gif": $type = "image/gif"; break;
	case ".jpg": $type = "image/jpeg"; break;
	case ".png": $type = "image/png"; break;
	case ".txt": $type = "text/plain"; break;
	case ".php": exit; break;
	default: $type = "application/octet-stream"; exit ; break;
}
header("Content-disposition: attachment; filename=$filename");
header("Content-Type: $type");
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
