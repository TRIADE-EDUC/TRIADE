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
error_reporting(0);
include("./librairie_php/lib_licence.php"); ?>
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
<script language="JavaScript" src="../librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="../librairie_js/lib_ordre_liste.js"></script>
<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<table border="0" cellpadding="3" cellspacing="1" width="703"  height="503" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Configuration de l'affichage des bulletins</font></b></td></tr>
<tr id="cadreCentral0" ><td valign='top'>
<?php
include_once("../librairie_php/lib_bulletin.php");
include_once("../librairie_php/langue.php");
$listeaffiche=$_POST["saisie_recherche_final"];
$listeaffiche=preg_replace("/,/",", ",$listeaffiche);
//------------------------------------------------------------------
@unlink("../common/config.bulletin.php");
$text2 = '<?php'."\n";
$text2.= 'define("LISTEBULLETIN","'.$listeaffiche.'");'."\n";
$text2.= '?>'."\n";
$fp=fopen("../common/config.bulletin.php","w");
fwrite($fp,"$text2");
fclose($fp);
//-------------------------------------------------------------------
if (trim($listeaffiche) == "") @unlink("../common/config.bulletin.php");
?>
<br><br><center><font class='T2'><?php print LANGRESA69 ?></font>
<br><br><br>
<table align='center'><tr><td><script>buttonMagicFermeture()</script></td></tr></table>
</center> 

</td></tr></table>
</body>
</html>
