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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
validerequete("2");

include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(0);
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Export des données Triade vers Visual Timetabling" ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php
if (!file_exists("./data/triade2vt")) { @mkdir("./data/triade2vt",0755); }


$fichier="./data/triade2vt/triade2vt.xml";
@unlink($fichier);

$xml=fopen($fichier,"a+");
fwrite($xml,'<?xml version="1.0" encoding="UTF-8"?>'."\n"); //ISO-8859-1
fwrite($xml,'<TRIADE2VT>'."\n");


fwrite($xml,"\t".'<PARAMETRAGE>'."\n");
fwrite($xml,"\t\t".'<VERSION_TRIADE>'.VERSION.'</VERSION_TRIADE>'."\n");
fwrite($xml,"\t\t".'<VERSION_PATCH>'.VERSIONPATCH.'</VERSION_PATCH>'."\n");
fwrite($xml,"\t\t".'<VERSION_XML_VT>'."1.1".'</VERSION_XML_VT>'."\n");
fwrite($xml,"\t\t".'<DATE_CREATION_XML>'.dateDMY2().'</DATE_CREATION_XML>'."\n");
fwrite($xml,"\t".'</PARAMETRAGE>'."\n");

$cnx=cnx();
$data=affPers("ENS"); // pers_id, civ, nom, prenom, identifiant
fwrite($xml,"\t".'<LES_ENSEIGNANTS>'."\n");
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$nom=$data[$i][2];
	$prenom=$data[$i][3];
	$identifiant=$data[$i][4];
	if (trim($identifiant) == ""){ $identifiant="#id#".$data[$i][0]; }

	fwrite($xml,"\t\t".'<UN_ENSEIGNANT>'."\n");
	fwrite($xml,"\t\t\t".'<ID>'.$id."</ID>\n");
	fwrite($xml,"\t\t\t".'<NOM>'.$nom."</NOM>\n");
	fwrite($xml,"\t\t\t".'<PRENOM>'.$prenom."</PRENOM>\n");
	fwrite($xml,"\t\t\t".'<IDTRIADEVT>'.$identifiant."</IDTRIADEVT>\n");
	fwrite($xml,"\t\t".'</UN_ENSEIGNANT>'."\n");
}
fwrite($xml,"\t".'</LES_ENSEIGNANTS>'."\n");


$data=affMatiere(); // code_mat,libelle,sous_matiere
fwrite($xml,"\t".'<LES_MATIERES>'."\n");
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$nom=$data[$i][1];
	fwrite($xml,"\t\t".'<UNE_MATIERE>'."\n");
	fwrite($xml,"\t\t\t".'<ID>'.$id."</ID>\n");
	fwrite($xml,"\t\t\t".'<NOM>'.$nom."</NOM>\n");
	fwrite($xml,"\t\t".'</UNE_MATIERE>'."\n");
}
fwrite($xml,"\t".'</LES_MATIERES>'."\n");


$data=affClasse(); // code_class,libelle
fwrite($xml,"\t".'<LES_CLASSES>'."\n");
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$nom=$data[$i][1];
	fwrite($xml,"\t\t".'<UNE_CLASSE>'."\n");
	fwrite($xml,"\t\t\t".'<ID>'.$id."</ID>\n");
	fwrite($xml,"\t\t\t".'<NOM>'.$nom."</NOM>\n");
	fwrite($xml,"\t\t".'</UNE_CLASSE>'."\n");
}
fwrite($xml,"\t".'</LES_CLASSES>'."\n");

$data=affGroupe(); // group_id,libelle
fwrite($xml,"\t".'<LES_GROUPES>'."\n");
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$nom=$data[$i][1];
	fwrite($xml,"\t\t".'<UN_GROUPE>'."\n");
	fwrite($xml,"\t\t\t".'<ID>'.$id."</ID>\n");
	fwrite($xml,"\t\t\t".'<NOM>'.$nom."</NOM>\n");
	fwrite($xml,"\t\t".'</UN_GROUPE>'."\n");
}
fwrite($xml,"\t".'</LES_GROUPES>'."\n");



$data=affEleve(); //elev_id, nom, prenom, classe
fwrite($xml,"\t".'<LES_ELEVES>'."\n");
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$nom=$data[$i][1];
	$prenom=$data[$i][2];
	$idclasse=$data[$i][3];
	fwrite($xml,"\t\t".'<UN_ELEVE>'."\n");
	fwrite($xml,"\t\t\t".'<ID>'.$id."</ID>\n");
	fwrite($xml,"\t\t\t".'<NOM>'.$nom."</NOM>\n");
	fwrite($xml,"\t\t\t".'<PRENOM>'.$prenom."</PRENOM>\n");
	fwrite($xml,"\t\t\t".'<IDCLASSE>'.$idclasse."</IDCLASSE>\n");
	fwrite($xml,"\t\t\t".'<LES_GROUPES>'."\n");
	$data2=rechercheEleveDansGroupe($id);
	foreach($data2 as $key) {
		fwrite($xml,"\t\t\t\t".'<UN_GROUPE>'."\n");
		fwrite($xml,"\t\t\t\t\t".'<ID>'.$key.'</ID>'."\n");
		fwrite($xml,"\t\t\t\t".'</UN_GROUPE>'."\n");
	}
	fwrite($xml,"\t\t\t".'</LES_GROUPES>'."\n");
	fwrite($xml,"\t\t".'</UN_ELEVE>'."\n");
}
fwrite($xml,"\t".'</LES_ELEVES>'."\n");

fwrite($xml,'</TRIADE2VT>'."\n");
fclose($xml);

include_once('./librairie_php/pclzip.lib.php');
$archive = new PclZip('./data/dump/triade2vt.zip');
$archive->create('./data/triade2vt');

nettoyage_repertoire("./data/triade2vt");
@rmdir("./data/triade2vt");

Pgclose();
?>
<font class="T2">
&nbsp;&nbsp;<img src="./image/on1.gif" align='center' width='8' height='8' /> <a href='telecharger.php?fichier=./data/dump/triade2vt.zip'>Récupérer le fichier pour Visual Timetabling <b>cliquez ici</b></a>
</font>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
