<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if (empty($_SESSION["adminplus"])) {
	print "<script>location.href='./base_de_donne_importation.php'</script>";
	exit;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importation du fichier XML</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php if (isset($_GET["err"])) { ?>
<div style="margin:8px;padding:8px 12px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:13px;color:#c62828;font-weight:600;">Mot de passe non conforme !</div>
<?php } ?>

<form method="post" action="./base_de_donne_importation620.php" name="formulaire" enctype="multipart/form-data">
<div class="na-card">

  <div style="margin-bottom:12px;">
    <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;"><?php print LANGGEP2 ?> (<b>XML</b>) :</label>
    <input type="file" name="fichier1" size="25" style="font-size:12px;">
  </div>

  <div style="margin-bottom:12px;">
    <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">
      <?php print "Mot de passe par défaut pour tous les enseignants" ?> :
      <span class="htip-wrap" style="vertical-align:middle;margin-left:4px;">
        <img src="./image/help.gif" class="htip-icon" style="cursor:pointer;">
        <span class="htip"><?php $affiche = affichageMessageSecurite(); print htmlspecialchars($affiche) ?></span>
      </span>
    </label>
    <input type="text" name="passwd" size="15" class="cc-select" style="width:140px;">
  </div>

</div>
<div class="na-foot">
  <button type="submit" class="btn-enr">Confirmer Importation</button>
</div>
</form>

</td>
</tr>
</table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
