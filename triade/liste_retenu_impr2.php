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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCOUR1?></font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br>
<!-- // fin  -->
<?php


$data=config_param_visu('param_retenu');
$texte=$data[0][0];
nettoyage_repertoire("./data/pdf_certif/courrierretenu/".$_SESSION["id_pers"]);


if ($texte ==  "{FICHIERRTFRETENU}") {
	
	$nb=$_POST["nb"];
	$textecomplet="";
	$ok=0;

	for($i=0;$i<$nb;$i++) {
		//$idEleve=$_POST["liste"][$i];
		//$ideleve#$dateretenue#$heureretenue#$duree#$idcategory#$motif#$fait
		//
		//1505    #2008-03-19  #08:20:00     #04:00:00#18       #Violence physique#M. DELAUBERT xavier##
		list($idEleve,$dateretenue,$heureretenue,$duree,$idcategory,$motif,$attribuerpar,$devoirafaire,$faits) = preg_split('/#/',$_POST["liste"][$i]);
		if ($idEleve == "")  { continue; }

		$nomEleve=recherche_eleve_nom($idEleve);
		$prenomEleve=recherche_eleve_prenom($idEleve);
		$idClasse=chercheIdClasseDunEleve($idEleve);
		$classe_nom=chercheClasse_nom($idClasse);
		$adr1=trim(rechercheAdresseEleve($idEleve));
		$code_post_adr1=rechercheCodePostalEleve($idEleve);
		$commune_adr1=rechercheVilleEleve($idEleve);

		$faits=preg_replace('/\/\|\|A\|\|\//','"',$faits);
		$devoirafaire=preg_replace('/\/\|\|A\|\|\//','"',$devoirafaire);
		$motif=preg_replace('/\/\|\|A\|\|\//','"',$motif);
	
		valideEnvoiCourrierDiscipline($idEleve,$dateretenue,$heureretenue,$duree,$idcategory,$motif,$attribuerpar,$devoirafaire,$faits);

		include_once("librairie_php/timezone.php");
		$date=dateDMY();

		
		$dateretenue=dateForm($dateretenue);
		$heureretenue=timeForm($heureretenue);

		$TempFilename="./data/parametrage/courrier_retenu.rtf";
		$fichier=fopen($TempFilename,"r");
		$longueur=9000000;
		$texte=fread($fichier,$longueur);
		fclose($fichier);

		$duree=timeForm($duree);
		$category=chercheCategory($idcategory);
		$category=$category[0][1];

		$prenomtuteur1=recherche_eleve_prenom_parent($idEleve,'1');
		$nomtuteur1=recherche_eleve_nom_parent($idEleve,'1');
		$civtuteur1=recherche_eleve_civ_parent($idEleve,'1');


		$nomEleve=preg_replace("/'/"," \\rquote ",$nomEleve);
		$nomEleve=preg_replace('/"/'," \\ldblquote ",$nomEleve);
		$prenomEleve=preg_replace("/'/"," \\rquote ",$prenomEleve);
		$prenomEleve=preg_replace('/"/'," \\ldblquote ",$prenomEleve);
		$commune_adr1=preg_replace("/'/"," \\rquote ",$commune_adr1);
		$commune_adr1=preg_replace('/"/'," \\ldblquote ",$commune_adr1);
		$category=preg_replace("/'/"," \\rquote ",$category);
		$category=preg_replace('/"/'," \\ldblquote ",$category);
		$motif=preg_replace("/'/"," \\rquote ",$motif);
		$motif=preg_replace('/"/'," \\ldblquote ",$motif);
		$attribuerpar=preg_replace("/'/"," \\rquote ",$attribuerpar);
		$attribuerpar=preg_replace('/"/'," \\ldblquote ",$attribuerpar);
		$devoirafaire=preg_replace("/'/"," \\rquote ",$devoirafaire);
		$devoirafaire=preg_replace('/"/'," \\ldblquote ",$devoirafaire);
		$faits=preg_replace("/'/"," \\rquote ",$faits);
		$faits=preg_replace('/"/'," \\ldblquote ",$faits);
		$texte=preg_replace("/NomEleve/","$nomEleve",$texte);
		$texte=preg_replace("/PrenomEleve/","$prenomEleve",$texte);
		$texte=preg_replace("/ClasseEleve/","$classe_nom",$texte);
		$texte=preg_replace("/AdresseEleve/","$adr1",$texte);
		$texte=preg_replace("/CodePostalEleve/","$code_post_adr1",$texte);
		$texte=preg_replace("/VilleEleve/","$commune_adr1",$texte);
		$texte=preg_replace("/RETENUCATEGORY/",$category,$texte); 
		$texte=preg_replace("/DATERETENU/",$dateretenue,$texte); 
		$texte=preg_replace("/HEURERETENU/",$heureretenue,$texte);
		$texte=preg_replace("/RETENUDUREE/",$duree,$texte);
		$texte=preg_replace("/RETENUMOTIF/",$motif,$texte);
		$texte=preg_replace("/ATTRIBUEPAR/",$attribuerpar,$texte);
		$texte=preg_replace("/DEVOIRAFAIRE/",$devoirafaire,$texte);
		$texte=preg_replace("/FAITS/",$faits,$texte);
		$texte=preg_replace("/DATEDUJOUR/",$date,$texte);
		$texte=preg_replace("/PRENOMRESP1/",$prenomtuteur1,$texte);
		$texte=preg_replace("/NOMRESP1/",$nomtuteur1,$texte);
		$texte=preg_replace("/CIVILITETUTEUR1/",$civtuteur1,$texte);



		if (!is_dir("./data/pdf_certif/courrierretenu")) { mkdir("./data/pdf_certif/courrierretenu"); }
		mkdir("./data/pdf_certif/courrierretenu/".$_SESSION["id_pers"]);
		$nomfic=$nomEleve." ".$prenomEleve."($i).rtf";
		$fic="./data/pdf_certif/courrierretenu/".$_SESSION["id_pers"]."/$nomfic";
		$fichier=fopen("$fic","a+");
		fwrite($fichier,$texte);
		fclose($fichier);
		$ok=1;
		enrHistoEleve($idEleve,$date,"Envoi courrier retenu non justifiée","");

	}
	if ($ok) {
		include_once('./librairie_php/pclzip.lib.php');
		$archive = new PclZip('./data/sauvegarde/courrierretenu'.$_SESSION["id_pers"].'.zip');
		$archive->create('./data/pdf_certif/courrierretenu/'.$_SESSION["id_pers"],PCLZIP_OPT_REMOVE_ALL_PATH);
		$bouton="<input type=button onclick=\"open('telecharger.php?fichier=./data/sauvegarde/courrierretenu".$_SESSION["id_pers"].".zip','_blank','');\" value=\""."Récuperation des courriers"."\"  STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\">";
	}

}else{

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
// recuperation des coordonnées
// de l etablissement


$nb=$_POST["nb"];

for($i=0;$i<$nb;$i++) {
	//$idEleve=$_POST["liste"][$i];
	//    1505    #2008-03-19  #08:20:00    #04:00:00 #18     #Violence physique  #M. DELAUBERT xavier #  #
	list($idEleve,$dateretenue,$heureretenue,$duree,$idcategory,$motif,$attribuerpar,$devoirafaire,$faits) = preg_split('/#/',$_POST["liste"][$i]);
	if ($idEleve == "")  { continue; }
	//print "-".$_POST["liste"][$i]."<br>";


	$faits=preg_replace('/\/\|\|A\|\|\//','"',$faits);
	$devoirafaire=preg_replace('/\/\|\|A\|\|\//','"',$devoirafaire);
	$motif=preg_replace('/\/\|\|A\|\|\//','"',$motif);

	$pdf->AddPage();


// adresse de l'élève
// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1,	adr2,	code_post_adr2, commune_adr2,
$dataadresse=chercheadresse($idEleve);
$nomtuteur=$dataadresse[0][1];
$prenomtuteur=$dataadresse[0][2];
$adr1=$dataadresse[0][3];
$code_post_adr1=$dataadresse[0][4];
$commune_adr1=ucfirst($dataadresse[0][5]);
$duree=timeForm($duree);

// declaration variable
$nomEleve=recherche_eleve_nom($idEleve);
$prenomEleve=recherche_eleve_prenom($idEleve);
$idClasse=chercheIdClasseDunEleve($idEleve);
$classe_nom=chercheClasse_nom($idClasse);
include_once("librairie_php/timezone.php");
$date=dateDMY();

$prenomtuteur1=recherche_eleve_prenom_parent($idEleve,'1');
$nomtuteur1=recherche_eleve_nom_parent($idEleve,'1');
$civtuteur1=recherche_eleve_civ_parent($idEleve,'1');


$dateretenue=dateForm($dateretenue);
$heureretenue=timeForm($heureretenue);
$category=chercheCategory($idcategory);
$category=$category[0][1];

$data=config_param_visu('param_retenu');
$texte=$data[0][0];
$texte=preg_replace("/NomEleve/","$nomEleve",$texte);
$texte=preg_replace("/PrenomEleve/","$prenomEleve",$texte);
$texte=preg_replace("/ClasseEleve/","$classe_nom",$texte);
$texte=preg_replace("/AdresseEleve/","$adr1",$texte);
$texte=preg_replace("/CodePostalEleve/","$code_post_adr1",$texte);
$texte=preg_replace("/VilleEleve/","$commune_adr1",$texte);
$texte=preg_replace("/DATERETENU/","$dateretenue",$texte); 
$texte=preg_replace("/HEURERETENU/","$heureretenue",$texte);
$texte=preg_replace("/RETENUDUREE/","$duree",$texte);
$texte=preg_replace("/RETENUMOTIF/","$motif",$texte);
$texte=preg_replace("/RETENUCATEGORY/","$category",$texte); 
$texte=preg_replace("/ATTRIBUEPAR/",$attribuerpar,$texte);
$texte=preg_replace("/DEVOIRAFAIRE/",$devoirafaire,$texte);
$texte=preg_replace("/FAITS/",$faits,$texte);
$texte=preg_replace("/DATEDUJOUR/",$date,$texte);
$texte=preg_replace("/PRENOMRESP1/",$prenomtuteur1,$texte);
$texte=preg_replace("/NOMRESP1/",$nomtuteur1,$texte);
$texte=preg_replace("/CIVILITETUTEUR1/",$civtuteur1,$texte);


// cadre principale
$pdf->SetFont('Arial','',11);
$pdf->SetXY(0,0);
$pdf->WriteHTML($texte);
// fin cadre principale

enrHistoEleve($idEleve,$date,"Envoi courrier retenu non justifiée","");
valideEnvoiCourrierDiscipline($idEleve,$dateretenue,$heureretenue,$duree,$idcategory,$motif,$attribuerpar,$devoirafaire,$faits);

}


$fichierpdf="./data/pdf_abs/absence_".$_SESSION["id_pers"].".pdf";
if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
$pdf->output('F',$fichierpdf);

$bouton="<input type=button onclick=\"open('visu_pdf_admin.php?id=$fichierpdf','_blank','');\" value=\"".LANGPER5."\"  STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\">";

}

?>

<br />
<center><font class=T2><?php print LANGCOUR ?></font>
<br><br><br>
<?php print $bouton ?>
<!-- // fin  -->
</center>
<br>
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
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
