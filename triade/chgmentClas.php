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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script language="JavaScript">
function suite() {
  location.href = "./base_de_donne_key.php?base=change";
}
</script>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGBASE23 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <div class="card">
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

      <div style="background:#eef0f8;border:1px solid #c5cae9;border-radius:6px;padding:10px 14px;font-size:12px;color:#333;line-height:1.6">
        <?php print LANGCHAN0 ?>.
      </div>

      <div style="background:#fff9f9;border:1px solid #f5c6cb;border-radius:6px;padding:10px 14px;display:flex;align-items:flex-start;gap:10px">
        <i class="bi bi-exclamation-triangle-fill" style="color:#c62828;font-size:15px;flex-shrink:0;margin-top:2px"></i>
        <p style="margin:0;font-size:12px;color:#c62828;font-weight:600;line-height:1.6"><?php print LANGCHAN1 ?>.</p>
      </div>

      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <button type="button" onclick="suite();"
                class="btn btn-primary" style="display:inline-flex;align-items:center;gap:7px">
          <i class="bi bi-arrow-right-circle"></i>
          <?php print LANGBTS ?>
        </button>
        <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
      </div>

      <p style="margin:0;font-size:11px;color:#888;font-style:italic">
        <i class="bi bi-info-circle" style="color:#3949ab"></i>
        <b><?php print LANGASS10 ?></b> <?php print "Brevets, plan de classe" ?>
      </p>

    </div>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
