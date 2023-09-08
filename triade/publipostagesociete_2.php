<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
}
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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Publipostage" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >

<?php

if (isset($_POST["consult1"])) {
	$type_societe=$_POST["type_societe"];
	$id_vignette=$_POST["id_vignette"];
	$siege=$_POST["adr"];
	$ville_societe=$_POST["ville_societe"];
}

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf3.php');
include_once('./librairie_pdf/etiquette.php');

/*--------------------------------------------------------------------------------
Pour crÃÂ©er l'objet on a 2 maniÃÂ¨res :
soit on donne les valeurs d'un format personnalisÃÂ© en les passant dans un tableau
soit on donne le nom d'un format AVERY
--------------------------------------------------------------------------------*/

// Exemple avec un format personnalisÃÂ©
if ($id_vignette == 1) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>3, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>70, 'height'=>42.3, 'font-size'=>10));

}elseif($id_vignette == 2) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>105, 'height'=>39, 'font-size'=>10));
}else{
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>5, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>2, 'width'=>105, 'height'=>39, 'font-size'=>10));
}

// Format standard
//$pdf = new PDF_Label('L7163');

$pdf->AddPage();

$idclasse=$_POST["idclasse"];

$data=recupAdrSociete($siege,$type_societe,$idclasse); // e.nom,e.adresse,e.ville_stage,e.code_p,e.pays_ent,se.tuteur_stage

// On imprime les étiquettes
for($i=0;$i<count($data);$i++) { // e.nom,e.adresse,e.ville_stage,e.code_p,e.pays_ent,se.tuteur_stage,id_entreprise
	if (trim($data[$i][0]) == "") { continue; }
	$nom=$data[$i][0];
	$adresse=$data[$i][1];
	$ville=$data[$i][2];
	if ((trim($ville_societe) != "") && (strtolower($ville_societe) != strtolower($ville))) continue;
	$ccp=$data[$i][3];
	$pays=$data[$i][4];
	$id_entreprise=$data[$i][6];
//	print $data[$i][5]."<br>";
	if ($data[$i][5] != "") { 
		$nomtuteur=recherche_contact_entreprise($id_entreprise);
		$tuteurStage="A l'attention de $nomtuteur"; 
	}
    	$text = sprintf("%s\n%s\n%s\n%s %s\n%s", "$nom", "$tuteurStage", "$adresse", "$ccp", "$ville", "$pays");
	$pdf->Add_Label($text);
}


if (!is_dir("./data/pdf_quantification/")) { mkdir("./data/pdf_quantification/"); }
$fichier="./data/pdf_quantification/publipostage_".$_SESSION['id_pers'].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();

?>

<br><ul><ul>
<?php if ($_SESSION["membre"] == "menuadmin") { ?>
	<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print "Récupération du document Publipostage" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } ?>
<?php if ($_SESSION["membre"] == "menupersonnel") { ?>
	<input type=button onclick="open('visu_pdf_personnel.php?id=<?php print $fichier?>','_blank','');" value="<?php print "Récupération du document Publipostage" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } ?>
<?php if ($_SESSION["membre"] == "menuscolaire") { ?>
	<input type=button onclick="open('visu_pdf_scolaire.php?id=<?php print $fichier?>','_blank','');" value="<?php print "Récupération du document Publipostage" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } ?>


</ul></ul>


</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
// deconnexion en fin de fichier
Pgclose();
?>

</BODY>
</HTML>
