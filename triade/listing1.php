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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGLIST1 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<br><ul>
<?php
$idclasse=$_POST["saisie_classe_edition"];
$anneeScolaire=$_POST["anneeScolaire"];

if ($idclasse == "tous") {

	// creation PDF
	//
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');

	$pdf=new PDF();  // declaration du constructeur


	$data=visu_affectation($anneeScolaire);  


	for($i=0;$i<count($data);$i++)  {
		$classe=chercheClasse($data[$i][0]);
		$idclasse=$classe[0][0];
	       	$nomClasse=$classe[0][1];

		$idprofp=aff_prof_p_classe($idclasse);
		$nomprof=recherche_personne($idprofp);
		$textp="";
		$textp="<font class=T2>".LANGLIST2." : <b>".chercheClasse_nom($idclasse)."</b><br><br><br><br><br>";

		if ( ! empty($idprofp)) {
			$textp.=LANGLIST3." : <b>$nomprof</b> <br><br><br><br><br>";
		}


		$datapp=visu_affectation_detail($idclasse,$anneeScolaire);
		for($j=0;$j<count($datapp);$j++) {
      		$textp.=recherche_personne($datapp[$j][2])." ( <i>".ucwords(chercheMatiereNom($datapp[$j][1]))."</i> ) ";
			$textp.="<br>";
		}
		unset($datapp);

	$pdf->AddPage();

	// Debut création PDF
	// insertion de la date
	$date=dateDMY();
	$Pdate=LANGLIST4.": ".$date;
	$pdf->SetFont('Courier','',10);
	$pdf->SetXY(150,3);
	$pdf->WriteHTML($Pdate);
	// fin d'insertion

	// cadre principale
	$pdf->SetFont('Arial','',11);
	$pdf->SetXY(15,35);
	$pdf->WriteHTML($textp);
	// fin cadre principale

}
unset($data);

$fichierpdf="./data/pdf_certif/edition-classe.pdf";
if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
$pdf->output('F',$fichierpdf);

?>
<ul><font class='T2' ><?php print LANGLIST5 ?></font><br><br><br>
<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichierpdf?>','_blank','');" value="<?php print LANGPER5?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<br><br>
<?php

}else{

$idprofp=aff_prof_p_classe($idclasse);
$nomprof=recherche_personne($idprofp);

if ( ! empty($idprofp)) {
	$textp=LANGLIST6." : <b>$nomprof</b> <br/><br/>";
}

print "<font class=T2>".LANGLIST2.": <b>".chercheClasse_nom($idclasse)."</font></b> pour l'ann&eacute;e scolaire <b>".$anneeScolaire."</b> <br /><br />";
print $textp;


$data=visu_affectation_detail($idclasse,$anneeScolaire);
for($i=0;$i<count($data);$i++) {
      print recherche_personne($data[$i][2]); print " ( <i>".ucwords(chercheMatiereNom($data[$i][1]))."</i> ) ";
	  print "<br>";
	}

}

Pgclose();
?>
</font>
</ul> <BR>

<ul>
<script language=JavaScript>buttonMagicRetour("listing.php","_parent")</script>
</ul>
<br><br>


<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
