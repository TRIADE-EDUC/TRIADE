<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<!-- ── Filtre année scolaire ── -->
<form method="post" action="gestion_examen.php">
<div class="na-card">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <select name="anneeScolaire" class="cc-select" onChange="this.form.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,6); ?>
    </select>
  </div>
</div>
</form>

<?php if (trim($anneeScolaire) != "") { ?>

<div class="dest-wrap" style="margin-top:12px;">

<?php if (VATEL != 1) { ?>

<!-- ── Examens / Brevets ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;">Examens &amp; Brevets</div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="gestion_examen_brevet_nf.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">Fiche Scolaire Brevet série collège</span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

  <form action="gestion_examen_brevet_pf.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">Fiche Scolaire Brevet série professionnelle</span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

  <form action="gestion_examen_brevet_techno.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">Fiche Scolaire Brevet série professionnelle <?php print date("Y") ?></span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

  <form action="gestion_examen_b2i.php" method="post" style="display:contents">
    <input type="hidden" name="type_notation" value="A2R">
    <div class="dest-row">
      <span class="dest-row-label">Notation niveau A2 de langue régionale</span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

  <form action="gestion_examen_listing.php" method="post" style="display:contents">
    <input type="hidden" name="type_notation" value="A2">
    <div class="dest-row">
      <span class="dest-row-label">Examen par matières (listing)</span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

  <form action="gestion_examen_jury.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">Évaluation et notation du jury</span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

</div>
<?php } ?>

<!-- ── Suppléments au titre ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;">Suppléments au titre</div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="gestion_supplement_titre_acces.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGTMESS507 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

  <form action="gestion_supplement_titre.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGTMESS508 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGMESS447 ?></button>
    </div>
  </form>

</div>

</div><!-- /.dest-wrap -->

<?php } ?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
