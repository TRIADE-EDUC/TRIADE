<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
$_SESSION["profP"]=$_SESSION["id_pers"];

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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
if (isset($_SESSION["profpclasse"])) {
	$saisie_classe=trim($_SESSION["profpclasse"]);
	if ($saisie_classe == "") {
		print "<script>location.href='profp.php';</script>";
	}
}else{
	$saisie_classe=$_POST["sClasseGrp"];
	$_SESSION["profpclasse"]=$saisie_classe;
}

if (isset($_GET["sClasseGrp"])) $saisie_classe=$_GET["sClasseGrp"];

$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROFP4 ?></font> <font id="color2" ><?php print $cl?></font> <font id='menumodule1' > / <?php print  LANGBULL3." <font id='color2' >$anneeScolaire</font>" ?> </font></td>
</tr>
<tr id='cadreCentral0'>
<td>
        <br><br>
        <div align=center>
        <table border=0  width=90% height=100>
	<tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP25 ?> : </font></td>
        <td id='bordure' ><script language=JavaScript>buttonMagic("cliquez-ici","trombinoscope-profp.php?idclasse=<?php print $saisie_classe?>","photo","width=800;height=600,scrollbars=yes","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

        <tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP26 ?> : </font></td>
        <td id='bordure'><script language=JavaScript>buttonMagic("cliquez-ici","profplisteeleve.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
        <tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP27 ?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","profpdelegue.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

        <tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP28 ?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","profpmessage.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

  	<tr><td id='bordure' align="right"><font class="T2"><?php print LANGMESS50 ?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","planclasse.php?idclasse=<?php print $saisie_classe?>","planclasse","width=900,height=700,status=yes,resizable=yes,scrollbars=yes","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

        <tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP29 ?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","profpcirculaire.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

        <tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP30?> : </font></td>
        <td id='bordure' >
        <script language=JavaScript>buttonMagic("cliquez-ici","gestion_stage_profp.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
        </td></tr>
        <tr><td colspan=2></td></tr>
        <tr><td colspan=2></td></tr>


<?php if (PROFPACCESNOTE == "oui") { ?>
        <tr><td id='bordure' align="right"><font class="T2"><?php print "Carnet de notes" ?> : </font></td>
        <td id='bordure' >
        <script language=JavaScript>buttonMagic("cliquez-ici","carnetnoteprofp.php?sClasseGrp=<?php print $saisie_classe?>&annee_scolaire=<?php print $anneeScolaire ?>","_parent","","");</script>
        </td></tr>
        <tr><td colspan=2></td></tr>
        <tr><td colspan=2></td></tr>
<?php }  ?>


	<tr><td id='bordure' align="right"><font class="T2"><?php print LANGPROFP33." ".LANGGRP54 ?>   : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","profpcombulletin.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
        <tr><td colspan=2></td></tr>
        <tr><td colspan=2></td></tr>


<?php if (PROFPACCESVISADIRECTION == "oui") { ?>

        <tr><td id='bordure' align="right"><font class="T2"><?php print "Remplir le commentaire du visa de direction" ?> : </font></td>
        <td id='bordure' >
        <script language=JavaScript>buttonMagic("cliquez-ici","visa_direction.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
        </td></tr>
        <tr><td colspan=2></td></tr>
        <tr><td colspan=2></td></tr>
<?php }  ?>


<?php 
	if (PROFPBULLETINVERIF == "oui") {
?>
	<tr><td id='bordure' align='right'><font class="T2"><?php print LANGPROFP34?>  : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","editer_bulletin.php?sClasseGrp=<?php print $saisie_classe?>","editer_bulletin","width=850,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php } ?>


<?php 
	if (PROFPMODIFAFFECT == "oui") {
?>
	<tr><td id='bordure' align='right'><font class="T2"><?php print "Modification des affectations"?>  : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","affectation_modif_key.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php } ?>


<?php 
	if (PROFPBULLETIN == "oui") {
?>	
	<tr><td id='bordure' align="right"><font class="T2"><?php print "Imprimer le bulletin de classe"?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","imprimer_trimestre.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>

	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td id='bordure' align="right"><font class="T2"><?php print "Tableau des moyennes"?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","profpprojo.php?idClasse=<?php print $saisie_classe?>","_blank","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

<?php } ?>

<?php 
	if (PROFPVIDEOPROJO == "oui") {
?>	
	<tr><td id='bordure' align="right"><font class="T2"><?php print "Vidéo Projecteur"?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","video-proj-index.php?sClasseGrp=<?php print $saisie_classe?>","video","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php } ?>


<?php 
	if (PROFPRELEVE == "oui") {
?>	
	<tr><td id='bordure' align="right"><font class="T2"><?php print "Imprimer le relevé de classe"?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","imprimer_periode.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

<?php } ?>

<?php 
	if (GROUPEGESTIONPROF == "oui") {
?>	
	<tr><td id='bordure' align="right"><font class="T2"><?php print "Gestion de vos groupes"?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","profpgestiongroupe.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php } ?>

<?php 
	if (PROFPACCESABSRTD == "oui") {
?>	
	<tr><td id='bordure' align="right"><font class="T2"><?php print "Gestion des absences et retards" ?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","gestion_abs_retard.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php } ?>


	<tr><td id='bordure' align='right'><font class="T2"><?php print LANGPROFP38 ?> : </font></td>
        <td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","carnet_editer.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script>
	</td></tr>

	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td id='bordure' align='right'><font class="T2"><?php print "Insertion signature " ?> : </font></td>
	<td id='bordure' >
	<script language=JavaScript>buttonMagic("cliquez-ici","signatureprofp.php?sClasseGrp=<?php print $saisie_classe?>","logo","width=400,height=200","");</script>
	</td></tr>

	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
        </table>
        </div>
        <br><br>
</td>
</tr>
</table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
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
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
