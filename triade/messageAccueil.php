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
?>
<html>
<head>
   <link rel="stylesheet" type="text/css" href="./librairie_css/css.css" media="screen" />
</head>
<body style="margin:0;" >
<?php 
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

$recupMessAdmin=consultMessAdminId($_GET["id"]); //idnews,nom,prenom,date,heure,titre,texte,type
print "<table border='0' width='100%' ><tr><td align='left'>";
if (($_SESSION["membre"] == "menuadmin") || (($_SESSION["membre"] == "menuscolaire") && (MODULENEWSVIESCOLAIRE == "oui")) ) {
	print "&nbsp;&nbsp;[ <a href=\"news_actualite_supp.php?id=".$_GET["id"]."\">Supprimer ce message</a> ]";
	if ($recupMessAdmin[0][7] == "video") {
	//	print "&nbsp;&nbsp;[ <a href=\"commvideo.php?id=".$_GET["id"]."\">Modifier ce message</a> ]";
	}else{
		if (($_SESSION["membre"] == "menuadmin") || (($_SESSION["membre"] == "menuscolaire") && (MODULENEWSVIESCOLAIRE == "oui")) ) {
			print "&nbsp;&nbsp;[ <a href=\"actualiteetablissement.php?id=".$_GET["id"]."\">Modifier ce message</a> ]";
		}
	}
}
print "</td><td align='right'>";
print "<a href='#' onclick='closeMessage();return false' title='Fermer' ><img src='image/commun/quitter.gif' border='0'></a>";
print "</td></tr></table>";
print "<br />";		
$message=filtreCopierColler($recupMessAdmin[0][6]);
print $message;
Pgclose();
?>
</BODY></HTML>
