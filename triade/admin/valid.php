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
error_reporting(0);

include("./librairie_php/lib_licence.php");
include("./librairie_php/lib_netscape.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/valide.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade admin</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGVAL?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<table height=100% width=100% border=0>
<TR><TD align=top id=bordure>
<br>
<?php print LANGVAL7 ?>
<i><?php print WEBROOT."/".REPECOLE ?></i>
<?php print LANGVAL8 ?>
<br>
<br><hr width="80%"><br>
	<?php print LANGVAL2?>
	<br><br>
	<form method=post action="valid2.php" onsubmit="return valide()" name="formulaire">
	Nom du compte : <input type="text" name="nom"><br><br>
	Prénom du compte : <input type="text" name="prenom"><br><br>
	Mot de passe du compte : <input type="text" name="mdp"><br><br><br>
	<script language=JavaScript>buttonMagicSubmit("Enregistrer le compte","Submit"); //text,nomInput</script>
	</form>
<br><br><br>
<!-- // fin de la saisie -->
</TD></TR></TABLE>
</td></tr></table> <BR><BR><BR>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
