<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
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
$urlbrouillon = "&brouillon=0";
$idbrouillon  = 0;
$brouillon    = "";
if ($_GET["brouillon"] == 1) {
    $brouillon    = " <font color='#cc0000'>(Type Brouillon)</font>";
    $urlbrouillon = "&brouillon=".$_GET["brouillon"];
    $idbrouillon  = $_GET["brouillon"];
}
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_verif_message.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom]") ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGMESS1 ?> &mdash; <?php print dateDMY() ?><?php print $brouillon ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td style="padding:0">
<?php
if (isset($_GET["erreur"])) {
    $params = "saisie_classe=".urlencode($_GET["saisie_classe"])
             ."&saisie_envoi=".urlencode($_GET["saisie_envoi"])
             ."&saisie_objet=".urlencode($_GET["saisie_obj"])
             ."&message=".urlencode($_GET["message"])
             ."&erreur=1&typequi=".urlencode($_POST["typequi"])
             .$urlbrouillon
             ."&f=".urlencode($_GET["f"])
             ."&saisie_id_message=".urlencode($_GET['saisie_id_message']);
} else {
    $params = "saisie_classe=".urlencode($_GET["saisie_classe"])
             ."&saisie_envoi=".urlencode($_GET["saisie_envoi"])
             ."&typequi=".urlencode($_GET["typequi"])
             .$urlbrouillon
             ."&f=".urlencode($_GET["f"])
             ."&saisie_id_message=".urlencode($_GET['saisie_id_message']);
}
?>
<iframe height="1150" src="messagerie_envoi_suite2.php?<?php print $params ?>"
  width="100%" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no">
</iframe>
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
