<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(0);
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
include_once("./librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}	
$cnx=cnx();
$classe=chercheClasse_nom($_POST["saisie_classe"]);

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Impression des absences de la classe "." <font id='color2'>".$classe."</font>" ?></font></b></td></tr>
<tr id='cadreCentral0' ><td valign='top' >
<br>

<!-- // fin  -->
<?php
$dateDebut=dateFormBase($_POST["saisie_date_debut"]);

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');


$pdf=new PDF('L','mm','A4'); 
/*
$data=visu_param();
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
}
 */
include_once('librairie_php/recupnoteperiode.php');

$listclasse=affClasse();

for($c=0;$c<count($listclasse);$c++) {
	$idClasse=$listclasse[$c][0];
	if (($_POST["saisie_classe"] != $idClasse) && ($_POST["saisie_classe"] != "tous")) { continue; }



	$eleveT=recupEleve($idClasse);      // recup liste eleve
	$classe=chercheClasse_nom($idClasse);
	$nbeleve=count($eleveT);



	$pdf->AddPage();
	$pdf->SetTitle("Abs/Retard - $classe");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Abs/Retard - $classe"); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
	$xcoor0="2";
	$ycoor0="2";

	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(50,10,"Nom de l'élève\n CLASSE : $classe ",1,'C',0);

	$pdf->SetXY($xcoor0+=50,$ycoor0);

	//LUNDI
	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(45,20,"LUNDI\n",1,'C',0);
	$pdf->SetXY($xcoor0,$ycoor0+=15);
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(4.5,5,"8",1,'C',0); // 8
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"9",1,'C',0); // 9	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"10",1,'C',0); // 10	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"11",1,'C',0); // 11
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"12",1,'C',0); // 12
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"13",1,'C',0); // 13
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"14",1,'C',0); // 14
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"15",1,'C',0); // 15
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"16",1,'C',0); // 16
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"17",1,'C',0); // 17
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);

	//MARDI
	$ycoor0="2";
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(45,20,"MARDI\n",1,'C',0);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($xcoor0,$ycoor0+=15);
	$pdf->MultiCell(4.5,5,"8",1,'C',0); // 8
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"9",1,'C',0); // 9	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"10",1,'C',0); // 10	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"11",1,'C',0); // 11
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"12",1,'C',0); // 12
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"13",1,'C',0); // 13
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"14",1,'C',0); // 14
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"15",1,'C',0); // 15
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"16",1,'C',0); // 16
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"17",1,'C',0); // 17
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);

	//MERCREDI
	$ycoor0="2";
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(45,20,"MERCREDI\n",1,'C',0);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($xcoor0,$ycoor0+=15);
	$pdf->MultiCell(4.5,5,"8",1,'C',0); // 8
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"9",1,'C',0); // 9	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"10",1,'C',0); // 10	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"11",1,'C',0); // 11
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"12",1,'C',0); // 12
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"13",1,'C',0); // 13
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"14",1,'C',0); // 14
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"15",1,'C',0); // 15
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"16",1,'C',0); // 16
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"17",1,'C',0); // 17
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);

	//JEUDI
	$ycoor0="2";
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(45,20,"JEUDI\n",1,'C',0);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($xcoor0,$ycoor0+=15);
	$pdf->MultiCell(4.5,5,"8",1,'C',0); // 8
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"9",1,'C',0); // 9	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"10",1,'C',0); // 10	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"11",1,'C',0); // 11
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"12",1,'C',0); // 12
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"13",1,'C',0); // 13
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"14",1,'C',0); // 14
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"15",1,'C',0); // 15
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"16",1,'C',0); // 16
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"17",1,'C',0); // 17
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);

	//VENDREDI
	$ycoor0="2";
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(45,20,"VENDREDI\n",1,'C',0);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($xcoor0,$ycoor0+=15);
	$pdf->MultiCell(4.5,5,"8",1,'C',0); // 8
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"9",1,'C',0); // 9	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"10",1,'C',0); // 10	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"11",1,'C',0); // 11
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"12",1,'C',0); // 12
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"13",1,'C',0); // 13
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"14",1,'C',0); // 14
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"15",1,'C',0); // 15
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"16",1,'C',0); // 16
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"17",1,'C',0); // 17
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);

	//SAMEDI
	$ycoor0="2";
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(18,20,"SAMEDI\n",1,'C',0);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($xcoor0,$ycoor0+=15);
	$pdf->MultiCell(4.5,5,"8",1,'C',0); // 8
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"9",1,'C',0); // 9	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"10",1,'C',0); // 10	
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);
	$pdf->MultiCell(4.5,5,"11",1,'C',0); // 11
	$pdf->SetXY($xcoor0+=4.5,$ycoor0);








	$ycoor0+=5;
	for($i=0;$i<count($eleveT);$i++) {
		$xcoor0=2;

		$idEleve=$eleveT[$i][4];
		$nomEleve=ucwords($eleveT[$i][0]);
		$prenomEleve=ucfirst($eleveT[$i][1]);
		$nomprenomEleve=trunchaine("$nomEleve $prenomEleve",25);
	
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->MultiCell(50,5,"$nomprenomEleve",1,'L',0);
		$pdf->SetXY($xcoor0+=50,$ycoor0);

		//LUNDI
		$pdf->SetFont('Arial','B',6);
		$pdf->SetFillColor(255);

		absEleve($dateDebut,'0','08:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','08:',$idEleve);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 8
		unset($info);

		absEleve($dateDebut,'0','09:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','09:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 9	
		unset($info);

		absEleve($dateDebut,'0','10:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','10:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 10
		unset($info);

		absEleve($dateDebut,'0','11:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','11:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 11
		unset($info);

		absEleve($dateDebut,'0','12:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','12:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 12
		unset($info);

		absEleve($dateDebut,'0','13:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','13:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 13
		unset($info);

		absEleve($dateDebut,'0','14:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','14:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 14
		unset($info);

		absEleve($dateDebut,'0','15:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','15:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 15
		unset($info);

		absEleve($dateDebut,'0','16:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','16:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 16
		unset($info);

		absEleve($dateDebut,'0','17:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'0','17:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 17
		unset($info);

		$pdf->SetXY($xcoor0+=4.5,$ycoor0);

		//MARDI
		$pdf->SetFont('Arial','B',6);
		$pdf->SetFillColor(255);

		absEleve($dateDebut,'1','08:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','08:',$idEleve);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 8
		unset($info);

		absEleve($dateDebut,'1','09:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','09:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 9	
		unset($info);

		absEleve($dateDebut,'1','10:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','10:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 10
		unset($info);

		absEleve($dateDebut,'1','11:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','11:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 11
		unset($info);

		absEleve($dateDebut,'1','12:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','12:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 12
		unset($info);

		absEleve($dateDebut,'1','13:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','13:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 13
		unset($info);

		absEleve($dateDebut,'1','14:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','14:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 14
		unset($info);

		absEleve($dateDebut,'1','15:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','15:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 15
		unset($info);

		absEleve($dateDebut,'1','16:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','16:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 16
		unset($info);

		absEleve($dateDebut,'1','17:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'1','17:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 17
		unset($info);

		$pdf->SetXY($xcoor0+=4.5,$ycoor0);

		//MERCREDI
		$pdf->SetFont('Arial','B',6);
		$pdf->SetFillColor(255);

		absEleve($dateDebut,'2','08:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','08:',$idEleve);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 8
		unset($info);

		absEleve($dateDebut,'2','09:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','09:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 9	
		unset($info);

		absEleve($dateDebut,'2','10:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','10:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 10
		unset($info);

		absEleve($dateDebut,'2','11:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','11:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 11
		unset($info);

		absEleve($dateDebut,'2','12:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','12:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 12
		unset($info);

		absEleve($dateDebut,'2','13:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','13:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 13
		unset($info);

		absEleve($dateDebut,'2','14:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','14:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 14
		unset($info);

		absEleve($dateDebut,'2','15:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','15:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 15
		unset($info);

		absEleve($dateDebut,'2','16:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','16:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 16
		unset($info);

		absEleve($dateDebut,'2','17:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'2','17:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 17
		unset($info);

		$pdf->SetXY($xcoor0+=4.5,$ycoor0);

		//JEUDI
		$pdf->SetFont('Arial','B',6);
		$pdf->SetFillColor(255);

		absEleve($dateDebut,'3','08:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','08:',$idEleve);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 8
		unset($info);

		absEleve($dateDebut,'3','09:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','09:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 9	
		unset($info);

		absEleve($dateDebut,'3','10:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','10:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 10
		unset($info);

		absEleve($dateDebut,'3','11:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','11:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 11
		unset($info);

		absEleve($dateDebut,'3','12:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','12:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 12
		unset($info);

		absEleve($dateDebut,'3','13:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','13:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 13
		unset($info);

		absEleve($dateDebut,'3','14:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','14:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 14
		unset($info);

		absEleve($dateDebut,'3','15:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','15:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 15
		unset($info);

		absEleve($dateDebut,'3','16:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','16:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 16
		unset($info);

		absEleve($dateDebut,'3','17:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'3','17:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 17
		unset($info);

		$pdf->SetXY($xcoor0+=4.5,$ycoor0);



		//VENDREDI
		$pdf->SetFont('Arial','B',6);
		$pdf->SetFillColor(255);

		absEleve($dateDebut,'4','08:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','08:',$idEleve);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 8
		unset($info);

		absEleve($dateDebut,'4','09:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','09:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 9	
		unset($info);

		absEleve($dateDebut,'4','10:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','10:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 10
		unset($info);

		absEleve($dateDebut,'4','11:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','11:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 11
		unset($info);

		absEleve($dateDebut,'4','12:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','12:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 12
		unset($info);

		absEleve($dateDebut,'4','13:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','13:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 13
		unset($info);

		absEleve($dateDebut,'4','14:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','14:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 14
		unset($info);

		absEleve($dateDebut,'4','15:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','15:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 15
		unset($info);

		absEleve($dateDebut,'4','16:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','16:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 16
		unset($info);

		absEleve($dateDebut,'4','17:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'4','17:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 17
		unset($info);

		$pdf->SetXY($xcoor0+=4.5,$ycoor0);



		//SAMEDI
		$pdf->SetFont('Arial','B',6);
		$pdf->SetFillColor(255);

		absEleve($dateDebut,'5','08:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'5','08:',$idEleve);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 8
		unset($info);

		absEleve($dateDebut,'5','09:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'5','09:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 9	
		unset($info);

		absEleve($dateDebut,'5','10:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'5','10:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 10
		unset($info);

		absEleve($dateDebut,'5','11:',$idEleve,$pdf);
		$info=absInfoEleve($dateDebut,'5','11:',$idEleve);
		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
		$pdf->MultiCell(4.5,5,"$info",1,'C',1); // 11
		unset($info);

		$pdf->SetXY($xcoor0+=4.5,$ycoor0);
 

		$ycoor0+=5;

		if ($i == 28) { 
			$pdf->AddPage();
			$xcoor0="2";
			$ycoor0="2";
		}
	
	}
 

	$xcoor0=2;
	$dateDebut1=$_POST["saisie_date_debut"];
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($xcoor0,$ycoor0+=7);
	$pdf->MultiCell(40,5,"Semaine du $dateDebut1 ",0,'L',0); 
	$pdf->SetXY($xcoor0+=45,$ycoor0);

	$pdf->SetFillColor(255,204,0);
	$pdf->MultiCell(5,5,"A",1,'C',1); 
	$pdf->SetXY($xcoor0+=7,$ycoor0);
	$pdf->MultiCell(20,5,"Absence",0,'L',0);
	$pdf->SetXY($xcoor0+=20,$ycoor0);
	$pdf->MultiCell(5,5,"?",1,'C',1); 
	$pdf->SetXY($xcoor0+=7,$ycoor0);
	$pdf->MultiCell(40,5,"Absence non justifiée",0,'L',0);

	$pdf->SetXY($xcoor0+=43,$ycoor0);
	$pdf->SetFillColor(79,176,145);
	$pdf->MultiCell(5,5,"R",1,'C',1); 
	$pdf->SetXY($xcoor0+=7,$ycoor0);
	$pdf->MultiCell(20,5,"Retard",0,'L',0);
	$pdf->SetXY($xcoor0+=20,$ycoor0);
	$pdf->MultiCell(5,5,"?",1,'C',1); 
	$pdf->SetXY($xcoor0+=7,$ycoor0);
	$pdf->MultiCell(40,5,"Retard non justifié",0,'L',0);


}




if ($_POST["saisie_classe"] == "tous") { $classe="Toutes_Les_classes"; }
$classe=TextNoAccent($classe);
$classe=TextNoCarac($classe);
$classe_nom=preg_replace('/\//',"_",$classe);
$fichier=urlencode($fichier);
if (!is_dir("./data/pdf_abs/")) { mkdir("./data/pdf_abs/"); }
$fichier="./data/pdf_abs/${classe}_abscomplet_semaine_".$dateDebut.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();


if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$url="visu_pdf_prof.php";
}else{
	$url="visu_pdf_admin.php";
}

?>

<br><ul><ul>
<table><tr><td><input type=button onclick="open('<?php print $url ?>?id=<?php print $fichier?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td><td><script>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour')</script></td></tr></table>
</ul></ul>
<br><br>

     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
<script language=JavaScript>attente_close();</script>
</BODY></HTML>
