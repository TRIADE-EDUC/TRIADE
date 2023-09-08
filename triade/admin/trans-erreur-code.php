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
include("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<title>Triade Admin</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Erreurs</font></b></td></tr>
<tr id='cadreCentral0'><td valign='top'>
<?php
include_once("../common/lib_patch.php");
include_once("../common/config2.inc.php");
$emailadministrateur=MAILADMIN;
if ($emailadministrateur == "") {
	$emailadministrateur="<font color='red'>NO EMAIL ADMINISTRATEUR (VOIR MODULE CONFIG GENERAL)</font>";
}
?>
<form method=post action="trans-erreur2.php" >
<!-- <form target='_blank' method=post action="http://www.triade-educ.com/admin_triade/recuperreur.php" > -->
&nbsp;&nbsp;<textarea name="message" cols=105 rows=20 readonly='readonly' style="display:none;" >
Email : <?php print $emailadministrateur."\n"; ?>
----------Information TRIADE------------------------------------
<?php 	readfile("../data/install_log/install.inc");
include_once("../librairie_php/lib_get_init.php");
$configSafeMode="PasOk";
$id=php_ini_get("safe_mode");
if ($id != 1) { $configSafeMode="OK"; }

$configRegisterGlobals="PasOk";
$id=php_ini_get("register_globals");
if ($id != 1) { $configRegisterGlobals="OK"; }

$configMagicQuotesGPC="PasOk";
$id=php_ini_get("magic_quotes_gpc");
if ($id == 1) { $configMagicQuotesGPC="OK"; }

?>
safe_mode : <?php print $configSafeMode."\n" ?>
register_globals : <?php print $configRegisterGlobals."\n" ?>
magic_quotes_gpc : <?php print $configMagicQuotesGPC."\n" ?>
Version en Cours : <?php print VERSION."\n"?>
Patch Version : <?php print VERSIONPATCH."\n" ?>


----------Information Erreur-----------------------------------
<?php 	readfile("../data/erreurs.log"); ?>
---------------------------------------------------------------
</textarea>


<div style="height:300px;border: 2px solid #CCCCCC; background-color: #FFFFEE; border-radius: 10px 10px 10px 10px; overflow:auto;padding:5px "   >
Email : <?php print $emailadministrateur ?><br>
<hr>
<u>Information TRIADE</u> : <br/><br/>
<?php 	
$mess=readfile("../data/install_log/install.inc");
print $mess;

include_once("../librairie_php/lib_get_init.php");
$configSafeMode="PasOk";
$id=php_ini_get("safe_mode");
if ($id != 1) { $configSafeMode="OK"; }

$configRegisterGlobals="PasOk";
$id=php_ini_get("register_globals");
if ($id != 1) { $configRegisterGlobals="OK"; }

$configMagicQuotesGPC="PasOk";
$id=php_ini_get("magic_quotes_gpc");
if ($id == 1) { $configMagicQuotesGPC="OK"; }

?>
<br>
safe_mode : <?php print $configSafeMode."\n" ?><br />
register_globals : <?php print $configRegisterGlobals."\n" ?><br />
magic_quotes_gpc : <?php print $configMagicQuotesGPC."\n" ?><br />
Version en Cours : <?php print VERSION."\n"?><br />
Patch Version : <?php print VERSIONPATCH."\n" ?><br />
<hr>
<br /><u>Informations Erreurs</u>:<br /><br />
<?php 	readfile("../data/erreurs.log"); ?>
</div>


<br><br>
<table border=0 align=center>
<tr><td>
<?php if (filesize("../data/erreurs.log") > 0) { ?>
 <script language=JavaScript>buttonMagicSubmit3("Envoyer vos alertes","trans-erreur2.php","_parent","",""); //text,nomInput</script>
<?php } ?>
</td></tr></table>
</form>
<br />
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
