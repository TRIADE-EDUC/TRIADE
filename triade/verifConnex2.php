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
include_once("./common/config2.inc.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
verifCnxIntraMsn();

$nb=$_POST["nb"];
if (defined("TIMESESSION")) {
	$max=TIMESESSION;
}else{
	$max=6;
}
$alerte=$max-1;
if ($nb <= $max) {
	print "0";
}elseif( $nb <= $alerte)   { // alerte !!
	print "2";
}else{
	// mettre en veille
	if (isset($_COOKIE["CONNEXION"])) {
		print "0";
	}else{
		print "1";
	}
}
?>
