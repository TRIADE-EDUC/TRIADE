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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("3");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php if (isset($_GET["saisie_resultat"]) && $_GET["saisie_resultat"] == "erreur"): ?>
<div class="alert alert-warning" style="margin:8px 16px;">
  <?php print LANGTERREURCONNECT ?>
</div>
<?php endif; ?>

<form method="post" action='./base_de_donne_central.php'>
<input type="hidden" name="base"           value="<?php print htmlspecialchars($_GET["base"] ?? '') ?>">
<input type="hidden" name="dbf_name"       value="<?php print htmlspecialchars($_GET["dbf_name"] ?? '') ?>">
<input type="hidden" name="modulepost"     value="<?php print htmlspecialchars($_POST["modulepost"] ?? '') ?>">
<input type="hidden" name="modulesecurite" value="<?php print htmlspecialchars($_GET["key"] ?? '') ?>">
<input type="hidden" name="eid"            value="<?php print htmlspecialchars($_GET["eid"] ?? '') ?>">
<input type="hidden" name="supp_date_cal"  value="<?php print htmlspecialchars($_POST["supp_date_cal"] ?? '') ?>">
<input type="hidden" name="supp_date_dst"  value="<?php print htmlspecialchars($_POST["supp_date_dst"] ?? '') ?>">
<input type="hidden" name="supp_date_edt"  value="<?php print htmlspecialchars($_POST["supp_date_edt"] ?? '') ?>">
<input type="hidden" name="sClasseGrp"     value="<?php print htmlspecialchars($_GET["sClasseGrp"] ?? '') ?>">

<div class="card" style="margin:12px 8px;">
  <div class="card-header"><?php print LANGPER12 ?></div>
  <div class="card-body" style="text-align:center;">
    <div style="display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
      <input type="password" name="saisie_code1" class="form-control" style="max-width:90px;text-align:center;">
      <span style="color:#888;">—</span>
      <input type="password" name="saisie_code2" class="form-control" style="max-width:90px;text-align:center;">
      <span style="color:#888;">—</span>
      <input type="password" name="saisie_code3" class="form-control" style="max-width:90px;text-align:center;">
    </div>
    <div style="font-size:11px;color:#888;margin-bottom:14px;">
      <img src="image/commun/important.png" align="middle" style="vertical-align:middle;margin-right:4px;">
      <?php print LANGMESS376 ?> <a href="admin/index.php" target="_blank" style="color:#080A66;"><?php print LANGMESS377 ?></a> <?php print LANGMESS378 ?>
    </div>
    <button type="submit" class="btn btn-primary"><?php print LANGPER13 ?></button>
  </div>
</div>
</form>

<?php if ((isset($_SESSION['adminplus'])) && ($_SESSION['adminplus'] == "suppreme")): ?>
<script language="JavaScript">document.forms[0].submit();</script>
<?php endif; ?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
