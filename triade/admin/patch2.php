<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET
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
include_once("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Installation des patches</font></b></td></tr>
<tr id='cadreCentral0' ><td ><p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<ul><br>
<font color=red><b>INFORMATION.</b></font><br>
<i>L'installation de patch ne peut être réalisé qu'avec des patchs provenant du <u>Support Triade</u>, tout patch non conforme
peut provoquer la perte des données de Triade.</font></i><br><br>
<?php

include_once("librairie_php/db_triade_admin.php");
$cnx=cnx();

$fichier_orig="./patch_ftp/".$_POST["patch_ftp"];

if (file_exists($fichier_orig)) {
	$fichier=preg_replace('/\.zip/',"",$_POST["patch_ftp"]);
	rename($fichier_orig,"./patch.zip");
}
$md5fichier=md5_file("./patch.zip");

include_once('./librairie_php/pclzip.lib.php');
$archive = new PclZip('patch.zip');
	
if ($archive->extract(PCLZIP_OPT_PATH, '../data/patch') == 0) {
die(print "<a href='javascript:history.go(0)'><b>Cliquez ici pour réactualiser le patch</a></b>"); }

$fichier_info="../data/patch/$fichier/LISEZMOI";
if (file_exists($fichier_info)) {
	$fic=fopen($fichier_info,"r");
	$donnee=fread($fic,900000);
	$donnee=nl2br($donnee);
	print "<font class='T2'>$donnee</font>";
	fclose($fichier);
}

		
$fichier_info="../data/patch/$fichier/PATCHREQ";
if (file_exists($fichier_info)) {
	$fic=fopen($fichier_info,"r");
	$patchrequis=fread($fic,900000);
	$patchrequis=trim($patchrequis);
	fclose($fichier);
}

if (isset($patchrequis)) {
	if (file_exists("../common/lib_patch.php")) include_once("../common/lib_patch.php");
	if ($patchrequis != "aucun") {
		if ($patchrequis != VERSIONPATCH) {
			print "<br><font class='T2' color='red'><b>ATTENTION, VOUS DEVEZ INSTALLER <br> LE PATCH $patchrequis AVANT !!! ".VERSIONPATCH."</b><br /></font>";
		}
	}
}

?>
	<br><table>
	<?php if (!preg_match('/^000-/',$fichier)) { ?>
		<tr><td>
		<form method="post" action="https://support.triade-educ.org/support/verifpatch.php" target='_blank' >
		<input type=hidden value="<?php print $fichier ?>" name="patch">
		<input type=hidden value="<?php print $md5fichier ?>" name="md5sum">
		<input type=submit  class="bouton2"  value="Vérifier la validité du patch" >
		</form>
		</td><td>
	<?php } ?>

        <form method="post" action="patch3.php"   name="formulaire" onSubmit="document.formulaire.envoi.disabled=true" >
		<input type=hidden value="<?php print $fichier ?>" name="patch">
		<input type=hidden value="<?php print $donnee ?>" name="info">
		<input type=hidden value="<?php print md5_file("patch.zip"); ?>" name="md5md5">
		<input type=submit class="bouton2"  value="Continuer l'installe" name="envoi"  >
		</form>
	</td></tr></table>
	<br><br><br></ul>
		<?php
		
	

Pgclose($cnx);

?>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
