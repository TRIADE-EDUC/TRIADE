<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Fiche règlements <?php print "$_SESSION[nom] $_SESSION[prenom]"; ?></title>
<style>
.cf2-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.cf2-card-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:10px 16px; font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; display:flex; align-items:center; justify-content:space-between; }
.cf2-card-badge { background:rgba(255,255,255,.22); border-radius:20px; padding:2px 12px; font-size:11px; font-weight:700; text-transform:none; letter-spacing:0; }
.cf2-no-data { text-align:center; padding:36px 20px; color:#888; font-size:13px; }
.cf2-btn-row { display:flex; gap:10px; align-items:center; padding:12px 16px; border-top:1px solid #e8eaf6; background:#f5f7ff; }
.cf2-print-btn { padding:7px 20px; background:#080A66; color:#fff; border:none; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer; letter-spacing:.3px; }
.cf2-print-btn:hover { background:#1e4d8c; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Imprimer fiche d'&eacute;tat des r&egrave;glements</font></b></td></tr>
<tr id='cadreCentral0'><td valign='top' style="padding:8px 4px;">

<?php
$anneeScolaire = $_POST["anneeScolaire"] ?? '';

if (isset($_POST["sClasseGrp"])) {
    $saisie_classe = $_POST["sClasseGrp"];
    $sql  = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
    $res  = execSql($sql);
    $data = chargeMat($res);
    $cl   = $data[0][0];
    $nb   = countTriade($data);
?>

<form method='post' action='compta_fiche3.php' name='formulaire'>
<input type='hidden' name='anneeScolaire' value='<?php print $anneeScolaire ?>'>

<div class="cf2-card">
  <div class="cf2-card-header">
    <?php print LANGELE4 ?> — <?php print ucwords($cl) ?>
    <span class="cf2-card-badge"><?php print $nb ?> &eacute;l&egrave;ve<?php print $nb > 1 ? 's' : '' ?></span>
  </div>

  <?php if ($nb <= 0): ?>
  <div class="cf2-no-data"><?php print LANGPROJ6 ?></div>
  <?php else: ?>

  <table class="cc-table" width="100%" style="border-collapse:collapse;">
  <thead>
    <tr class="cc-thead-row">
      <th class="cc-th" style="text-align:left;"><?php print LANGELE2 ?></th>
      <th class="cc-th" style="text-align:left;"><?php print LANGELE3 ?></th>
      <th class="cc-th" style="width:44px; text-align:center;">
        <input type='checkbox' onclick='checktous();' name='tous' title="Tout sélectionner">
      </th>
    </tr>
  </thead>
  <tbody>
  <?php for ($i = 0; $i < $nb; $i++): ?>
    <tr id="tr<?php print $i ?>" class="cc-tr-data">
      <td class="cc-td" style="text-align:left;"><?php print strtoupper($data[$i][2]) ?></td>
      <td class="cc-td" style="text-align:left;"><?php print ucwords($data[$i][3]) ?></td>
      <td class="cc-td" style="text-align:center;">
        <input type='checkbox' value="<?php print $data[$i][1] ?>" name='ideleve[]'
               onClick="DisplayLigne('tr<?php print $i ?>');">
      </td>
    </tr>
  <?php endfor; ?>
  </tbody>
  </table>

  <div class="cf2-btn-row">
    <input type='submit' value='Imprimer' class='cf2-print-btn'>
    <script language=JavaScript>buttonMagicRetour("compta_fiche.php","_self");</script>
  </div>

  <?php endif; ?>
</div>
</form>

<?php } ?>

</td></tr></table>

<script>
function checktous() {
    var checked = document.formulaire.tous.checked;
    var boxes = document.querySelectorAll('input[name="ideleve[]"]');
    for (var i = 0; i < boxes.length; i++) {
        boxes[i].checked = checked;
        var row = document.getElementById('tr' + i);
        if (row) row.style.backgroundColor = checked ? '#c0c0c0' : '';
    }
}
</script>

<?php
if ($_SESSION["membre"] == "menuadmin") {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY>
</HTML>
