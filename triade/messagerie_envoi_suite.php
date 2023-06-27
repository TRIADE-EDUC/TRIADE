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
$urlbrouillon="&brouillon=0";
$idbrouillon=0;
if ($_GET["brouillon"] == 1) {
	$brouillon=" <font color='color3'>(Type Brouillon)</font>";
	$urlbrouillon="&brouillon=".$_GET["brouillon"];
	$idbrouillon=$_GET["brouillon"];
}

?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_verif_message.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom] ") ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS1?>  <?php print dateDMY()." ".$brouillon ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php
if (isset($_GET["erreur"])) {
?>
	<iframe height=950 src="messagerie_envoi_suite2.php?saisie_classe=<?php print $_GET["saisie_classe"]?>&saisie_envoi=<?php print $_GET["saisie_envoi"]?>&saisie_objet=<?php print $_GET["saisie_obj"]?>&message=<?php print $_GET["message"]?>&erreur=1&typequi=<?php print $_POST["typequi"]?><?php print $urlbrouillon?>&f=<?php print $_GET["f"]?>&saisie_id_message=<?php print $_GET['saisie_id_message'] ?>",width=100% MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no  ></iframe>
<?php }else { ?>
	<iframe height=950 src="messagerie_envoi_suite2.php?saisie_classe=<?php print $_GET["saisie_classe"]?>&saisie_envoi=<?php print $_GET["saisie_envoi"]?>&typequi=<?php print $_GET["typequi"]?><?php print $urlbrouillon?>&f=<?php print $_GET["f"]?>&saisie_id_message=<?php print $_GET['saisie_id_message'] ?>" width=100% MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no  ></iframe>
<?php } ?>
<!-- // fin  -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ) :
            print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
</BODY></HTML>
