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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Export des données vers Photographe de France"?></font></b></td>
</tr>
<tr id='cadreCentral0'  >
<td >
<?php
if (LAN == "oui") {
?>
  &nbsp;&nbsp;&nbsp;Triade vous propose en partenariat avec Photographe de France, d'importer automatiquement les trombinoscopes de
vos élèves. <br /><br />



<form method="post" action="trombi-export-pdf2.php">
	<table border=0 align=center>
	<tr>
		<td align="right"><font class="T2">Identifiant Photographe de France : </td>
		<td><input type=text name="identPDF" size=30 />
	</tr>
	<tr>
		<td align="right"><font class="T2">Email administrateur Triade  : </td>
		<td><input type=text name="email" size=30 />
	</tr>		
	<tr><td colspan="2" align=center><br /><table><tr><td><script language=JavaScript>buttonMagicSubmit('Envoyer','create'); //text,nomInput</script></td></tr></table></td></tr>
	
	</table>
</form>

	<i>Ce module permet de transférer les informations au logiciel WellPhoto  afin que le photographe puisse obtenir la liste des élèves.</i> 
	


<?php
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR3."</i></center>";
}
?>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
