<?php
session_start();
exit;
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
error_reporting(0);
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >WebRadio</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
$webradioactiveoui="";
$webradioactivenon="";
$webradiotype1="";
$webradiotype2="";
$webradiourl="http://";
$webradioport="";
$webradiomontage="";

$fichier="../common/config-webradio.php";
if ( file_exists($fichier)) {
	include_once("../common/config-webradio.php");

	if (WEBRADIOACTIVE == "non") {$webradioactivenon="checked";}
	if (WEBRADIOACTIVE == "oui") {$webradioactiveoui="checked";}
	if (WEBRADIOTYPE == "shoutcast") {$webradiotype1="checked";}
	if (WEBRADIOTYPE == "icecast") {$webradiotype2="checked";}
	if (WEBRADIOURL != "") {$webradiourl=WEBRADIOURL; }
	if (WEBRADIOPORT != "") {$webradioport=WEBRADIOPORT; }
	if (WEBRADIOMONTAGE != "") {$webradiomontage=WEBRADIOMONTAGE; }
	if (WEBRADIOTITLE != "") {$webradiotitre=WEBRADIOTITLE; }
	if (($webradiotitre == "WEBRADIOTITLE") || ($webradiotitre == "")) { $webradiotitre="Web-Radio de votre établissement scolaire!!"; }

}

?>
<form name=formulaire  method=post action="config_webradio2.php" >
<table border=0 align=center width=100% >

<tr><td colspan=2 >&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <b>Configuration</b> </td></tr>
<tr height=30 align=right>
<td>Activation :  </td>
		<td align=left>
		<input type=radio <?php print $webradioactiveoui ?> name="webradioactive" value="oui" class=btradio1  > oui
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $webradioactivenon ?> name="webradioactive" value="non" class=btradio1  > non
		</td>
</tr>


<tr height=30 align=right>
<td>Type de radio :  </td>
		<td align=left>
		<input type=radio <?php print $webradiotype1 ?> name=type value="shoutcast" class=btradio1  > Shoutcast
		&nbsp;&nbsp;&nbsp;
		<input type=radio <?php print $webradiotype2 ?> name=type value="icecast" class=btradio1  > ICecast
		</td>
</tr>

<tr height=30 align=right>
<td>Adresse webRadio :  </td>
		<td align=left>
		<input type=text value="<?php print $webradiourl ?>" name=webradiourl class=button size=40 > 
		</td>
</tr>


<tr height=30 align=right>
<td>Numéro de port :  </td>
		<td align=left>
		<input type=text value="<?php print $webradioport ?>" name=webradioport class=button  > 
		</td>
</tr>



<tr height=30 align=right>
<td>Montage (ICecast Uniquement) :  </td>
		<td align=left>
		<input type=text value="<?php print $webradiomontage ?>" name=webradiomontage class=button  > 
		</td>
</tr>

<tr height=30 align=right>
<td>Titre de votre WebRadio :  </td>
		<td align=left>
		<input type=text value="<?php print $webradiotitre ?>" name=webradiotitre class=button  size='40' maxlength='50' > 
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
