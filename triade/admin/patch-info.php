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
Last updated: 29.09.2005    par Taesch  Eric
*************************************************************/ -->
<HTML>
<head>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Triade - Patch <?php print $_GET[idpatch] ?></title>
</head>
<body id='bodyfond2' >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade_admin.php");
$cnx=cnx();
$data=info_patch($_GET["idpatch"]);
print $data;
Pgclose($cnx);
?><br>
<input type=button value='Fermer la fenêtre' onclick="parent.window.close();" class="bouton2">
</body>
</html>
