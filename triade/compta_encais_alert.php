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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Gestion des encaissements" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br />
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$dateJ=$_POST["dateencaissement"];
if ($dateJ == "") $dateJ=date("d/m/Y");
$dateJ2=$_POST["dateencaissementFin"];
if ($dateJ2 == "") $dateJ2=date("d/m/Y");
?>
<table width='100%' >
<tr>
<td>
<form name="formulaire" method="post" >
<font class='T2'>&nbsp;&nbsp;Encaissement du <input type="text" name="dateencaissement" value="<?php print $dateJ?>" size=12 > 
	<?php include_once("librairie_php/calendar.php"); calendarDim('id1','document.formulaire.dateencaissement',$_SESSION["langue"],"0","0");?>
au <input type="text" name="dateencaissementFin" value="<?php print $dateJ2?>" size=12 > 
	<?php include_once("librairie_php/calendar.php"); calendarDim('id1','document.formulaire.dateencaissementFin',$_SESSION["langue"],"0","0");?>
<input type=submit value="ok" class='button' name='chgdate' />&nbsp;&nbsp;<span id="btpdf"></span> 
</form>
</td></tr></table>


<br /><br /> 
<table width='100%' border=1>
<tr><td bgcolor='yellow'>&nbsp;Montant&nbsp;</td><td bgcolor='yellow'>&nbsp;Tireur&nbsp;</td><td bgcolor='yellow'>&nbsp;N°&nbsp;de&nbsp;cheque&nbsp;</td></tr>
<?php
if (!is_dir("./data/pdf_depot")) { mkdir("./data/pdf_depot");htaccess("./data/pdf_depot"); }
nettoyage_repertoire("./data/pdf_depot");

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF('L','mm','A4');  // declaration du constructeur

$bancsoc=aff_enr_parametrage("bancsociete");
$banccode=aff_enr_parametrage("banccode");
$bancguic=aff_enr_parametrage("bancguic");
$bancompte=aff_enr_parametrage("bancompte");
$banrib=aff_enr_parametrage("banrib");


$pdf->SetTitle("DEPOT DE CHEQUE");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("DEPOT DE CHEQUE du  $dateJ "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$pdf->AddPage();
$X=0;
$Y=5;

$pdf->SetXY($X,$Y); 
$pdf->SetFont('Arial','B',16);
$pdf->MultiCell(297,10,$bancsoc[0][1]."\n DATE DE DEPOT DE CHEQUE : $dateJ ",0,'C',0);  // PARAM

$pdf->SetXY($X,$Y+=20); 
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(297,10,"CODE BANQUE : ".$banccode[0][1]."      - GUICHET : ".$bancguic[0][1]."        - N° compte : ".$bancompte[0][1]."       - CLE RIB : ".$banrib[0][1],0,'C',0);

$pdf->SetFont('Arial','B',12);

$pdf->SetXY($X+=5,$Y+=10); 
$pdf->MultiCell(40,10,"MONTANT",1,'C',0);
$pdf->SetXY($X+=40,$Y); 
$pdf->MultiCell(40,10,"BANQUE",1,'C',0);
$pdf->SetXY($X+=40,$Y); 
$pdf->MultiCell(70,10,"TIREUR",1,'C',0);
$pdf->SetXY($X+=70,$Y); 
$pdf->MultiCell(70,10,"CHEQUE / VIR. / ESPECE",1,'C',0);
$pdf->SetXY($X+=70,$Y); 
$pdf->MultiCell(70,10,"OBSERVATION",1,'C',0);

$pdf->SetXY($X=5,$Y+=10);

$pdf->SetFont('Arial','',9);
// ideleve,montantvers,num_cheque,modepaiement,idversement,etablissement_bancaire
$data=chercheVersementPeriode($dateJ,$dateJ2);
$ii=0;
for($i=0;$i<count($data);$i++){
	$ii++;
	$montant=affichageFormatMonnaie($data[$i][1]);
	$tireur=recherche_eleve_nom($data[$i][0]);
	$cheque=$data[$i][2];
	print "<tr><td bgcolor='#FFFFFF'>&nbsp;".$montant." ".unitemonnaie()."</td><td bgcolor='#FFFFFF'>&nbsp;".$tireur."</td><td bgcolor='#FFFFFF'>&nbsp;".$data[$i][2]."</td></tr>";

	$obs=$data[$i][3];
	$banque=$data[$i][5];
	

	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(40,10,"$montant",1,'R',0);
	$pdf->SetXY($X+=40,$Y); 
	$banque=trunchaine($banque,20);
	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(40,10,"$banque",1,'R',0);
	$pdf->SetXY($X+=40,$Y); 
	$pdf->SetFont('Arial','',12);
	$tireur=trunchaine($tireur,30);
	$pdf->MultiCell(70,10,"$tireur",1,'R',0);
	$pdf->SetXY($X+=70,$Y); 
	$pdf->MultiCell(70,10,"$cheque",1,'R',0);
	$pdf->SetXY($X+=70,$Y);
	$pdf->MultiCell(70,10,"",1,'R',0);
	$pdf->SetFont('Arial','',8);
	$obs=trunchaine(strtolower($obs),100);
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(70,3,"$obs",0,'L',0);

	if ($i < 14) {
		if ($ii == 14) {
			$pdf->AddPage(); 
			$ii=0; 
			$X=5; 
			$Y=2;  
		}
	}else{
		if ($ii == 17) {
			$pdf->AddPage(); 
			$ii=0; 
			$X=5; 
			$Y=2;  
		}
	}
	
	$pdf->SetXY($X=5,$Y+=10);
	

}


$fichier="./data/pdf_depot/depot_cheque_".md5(dateDMY2().dateHIS()).".pdf";
@unlink($fichier); // destruction avant creation
$pdf->close();
$pdf->output('F',$fichier);


if (file_exists($fichier)) { ?>
	<script>document.getElementById('btpdf').innerHTML="<input class='bouton2' type='button' value='Dépôt de chèques' onclick=\"open('visu_pdf_admin.php?id=<?php print $fichier ?>','_parent','')\" />";</script>
<?php } ?>
</table>
</font>
     </td></tr></table>
<?php
Pgclose();
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
   </BODY></HTML>
