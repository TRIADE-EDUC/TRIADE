<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuprof");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE29?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // debut form  -->
     <blockquote><BR>
		 <font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='anneeScolaire'  >
                 <?php
                 $anneeScolaire=$_COOKIE["anneeScolaire"];
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
		<br><br>
	       <font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe" onchange="this.form.submit()">
<?php
if ($_POST["saisie_classe"] > "0") {
	print "<option id='select1' value='".$_POST["saisie_classe"]."' >".chercheClasse_nom($_POST["saisie_classe"])."</option>";
}
?>
                                   <option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select> <BR><br>
<UL>
<?php
if ( (isset($_POST["consult"])) || (isset($_POST["saisie_classe"]) ) ) {
	$saisie_classe=$_POST["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);

	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
	$nomClasse=$cl;

	if( count($data) > 0 ) {
		$fic=$_POST["saisie_classe"];
		$fichierpdf="./data/pdf_certif/Classe_".suppCaracFichier($cl).".pdf";
		$cl=preg_replace("/'/"," ",$cl);
		$cl=preg_replace('/"/'," ",$cl);
		print "<script language=JavaScript>buttonMagic('Imprimer $cl','visu_pdf_prof.php?id=$fichierpdf','_blank','','');</script>";
		print "<script language=JavaScript>buttonMagic('Impression (3)','listingElevePdf.php?idClasse=$saisie_classe','_blank','','');</script>&nbsp;&nbsp;";
	
		define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
		include_once('./librairie_pdf/fpdf/fpdf.php');
		include_once('./librairie_pdf/html2pdf.php');
		include_once("librairie_php/timezone.php");

		require_once "./librairie_php/class.writeexcel_workbook.inc.php";
		require_once "./librairie_php/class.writeexcel_worksheet.inc.php";
		$fichierxls="./data/fichier_ASCII/export_classe_".$_SESSION["id_pers"].".xls";
		@unlink($fichier);
		$workbook = new writeexcel_workbook($fichierxls);
		$worksheet1 =& $workbook->addworksheet('Listing');
		$header =& $workbook->addformat();$header->set_color('white');
		$header->set_align('center');
		$header->set_align('vcenter');
		$header->set_pattern();
		$header->set_fg_color('blue');
		$center =& $workbook->addformat();
		$center->set_align('left');

		$worksheet1->set_selection('A0');
		$worksheet1->write(0, 0, "Nom", $header);
		$worksheet1->write(0, 1, utf8_decode("Prenom"), $header);
		$worksheet1->write(0, 2, "Classe", $header);
	
		$pdf=new PDF();  // declaration du constructeur
		$pdf->AddPage();

		$date=dateDMY();
		// insertion de la Annee SCOLAIRE
		$Pdate="En classe $cl -  $date - Année Scolaire : $anneeScolaire";
		$pdf->SetFont('Courier','',12);
		$xcoor0=10;
		$ycoor0=20;
		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->WriteHTML($Pdate);
		
		$xcoor0+=20;
		$ycoor0+=10;
		$j=0;
		for($i=0;$i<count($data);$i++) {

			if ($ii == 45) {
	                	$pdf->AddPage();
				$ii=0;
				$xcoor0=50;
				$ycoor0=20;
			}
			$ii++;

			$ycoor0+=5;
			$j++;
			$eleve=$j.") ".strtoupper($data[$i][2])." ".trunchaine(ucwords($data[$i][3]),30);
			$pdf->SetXY($xcoor0,$ycoor0);
			$pdf->WriteHTML($eleve);
			$worksheet1->write($i, 0,utf8_decode($data[$i][2]), $center);
			$worksheet1->write($i, 1,utf8_decode($data[$i][3]), $center);
			$worksheet1->write($i, 2,utf8_decode($nomClasse), $center);
		}
		if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
		$pdf->output('F',$fichierpdf);
	}
}

$workbook->close();

print "<script language=JavaScript>buttonMagic('Export Excel','visu_document.php?fichier=$fichierxls','_blank','','');</script>&nbsp;&nbsp;";

?>
</UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>

<!-- // fin form -->
 </td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
