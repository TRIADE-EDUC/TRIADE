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
// connexion
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { header("Location:./acces_refuse.php"); exit; }
include_once("./common/config.inc.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"trombinoscopeRead")){
		validerequete("2");
	}
}else{
	validerequete("2");
}
include_once("./common/config2.inc.php");
$cnx=cnx();
// nom classe

$sqlsuite="(type_pers='ENS' OR type_pers='ADM' OR type_pers='PER' OR type_pers='MVS')";
if (isset($_GET["saisie_type"])) {
	if ($_GET["saisie_type"] != "0") $sqlsuite=" type_pers='".$_GET["saisie_type"]."' ";
}

$sql="SELECT pers_id,pers_id,nom,prenom FROM ${prefixe}personnel WHERE $sqlsuite AND  offline='0' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Trombinoscope - Personnel");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Trombinoscope - Personnel "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
$x=0;
$y=10;
$pdf->SetFont('Arial','B',12);
$pdf->SetXY($x,$y);
$pdf->MultiCell(210,5,"Trombinoscope du personnel",0,'C',0);
$y+=10;
$x=7;
$nbp=1;
$pdf->SetFont('Arial','',8);
for($i=0;$i<count($data);$i++) {

	$logo="./data/image_pers/".trim($data[$i][1]).".jpg";
	if (!file_exists($logo)) { $logo="./image/commun/photo_vide.jpg"; }
	$pdf->Image($logo,$x,$y,20,20);
	$pdf->SetXY($x+20,$y);
	$nomprenom=strtoupper($data[$i][2])."\n".ucwords($data[$i][3]);
	$pdf->MultiCell(30,5,"$nomprenom",0,'L',0);	
	$x+=50;
	if ($x >= 200) {
		$x=7;
		$y+=25;
	}

	if ($y > 250) {
		$pdf->AddPage();
		$y=10;$x=7;
	}
}
Pgclose();
$fic="./data/pdf_certif/trombinoscope-personnel.pdf";
@unlink($fic);
$pdf->output('F',$fic);

header("Content-disposition: attachment; filename=trombinoscope-$cl.pdf");
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: application/pdf\n"); // Surtout ne pas enlever le \n
header("Content-Length: ".filesize($fic));
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile($fic);


?>
