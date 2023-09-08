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
 ***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
error_reporting(E_ALL ^ E_NOTICE);
?>
<?php include("./librairie_php/lib_licence.php"); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
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
<FORM method=POST action="forum.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"> <b><font   id='menumodule1' >Configuration de Triade</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
include_once("./librairie_php/edit_fichier.php");



$mailblacklist=$_POST["mailblacklist"];
$mailmesssys=$_POST["mailmesssys"];
$mailmessinter=$_POST["mailmessinter"];


$texte="<?php\n";
$texte.="define(\"MAILBLACKLIST\",\"$mailblacklist\");\n";
$texte.="define(\"MAILMESSSYS\",\"$mailmesssys\");\n";
$texte.="define(\"MAILMESSINTER\",\"$mailmessinter\");\n";



$texte.="?>\n";

$fp=fopen("../common/config4.inc.php","w");
fwrite($fp,"$texte");
fclose($fp);

?>
<br><br>
<center><font size=2>Configuration enregistrée
<br><br> Réactualiser la page pour visualiser les changements.
<?php print $erreur ?>
</font></center>

<br><br>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
