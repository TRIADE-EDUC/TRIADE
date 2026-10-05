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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<div class="dest-wrap">

<!-- ── Excel (XLS) ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGIMP1 ?> — Excel</div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">
  <form action="base_de_donne_importation20.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGMESS225 ?> <small style="color:#888;">(xls - office 2003)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
  <form action="base_de_donne_importation200.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">SIECLE <small style="color:#888;">(xls - office 2003)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
  <form action="base_de_donne_importation400.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">CTI <small style="color:#888;">(xls - office 2003)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
  <form action="base_de_donne_importation800.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGMESS227 ?> <small style="color:#888;">(xls - office 2003)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
</div>

<!-- ── XML ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGIMP1 ?> — XML</div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">
  <form action="base_de_donne_importation600.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">STSweb <small style="color:#888;">(XML)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
  <form action="base_de_donne_importation1100.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">SIECLE BEE <small style="color:#888;">(XML)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
</div>

<!-- ── Absences ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGMESS226 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">
  <form action="base_de_donne_importation700.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">SIECLE <small style="color:#888;">(absences)</small></span>
      <button type="submit" class="btn-dest">Importer</button>
    </div>
  </form>
</div>

</div>

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
