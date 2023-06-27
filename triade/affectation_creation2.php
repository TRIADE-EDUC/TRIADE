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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGaffec_cre21?>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$nom_classe=chercheClasse($_POST["saisie_classe_envoi"]);
$tri=$_POST['saisie_tri'];
$anneeScolaire=$_POST["anneeScolaire"];
print "<font id='color2'>".$nom_classe[0][1]."</font>";
?>
</font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- //  debut -->
<center><?php print LANGaffec_cre22?>
<img src="./image/commun/indicator.gif">
<br><br>
<?php print LANGaffec_cre23?><a href="#" onclick="open('./affectation_creation3.php?saisie_nb_matiere=<?php print "$_POST[saisie_nb_matiere]"?>&saisie_classe_envoi=<?php print "$_POST[saisie_classe_envoi]" ?>&tri=<?php print $tri ?>&anneeScolaire=<?php print $anneeScolaire ?>','affectation','width=700,height=500,tollbar=no,menubar=no,scrollbars=yes,resizable=yes');"><b>ici</b></A>
</center>
<script language='JavaScript'>
PopupCentrerAttente('./affectation_creation3.php?saisie_nb_matiere=<?php print "$_POST[saisie_nb_matiere]"?>&saisie_classe_envoi=<?php print "$_POST[saisie_classe_envoi]"?>&tri=<?php print $tri ?>&anneeScolaire=<?php print $anneeScolaire ?>',700,500,'tollbar=no,menubar=no,scrollbars=yes,resizable=yes');

</script>
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
