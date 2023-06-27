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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Emargement Vierge" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once('librairie_php/db_triade.php');
validerequete("3");
$cnx=cnx();

if (isset($_POST["saisie_groupe"])) {
	$idgroupe=$_POST["saisie_groupe"];
	$nomClasse=chercheGroupeNom($idgroupe);
	$CLASSE="GROUPE";
}else{
	$idClasse=$_POST["saisie_classe"];
	$nomClasse=chercheClasse_nom($idClasse);
	$CLASSE="CLASSE";
}

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

config_param_ajout($_POST["hauteur"],"hauteuremarg");

$pdf=new PDF();  // declaration du constructeur

$pdf->AddPage();
$pdf->SetTitle("Emargement -");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Emargement "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$hauteur=$_POST["hauteur"];

$X=0;
$Y=5;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(210,6,stripslashes("FEUILLE DE PRESENCE EN $CLASSE DE \n $nomClasse"),0,'C',0);
$pdf->SetFont('Arial','',9);
$pdf->SetXY($X+=5,$Y+=15);
$pdf->SetFillColor(230,230,255);
if (EMARGEMENT == "STANDARD" ) {
	$pdf->RoundedRect($X, $Y, 120, 25, 3.5, 'DF');
}else{
	$pdf->RoundedRect($X, $Y, 120, 28, 3.5, 'DF');
}
$pdf->SetFillColor(255);
$pdf->SetXY($X+2,$Y+=2);
$pdf->MultiCell(130,3,"Objet  : .................................................................................................................... ",0,'L',0);
$pdf->SetXY($X+2,$Y+=5);
$pdf->MultiCell(130,3,"Date : ................ Horaire début : ..............  Horaire fin : ..............   Durée : ........... ",0,'L',0);
$pdf->SetXY($X+2,$Y+=5);
$pdf->MultiCell(130,3,"Enseignant : ............................................................................................................ ",0,'L',0);
if (EMARGEMENT == "STANDARD" ) {
	$pdf->SetXY($X+2,$Y+=5);
	$pdf->MultiCell(130,3,"Intitulé du cours : ..................................................................................................... ",0,'L',0);
}else{
	$pdf->SetXY($X+2,$Y+=5);
	$pdf->MultiCell(130,3,"P.E : ........................................................................................................................ ",0,'L',0);
	$pdf->SetXY($X+2,$Y+=5);
	$pdf->MultiCell(130,3,"Matière : .................................................................................................................. ",0,'L',0);
}

$pdf->SetXY($X+=95,$Y+=10);
$pdf->SetFont('Arial','',5);
$pdf->MultiCell(10,$hauteur,"Présent",1,'C',1);
$pdf->SetXY($X+=10,$Y);
$pdf->MultiCell(10,$hauteur,"Absent",1,'C',1);
$pdf->SetXY($X+=10,$Y);
$pdf->MultiCell(40,$hauteur,"Emargement Etudiant",1,'C',1);
$pdf->SetXY($X+=40,$Y);
$pdf->MultiCell(45,$hauteur,"Observation",1,'C',1);

$Y+=$hauteur;


include_once('librairie_php/recupnoteperiode.php');
if ($idgroupe == 0) {
	$eleveT=recupEleve($idClasse); // recup liste eleve
}else{
	$tabEleveT=listeEleveDansGroupe($idgroupe);
	$i=0;
	foreach ($tabEleveT as $key=>$value) {
		$sql="SELECT nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve,tel_fixe_eleve FROM ${prefixe}eleves WHERE elev_id='$value' ";
		$curs=execSql($sql);
		$liste=chargeMat($curs);
		$eleveT[$i][0]=$liste[0][0];
		$eleveT[$i][1]=$liste[0][1];
		$eleveT[$i][2]=$liste[0][2];
		$eleveT[$i][3]=$liste[0][3];
		$eleveT[$i][4]=$liste[0][4];
		$eleveT[$i][5]=$liste[0][5];
		$eleveT[$i][6]=$liste[0][6];
		$eleveT[$i][7]=$liste[0][7];
		$eleveT[$i][8]=$liste[0][8];
		$eleveT[$i][9]=$liste[0][9];
		$eleveT[$i][10]=$liste[0][10];
		$eleveT[$i][11]=$liste[0][11];
		$eleveT[$i][12]=$liste[0][12];
		$i++;
	}
}


for($j=0;$j<count($eleveT);$j++) {  // variable eleve
	$nomEleve=strtoupper($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$nomprenom=trunchaine("$nomEleve $prenomEleve",25);
	$idEleve=$eleveT[$j][4];
	$numEleve=$eleveT[$j][11];
	$X=0;
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(100,$hauteur,"$nomprenom",1,'L',0);
	$pdf->SetXY($X+=100,$Y);
//	$pdf->MultiCell(40,$hauteur,"N° $numEleve",1,'L',1);
	//	$pdf->SetXY($X+=40,$Y);

	$codebarre=recupIdCodeBar($idEleve,"menueleve");
	$url="http://".$_SERVER['SERVER_NAME']."/".ECOLE."/codebar/image-impr.php?code=code39&text=$codebarre";
	$pdf->Image("$url",$X-18,$Y+1,15,$hauteur-2,'PNG');

	$pdf->MultiCell(10,$hauteur,"",1,'C',0);
	$pdf->SetXY($X+=10,$Y);
	$pdf->MultiCell(10,$hauteur,"",1,'C',0);
	$pdf->SetXY($X+=10,$Y);
	$pdf->MultiCell(40,$hauteur,"",1,'C',0);
	$pdf->SetXY($X+=40,$Y);
	$pdf->MultiCell(45,$hauteur,"",1,'C',0);
	$Y+=$hauteur;
	if ($Y >= 260) {
		$pdf->AddPage(); 
		$Y=10;
	}
}
$pdf->SetFont('Arial','B',8);
$pdf->SetXY($X=150,$Y+=3);
$pdf->MultiCell(55,15,"",1,'L',0);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell(45,3,"Signature de l'enseignant : ",0,'L',0);

$pdf->SetFont('Arial','',8);
$pdf->SetXY($X=10,$Y);
$pdf->MultiCell(130,15,"",1,'L',0);
$pdf->SetXY($X,$Y+1);
$pdf->MultiCell(100,3,"Observations générales",0,'L',0);



$fichier="./data/pdf_certif/emargementvierge_".$idClasse.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;
?>
<br><br>
<center>
<?php 
$url="visu_pdf_scolaire.php";
if ($_SESSION["membre"] == "menuprof") { $url="visu_pdf_prof.php"; }	
?>
<input type=button onclick="open('<?php print $url?>?id=<?php print $fichier?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</center>

<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
<?php PgClose(); ?>
