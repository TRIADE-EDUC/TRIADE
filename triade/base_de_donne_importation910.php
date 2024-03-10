<?php
error_reporting(0);
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(300);
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onunload="attente_close()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE42 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];
//$size=$_FILES["fichier1"]["size"];
if ( (!empty($fichier)) && (($type == "application/octet-stream" ) || ($type == "application/vnd.ms-excel" ))) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement1.xls");
	@unlink("data/fichier_gep/$fichier");
	$fic_xls="data/fichier_gep/traitement1.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
//	$data->setOutputEncoding('CP1251');
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
	$cnx=cnx();
	$nbentrepriseaffecte=0;

	$ii=1;
	$optionligne=$_POST["optionligne"];
	if ($optionligne == "oui") {
		$ii=0;
	}

	for ($i = $ii; $i <= $data->sheets[0]['numRows']; $i++) {
				
/*
1) Nom entreprise 2) Registre du commerce 3) SIREN 4) SIRET 
5) Forme Juridique 6) Secteur Economique 7) INSEE 8) NAF/APE 
9) NACE 10) Type d'organisation 11) Nom du responsable 12) Fonction du responsable 
13) Adresse entreprise 14) Code postal entreprise 15) Ville entreprise 16) Pays entreprise 
17) Secteur activité 18) 2ieme secteur activité 19) 3ieme secteur activité 20) Activité principale 
21) Téléphone 22) Fax 23) Email 24) Site Web 
25) Informations 
*/ 
		$params["nom_entreprise"]=trim(addslashes($data->sheets[0]['cells'][$i][1]));
		$params["registre_commerce"]=trim(addslashes($data->sheets[0]['cells'][$i][2]));
		$params["siren"]=trim(addslashes($data->sheets[0]['cells'][$i][3]));
		$params["siret"]=trim(addslashes($data->sheets[0]['cells'][$i][4]));
		$params["forme_juridique"]=trim(addslashes($data->sheets[0]['cells'][$i][5]));
		$params["secteur_economique"]=trim(addslashes($data->sheets[0]['cells'][$i][6]));
		$params["insee"]=trim(addslashes($data->sheets[0]['cells'][$i][7]));
		$params["naf_ape"]=trim(addslashes($data->sheets[0]['cells'][$i][8]));
		$params["nace"]=trim(addslashes($data->sheets[0]['cells'][$i][9]));
		$params["organisation"]=trim(addslashes($data->sheets[0]['cells'][$i][10]));
		$params["nom_responsable"]=trim(addslashes($data->sheets[0]['cells'][$i][11]));
		$params["fonction_responsable"]=trim(addslashes($data->sheets[0]['cells'][$i][12]));
		$params["adresse"]=trim(addslashes($data->sheets[0]['cells'][$i][13]));
		$params["code_postal"]=trim(addslashes($data->sheets[0]['cells'][$i][14]));
		$params["ville_entreprise"]=trim(addslashes($data->sheets[0]['cells'][$i][15]));
		$params["pays_entreprise"]=trim(addslashes($data->sheets[0]['cells'][$i][16]));
		$params["secteur_activite"]=trim(addslashes($data->sheets[0]['cells'][$i][17]));
		$params["secteur_activite_2"]=trim(addslashes($data->sheets[0]['cells'][$i][18]));
		$params["secteur_activite_3"]=trim(addslashes($data->sheets[0]['cells'][$i][19]));
		$params["activite_principale"]=trim(addslashes($data->sheets[0]['cells'][$i][20]));
		$params["telephone"]=trim(addslashes($data->sheets[0]['cells'][$i][21]));
		$params["fax"]=trim(addslashes($data->sheets[0]['cells'][$i][22]));
		$params["email"]=trim(addslashes($data->sheets[0]['cells'][$i][23]));
		$params["web"]=trim(addslashes($data->sheets[0]['cells'][$i][24]));
		$params["information"]=trim(addslashes($data->sheets[0]['cells'][$i][25]));

		$cr=import_entreprise($params);
		if ($cr) {
			$nbentrepriseaffecte++;
		}
		unset($params);
	}
	Pgclose();	
	@unlink("data/fichier_gep/traitement1.xls");
?>
<br />
<ul>
<font class="T2">- <?php print "Nombre d'éléments mis à jour "  ?> : <?php print $nbentrepriseaffecte?><br></font>
<br /><br />
<table align='center'><tr><td><script>buttonMagicRetour('base_de_donne_importation20.php','_self')</script></td></tr></table>
<br /><br />
<?php
}else {
?>
<br />
<center> <font color=red><?php print LANGbasededon203?></font> <BR><BR>
<?php print LANGDISP26?>
<br /><br />
<?php print "Information Support : $type" ?>
<br /><br />
<input type=button Value="<?php print LANGBT24 ?>" onclick="javascript:history.go(-1)" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br />
<br />
</center>
<?php
}
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
