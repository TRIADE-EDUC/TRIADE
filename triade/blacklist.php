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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Accès IMPOSSIBLE</title>
</head>
<?php
include_once('librairie_php/lib_licence.php');
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$date=date("Y-m-d");
blacklisteenr($_SESSION["nom"],$_SESSION["prenom"],$date,$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"],$_SESSION["membre"],$_GET["fichier"]);
?>
<body id='bodyfond2'>
<BR><BR><BR><BR>
<center>
<table width="57%" border="0" align="center" >
<tr><td align=center id='bordure' >
<font color="red" class="T2"><br /><?php print LANGacce_ref2 ?><br /></font>
<br>
<font class="T2"><?php print LANGBLK1 ?>
<br /><br />
</td>
</tr>
</table>
<br />
<?php print LANGPIEDPAGE ?>
</center>
<BR><BR>
<?php
session_set_cookie_params(0);
$_SESSION=array();
session_unset();
session_destroy();
?>
<BR><BR>
</BODY></HTML>
