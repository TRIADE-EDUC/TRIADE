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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include("./librairie_php/lib_attente.php");
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
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>Archivage des bulletins</font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-calendar-range"></i> Sélectionner l'année scolaire
    </div>
    <div class="card-body" style="padding:12px 16px">

      <form method="post" action="archivage3.php">
        <div class="form-row" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
          <label class="form-label" style="margin:0;font-size:12px;font-weight:600">Année scolaire :</label>
          <select name="annee_scolaire" class="form-control" style="width:auto">
            <option value=""><?php print LANGCHOIX ?></option>
            <?php filtreAnneeScolaireSelect(''); ?>
          </select>
        </div>
        <div style="margin-top:14px">
          <script language=JavaScript>buttonMagicSubmit3("<?php print VALIDER ?>", 'etape1', "onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>
        </div>
      </form>

    </div>
  </div>

  <div>
    <script language=JavaScript>buttonMagicRetour("archivage.php","_self")</script>
  </div>

</div>

<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php attente(); ?>
</BODY></HTML>
