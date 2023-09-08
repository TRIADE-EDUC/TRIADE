<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E.
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if ((isset($_POST["modif"])) &&  ($_POST["saisie_carnet"] > 0)) {
	if (isset($_POST["saisie_carnet"])) {
		$idcarnet=$_POST["saisie_carnet"];
	}
	$nom_carnet=chercheNomCarnet($idcarnet);
}else{
	print "<script>location.href='carnet_admin_modif.php?erreur'</script>";
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Carnet de Suivi : <font id='color2'> $nom_carnet </font>" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br />
<?php
if (!file_exists("./data/triade2CdS")) { @mkdir("./data/triade2CdS",0755); }

$fichier="./data/triade2CdS/triade2CdS.xml";
@unlink($fichier);

$xml=fopen($fichier,"a+");
fwrite($xml,'<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
fwrite($xml,'<TRIADE2CDS>'."\n");


fwrite($xml,"\t".'<PARAMETRAGE>'."\n");
fwrite($xml,"\t\t".'<VERSION_TRIADE>'.VERSION.'</VERSION_TRIADE>'."\n");
fwrite($xml,"\t\t".'<VERSION_PATCH>'.VERSIONPATCH.'</VERSION_PATCH>'."\n");
fwrite($xml,"\t\t".'<VERSION_XML_CDS>'."1.0".'</VERSION_XML_CDS>'."\n");
fwrite($xml,"\t\t".'<DATE_CREATION_XML>'.dateDMY2().'</DATE_CREATION_XML>'."\n");
fwrite($xml,"\t".'</PARAMETRAGE>'."\n");

$data=affCarnet($idcarnet); // nom_carnet,code_lettre,code_chiffre,code_couleur,code_note,section,nb_periode
fwrite($xml,"\t".'<LES_CARNETS>'."\n");
for($i=0;$i<count($data);$i++) {
	$nom_carnet=$data[$i][0];
	$code_lettre=$data[$i][1];
	$code_chiffre=$data[$i][2];
	$code_couleur=$data[$i][3];
	$code_note=$data[$i][4];
	$nb_periode=$data[$i][6];
	fwrite($xml,"\t\t".'<UN_CARNET>'."\n");
	fwrite($xml,"\t\t\t".'<NOM_CARNET>'.accent_import($nom_carnet)."</NOM_CARNET>\n");
	fwrite($xml,"\t\t\t".'<CODE_LETTRE>'.$code_lettre."</CODE_LETTRE>\n");
	fwrite($xml,"\t\t\t".'<CODE_CHIFFRE>'.$code_chiffre."</CODE_CHIFFRE>\n");
	fwrite($xml,"\t\t\t".'<CODE_COULEUR>'.$code_couleur."</CODE_COULEUR>\n");
	fwrite($xml,"\t\t\t".'<CODE_NOTE>'.$code_note."</CODE_NOTE>\n");
	fwrite($xml,"\t\t\t".'<NB_PERIODE>'.$nb_periode."</NB_PERIODE>\n");
	fwrite($xml,"\t\t".'</UN_CARNET>'."\n");
}
fwrite($xml,"\t".'</LES_CARNETS>'."\n");


$data=affCompetence($idcarnet); // id,libelle,ordre
fwrite($xml,"\t".'<LES_COMPETENCES>'."\n");
for($i=0;$i<count($data);$i++) {
	$nom_competence=$data[$i][1];
	$ordre=$data[$i][2];
	$idcompetence=$data[$i][0];
	fwrite($xml,"\t\t".'<UNE_COMPETENCE>'."\n");
	fwrite($xml,"\t\t\t".'<NOM_COMPETENCE>'.accent_import($nom_competence)."</NOM_COMPETENCE>\n");
	fwrite($xml,"\t\t\t".'<ORDRE>'.$ordre."</ORDRE>\n");
	fwrite($xml,"\t\t\t".'<DES_DESCRIPTIFS>'."\n");
	$data2=affDescriptif($idcompetence); // bold,libelle,ordre
	for($ii=0;$ii<count($data2);$ii++) {
		$libelle=$data2[$ii][1];
		$bold=$data2[$ii][0];
		$ordre=$data2[$ii][2];
		fwrite($xml,"\t\t\t".'<UN_DESCRIPTIF>'."\n");
		fwrite($xml,"\t\t\t\t".'<LIBELLE>'.accent_import($libelle).'</LIBELLE>'."\n");
		fwrite($xml,"\t\t\t\t".'<TITRE>'.$bold.'</TITRE>'."\n");
		fwrite($xml,"\t\t\t\t".'<ORDRE>'.$ordre.'</ORDRE>'."\n");
		fwrite($xml,"\t\t\t".'</UN_DESCRIPTIF>'."\n");
	}
	fwrite($xml,"\t\t\t".'</DES_DESCRIPTIFS>'."\n");
	fwrite($xml,"\t\t".'</UNE_COMPETENCE>'."\n");
}
fwrite($xml,"\t".'</LES_COMPETENCES>'."\n");
fwrite($xml,'</TRIADE2CDS>'."\n");
fclose($xml);

include_once('./librairie_php/pclzip.lib.php');
if (!is_dir("./data/dump/")) { 
	mkdir("./data/dump/"); 
	$fp = fopen("./data/dump/.htaccess", "w");
	$text="<Files \"*\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>\n";
	fwrite($fp,$text);
	fclose($fp);
}
$archive = new PclZip('./data/dump/triade2CdS.zip');
$archive->create('./data/triade2CdS');

nettoyage_repertoire("./data/triade2CdS");
@rmdir("./data/triade2CdS");

Pgclose();

?>

<font class="T2">
&nbsp;&nbsp; <?php print LANGCARNET59 ?> :  <input type=button onclick="open('telecharger.php?fichier=./data/dump/triade2CdS.zip','_blank','');" value="<?php print CLICKICI ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</font>
<br /><br />
<script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<br /><br />
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
