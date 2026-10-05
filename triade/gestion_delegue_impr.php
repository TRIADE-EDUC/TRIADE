<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
error_reporting(0);
include("./common/config.inc.php");
include("./librairie_php/db_triade.php");
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
$Spid=$mySession["Spid"];
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<style>
.na-card  { background:#fff !important; border:1px solid #c5cae9 !important; border-radius:8px !important; padding:14px 16px !important; margin:10px 0 10px !important; }
.na-row   { display:flex !important; align-items:center !important; margin-bottom:8px !important; gap:8px !important; flex-wrap:wrap !important; }
.na-lbl   { font-size:12px !important; font-weight:600 !important; color:#333 !important; min-width:140px !important; flex-shrink:0 !important; }
.na-foot  { margin-top:8px !important; overflow:hidden !important; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
<?php print "Gestion délégués / Impression" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td>
<form method="post" name="formulaire" action="gestion_delegue_impr.php">
<?php
validerequete("3");
$cnx=cnx();

$data=affClasse();
$j=0;
?>
<div class="na-card">
<table width="100%">
<tr>
<?php
for($i=0;$i<countTriade($data);$i++) {
	$j++;
	if (isset($_POST["label"])) {
		foreach($_POST["label"] as $key=>$value) {
			if ($value == $data[$i][0]) {
				$checked="checked='checked'";
				break;
			}
		}
	}
	print "<td><input type='checkbox' name='label[]' value='".$data[$i][0]."' $checked /> ".$data[$i][1]."</td>";
	if ($j == 4) { print "</tr><tr>"; $j=0; }
	$checked="";
}
?>
</tr>
</table>
</div>
<div class="na-foot">
&nbsp;&nbsp;&nbsp;<input type='submit' class="button" value="Imprimer" name="imprime" />
<?php if ($_SESSION["membre"] != "menuprof") { ?>
&nbsp;&nbsp;&nbsp;<input type='button' class="button" value="Retour" name="retour" onClick="open('gestion_delegue.php','_self','')" />
<?php } ?>

<?php
if (isset($_POST["imprime"])) {

	$anneescolaire=anneescolaire();

	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');

	$pdf=new PDF();
	$pdf->AddPage();
	$pdf->SetTitle(utf8_decode("Gestion des délégués"));
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Gestion des délégués");
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com");

	$X="10";
	$Y="10";

	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(200,8,"Liste des délégues pour l'année scolaire $anneescolaire",0,'L',0);
	$Y+=15;

	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(30,8,"Classe",1,'C',0);
	$pdf->SetXY($X+=30,$Y);
	$pdf->MultiCell(35,8,"1er délégué",1,'C',0);
	$pdf->SetXY($X+=35,$Y);
	$pdf->MultiCell(35,8,"2eme délégué",1,'C',0);
	$pdf->SetXY($X+=35,$Y);
	$pdf->MultiCell(45,8,"1er parent délégué",1,'C',0);
	$pdf->SetXY($X+=45,$Y);
	$pdf->MultiCell(45,8,"2eme parent délégué",1,'C',0);

	$Y+=8;

	$pdf->SetFont('Arial','',6);
	$tab=$_POST["label"];
	foreach($tab as $key=>$idclasse) {

		$dataaff=aff_delegue($idclasse);
		$nomclasse=chercheClasse_nom($dataaff[0][0]);
		if ($nomclasse == "") continue;
		$nomparent1=ucwords(recherche_personne_nom($dataaff[0][1],'ELE'));
		$nomparent2=ucwords(recherche_personne_nom($dataaff[0][2],'ELE'));
		$eleve1=ucwords(recherche_personne_prenom($dataaff[0][3],'ELE'))." ".ucwords(recherche_personne_nom($dataaff[0][3],'ELE'));
		$eleve2=ucwords(recherche_personne_prenom($dataaff[0][4],'ELE'))." ".ucwords(recherche_personne_nom($dataaff[0][4],'ELE'));
		$X=10;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,5,"$nomclasse",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		$pdf->MultiCell(35,5,"$eleve1",1,'C',0);
		$pdf->SetXY($X+=35,$Y);
		$pdf->MultiCell(35,5,"$eleve2",1,'C',0);
		$pdf->SetXY($X+=35,$Y);
		$pdf->MultiCell(45,5,"Parent de $nomparent1",1,'C',0);
		$pdf->SetXY($X+=45,$Y);
		$pdf->MultiCell(45,5,"Parent de $nomparent2",1,'C',0);

		$Y+=5;
	}

	$classe_nom=preg_replace('/\//',"_",$classe_nom);
	$fichier="./data/pdf_bull/delegue.pdf";
	@unlink($fichier);
	$pdf->output('F',$fichier);

	print "&nbsp;&nbsp;&nbsp;<input type=button onclick=\"open('telecharger.php?fichier=$fichier','_blank','');\" value=\"Récupération du PDF\" STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' />";
}
?>
</div>
</form>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
</BODY>
</HTML>
<?php @Pgclose() ?>
