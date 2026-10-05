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

<!-- ── Section Études ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGETUDE1 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">
  <form action="gestion_etude_liste.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE3 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>
  <form action="gestion_etude_ajout.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE4 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE3 ?></button>
    </div>
  </form>
  <form action="gestion_etude_modif.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE5 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGPER30 ?></button>
    </div>
  </form>
  <form action="gestion_etude_supp.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE6 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
    </div>
  </form>
</div>

<!-- ── Section Élèves en étude ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGETUDE2 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">
  <form action="gestion_etude_eleve_liste.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE7 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>
  <form action="gestion_etude_eleve_ajout.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE8 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE3 ?></button>
    </div>
  </form>
  <form action="gestion_etude_eleve_modif.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE9 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGPER30 ?></button>
    </div>
  </form>
  <form action="gestion_etude_eleve_supp.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGETUDE10 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
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
