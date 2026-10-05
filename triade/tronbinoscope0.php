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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
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
$cnx  = cnx();
$visu = 0;
if ($_SESSION["membre"] == "menupersonnel") {
	if (verifDroit($_SESSION["id_pers"], "trombinoscopeRead")) {
		$visu  = 1;
		$visu2 = 0;
	}
} else {
	validerequete("2");
	$visu  = 1;
	$visu2 = 1;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<div class="dest-wrap">

<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGTITRE32 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;">

<?php if (($visu == 1) || ($visu2 == 1)) : ?>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGTRONBI1 ?></span>
    <a href="tronbinoscope-visu.php" class="btn-dest" style="text-decoration:none;"><?php print CLICKICI ?></a>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGMESS322 ?></span>
    <a href="tronbinoscope-visu-pdf.php" class="btn-dest" style="text-decoration:none;"><?php print CLICKICI ?></a>
  </div>
<?php endif; ?>

<?php if ($visu2 == 1) : ?>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGTRONBI2 ?></span>
    <a href="tronbinoscope.php" class="btn-dest" style="text-decoration:none;"><?php print CLICKICI ?></a>
  </div>
<?php endif; ?>

<?php if (($visu == 1) || ($visu2 == 1)) : ?>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGTRONBI30 ?></span>
    <a href="tronbinoscope-visu-pers.php" class="btn-dest" style="text-decoration:none;"><?php print CLICKICI ?></a>
  </div>
<?php endif; ?>

<?php if ($visu2 == 1) : ?>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGTRONBI20 ?></span>
    <a href="tronbinoscope-pers.php" class="btn-dest" style="text-decoration:none;"><?php print CLICKICI ?></a>
  </div>
  <div style="border-top:1px solid #e0e4f8;margin:4px 0;"></div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGMESS323 ?></span>
    <a href="trombi-import-zip.php" class="btn-dest" style="text-decoration:none;"><?php print CLICKICI ?></a>
  </div>
<?php endif; ?>

<?php if ($visu == 0) : ?>
  <div style="padding:12px 14px;">
    <?php accesNonReserve(); ?>
  </div>
<?php endif; ?>

</div>
</div>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY>
</HTML>
