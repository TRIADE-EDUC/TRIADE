<?php
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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Accès IMPOSSIBLE</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/langue-text-fr.php");
?>
<BR><BR><BR><BR><br>
<center>
<table width="57%" border="0" align="center" >
<tr >
<td colspan=2>
<div align="center"><b><font color="red" class=T2 ><?php print LANGacce_ref2  ?><br>
</font></b>
<p><font color="#000000" class=T2><?php print LANGacce_ref3  ?></p>
<BR>
<form method="post" action="index.html" >
<table align="center"><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGTCONNEXION ?>","create"); </script></td></tr>
</form>
</font>
</div>
</td>
</tr>
</table>
<br><br><br><br>
<?php  print LANGPIEDPAGE ?>
<BR><BR>
</center></BODY></HTML>
