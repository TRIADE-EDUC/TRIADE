<?php
session_start();
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print "Liste des étudiants en entreprise"  ?> <?php if ($_GET["tous"] != 1) print "actuellement" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<table width=100% border=1 bordercolor="#000000" style='border-collapse: collapse;' >
<tr>
<td bgcolor="yellow" >&nbsp;Nom Prénom</td>
<td bgcolor="yellow" >&nbsp;Classe</td>
<td bgcolor="yellow" >&nbsp;Entreprise</td>
</tr>
<!-- // debut  -->
<?php
$fichier="gestion_stage_visu_eleve_liste.php?tous=".$_GET['tous'];
$table="stage_eleve";
$champs="";
$iddest="$destinataire";
$nbaff=20;
if (isset($_GET["nba"])) {
	$depart=$_GET["limit"];
}else {
	$depart=0;
}



$data=liste_eleve_entreprise();
for($i=0;$i<count($data);$i++) {
	if (trim(recherche_eleve($data[$i][1])) == "") {
		deleteListeEleveEntr($data[$i][0]);
	}
}


// creation PDF
if (!is_dir("./data/pdf_bull/listingstage")) { mkdir("./data/pdf_bull/listingstage"); }
$fichierPDF="./data/pdf_bull/listingstage/listingEntrepriseStage.pdf";

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Listing Etudiant en entreprise");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Compte Rendu de Stage"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
$X=0;
$Y=0;

$date=anneeScolaire();


$pdf->SetFont('Arial','B',14);
$pdf->SetXY($X,$Y+=10);
$pdf->MultiCell(210,10,"Année $date",0,'C',0);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY($X,$Y+=10);
$pdf->MultiCell(210,10,"LISTE DES ETUDIANTS EN ENTREPRISE",0,'C',0);
$X=5;
$pdf->SetFillColor(240);
$pdf->SetFont('Arial','',12);
$pdf->SetXY($X,$Y+=20);
$pdf->MultiCell(30,10,"Etudiant",1,'C',1);
$pdf->SetXY($X+=30,$Y);
$pdf->MultiCell(30,10,"Classe",1,'C',1);
$pdf->SetXY($X+=30,$Y);
$pdf->MultiCell(70,10,"Entreprise",1,'C',1);
$pdf->SetXY($X+=70,$Y);
$pdf->MultiCell(70,10,"Lieu",1,'C',1);

$pdf->SetFillColor(255);
$pdf->SetFont('Arial','',9);
$data=liste_eleve_entreprise_limit($depart,$nbaff);
// id  id_eleve  id_entreprise  id_prof_visite  lieu_stage  visite_effectuer  ville_stage  code_p  tuteur_stage  jour_repos  info_plus  loger  nourri  passage_x_service  raison  date_visite_prof  num_stage  ,alternance ,jour_alternance ,dateDebutAlternance ,dateFinAlternance
for($i=0;$i<count($data);$i++) {
	$idclasse=chercheIdClasseDunEleve($data[$i][1]);
	$info=recherchedatestage3($data[$i][16],$idclasse);
	// idclasse,datedebut,datefin,numstage,id,nom_stage
	$datedebut=preg_replace('/-/','',$info[0][1]);
	$datefin=preg_replace('/-/','',$info[0][2]);
	$dateDebutAlternance=preg_replace('/-/','',$data[$i][19]);
	$dateFinAlternance=preg_replace('/-/','',$data[$i][20]);
	$datedujour=dateDMY2();
	$datedujour=preg_replace('/-/','',$datedujour);
	if ($datefin == "") $datefin="0";
	if ($datedebut == "") $datedebut="0";
	if ($_GET['tous'] != 1) { 
		    // print "( (($datefin >= $datedujour) && (".$data[$i][17]." == 0) &&  ($datedebut <= $datedujour)) || <br><br>  (($dateFinAlternance >= $datedujour) && (".$data[$i][17]." == 1) &&  ($dateDebutAlternance <= $datedujour)) )<hr>";
		if ( (($datefin < $datedujour) && ($data[$i][17] == 0)) || 
		     (($data[$i][17] == 0) &&  ($datedebut > $datedujour)) || 
		     (($dateFinAlternance <= $datedujour) && ($data[$i][17] == 1) &&  ($dateDebutAlternance >= $datedujour)) )  { 
		     continue; 
	        }
	}

	if ($Y >= 270) { $pdf->AddPage(); $Y=20; }

	$nomprenom=recherche_eleve($data[$i][1]);
	$nomclass=chercheClasse_nom(chercheIdClasseDunEleve($data[$i][1]));
	$nomentreprise=recherche_entr_nom_via_id($data[$i][2]);
	$lieu=$data[$i][4]." ".$data[$i][7]." ".$data[$i][6];
		
	$X=5;
	$pdf->SetXY($X,$Y+=10);
	$pdf->MultiCell(30,10,"",1,'L',0);
	$pdf->SetXY($X+1,$Y+1);
	$pdf->MultiCell(30,3,"$nomprenom",0,'L',0);

	$pdf->SetXY($X+=30,$Y);
	$pdf->MultiCell(30,10,"",1,'L',0);
	$pdf->SetXY($X+1,$Y+1);
	$pdf->MultiCell(30,3,"$nomclass",0,'L',0);


	$pdf->SetXY($X+=30,$Y);
	$pdf->MultiCell(70,10,"",1,'L',0);
	$pdf->SetXY($X+1,$Y+1);
	$pdf->MultiCell(70,3,"$nomentreprise",0,'L',0);

	$pdf->SetXY($X+=70,$Y);
	$pdf->MultiCell(70,10,"",1,'L',0);
	$pdf->SetXY($X+1,$Y+1);
	$pdf->MultiCell(70,3,"$lieu",0,'L',0);

?>
	<tr>
	<td bgcolor="#FFFFFFF" >&nbsp;<?php print $nomprenom ?></td>
	<td bgcolor="#FFFFFFF" >&nbsp;<?php print $nomclass ?></td>
	<td bgcolor="#FFFFFFF" >&nbsp;<?php print $nomentreprise ?></td>
	</tr>

<?php	
}

@unlink($fichierPDF); // destruction avant creation
$pdf->output('F',$fichierPDF);

?>
</table>
<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent2($fichier,$table,$depart,$nbaff); ?><br><br></td>
<td width=33%>
<?php 
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>&nbsp;&nbsp;";	
}
?>
</td><td><input type="button" value="Imprimer la liste complète" class="bouton2" onclick="open('visu_document.php?fichier=<?php print $fichierPDF ?>','_blank','');" /></td>
<td align=right width=33%><br><?php suivant2($fichier,$table,$depart,$nbaff); ?>&nbsp;<br><br></td>
</tr></table>



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
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
