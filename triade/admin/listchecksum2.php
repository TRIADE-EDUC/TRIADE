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
<title>Triade</title>
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Vérification et optimisation de la Base</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br>
<?php
include_once("./librairie_php/db_triade_admin.php");
if (LAN == "oui") {

	$list=$_POST["liste"];

	print "<iframe width='468' height='80' name='TRIADE-SUPPORT' src='vide.html' MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe>";
	print "<form method='post' action='https://support.triade-educ.org/support/recup_liste_check2.php' target='TRIADE-SUPPORT' name='formulaire'>";
	print "<input type=hidden name='check-list' value=\"$list\">";
	print "<input type=hidden name='inc' value=\"".GRAPH."\">";
	print "</form>";
	print "<script>document.formulaire.submit()</script>";
?>
	<font class="T2">
	<ul>L'Equipe Triade.</ul>
	</font>
	<br /><br />
<?php
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR2."</i></center>";
}
?>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>alert("En cours de chargement, veuillez patientez quelques minutes... ")</script>
</body>
</html>
