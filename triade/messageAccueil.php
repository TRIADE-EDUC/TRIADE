<?php
session_start();
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
$id   = (int)$_GET["id"];
$type = $recupMessAdmin[0][7];
$nom  = $recupMessAdmin[0][1];
$prenom = $recupMessAdmin[0][2];
$date = dateForm($recupMessAdmin[0][3]);
$heure = timeForm($recupMessAdmin[0][4]);
$isAdmin = ($_SESSION["membre"] == "menuadmin") || (($_SESSION["membre"] == "menuscolaire") && (MODULENEWSVIESCOLAIRE == "oui"));

// Barre d'actions admin
if ($isAdmin) {
	print '<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-bottom:12px;border-bottom:2px solid #eef0f8">';
	print '<a href="news_actualite_supp.php?id='.$id.'" style="font-size:11px;font-weight:600;color:#c62828;text-decoration:none;border:1px solid #f48fb1;border-radius:5px;padding:4px 12px;background:#fce4ec">&#128465; Supprimer</a>';
	if ($type != "video") {
		print '<a href="actualiteetablissement.php?id='.$id.'" style="font-size:11px;font-weight:600;color:#080A66;text-decoration:none;border:1px solid #c5cae9;border-radius:5px;padding:4px 12px;background:#eef0f8">&#9998; Modifier</a>';
	}
	print '</div>';
}

// Méta : auteur + date
print '<div style="font-size:11px;color:#888;margin-bottom:14px">';
print '<span style="margin-right:14px">&#128100; '.htmlspecialchars(ucwords($prenom).' '.strtoupper($nom)).'</span>';
print '<span>&#128197; '.$date.' &nbsp;&#9200; '.$heure.'</span>';
print '</div>';

// Contenu
print '<div style="font-size:13px;color:#333;line-height:1.75">';
print filtreCopierColler($recupMessAdmin[0][6]);
print '</div>';
Pgclose();
?>
</BODY></HTML>
