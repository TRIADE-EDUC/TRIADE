<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom] ") ?></title>
		<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
		<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
		<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
		<script type="text/javascript" src="./librairie_js/function.js"></script>
		<script type="text/javascript" src="./librairie_js/menu-tab.js"></script>
		<script type="text/javascript" src="./librairie_js/ajax-menu-tab.js"></script>
		<script type="text/javascript" src="./librairie_js/prototype.js"></script>
		<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
		<script type="text/javascript" src="./tinymce/tinymce.min.js"></script>
	</head>

	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

	<?php 
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();
	?>
	
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "L'application TRIADE-PHONE" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td >
	<!-- // fin  -->
<img src='./image/commun/triade-phone.png' width='100%' /><br>

<ul>&nbsp;&nbsp;<a href="#" onclick="open('https://play.google.com/store/apps/details?id=com.triade.educ.phone','_blank','')"><img src="https://www.triade-educ.org/fr/image/GooglePlayLogo.png" title="Télécharger via Google Play"></a></ul>

<?php
include_once('./common/config2.inc.php');
$url=URLSITE;
?>
<br><br>
<center>
<table border='0' width='50%' >
<tr><td width='80%' align='center' ><font class=T2>Voici le QRCode permettant la configuration de votre application TRIADE-PHONE en fonction de votre &eacute;tablissement.</font>
<?php
// protocole,instance,compte,nom,prenom,flag
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['REQUEST_URI']), '/') . '/';
$adresseurl = $host.$path;
$compte=type_compte_triade_phone($_SESSION["membre"]);
$flag="QR";
$nom=$_SESSION["nom"];
$prenom=$_SESSION["prenom"];
$text="$protocol,$adresseurl,$compte,$nom,$prenom,$flag";
?>
</td><td style='padding:10px'><img id='codeim0' src="./codebar/image.php?code=qcode&text=<?php print $text ?>" width='180%' style='box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);'  /></td></tr></table>
<?php // print $text ?>
</center>
<br><br>

	<?php
	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
		print "</SCRIPT>";
	}
	Pgclose();
?>
</BODY></HTML>
