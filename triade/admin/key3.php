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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Enregistrement Triade</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top>
<TABLE  width=100%>
<TR><TD valign=top>
<?php
include_once("./librairie_php/db_triade_admin.php");
function erreur_aff() {
	print "<br>";
	print "<font class=T2>";
	print "<ul> <b><font color=red>Erreur d'enregistrement !!! </font></b>";
	print "<br><br><br>";
	print "<font class=T2>Service Triade";
	print "</font>";
	print "<br><br>";
	print "</ul>";
}
if ( (trim($_POST["pw1"]) != "" ) && (trim($_POST["pw2"]) != "" ) && (trim($_POST["pw3"]) != "" ) ) {

	$ok=verifkey($_POST["pw1"],$_POST["pw2"],$_POST["pw3"]);
	if ($ok) {
		$mp1=crypt(md5(trim($_POST["pw1"])),"T2");
		$mp2=crypt(md5(trim($_POST["pw2"])),"T2");
		$mp3=crypt(md5(trim($_POST["pw3"])),"T2");
		@unlink("../librairie_php/config2-inc.php");
		//---------------------------------------------
		$fichier=fopen("../common/config3.inc.php","w");
		$donne="<?php\n";
		$donne.="define(\"PASS1\",\"".$mp1."\");\n";
		$donne.="define(\"PASS2\",\"".$mp2."\");\n";
		$donne.="define(\"PASS3\",\"".$mp3."\");\n";
		$donne.="?>\n";
		fwrite($fichier,"$donne");
		fclose($fichier);
		?>
		<br>
		<font class=T2>
		<ul><b> Enregistrement Terminé.</b> <br>
		<br><br><br>
		Service Triade
		</font>
		<br><br>
		</ul>
<?php
	}else{
		erreur_aff();
	}
}else{
	erreur_aff();
}
?>
</td></tr></table>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
