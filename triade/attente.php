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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
include("librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title><?php print LANGattente1?></title>
</head>
<body id='bodyfond2' >
<TABLE border=0 width=100% height=100%>
<TR><TD align=center>
<?php print LANGattente2?>
<CENTER><br>
<table border=0><TR><TD><img src="./image/temps1.gif" align=center></TD></TR></TABLE>
<BR>
<?php print LANGattente3?>
</CENTER></TD><TR>
</table>
</body>
</html>
