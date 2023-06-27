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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<table width="100%" border="1" height="84" bgcolor="#006699" bordercolor="#CCCCFF" align="center">
<tr>
<td height="12" colspan="9">
<div align="center"><font color="#FFFFFF"><b><?php print LANGaccrob11?></b></font></div>
</td>
</tr>
<tr>
<td bgcolor="#CCCCFF" height="26" width="44%">
<p align="right">Taille : </p>
</td>
<td bgcolor="#FFFFFF" height="26" colspan="8" width="56%">
<div align="left"><font color="#006699"><b><?php print LANGaccrob2 ?></b></font></div>
</td>
</tr>
<tr>
<td bgcolor="#CCCCFF" height="2" width="44%">
<div align="right"><?php print LANGaccrob3?></div>
</td>
<td bgcolor="#FFFFFF" height="2" colspan="8" width="56%">
<div align="left"><font color="#006699"><b><?php print LANGaccrob4 ?>
</b><b><br>
<?php print LANGaccrob5 ?></b>
<br><b>
<?php print LANGaccrob6 ?></b></font> </div>
</td>
</tr>
</table>
<br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGTELECHARGER ?>","ftp://www.triade-educ.com/logiciels/AdbeRdr810_fr_FR.exe","acro","width=5,height=5","");</script> 
<script language=JavaScript>buttonMagicFermeture();</script> &nbsp;&nbsp;
</td></tr></table>
</body>
</html>
