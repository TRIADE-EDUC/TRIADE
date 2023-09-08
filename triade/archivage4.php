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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<?php
if (empty($_SESSION["adminplus"])) {
	print "<script>";
	print "location.href='./base_de_donne_importation.php'";
	print "</script>";
	exit;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Archivage des données"?></font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br /><ul>
<table border=0 >
<tr><td><img src="./image/commun/kitwarning.gif" /></td>
    <td width=300><font color=red> Ne sont archivés que les notes, disciplines, entretiens, absences et retards, affectations, cahier de textes (Pour les bulletins vous devez les sauvegarder sur votre ordinateur.)</font></td></tr>
</table>
</ul>
<?php 

$annee=date("Y")-1;
$annee=$annee."-".date("Y");

if (!file_exists("./data/archive/$annee.sqlite")) {
	$bouton="Effectuer";
	$attention="";
}else{
	$bouton="OUI remplacer archive";
	$attention="<ul><font class='T2' color='red'>Attention !! <br /> Une archive est déjà réalisé pour cette même année. <br /> Souhaitez vous remplacer l'archive ?</font></ul> ";
}

?>
<center><font class="T2">Effectuer un archivage pour l'année scolaire (<?php print $annee ?>)</font></center>
<?php
		print "<form method='post' action='archivage44.php' >";
		print $attention;
		print "<table align='center'>";
?>
		<tr><td><script language=JavaScript>buttonMagicSubmit3("<?php print $bouton ?>",'etape1',"onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script> 
<?php
		print "</td></tr>";
		print "</table>";
		print "<input type='hidden' name='annee' value='$annee' >";
		print "</form>";

?>
<br />
<i>ATTENTION, la procédure peut durer plus de 20 minutes en fonction de la puissance du serveur !! </i> 
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
<?php attente(); ?>
</BODY></HTML>
