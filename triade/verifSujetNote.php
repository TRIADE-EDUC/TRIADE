<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
 //
 ***************************************************************************/
 
 if (file_exists("./common/config.inc.php")) {
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
}

if (file_exists("../common/config.inc.php")) {
	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
}

$nom=utf8_decode($_POST["sujet"]);
$date=$_POST["date"];
$idg=$_POST["idg"];
$idc=$_POST["idc"];
$idm=$_POST["idm"];
$cnx=cnx();
$nom=addslashes($nom);
$cr=verifNomSujet(addslashes($nom),$date,$idg,$idc,$idm);
if ($cr) {
	print "&nbsp;&nbsp;&nbsp;<font class=T1 color=red><b>ATTENTION, Ce nom de sujet est déjà attribué pour la même date.</b><br /></font>";
}else{
	print "";
}
PgClose();
?>
