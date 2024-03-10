<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2023
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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

include_once("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "TRIADE-COPILOT" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<?php
if (LAN == "oui") {
	include_once("../librairie_php/db_triade.php");
	valideProductId();
	if (!file_exists("../common/config-ia.php")) {
		if (file_exists("../common/productid.php")) include_once("../common/productid");
	        if (defined('PRODUCTID')) $productId=PRODUCTID;
		print "<form method='post' action='ia-inscription.php'  >";
		print "<input type='hidden' name=productid value='".$productid."'   >";
		print "<ul><br /><font class=T2>";
		print "Vous n'avez aucun compte de créé pour la gestion  TRIADE-COPILOT.";
		print "<br /><br />";
		print "<script language=JavaScript>buttonMagicSubmit4('Inscription Gratuite','create','');</script>";
		print "</font></ul><br /><br /></form>";
	}else{
		include_once("../common/config-ia.php");
		$idkey=IAKEY;
		$inc=GRAPH;
		print "<table align=center><tr><td><font class=T2>Acc&egrave;s &agrave; votre compte TRIADE-COPILOT : </font></td>";
		print "<td><script language=JavaScript>buttonMagicSubmit3('Cliquez ICI','https://www.triade-educ.org/accueil/triade-ia-compte.php?idkey=$idkey&inc=$inc','_blank','','','');</script></td></tr></table>";
	}

	include_once("../common/config2.inc.php");
	if (AFFICHAGEIA != "oui") {
		print "<ul><br /><font class=T2 color='red' >";
                print "Vous devez activer l'autorisation d'utilisation de TRIADE-COPILOT dans la module <a href='configuration.php'><font color='red'><b>Config. G&eacute;n&eacute;rale.</b></font></a>";
                print "<br /><br />";

	}

}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR2."</i></center>";
}
?>

<!-- // fin form -->
</td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT></BODY>
</HTML>
