<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
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
<tr id='cadreCentral0' ><td valign='top'>
<br>

<!-- // fin  -->
<?php
$dateDebut=dateFormBase($_POST["saisie_date_debut"]);
$dateFin=dateFormBase($_POST["saisie_date_fin"]);
$idClasse=$_POST["saisie_classe"];

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');


$pdf=new PDF();  // declaration du constructeur

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
	$xcoor0="5";
	$ycoor0="5";

	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML("$nom_etablissement");
	
	$pdf->SetXY(175,$ycoor0);
	$pdf->WriteHTML(dateDMY());

	$ycoor0+=10;
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML("LISTE DU NOMBRE D'ABSENCE ET DE RETARD EN CLASSE DE : <b>$classe</b> ");
	$ycoor0+=10;
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML("Période : ".$_POST["saisie_date_debut"]." du ".$_POST["saisie_date_fin"]);
	$ycoor0+=5;
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML("Un élève est considéré absent s'il a au moins 1 (AM) ou 1 (PM) heures d'absence.");
	$ycoor0+=7;

	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(60,7,"Nom Prénom",1,'C',0);
	$pdf->SetXY($xcoor0+=60,$ycoor0);
	$pdf->MultiCell(93,7,"Nbr de demi-jours et heures d'absence",1,'C',0);
	$pdf->SetXY($xcoor0+=93,$ycoor0);
	$pdf->MultiCell(47,7,"Nbr de retards",1,'C',0);

	$xcoor0=5;
	$ycoor0+=7;

	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(60,7,"Nb d'élèves : $nbeleve ",1,'C',0);

	$pdf->SetXY($xcoor0+=60,$ycoor0);
	$pdf->MultiCell(13,7,"TNJ",1,'C',0);
	$pdf->SetXY($xcoor0+=13,$ycoor0);
	$pdf->MultiCell(13,7,"TJ",1,'C',0);
	$pdf->SetXY($xcoor0+=13,$ycoor0);
	$pdf->MultiCell(13,7,"TJM",1,'C',0);
	$pdf->SetXY($xcoor0+=13,$ycoor0);
	$pdf->MultiCell(13,7,"TH",1,'C',0);
	$pdf->SetXY($xcoor0+=13,$ycoor0);
	$pdf->MultiCell(13,7,"THJ",1,'C',0);
	$pdf->SetXY($xcoor0+=13,$ycoor0);
	$pdf->MultiCell(28,7,"TOTAL",1,'C',0);

	$pdf->SetXY($xcoor0+=28,$ycoor0);
	$pdf->MultiCell(20,7,"TOTAL",1,'C',0);
	$pdf->SetXY($xcoor0+=20,$ycoor0);
	$pdf->MultiCell(14,7,"RNJ",1,'C',0);
	$pdf->SetXY($xcoor0+=14,$ycoor0);
	$pdf->MultiCell(13,7,"RJ",1,'C',0);
	
	$ycoor0+=7;

	for($i=0;$i<count($eleveT);$i++) {
		$xcoor0=5;
		$absHeure=0;
		$idEleve=$eleveT[$i][4];
		$nomEleve=ucwords($eleveT[$i][0]);
		$prenomEleve=ucfirst($eleveT[$i][1]);
		$nomprenomEleve=trunchaine("$nomEleve $prenomEleve",25);
	


		$absNonJustifie=nombre_absNonJustifie($idEleve,$dateDebut,$dateFin);
		$absJustifie=nombre_absJustifie($idEleve,$dateDebut,$dateFin);
		$rtdNonJustifie=nombre_retardNonJustifie($idEleve,$dateDebut,$dateFin);
		$rtdJustifie=nombre_retardJustifie($idEleve,$dateDebut,$dateFin);
		$absJustifieMaladie=nombre_absJustifieMaladie($idEleve,$dateDebut,$dateFin);
		$absHeureTotal=nombre_absHeureTotal($idEleve,$dateDebut,$dateFin);
		$absHeureJustifie=nombre_absHeureTotalJustifie($idEleve,$dateDebut,$dateFin);
		$absHeureNonJustifie=nombre_absHeureTotalNonJustifie($idEleve,$dateDebut,$dateFin);

		$totalRetard=$rtdNonJustifie + $rtdJustifie;
		$totalAbs=$absNonJustifie + $absJustifie + $absJustifieMaladie;

		if ($totalAbs >= 10) {
			$pdf->SetFillColor(220);
		}else{
			$pdf->SetFillColor(255);
		}

		if ($ycoor0 >= 250) {
			$pdf->AddPage();
			$ycoor0=10;
		}	

		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->MultiCell(60,7,"$nomprenomEleve",1,'L',1);	

		$pdf->SetXY($xcoor0+=60,$ycoor0);
		$pdf->MultiCell(13,7,"$absNonJustifie",1,'C',1);
		$pdf->SetXY($xcoor0+=13,$ycoor0);
		$pdf->MultiCell(13,7,"$absJustifie",1,'C',1);
		$pdf->SetXY($xcoor0+=13,$ycoor0);			
		$pdf->MultiCell(13,7,"$absJustifieMaladie",1,'C',1);
		$pdf->SetXY($xcoor0+=13,$ycoor0);
		$pdf->MultiCell(13,7,"$absHeureNonJustifie",1,'C',1);
		$pdf->SetXY($xcoor0+=13,$ycoor0);
		$pdf->MultiCell(13,7,"$absHeureJustifie",1,'C',1);
		$pdf->SetXY($xcoor0+=13,$ycoor0);

		$pdf->MultiCell(28,7,"$totalAbs dj / $absHeureTotal h",1,'C',1);	

		$pdf->SetFillColor(255);
		$pdf->SetXY($xcoor0+=28,$ycoor0);
		$pdf->MultiCell(20,7,"$totalRetard",1,'C',0);
		$pdf->SetXY($xcoor0+=20,$ycoor0);
		$pdf->MultiCell(14,7,"$rtdNonJustifie",1,'C',0);
		$pdf->SetXY($xcoor0+=14,$ycoor0);
		$pdf->MultiCell(13,7,"$rtdJustifie",1,'C',0);	

		$pdf->SetFillColor(255);

		$ycoor0+=7;
	}

	$xcoor0=5;
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor0,$ycoor0+=5);
	$pdf->WriteHTML("<u>Légende</u> : TNJ : total non justifié / TJ : total justifié / TH : total heure / THJ : total heure justifié / RNJ : retard non justifié / RJ : retard justifié TJM : total justifié par CM (certificat médical de plus d'un jour, motif de l'absence : certificat médical )  ");
	$pdf->SetXY($xcoor0+3,$ycoor0+=12);
	$pdf->SetFillColor(220);
	$pdf->MultiCell(3,3,'',1,'C',1);
	$pdf->SetFillColor(255);
	$pdf->SetXY($xcoor0+7,$ycoor0-2);
	$pdf->MultiCell(200,7,": Total des absences suppérieur à 10",0,'L',0);

}

if ($_POST["saisie_classe"] == "-10") { $classe="Toutes_Les_classes"; }
$classe=TextNoAccent($classe);
$classe=TextNoCarac($classe);
$classe_nom=preg_replace('/\//',"_",$classe);
$fichier=urlencode($fichier);
if (!is_dir("./data/pdf_abs/")) { mkdir("./data/pdf_abs/"); }
$fichier="./data/pdf_abs/${classe}_abscomplet_".$dateDebut."_".$dateFin.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();

?>

<br><ul><ul>
<?php
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$url="visu_pdf_prof.php";
}else{
	$url="visu_pdf_admin.php";
}
?>
<input type=button onclick="open('<?php print $url ?>?id=<?php print $fichier?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul></ul><br><br>

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
</BODY></HTML>
