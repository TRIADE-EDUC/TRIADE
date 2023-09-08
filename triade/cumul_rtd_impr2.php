<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(600);
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Impression des absences et retards de la classe"." <font id='color2'>".$classe."</font>" ?></font></b></td></tr>
<tr id='cadreCentral0' ><td >
<br><br>
<ul>
<font class="T2">
<b>Valeur prise en compte</b> : <ul><br />
	<img src="image/commun/on1.gif" height='8' width='8' > "O" => Non justifié. <br>
	<img src="image/commun/on1.gif" height='8' width='8' > "E" => Motif Absence justifiée.  <br>
	<img src="image/commun/on1.gif" height='8' width='8' > "M" => Motif <u>certificat médical</u> justifiée > d'un jour. <br>
	<img src="image/commun/on1.gif" height='8' width='8' > "C" => Motif <u>congé</u> et les congés de la semaine.<br>
	<img src="image/commun/on1.gif" height='8' width='8' > "S" => Motif <u>sorti</u>.<br>
	</ul>
</font>
</ul>

<!-- // fin  -->
<?php
$mois=$_POST["saisie_mois"];
$idClasse=$_POST["saisie_classe"];
$annee=$_POST["saisie_annee"];

$dateDebut=recupdateDebut2($mois,$annee);
$dateFin=recupdateFin2($mois,$annee);

$dateLettre1=dateLettre($dateDebut);
$dateLettre2=dateLettre($dateFin);

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
		if ($_POST["saisie_classe"] != "-10") {
			if ($_POST["saisie_classe"] != $idClasse) {
				continue;
			}
		}
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
	$pdf->WriteHTML("$nom_etablissement - Classe : $classe");
	$pdf->SetXY(170,$ycoor0);
	$dateJour=dateDMY();
	$pdf->WriteHTML("$dateJour");
	$pdf->SetXY(5,$ycoor0+=10);
	$pdf->WriteHTML("Registre des absences du $dateLettre1  au $dateLettre2 ");
	$pdf->SetXY(5,$ycoor0+=5);
	$pdf->WriteHTML("----------------------------------------------------------------------------------------------------------------------------------------");
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY(5,$ycoor0+=5);
	$classe2=trunchaine("$classe","10");
	$pdf->WriteHTML("Classe : $classe2");
        $pdf->SetXY(5,$ycoor0+5);
	$pdf->WriteHTML("Nb.élèves : $nbeleve");

	$pdf->SetFont('Arial','',12);
	$xcoor0=43;
	$pdf->SetXY($xcoor0,$ycoor0);
	$nbjours=nbjourdumois($mois);
	for ($o=1;$o<=$nbjours;$o++) {
		$pdf->MultiCell(5,5,$o,1,'C',0);
		$pdf->SetXY($xcoor0+=5,$ycoor0);
	}

	$ycoor0+=10;

	$pdf->SetFont('Arial','',11);

for($j=0;$j<count($eleveT);$j++) { 
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$idEleve=$eleveT[$j][4];	

	$nomprenom=trunchaine("$nomEleve $prenomEleve",22);
	$xcoor0=5;
	$pdf->SetXY($xcoor0,$ycoor0);

	$cr=verifGroupBis($idEleve);
	if ($cr) {
		$pdf->SetTextColor(255,0,0);
	}else{
		$pdf->SetTextColor(0,0,0);
	}
	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(43,6,$nomprenom,1,'L',0);
	$pdf->SetFont('Arial','',11);
	$pdf->SetTextColor(0,0,0);
	$xcoor0=43;
	
	$nbjours=nbjourdumois2($mois,$annee);
	for ($o=1;$o<=$nbjours;$o++) {
		$infoM=".";
		$infoA=".";
		$recupData=recupabsDuJour($o,$mois,$annee,$idEleve); 
		$jourDeLaSemaine=date_jour2("${o}/${mois}/${annee}");
		// elev_id,date_ab,date_saisie,duree_ab,origin_saisie,date_fin,motif,duree_heure,id_matiere,time,justifier
		if ($recupData[0][10] == "0") {
			$infoM="O";
			$infoA="O";
		}

		if ($recupData[0][10] == "1") {  // tout abs justifié
			$infoM="E";
			$infoA="E";
		}

	/*	if (($recupData[0][10] == "1") && (strtolower($recupData[0][6]) == "maladie") && ($recupData[0][3] == 1) ) {
			$infoM="E";
			$infoA="E";
	} */

		if (($recupData[0][10] == "1") && (strtolower($recupData[0][6]) == "certificat médical") && ($recupData[0][3] > 1) ) {
			$infoM="M";
			$infoA="M";
		}



		if (strtolower($recupData[0][6]) == "congé"){
			$infoM="C";
			$infoA="C";
		}


		if (strtolower($recupData[0][6]) == "sorti"){
			$infoM="s";
			$infoA="s";
		}


		if ((CALSAMEDIAP == "oui") && ($jourDeLaSemaine == "sa")) {
			$infoA="C";
		}
		if ((CALSAMEDIMATIN == "oui") && ($jourDeLaSemaine == "sa")) {
			$infoM="C";
		}


		if ((CALMERCREDIAP == "oui") && ($jourDeLaSemaine == "me")) {
			if ($infoM == "") { $infoM="."; }
			$infoA="C";
		}	
		if ((CALMERCREDIMATIN == "oui") && ($jourDeLaSemaine == "me")) {
			if ($infoA == "") { $infoM="."; }
			$infoM="C";
		}
			
		if ($jourDeLaSemaine == "di") { 		
			$infoM="C";
			$infoA="C";
		}

		$ferie=FERIE; //ex: '01/01','01/05','08/05','14/07','15/08','01/11','11/11','25/12'
		$tab=explode(",",$ferie);
		foreach($tab as $valeur) {
			$valeur=preg_replace("/'/","",$valeur);
			$valeur=preg_replace('/^0/',"",$valeur);
			if ($valeur == "${o}/${mois}") {
				$infoM="C";
				$infoA="C";
			}

		}

		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->MultiCell(5,6,'',1,'C',0);
		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($xcoor0,$ycoor0-1.5);
		$pdf->MultiCell(5,6,$infoM,0,'C',0);
		$pdf->SetXY($xcoor0,$ycoor0+1.5);
		$pdf->MultiCell(5,6,$infoA,0,'C',0);
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY($xcoor0+=5,$ycoor0);
	}
	$ycoor0+=6;
	
	if ($ycoor0 > 230) {
		$pdf->AddPage();
		$ycoor0=20;
	}

}

$xcoor0=5;
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML("--------------------------------------------------------------------------------------------------------------------------------------------------");
$pdf->SetXY($xcoor0,$ycoor0+=5);
$pdf->WriteHTML("Légende: <b>O</b>: non justifié;  <b>E</b>: justifiée par une note explicatif des parents; <b>M</b>: justifiée par C.M. ou C.P.E. ; <br> <b>C</b>: congé;   <b>e</b>: par encore entré; <b>c</b>: changement de classe; <b>s</b>:sorti ");

	}

if ($_POST["saisie_classe"] == "-10") { $classe="Toutes_Les_classes"; }
$classe=TextNoAccent($classe);
$classe=TextNoCarac($classe);
$classe_nom=preg_replace('/\//',"_",$classe);
$fichier=urlencode($fichier);
$dateDebut=dateFormBase($dateDebut);
$dateFin=dateFormBase($dateFin);
$fichier="./data/pdf_abs/${classe}_abs_".$dateDebut."_".$dateFin.".pdf";
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
<script language=JavaScript>buttonMagicRetour('cumul_rtd_impr.php','_parent') //text,nomInput</script>&nbsp;&nbsp;
<input type=button onclick="open('<?php print $url ?>?id=<?php print $fichier?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  class="bouton2" >
</ul></ul><br><br>

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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
