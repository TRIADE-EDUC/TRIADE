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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGIMP1 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<ul><font class=T2><?php print LANGIMP2?></font></ul>
<br />
<ul><ul>
<font class=T2>
<!-- <img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation01.php"><?php print LANGIMP3?>, (txt ou csv) </A> <br /> -->
<br />
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation20.php"><?php print LANGMESS225.", (xls - office 2003)"?></A> <br />
<br />
<!-- <img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation50.php"><s><?php print LANGMESS226 ?></s></A> <br /> -->
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation700.php" ><?php print LANGMESS226 ?> (SIECLE absences)</A> <br />
<br />
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation200.php"><?php print "SIECLE, (xls - office 2003)"?></A> <br />
<br />
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation400.php"><?php print "CTI, (xls - office 2003)"?></A> <br />
<br>
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation600.php"><?php print "STSweb, (XML)"?></A> <br />
<br />
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation800.php"><?php print LANGMESS227.", (xls - office 2003)"?></A> <br />
<br />
<?php
/*
include_once("./librairie_php/lib_get_init.php");
if (php_module_load("dbase") != 1) {
<img src="./image/commun/on1.gif" width="8" height="8"> <s><?php print LANGIMP4?></s> &nbsp;&nbsp;<A href='#' onMouseOver="AffBulle3(<?php print LANGbasededoni11?></i> </FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center border=0></A>
<br /><br />
<?php
}else{
?>
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./base_de_donne_importation_gep.php"><?php print "GEP" ?></A> <br /><br />
<?php 
} 
*/
?>
</ul></ul>
</font>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
