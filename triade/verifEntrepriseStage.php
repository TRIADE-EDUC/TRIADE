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
 //
 ***************************************************************************/
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$data=verifEntreprise($_POST["nomEntreprise"]);  //nom,code_p,ville
if (count($data) > 0) {
	for($i=0;$i<count($data);$i++) {
		$societe=html_quotes($data[$i][0]);
		$ccp=html_quotes($data[$i][1]);
		$ville=html_quotes($data[$i][2]);
		$listeEntreprise.="<img src=\'image/on10.gif\' /> $societe - $ville / $ccp <br />";
	}

	print "<a href='#' onMouseOver=\"AffBulle3('Entreprise ayant le même nom','./image/commun/warning.gif','$listeEntreprise');\" onMouseOut=\"HideBulle();\" ><img src='image/commun/important.png' border='0' /></a>";

}else{
	print "";
}
PgClose();
?>
