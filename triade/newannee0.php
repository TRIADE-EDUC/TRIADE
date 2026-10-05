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
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$ops = array();

purge_brevetCollege();
$ops[] = array('label' => 'Suppression des notes de brevet', 'ok' => true);

vide_notes_scolaire();
$ops[] = array('label' => 'Suppression des notes vie scolaire élèves', 'ok' => true);

purgeEleveEtude();
$ops[] = array('label' => "Suppression des études d'élèves", 'ok' => true);

vide_dispenses(); purge_present();
$ops[] = array('label' => 'Suppression des dispenses et présences élèves', 'ok' => true);

$date_dst = isset($_GET["supp_date_dst"]) ? $_GET["supp_date_dst"] : date("d/m/Y");
purge_dst2(dateFormBase($date_dst));
$ops[] = array('label' => 'Suppression calendrier D.S.T avant le '.$date_dst, 'ok' => true);

$date_cal = isset($_GET["supp_date_cal"]) ? $_GET["supp_date_cal"] : date("d/m/Y");
purge_evenement2(dateFormBase($date_cal));
$ops[] = array('label' => 'Suppression calendrier des événements avant le '.$date_cal, 'ok' => true);

$date_edt = isset($_GET["supp_date_edt"]) ? $_GET["supp_date_edt"] : date("d/m/Y");
purgeEdtSeance2(dateFormBase($date_edt));
$ops[] = array('label' => 'Suppression emploi du temps (EDT) avant le '.$date_edt, 'ok' => true);

purge_delete_delegue();
$ops[] = array('label' => 'Suppression des délégués', 'ok' => true);

$anneescolaire = paramnouvelleannee();
$ops[] = array('label' => 'Activation de la nouvelle année scolaire : <strong>'.htmlspecialchars($anneescolaire).'</strong>', 'ok' => true);

Pgclose();
?>
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

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <div class="card" style="border-top:3px solid #2e7d32">
    <div class="card-header">
      <span class="card-title" style="color:#2e7d32;display:flex;align-items:center;gap:6px">
        <i class="bi bi-check-circle-fill"></i>
        Opérations effectuées
      </span>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:6px;padding:10px 14px">
      <?php foreach ($ops as $op): ?>
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#333;padding:3px 0;border-bottom:1px solid #eef0f8">
        <i class="bi bi-check2" style="color:#2e7d32;flex-shrink:0"></i>
        <?php print $op['label'] ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div style="background:#eef0f8;border:1px solid #c5cae9;border-radius:6px;padding:10px 14px;font-size:12px;color:#080A66;font-weight:600;text-align:center;line-height:1.6">
    Vous pouvez maintenant effectuer le changement de classe des élèves,<br>puis valider ensuite les pré-inscriptions.
  </div>

  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <script language=JavaScript>buttonMagic("<?php print "En mode manuel" ?>","chgmentClas.php","_parent","","")</script>
    <script language=JavaScript>buttonMagic("<?php print "Via importation xls" ?>","base_de_donne_importation.php","_parent","","")</script>
  </div>

</div>

<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
