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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa du Professeur Principal." ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
$idclasse=$_POST["saisie_classe"];
$tri=$_POST["saisie_trimestre"];
$nb=$_POST["saisie_nb"];
$type_bulletin=$_POST["type_bulletin"];
$anneeScolaire=$_COOKIE["anneeScolaire"];

verif_profp_class($_SESSION["id_pers"],$idclasse);

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

$pdf->SetFont('Arial','',10);
$pdf->SetXY(20,10);
$pdf->MultiCell(200,5,"Classe : ".chercheClasse_nom($idclasse)." / $tri",0,'L',0);

// Debut création PDF
// mise en place des coordonnées
$pdf->SetFont('Arial','',10);
$x=20;
$y=0;
$j=0;
for($i=0;$i<$nb;$i++) {
	$com=$_POST["comm"][$i];
	$eleveid=$_POST["eleveid"][$i];

	$leap_felicitation=$_POST["leap_felicitation_$eleveid"];
	$leap_encouragement=$_POST["leap_encouragement_$eleveid"];
	$leap_megcomp=$_POST["leap_megcomp_$eleveid"];
	$leap_megtrav=$_POST["leap_megtrav_$eleveid"];
	
	$com=addslashes($com);

	$cr=create_comm_profp_bull($eleveid,$tri,$com,$anneeScolaire,$type_bulletin);
	if ($cr) {
		history_cmd($_SESSION["nom"],"BULLETIN","Commentaire Prof Principal. $anneeScolaire");
		if ($type_bulletin == "leap") {
			$cr=modifLeap($eleveid,$tri,$com,$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav);
			history_cmd($_SESSION["nom"],"BULLETIN","Modif   Prof Principal. $anneeScolaire");
		}
	}

	$nom=recherche_eleve_nom($eleveid);
	$prenom=recherche_eleve_prenom($eleveid);

	$pdf->SetXY($x,$y+=25);
	$pdf->MultiCell(200,5,"$nom $prenom",0,'L',0);
	
	if ($type_bulletin == "leap") {
		$pdf->SetFillColor(220);
		//  Félicitations    Encour.    MEG Comp.    MEG Trav
		$pdf->SetXY($x+60,$y-1);
		$check=0;
		if ($leap_felicitation == 1) $check=1;
		$pdf->MultiCell(5,5,"",1,'L',$check);
		$pdf->SetXY($x+60+6,$y-1);
		$pdf->MultiCell(22,5,"Félicitations",0,'L',0);

		$pdf->SetXY($x+60+22+6,$y-1);
		$check=0;
		if ($leap_encouragement == 1) $check=1;
		$pdf->MultiCell(5,5,"",1,'L',$check);
		$pdf->SetXY($x+60+22+6+6,$y-1);
		$pdf->MultiCell(20,5,"Encour.",0,'L',0);

		$pdf->SetXY($x+60+22+6+20,$y-1);
		$check=0;
		if ($leap_megcomp == 1) $check=1;
		$pdf->MultiCell(5,5,"",1,'L',$check);
		$pdf->SetXY($x+60+22+6+6+20,$y-1);
		$pdf->MultiCell(27,5,"MEG Comp.",0,'L',0);


		$pdf->SetXY($x+60+22+6+20+27,$y-1);
		$check=0;
		if ($leap_megtrav == 1) $check=1;
		$pdf->MultiCell(5,5,"",1,'L',$check);
		$pdf->SetXY($x+60+22+6+6+20+27,$y-1);
		$pdf->MultiCell(20,5,"MEG Trav.",0,'L',0);

	}

	$pdf->SetFillColor(255);
	$pdf->SetXY($x,$y+=5);
	$com=preg_replace('/\n/',' ',$com);
	$pdf->MultiCell(170,20,"",1,'L',0);
	$pdf->SetXY($x,$y+2);
	$com=preg_replace('/\n/'," ",$com);
	$com=stripslashes($com);
	$pdf->MultiCell(170,3,"$com",0,'L',0);

	if ($j == 7) {
		$j=0;
		$y=0;
		$pdf->AddPage();
	}else{
		$j++;
	}
	


}	
$fichier="data/pdf_bull/comp_".$_SESSION["id_pers"]."_".$tri.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;

?>
<br />
<center><font class=T2>Bulletin Enregistré</font></center>
<br /><br />
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>

</form>
<center>
<table><tr>
<td><script>buttonMagicRetour('profp2.php','_self')</script></td><td><input type=button onclick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td></tr></table>
</center>
<br>

<!-- // fin form -->
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
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
