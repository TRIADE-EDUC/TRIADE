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
include_once("librairie_php/db_triade.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Relevé complet des passages à la cantine" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
$cnx=cnx();
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 

?>

<!-- // fin  -->
<br><br><font class='T2'>
&nbsp;&nbsp;Information au <?php print dateDMY() ?> à <?php print dateHIS() ?><br><br></font><br>
<?php
	$titre="Information au ".dateDMY()." à ".dateHIS();
	$liste_eleveRegime=recupListEleveRegime();
	$nb_elevedemipension=count($liste_eleveRegime);
	$nb_eleve_abs=nbrEleveAbsCantineAujourdhui($liste_eleveRegime);
	$nb_eleve_en_stage=nbrEleveEnStageAujourdhui($liste_eleveRegime);
	$total=$nb_elevedemipension-$nb_eleve_abs-$nb_eleve_en_stage;

	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');

	$pdf=new PDF();  // declaration du constructeur
	$pdf->AddPage();
	$pdf->SetTitle("Rapport - Passage Cantine");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Rapport Passage Cantine"); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	$X=10;
	$Y=10;


	$pdf->SetFont('Arial','U',14);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(210,10,"$titre",0,'L',0);
	$pdf->SetXY($X,$Y+=20);
	$pdf->SetFont('Arial','',12);
	$contenu="Nombre d'élève au régime : $nb_eleveRegime";
	$pdf->SetXY($X,$Y+=5);
	$pdf->WriteHTML("$contenu");
	$contenu="Nombre d'élève absent : $nb_eleve_abs ";
	$pdf->SetXY($X,$Y+=5);
	$pdf->WriteHTML("$contenu");
	$contenu="Nombre d'élève en stage : $nb_eleve_en_stage";
	$pdf->SetXY($X,$Y+=5);
	$pdf->WriteHTML("$contenu");
	$contenu="Nombre d'élève présent : <b>$total</b>";
	$pdf->SetXY($X,$Y+=5);
	$pdf->WriteHTML("$contenu");
 
	$fichier="./data/pdf_certif/passage_cantine_".$_SESSION["id_pers"].".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);
	$pdf->close();


?>
<table align='center' width='100%'>
<tr><td align='right'><font class='T2'>Nombre d'élèves aujourd'hui : </font></td><td width='50%'><font class='T2'><?php print count($liste_eleveRegime) ?></font></td></tr>
<tr><td align='right'><font class='T2'>Nombre d'élèves absent :</font></td><td><font class='T2'><?php print $nb_eleve_abs ?></font></td></tr>
<tr><td align='right'><font class='T2'>Nombre d'élèves en stage :</font></td><td><font class='T2'><?php print $nb_eleve_en_stage ?></font></td></tr>
<tr><td align='right'><font class='T2'>Nombre d'élèves présent :</font></td><td><font class='T2'><b><?php print $total ?></b></font></td></tr>
</table>
<br><center>
<input type=button onclick="open('visu_pdf_id.php?id=./data/pdf_certif/passage_cantine_&fichiername=Passage Cantine.pdf','_blank','');" value="<?php print "Imprimer" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</center>

<?php }else{ ?>
	<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<?php } ?>

<br>
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
