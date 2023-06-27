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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
        $profpclasse=$_SESSION["profpclasse"];
        validerequete("menuprof");
}else{
        validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Impression des retards et absences de l'élève $nomeleve $prenomeleve"  ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >

<!-- // fin  -->
<?php

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
include_once("librairie_php/timezone.php");
$fichierpdf="./data/pdf_certif/Classe_ABSRTD".$_POST["idEleve"].".pdf";


$nomeleve=strtoupper(recherche_eleve_nom($_POST["idEleve"]));
$prenomeleve=ucwords(recherche_eleve_prenom($_POST["idEleve"]));
$idclasse=chercheIdClasseDunEleve($_POST["idEleve"]);
$nomclasse=chercheClasse_nom($idclasse);

$pdf=new PDF();  // declaration du constructeur


// gestion des retards 
// -------------------

$pdf->AddPage();

$coorX=5;
$coorY=13;

$pdf->SetFont('Arial','',11);
$pdf->SetXY($coorX,$coorY);
$pdf->WriteHTML("Elève : <b>".$nomeleve." ".$prenomeleve."</b>");
$pdf->SetXY($coorX,$coorY+10);
$pdf->WriteHTML("Classe : ".$nomclasse);
$pdf->SetXY($coorX,$coorY+20);
$pdf->WriteHTML("<u>Liste des retards</u>");

$data=affRetard($_POST["idEleve"]);

$hauteur=8;

$pdf->SetXY($coorX,$coorY+35);
$pdf->MultiCell(50,$hauteur,'',1,'',0);
$pdf->SetXY($coorX+1,$coorY+35+2);
$pdf->WriteHTML("<b>Retard le - Creneau</b>");

$pdf->SetXY($coorX+50,$coorY+35);
$pdf->MultiCell(50,$hauteur,'',1,'',0);
$pdf->SetXY($coorX+51,$coorY+35+2);
$pdf->WriteHTML("<b>A     Durée</b>");

$pdf->SetXY($coorX+75+25,$coorY+35);
$pdf->MultiCell(90,$hauteur,'',1,'',0);
$pdf->SetXY($coorX+76+25,$coorY+35+2);
$pdf->WriteHTML("<b>Motif</b>");



$coorY=$coorY+35;
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere,justifier,heure_saisie,creneaux
$ii=0;
for($j=0;$j<count($data);$j++){
	$nomMatiere=chercheMatiereNom($data[$j][7]);
	$ii++;
	if ($ii == 25) {
		$pdf->AddPage();
		$coorY=20;
		$ii=0;
	}else{
		$coorY+=$hauteur;
	}
	list($creneaux,$debcre,$fincre)=preg_split('/#/',$data[$j][10]);
	$justifier=$data[$j][8];
	$justifier=($justifier == 1) ? "(retard justifié)" : "(retard non justifié)"; 

	$pdf->SetFont('Arial','',9);
	$date=dateForm($data[$j][2]);
	$duree=$data[$j][5];
	if ($duree == 0) { $duree="???"; }

	$pdf->SetXY($coorX,$coorY);
	$pdf->MultiCell(50,$hauteur,'',1,'',0);
	$pdf->SetXY($coorX+1,$coorY+0.5);
	$pdf->WriteHTML("$date - $creneaux ");
	$pdf->SetXY($coorX+20,$coorY+3.5);
	$pdf->SetFont('Arial','',6);
	$pdf->WriteHTML("($debcre - $fincre)");
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($coorX+25+25,$coorY);
	$pdf->MultiCell(50,$hauteur,'',1,'',0);
	$pdf->SetXY($coorX+26+25,$coorY+2);
	$pdf->WriteHTML($data[$j][1]." durant ".$duree);

	$pdf->SetXY($coorX+75+25,$coorY);
	$pdf->MultiCell(90,$hauteur,'',1,'',0);
	$pdf->SetXY($coorX+75+25,$coorY+0.5);
	$pdf->SetFont('Arial','',6);
	if ($data[$j][6] == "0") { $texte=LANGINCONNU; }else{  $texte=trunchaine($data[$j][6],130); }
	$pdf->MultiCell(90,3,"$justifier / $nomMatiere / $texte ",0,'',0);
	$pdf->SetFont('Arial','',11);
}




// gestion des absences 
// ---------------------

$pdf->AddPage();

$coorX=5;
$coorY=13;

$pdf->SetFont('Arial','',11);
$pdf->SetXY($coorX,$coorY);
$pdf->WriteHTML("Elève : <b>".$nomeleve." ".$prenomeleve."</b>");
$pdf->SetXY($coorX,$coorY+10);
$pdf->WriteHTML("Classe : ".$nomclasse);
$pdf->SetXY($coorX,$coorY+20);
$pdf->WriteHTML("<u>Liste des absences</u>");

$hauteur=8;

$pdf->SetXY($coorX,$coorY+35);
$pdf->MultiCell(50,$hauteur,'',1,'',0);
$pdf->SetXY($coorX+1,$coorY+35+2);
$pdf->WriteHTML("<b>Absent - Creneau</b>");

$pdf->SetXY($coorX+50,$coorY+35);
$pdf->MultiCell(50,$hauteur,'',1,'',0);
$pdf->SetXY($coorX+26+25,$coorY+35+2);
$pdf->WriteHTML("<b>Durée</b>");

$pdf->SetXY($coorX+75+25,$coorY+35);
$pdf->MultiCell(90,$hauteur,'',1,'',0);
$pdf->SetXY($coorX+76+25,$coorY+35+2);
$pdf->WriteHTML("<b>Motif</b>");

$coorY=$coorY+35;

$data=affAbsence($_POST["idEleve"]);
//  elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier, heure_saisie, heuredabsence, creneaux
$ii=0;
for($j=0;$j<count($data);$j++) {
	$nomMatiere=chercheMatiereNom($data[$j][8]);
	$ii++;
	if ($ii == 25) {
		$pdf->AddPage();
		$coorY=20;
		$ii=0;
	}else{
		$coorY+=$hauteur;
	}
	list($creneaux,$debcre,$fincre)=preg_split('/#/',$data[$j][13]);

	$date=dateForm($data[$j][1]);
	$duree=$data[$j][4];
	if ($duree == 0) { $duree="???"; }

	if ($duree == -1) { 
		$unite=" Heure(s) "; 
		$duree=$data[$j][7];
	}else{ 
		$unite=" Jour(s)"; 
		$duree=$data[$j][4];
	}
	
	$justifier=$data[$j][10];
	$justifier=($justifier == 1) ? "(absence justifiée)" : "(absence non justifiée)"; 
	
	$pdf->SetFont('Arial','',9);

	$pdf->SetXY($coorX,$coorY);
	$pdf->MultiCell(50,$hauteur,'',1,'',0);
	$pdf->SetXY($coorX+1,$coorY);

	if ($creneaux == "null") { $creneaux="non défini";}
	if ($debcre == ":") { $debcre="";}
	if ($fincre == ":") { $fincre="";}
	$creneaux=trunchaine(strtolower($creneaux),15);
	$pdf->WriteHTML("$date - $creneaux"); 
	$pdf->SetXY($coorX+2,$coorY+3.5);
	$pdf->SetFont('Arial','',6);
	$pdf->WriteHTML("($debcre - $fincre)");

	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($coorX+25+25,$coorY);
	$pdf->MultiCell(50,$hauteur,'',1,'',0);
	$pdf->SetXY($coorX+26+25,$coorY+2);
	$pdf->WriteHTML("durant  ".$duree." ".$unite);

	$pdf->SetXY($coorX+75+25,$coorY);
	$pdf->MultiCell(90,$hauteur,'',1,'',0);
	$pdf->SetXY($coorX+75+25,$coorY+0.5);
	$pdf->SetFont('Arial','',6);
	if ($data[$j][6] == "0") { $texte=LANGINCONNU; }else{  $texte=trunchaine($data[$j][6],130); }
	$pdf->MultiCell(90,3,"$justifier / $nomMatiere / $texte",0,'',0);
	$pdf->SetFont('Arial','',11);

}

@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichierpdf);
print "<form method='post' action='gestion_abs_retard_modif_donne.php' >";
print "<input type='hidden' name='saisie_nom_eleve' value='".$_POST["saisie_nom_eleve"]."' >";
print "<br />&nbsp;&nbsp;<font class=T2>Le document PDF est disponible<font><br /><br />";
if ($_SESSION["membre"] == "menuadmin") {
        print "<script language=JavaScript>buttonMagic('Imprimer','visu_pdf_admin.php?id=$fichierpdf','_blank','','');</script>";
}elseif($_SESSION["membre"] == "menuscolaire") {
        print "<script language=JavaScript>buttonMagic('Imprimer','visu_pdf_scolaire.php?id=$fichierpdf','_blank','','');</script>";
}else{
        print "<script language=JavaScript>buttonMagic('Imprimer','visu_pdf_prof.php?id=$fichierpdf','_blank','','');</script>";
}
print "<script language=JavaScript>buttonMagicSubmitAtt('Retour','create','');</script><br /><br />";
print "</form>";
?>
      <!-- // fin  -->
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
