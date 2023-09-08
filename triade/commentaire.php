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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Liste des Administrateurs </title>
</head>
<body  id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
validerequete("3");
?>
<BR>
<table border=0 align=center height=130 width=90%>
<tr><td valign=top>
<textarea cols="80" rows="8" >
<?php
print cherche_com_eleve($_GET["ide"],$_GET["idm"],$_GET["idc"],$_GET["tri"],$_GET["idprof"],$_GET["idgroupe"]);
?>
</textarea>
</td></tr></table>
<BR>
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td></tr></table><br>
<?php
Pgclose();
?>
</body>
</html>
