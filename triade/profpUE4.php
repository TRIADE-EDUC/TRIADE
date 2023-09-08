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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa du Responsable d'Unité Enseignement." ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
$idclasse=$_POST["saisie_classe"];
$tri=$_POST["saisie_trimestre"];
$nb=$_POST["saisie_nb"];
$nb_ue=$_POST["saisie_nb_ue"];

validerequete("menuprof");

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur

$pdf->AddPage();
$pdf->SetTitle("Bulletin - Commentaire");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Bulletin Commentaire $tri "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
$y=10;

$pdf->SetFont('Arial','',10);
$pdf->SetXY(20,$y);
$pdf->MultiCell(200,5,"Classe : ".chercheClasse_nom($idclasse)." / $tri",0,'L',0);

// Debut création PDF
// mise en place des coordonnées
$x=20;
$y+=5;
$j=0;
for($i=0;$i<$nb;$i++) {
	$eleveid=$_POST["eleveid_$i"];
	$nom=recherche_eleve_nom($eleveid);
	$prenom=recherche_eleve_prenom($eleveid);

	if ($y >= 260 ) {
		$y=10;
		$pdf->AddPage();
	}

	$pdf->SetXY($x,$y+=5);
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(200,5,"$nom $prenom",0,'L',0);
	$y+=5;
	for($j=0;$j<$nb_ue;$j++) {
		$com=$_POST["comm_$i$j"];
		$id_ue=$_POST["id_ue_$i$j"];
		$name_ue=$_POST["name_ue_$i$j"];
		$cr=create_comm_profp_ue($eleveid,$tri,$com,$idclasse,$id_ue);
		if ($cr) { history_cmd($_SESSION["nom"],"BULLETIN","Commentaire Resp. Unité Enseignement."); }

		
		$com=preg_replace('/\n/',' ',$com);
		$pdf->SetFillColor(220);
		$pdf->SetFont('Arial','I',10);
		$pdf->SetXY($x,$y);
		$pdf->MultiCell(170,5,"",1,'L',1);
		$pdf->SetXY($x,$y+1);
		$pdf->MultiCell(170,3,"$name_ue",0,'L',0);	

		if ($y >= 260 ) {
			$y=10;
			$pdf->AddPage();
		}

		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($x,$y+=5);
		$pdf->MultiCell(170,20,"",1,'L',0);
		$pdf->SetXY($x,$y+1);
		$pdf->MultiCell(170,3,"$com",0,'L',0);
		$y+=20;
		if ($y >= 260 ) {
			$y=10;
			$pdf->AddPage();
		}
	}	
}	
$fichier="data/pdf_bull/comp_".$_SESSION["id_pers"]."_".$tri.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;

?>
<br />
<center><font class=T2>Commentaire Enregistré</font></center>
<br /><br /><br />
</form>
<center>
<input type=button onclick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</center>
<br>

<!-- // fin form -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
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
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
