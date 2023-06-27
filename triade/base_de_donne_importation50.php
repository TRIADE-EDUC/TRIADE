<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<!-- /************************************************************
Last updated: 13.08.2004    par Taesch  Eric
*************************************************************/ -->

<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGbasededoni31 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top>
<br />
<ul><font class="T2"><?php print LANGbasededoni32 ?></font></ul>
<br />
<ul><ul>
<table height=150 bgcolor="#FFFFFF" border=1 bordercolor="#000000" >
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td bordercolor="#FFFFFF" >&nbsp; <img src="./image/commun/on1.gif" width="8" height="8"> <?php print LANGbasededoni33 ?></td>
    <td bordercolor="#FFFFFF" >&nbsp; <input type=button onclick="open('./base_de_donne_importation51.php','_parent','')" class="button" value="Cliquez ici" > &nbsp;</td>
</tr>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td bordercolor="#FFFFFF" >&nbsp; <img src="./image/commun/on1.gif" width="8" height="8"> <?php print LANGbasededoni34 ?></td>
    <td bordercolor="#FFFFFF" >&nbsp; <input type=button onclick="open('./base_de_donne_importation52.php?id=prof','_parent','')" class="button" value="Cliquez ici" > &nbsp;</td>
</tr>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td bordercolor="#FFFFFF" >&nbsp; <img src="./image/commun/on1.gif" width="8" height="8"> <?php print LANGbasededoni35 ?></td>
    <td bordercolor="#FFFFFF" >&nbsp; <input type=button onclick="open('./base_de_donne_importation52.php?id=scolaire','_parent','')" class="button" value="Cliquez ici" > &nbsp;</td>
</tr>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td bordercolor="#FFFFFF" >&nbsp; <img src="./image/commun/on1.gif" width="8" height="8"> <?php print LANGbasededoni36 ?> </td>
    <td bordercolor="#FFFFFF" >&nbsp; <input type=button onclick="open('./base_de_donne_importation52.php?id=administration','_parent','')" class="button" value="Cliquez ici" > &nbsp;</td>
</tr>
</table>
</ul></ul>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
</BODY></HTML>
