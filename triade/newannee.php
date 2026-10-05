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
include_once("./common/config5.inc.php");
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx = cnx();
header('Content-type: text/html; charset='.CHARSET);
?>
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
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<script language="JavaScript">
function suite() {
  var confirmation = confirm('<?php print LANGCHAN3 ?>','');
  return confirmation;
}
</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>Nouvelle année scolaire</font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<form method="post" onsubmit="return suite()" name="formulaire"
      action="./base_de_donne_key.php?base=newannee">

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <div style="background:#eef0f8;border:1px solid #c5cae9;border-radius:6px;padding:10px 14px;font-size:12px;color:#333;line-height:1.6">
    <strong>Module permettant de passer à l'année suivante.</strong>
  </div>

  <div class="card" style="border-top:3px solid #e65100">
    <div class="card-header">
      <span class="card-title" style="color:#e65100;display:flex;align-items:center;gap:6px">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Liste des éléments supprimés
      </span>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:8px">

      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:4px 0;border-bottom:1px solid #eef0f8">
        <i class="bi bi-dash-circle" style="color:#c62828;flex-shrink:0"></i>
        Suppression des notes vie scolaire élèves
      </div>
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:4px 0;border-bottom:1px solid #eef0f8">
        <i class="bi bi-dash-circle" style="color:#c62828;flex-shrink:0"></i>
        Suppression des études d'élèves
      </div>

      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:4px 0;border-bottom:1px solid #eef0f8;flex-wrap:wrap">
        <i class="bi bi-dash-circle" style="color:#c62828;flex-shrink:0"></i>
        <span>Suppression info D.S.T avant le</span>
        <input type="text" name="supp_date_dst" size="10" maxlength="10"
               value="<?php print date("d/m/Y") ?>"
               onkeypress="onlyChar2(event)"
               style="border:1px solid #c5cae9;border-radius:4px;padding:3px 8px;font-size:11px;width:90px">
      </div>
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:4px 0;border-bottom:1px solid #eef0f8;flex-wrap:wrap">
        <i class="bi bi-dash-circle" style="color:#c62828;flex-shrink:0"></i>
        <span>Suppression info calendrier des événements avant le</span>
        <input type="text" name="supp_date_cal" size="10" maxlength="10"
               value="<?php print date("d/m/Y") ?>"
               onkeypress="onlyChar2(event)"
               style="border:1px solid #c5cae9;border-radius:4px;padding:3px 8px;font-size:11px;width:90px">
      </div>
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:4px 0;border-bottom:1px solid #eef0f8;flex-wrap:wrap">
        <i class="bi bi-dash-circle" style="color:#c62828;flex-shrink:0"></i>
        <span>Suppression info Emploi du temps (EDT) avant le</span>
        <input type="text" name="supp_date_edt" size="10" maxlength="10"
               value="<?php print date("d/m/Y") ?>"
               onkeypress="onlyChar2(event)"
               style="border:1px solid #c5cae9;border-radius:4px;padding:3px 8px;font-size:11px;width:90px">
      </div>
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:4px 0">
        <i class="bi bi-dash-circle" style="color:#c62828;flex-shrink:0"></i>
        Suppression des délégués
      </div>

    </div>
  </div>

  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <button type="submit" class="btn btn-danger" style="display:inline-flex;align-items:center;gap:7px">
      <i class="bi bi-arrow-clockwise"></i>
      <?php print LANGBTS ?>
    </button>
    <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
  </div>

</div>

</form>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
