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
include_once('./common/config.inc.php');
include_once('./common/config2.inc.php');
include_once('./librairie_php/db_triade.php');
$anneeScolaire=$_COOKIE["anneeScolaire"];
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	verifDroit($_SESSION["id_pers"],"consultationRead");
}else{
	validerequete("3");
}
$idClasse=$_GET["idClasse"];
$idsite=chercherIdSiteClasse($idClasse);
$nometablissement=recupSite($idsite);

$nomClasse=chercheClasse_nom($idClasse);
$nomClasseLong=chercheClasse_description($idClasse);
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Listing -");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Emargement "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
$hauteur=5;
$X=5;
$Y=5;

$data=visu_paramViaIdSite(chercheIdSite($idClasse));
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,$anneeScolaire

$pdf->SetFont('Arial','B',9);

$pdf->SetXY($X,$Y);
$pdf->MultiCell(70,6,"$nometablissement",0,'L',0);

$pdf->SetXY($X+70,$Y);
$pdf->MultiCell(70,6,"Listing des élèves",0,'C',0);

$pdf->SetXY($X+140,$Y);
$pdf->MultiCell(64,6,"$anneeScolaire",0,'R',0);

$pdf->SetXY($X,$Y+=6);
$pdf->MultiCell(210,6,"$nomClasse             $nomClasseLong",0,'L',0);



$Y+=$hauteur+3;
include_once('librairie_php/recupnoteperiode.php');
$eleveT=recupEleve($idClasse); // recup liste eleve : nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve
for($j=0;$j<count($eleveT);$j++) {  // variable eleve
	$nomEleve=strtoupper($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$idEleve=$eleveT[$j][4];
	$numEleve=$eleveT[$j][11];
	$X=5;
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X,$Y);
	$jj=$j+1;
	$pdf->MultiCell(8,$hauteur,"$jj)",1,'L',0);
	$pdf->SetXY($X+=8,$Y);
	$pdf->MultiCell(50,$hauteur,"$nomEleve $prenomEleve",1,'L',0);
	$pdf->SetXY($X+=50,$Y);
	$pdf->MultiCell(145,$hauteur,"",1,'C',0);
	$Y+=$hauteur;
	if ($Y >= 270) {
		$pdf->AddPage(); 
		$Y=10;
	}
}
$fichier="./data/pdf_certif/listing_".$idClasse.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;
PgClose();
$filename = stripslashes(basename($fichier));
$type="application/pdf";
header("Content-disposition: attachment; filename=$filename");
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: $type\n"); // Surtout ne pas enlever le \n
header("Content-Length: ".filesize($fichier));
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile($fichier);
?>
