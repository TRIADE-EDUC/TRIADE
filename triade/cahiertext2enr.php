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
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./common/config2.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
	$cnx=cnx();
	if (!verifDroit($_SESSION["id_pers"],"cahiertextes")) {
		accesNonReserveFen();
		exit();
	}
	Pgclose();
}else{
	validerequete("profadmin");	
}
$pourle=$_POST["saisie_pour"];
$sClasseGrp=$_POST["sClasseGrp"];
$sMat=$_POST["saisie_idmatiere"];
$date=$_POST["date_contenu"];
$tempsestime=$_POST["tempsestime"];
header("Location:cahiertext2.php?sClasseGrp=$sClasseGrp&sMat=$sMat&date_convenu=$date&tempsestime=$tempsestime");
?>
