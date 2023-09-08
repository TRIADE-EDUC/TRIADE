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
error_reporting(E_ALL ^ E_NOTICE);
include("./librairie_php/lib_licence.php"); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="../librairie_js/info-bulle.js"></script>
<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Configuration de Triade - Mail Administrateur</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
$blacklisteoui="";
$blacklistenon="";
$messinteroui="";
$messinternon="";
$messsysoui="";
$messsysnon="";

$fichier="../common/config4.inc.php";
if ( file_exists($fichier)) {
	include_once("../common/config4.inc.php");

	if (MAILBLACKLIST == "non") {$blacklistenon="checked";}
	if (MAILBLACKLIST == "oui") {$blacklisteoui="checked";}
	if (MAILMESSINTER == "oui") {$messinteroui="checked";}
	if (MAILMESSINTER == "non") {$messinternon="checked";}
	if (MAILMESSSYS == "oui") {$messsysoui="checked";}
	if (MAILMESSSYS == "non") {$messsysnon="checked";}



}

?>
<form name=formulaire  method=post action="configuration_mail2.php" >
<table border=0 align=center width=100% >

<tr><td colspan=2 >&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <b>Configuration Mail</b> </td></tr>
<tr height=30 align=right>
<td>Recevoir un mail suite à une entrée dans la black-list :  </td>
		<td align=left>
		<input type=radio <?php print $blacklisteoui ?> name=mailblacklist value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $blacklistenon ?> name=mailblacklist value="non" class=btradio1  > non
		</td>
</tr>


<tr height=30 align=right>
<td>Recevoir un mail suite à un message interne :  </td>
		<td align=left>
		<input type=radio <?php print $messinteroui ?> name=mailmessinter value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $messinternon ?> name=mailmessinter value="non" class=btradio1  > non
		</td>
</tr>

<tr height=30 align=right>
<td>Recevoir un mail suite à un message système :  </td>
		<td align=left>
		<input type=radio <?php print $messsysoui ?> name=mailmesssys value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $messsysnon ?> name=mailmesssys value="non" class=btradio1  > non
		</td>
</tr>

<tr><td colspan=2><br>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script>
<br><br>
</td></tr></table>

</td></tr></table>
</form>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
