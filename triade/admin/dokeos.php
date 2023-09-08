<?php
session_start();
error_reporting(0);
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
<?php include("./librairie_php/lib_licence.php"); ?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >E-Learning - DOKEOS</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<font class=T2>
<br>
<form method=post action="../dokeos/index.php?admin" target='_blank'>
<table><tr><td>
<font class=T2>&nbsp;&nbsp;Accès à l'administration de Dokeos  : </font></td><td>
<script language=JavaScript>buttonMagicSubmit("Accès","create"); //text,nomInput</script>
</td></tr>
</table>
</form>

<?php 

include_once("../common/config.inc.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config2.inc.php");

if (!is_dir("../data/moodledata")) mkdir("../data/moodledata");
htaccess("../data/moodledata/");

if (isset($_POST["supp"])) {
	recursive_delete("../dokeos/main/install/");
}

$host=HOST;
$user=USER;
$pass=PWD;
$db=DB;
$type=TYPETABLE;

$cnx=cnx();
verifMoodle($host,$user,$pass,$db,$type);
Pgclose();

if (file_exists("../dokeos/main/install/index.php")) {
	print "<form method=post action='../dokeos/main/install/index.php' target='_blank'>";
	print "<table>";
	print "<tr><td><font class='T2'>&nbsp;&nbsp;Mise à jour de Dokeos :</font></td><td><script language=JavaScript>buttonMagicSubmit(\"Accès\",\"create\");</script></td></tr>";
	print "</table>";
	print "</form>";
	print "<form method=post action='dokeos.php' >";
	print "<table>";
	print "<tr><td><font class='T2' id='color2'>&nbsp;&nbsp; <b>APRES LA MISE A JOUR</b></td></tr>";
	print "<tr><td><font class='T2' >&nbsp;&nbsp; SUPPRESSION DE L'INSTALLEUR :</font></td><td><script language=JavaScript>buttonMagicSubmit(\"Accès\",\"supp\");</script></td></tr>";
	print "</table>";
	print "</form>";

}

?>
<br>
<form method=post action="../moodle/login/index.php?admin" target='_blank'>
<table><tr><td>
<font class=T2>&nbsp;&nbsp;Accès à l'administration de Moodle  : </font></td><td>
<script language=JavaScript>buttonMagicSubmit("Accès","create"); //text,nomInput</script>
</td><td><script language=JavaScript> buttonMagic("Mot de passe","pass_admin_moodle.php","pass",'width=400,height=200','')</script></td>

</tr>

</table>
</form>

</td></tr></table>
<br><br>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >TRIADE-DOKEOS - Enregistrement</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
if (LAN == "oui") {
	print "<iframe src='https://support.triade-educ.org/support/dokeos/index.php?graph=".GRAPH."' width=100% height=400 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe>";
}else{
	print "<br><center><font class=T2>Réseau Internet non disponible pour ce module.</font> <br><br> <i>Consulter le module de Configuration pour activer le réseau.</i></center>";
}
?>
</td></tr></table>

<!-- // fin de la saisie -->


<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
