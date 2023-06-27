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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Code d'accès</font></b></td></tr>
<tr id='cadreCentral0' > <td valign=top>
<!-- // debut de la saisie -->
<form method="post" action="key2.php">
<TABLE  bordercolor="#000000" border=0 width=100%>
<TR><TD valign=top>
<font class=T2>
<BR>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="image/on1.gif" align=center width=8 height=8> <?php print LANGKEY1?>
<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="image/on1.gif" align=center width=8 height=8> <?php print LANGKEY2?>
<br><br>

<ul>
<?php
if (!file_exists("../common/config3.inc.php") ) { ?>
	<script language=JavaScript>buttonMagicSubmit("Enregistrer votre code","Submit"); //text,nomInput</script> 
<?php }else{ ?>
	<script language=JavaScript>buttonMagicSubmit("Renouveler un nouveau code","Submit"); //text,nomInput</script>
<?php } ?>
<br ><br /><br />
</ul>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="../image/commun/img_ssl.gif" align="center" /> <b>Pourquoi un code ?</b><br>
<br />
Ce code permet de limiter l'accès à certains modules aux membres de la Direction. 
Certains modules sont irréversibles. Une mauvaise manipulation peut entraîner la perte d'informations. 
</font>
<br /><br />

</form>
<br><br><br>
</td></tr></table>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
