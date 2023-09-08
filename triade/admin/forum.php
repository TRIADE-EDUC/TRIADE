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
if (isset($_POST["saisieecole"])) {
	if ($saisieecole != '0' ) {
	print "<script language=JavaScript>open('../forum/admin.php?repforum=".$_POST["repforum"]."','forum','width=800,height=600,scrollbars=yes,resizable=yes');</script>";
	}
}
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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Forums</font></b></td></tr>
<tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<center><br />
<form method="POST">
<table border=0 align=center>
<tr><td valign=top>
<img src="../image/commun/img_news.gif" align="center" /> <font class=T2> Forum de l'établissement : </font>
<select name="repforum" >
<option value="menuadmin" id="select1"> Forum Direction </option>
<option value="menuscolaire" id="select1"> Forum Vie Scolaire </option>
<option value="menuprof" id="select1"> Forum Enseignant </option>
<option value="menueleve" id="select1"> Forum Elève </option>
<option value="menuparent" id="select1"> Forum Parent d'élève </option>
</select>
<input type=hidden name='saisieecole' value='<?php print REPECOLE?>'>
</td><td valign=top>
<script language=JavaScript>buttonMagicSubmit("Consulter","rien"); //text,nomInput</script></td></tr></table>
</form>
</center>
<!-- // fin de la saisie -->
</blockquote> </td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
