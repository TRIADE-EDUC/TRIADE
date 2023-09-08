<?php
session_start();
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) { set_time_limit(900); }
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
include_once("imprimeNote.php");
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
validerequete("7");
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<?php
$id_eleve=$_GET["id"];
$saisie_classe=$_GET["idclasse"];
?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  bgcolor="#0B3A0C"  height="400">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGPROF26 ?> <font id="color2" ><?php print recherche_eleve($id_eleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' valign='top' ><td>
<br>
<ul>
<font class='T2 shadow'>Choix des élèments à imprimer : </font>
<br />
<br />
<form method='post' >
<table>
<?php
if (isset($_POST['imp'])) {
	$checkedNotes=($_POST['notes'] == 1) ? "checked='checked'" : ""; 
	$checkedSavoiretre=($_POST['savoiretre'] == 1) ? "checked='checked'" : "";
	$checkedAbsrtd=($_POST['absrtd'] == 1) ? "checked='checked'" : "";
}else{
	$checkedNotes="checked='checked'";
	$checkedSavoiretre="checked='checked'";
	$checkedAbsrtd="checked='checked'";
}
?>
<tr><td align='right' ><input type='checkbox' value='1' name='notes' <?php print $checkedNotes ?>  onClick="document.getElementById('bouton').style.display='none'" > : </td><td>Notes</td></tr>
<tr><td align='right' ><input type='checkbox' value='1' name='savoiretre' <?php print $checkedSavoiretre ?> onClick="document.getElementById('bouton').style.display='none'"  > : </td><td>Savoir être </td></tr>
<tr><td align='right' ><input type='checkbox' value='1' name='absrtd' <?php print $checkedAbsrtd ?> onClick="document.getElementById('bouton').style.display='none'"  > : </td><td>Abs / Retard </td></tr>
<tr><td height='20' colspan='2' ></td></tr>
<tr><td colspan='2'><table>
	<tr>
	<td><script> buttonMagicSubmit3('<?php print VALIDER ?>','imp','')</script></td>
	<td><script> buttonMagic('Retour','<?php print $_SESSION["pageretour"] ?>','_self','','') </script></td>
	</tr>
	</table>

</table>
</form>
<?php
if (isset($_POST["imp"])) { 
	$anneeScolaire=anneeScolaire();
	$data=recupDateTrimIdclasse($saisie_classe,$anneeScolaire);
	// date_debut,date_fin,trim_choix,idclasse
	
	$dateDebut=dateForm($data[0][0]);

	if (($_POST['notes'] == 1) || ($_POST['absrtd'] == 1) || ($_POST['savoiretre'] == 1) ) {
		define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
		include_once('./librairie_pdf/fpdf/fpdf.php');
		include_once('./librairie_pdf/html2pdf.php');
		$pdfAll=new PDF();  // declaration du constructeur
	
		if ($_POST['notes'] == 1) 	 $pdfAll =& imprimeNote(&$pdfAll,$saisie_classe,$id_eleve,$dateDebut,$anneeScolaire);
		if ($_POST['absrtd'] == 1)	 $pdfAll =& imprimeABSRts(&$pdfAll,$saisie_classe,$id_eleve,$dateDebut);
		if ($_POST['savoiretre'] == 1) 	 $pdfAll =& imprimeSavoirEtre(&$pdfAll,$saisie_classe,$id_eleve,$dateDebut,$anneeScolaire);
	

		$nomprenomeleve=recherche_eleve_nom($id_eleve)."_".recherche_eleve_prenom($id_eleve);
	        $nomprenomeleve=TextNoAccent($nomprenomeleve);
	        $nomprenomeleve=TextNoCarac($nomprenomeleve);
	        $nomprenomeleve=preg_replace('/\//',"_",$nomprenomeleve);
	        $nomprenomeleve=preg_replace('/ /',"_",$nomprenomeleve);
	        $fichier="./data/pdf_bull/".$nomprenomeleve.".pdf";
	        @unlink($fichier); // destruction avant creation
        	$pdfAll->output('F',$fichier);
		print "<input type='button' onclick=\"open('telecharger.php?fichier=$fichier&filename=$fichier','_blank','');\" class='BUTTON'  value='PDF Complet' id='bouton' />&nbsp;&nbsp;";
	}
}
?>
<br /><br /></ul></td></tr>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
print "</SCRIPT>";
endif ;
?>
<?php @Pgclose() ?>
</BODY>
</HTML>
