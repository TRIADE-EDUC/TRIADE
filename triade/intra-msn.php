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
		<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
	</head>

	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

	<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
	<?php 
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade.php");
	?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Intra-MSN" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td ><br />
	<!-- // fin  -->

	<?php
	$validemodule=false;
	if (file_exists("./common/config-messenger.php")) {
		include_once("./common/config-messenger.php");
		if ( ( (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menuprof")) && (MESSENGERPERS == "oui")) || (($_SESSION["membre"] == "menueleve") && (MESSENGERELEV == "oui")) ) { 
		$validemodule=true;
		?>
		<font class='T2'><img src="./image/commun/personne.gif" align='left'/> &nbsp;&nbsp; TRIADE vous propose un service MSN local, lié aux seuls utilisateurs TRIADE de votre établissement.<br /><br />
		&nbsp;&nbsp;Pour bénéficier de cet outil, vous devez tout d'abord télécharger le fichier ZIP et
 		le dézipper sur votre ordinateur. <br /><br />
		&nbsp;&nbsp;Une fois dézippé, vous trouverez le répertoire "IntraMessengerClient", puis dans ce répertoire, 
		le fichier "IntraMessenger.exe"  à exécuter.  (double-cliquez sur le fichier) <br /><br />
		&nbsp;&nbsp;<u>Votre login est</u> : <b><?php print strtolower($_SESSION["nom"]).".".strtolower($_SESSION["prenom"]) ?></b> <br>
		&nbsp;&nbsp;<u>Votre mot de passe est</u> : <i>celui de votre compte TRIADE</i><br /><br />
		&nbsp;&nbsp;Toute la communauté de votre établissement <i>(<u>et seulement votre établissement</u>)</i> sera alors disponible directement via Intra-MSN 
		<br /><br /><center><a href='./intra-msn-download.php' ><img src="image/commun/download2.png" border='0' alt='' /></a></center></font>
		
		<br><br>
	<?php
		}

	}

	if ($validemodule == false) {
		print "<center><font color=red class='T2' >".LANGMESS37.".</font></center><br><br>";
	}

	$fichier="./messenger/im_setup.reg";
	@unlink($fichier);
	$fd=fopen($fichier,"a");
	$texte="
Windows Registry Editor Version 5.00

[HKEY_CURRENT_USER\Software\THe UDS\IM]
\"url\"=\"http://".$_SERVER['SERVER_NAME']."/".ECOLE."/messenger/\"
\"lang\"=\"FR\"
		";
       	fwrite($fd,$texte);
	fclose($fd);
	




	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
		print "</SCRIPT>";
	}
	Pgclose();
?>
</BODY></HTML>
