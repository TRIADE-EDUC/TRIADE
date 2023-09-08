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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importation SIECLE absence" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];
//$size=$_FILES["fichier1"]["size"];
$nbAjout=0;
@unlink("data/fichier_gep/traitementabs.xml");
$sur=0;
if ( (!empty($fichier)) && ($type == "text/xml" )) {
	$cnx=cnx();
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitementabs.xml");
	$fic_xml="data/fichier_gep/traitementabs.xml";
	$typefichier="xml";

	$stsweb = simplexml_load_file($fic_xml); 

	foreach($stsweb->PERIODE as $PERIODE) {
		$dateDebut=$PERIODE->DATE_DEBUT;
		$dateFin=$PERIODE->DATE_FIN;
		$tri=$PERIODE->LIBELLE;
	}

	foreach($stsweb->eleve as $ELEVE) {
		foreach($ELEVE->attributes() as $key => $value) {
                	if ($key == "elenoet") { 	$numEleve=$value; }
                	if ($key == "libelle") { 	$libelle=$value; }
                	if ($key == "nbAbs") { 		$nbAbs=$value; }
                	if ($key == "nbNonJustif") { 	$nbNonJustif=$value; }
                	if ($key == "nbRet") { 		$nbRet=$value; }
                	if ($key == "nomEleve") { 	$nomEleve=$value; }
                	if ($key == "prenomEleve") { 	$prenomEleve=$value; }
		}
		$cr=enrAbsSconet($numEleve,$nbAbs,$nbNonJustif,$nbRet,$tri,$nomEleve,$prenomEleve);
		if ($cr) {
			$nbAjout++;
		}else{
			$sur++;
			$listing.="<font class='T1'>- $nomEleve ".utf8_decode($prenomEleve)."</font><br>";
		}
	}
	print "<br><br><center><font class='T2'>$nbAjout enregistrement effectuée(s) sur $sur élève(s) </font></center>";
	print "<br><ul>Listing des élèves non enregistrés :<br><br><ul>";
	print $listing."</ul></ul>";
	print "<center><input type=button Value=".LANGSTAGE73." onclick='javascript:history.go(-1)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br><br>";
	PgClose($cnx);	
}else {
?>
<br />
<center> <font color=red><?php print LANGbasededon203?></font> <BR><BR>
<?php print "Le fichier doit être au format XML"?>
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
