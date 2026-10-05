<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {
    header("./index.php");
    exit;
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  - 
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
$fichier=$_FILES['Filedata']['name'];
$type=$_FILES['Filedata']['type'];
$tmp_name=$_FILES['Filedata']['tmp_name'];
$size=$_FILES['Filedata']['size'];
$md5=$_POST['md5'];
include_once("./common/config6.inc.php"); 
include_once("./librairie_php/db_triade.php"); 
if (MAXUPLOAD == "oui") {
	$taille="8000000";
}else{
	$taille="2000000";
}


$tt=date("d/m/Y")." -  Nom du fichier :".$fichier." ".$type." ".$size." ".$tmp_name."\n";
$fd=fopen("./data/IAimage.log","a");
fwrite($fd,"$tt");
fclose($fd);



if (!is_dir("./IAimg")) mkdir("./IAimg");

if ($type != "image/jpeg") exit;
$fichier=$md5.".jpg";

if ($size <= $taille) {
	move_uploaded_file($tmp_name,"./IAimg/$fichier");
	echo "";
}


?>
