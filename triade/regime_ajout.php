<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<style>
.regime-day-grid { display:grid; grid-template-columns:90px 1fr; gap:6px 8px; align-items:center; }
.regime-day-label { font-weight:600; color:#080A66; font-size:12px; text-align:right; }
.regime-day-checks { display:flex; gap:12px; align-items:center; font-size:12px; }
.regime-day-checks label { display:flex; align-items:center; gap:4px; cursor:pointer; }
</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Configuration régimes - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration des régimes</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
include_once('./librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx = cnx();

if (isset($_POST["createregime"])) {
    $libelle2 = $_POST["libelle"];
    if (trim($libelle2) != "") {
        $cr = enrRegime($libelle2,$_POST["lundimidi"],$_POST["lundisoir"],$_POST["mardimidi"],$_POST["mardisoir"],$_POST["mercredimidi"],$_POST["mercredisoir"],$_POST["jeudimidi"],$_POST["jeudisoir"],$_POST["vendredimidi"],$_POST["vendredisoir"],$_POST["samedimidi"],$_POST["samedisoir"],$_POST["dimanchemidi"],$_POST["dimanchesoir"]);
        if ($cr == 2) { alertJs("Le libellé est déjà utilisé, merci de choisir un autre."); }
    }
}
if (isset($_POST["updateregime"])) {
    $libelle1 = $_POST["libelle"];
    if (trim($libelle1) != "") {
        updateRegime($libelle1,$_POST["lundimidi"],$_POST["lundisoir"],$_POST["mardimidi"],$_POST["mardisoir"],$_POST["mercredimidi"],$_POST["mercredisoir"],$_POST["jeudimidi"],$_POST["jeudisoir"],$_POST["vendredimidi"],$_POST["vendredisoir"],$_POST["samedimidi"],$_POST["samedisoir"],$_POST["dimanchemidi"],$_POST["dimanchesoir"],$_POST["id"]);
    }
}
if (isset($_POST["suppregime"])) { suppRegime($_POST["id"]); }

if (isset($_GET["id"])) {
    $data = InfoRegime($_GET["id"]);
    $libelle = str_replace('"', '', $data[0][0]);
    $LUM=($data[0][1]==1)?"checked='checked'":""; $LUS=($data[0][2]==1)?"checked='checked'":"";
    $MAM=($data[0][3]==1)?"checked='checked'":""; $MAS=($data[0][4]==1)?"checked='checked'":"";
    $MEM=($data[0][5]==1)?"checked='checked'":""; $MES=($data[0][6]==1)?"checked='checked'":"";
    $JEM=($data[0][7]==1)?"checked='checked'":""; $JES=($data[0][8]==1)?"checked='checked'":"";
    $VEM=($data[0][9]==1)?"checked='checked'":""; $VES=($data[0][10]==1)?"checked='checked'":"";
    $SAM=($data[0][11]==1)?"checked='checked'":""; $SAS=($data[0][12]==1)?"checked='checked'":"";
    $DIM=($data[0][13]==1)?"checked='checked'":""; $DIS=($data[0][14]==1)?"checked='checked'":"";
}
?>

<div class="card" style="margin-bottom:10px;">
  <div class="card-header card-header-primary">
    <span><?php print isset($_GET["id"]) ? "Modifier le régime" : "Créer un régime" ?></span>
  </div>
  <div class="card-body">
    <form method='post' action="regime_ajout.php">
      <div class="form-row" style="margin-bottom:10px;">
        <label class="form-lbl">Nom du régime :</label>
        <?php if (!empty($_GET["id"])): ?>
          <strong><?php print htmlspecialchars($libelle) ?></strong>
          <input type="hidden" name="libelle" value="<?php print htmlspecialchars($libelle) ?>">
        <?php else: ?>
          <input type="text" name="libelle" size="30" maxlength="25" class="bouton2">
        <?php endif; ?>
      </div>

      <div class="regime-day-grid">
        <?php
        $days = [
            LANGLETTRELUNDI   => ['lundimidi',   'lundisoir',   $LUM ?? '', $LUS ?? ''],
            LANGLETTREMARDI   => ['mardimidi',   'mardisoir',   $MAM ?? '', $MAS ?? ''],
            LANGLETTREMERCREDI=> ['mercredimidi','mercredisoir',$MEM ?? '', $MES ?? ''],
            LANGLETTREJEUDI   => ['jeudimidi',   'jeudisoir',   $JEM ?? '', $JES ?? ''],
            LANGLETTREVENDREDI=> ['vendredimidi','vendredisoir',$VEM ?? '', $VES ?? ''],
            LANGLETTRESAMEDI  => ['samedimidi',  'samedisoir',  $SAM ?? '', $SAS ?? ''],
            LANGLETTREDIMANCHE=> ['dimanchemidi','dimanchesoir',$DIM ?? '', $DIS ?? ''],
        ];
        foreach ($days as $label => $d): ?>
        <div class="regime-day-label"><?php print $label ?> :</div>
        <div class="regime-day-checks">
          <label><input type="checkbox" name="<?php print $d[0] ?>" value="1" <?php print $d[2] ?>> midi</label>
          <label><input type="checkbox" name="<?php print $d[1] ?>" value="1" <?php print $d[3] ?>> soir</label>
        </div>
        <?php endforeach; ?>
      </div>

      <input type="hidden" name="id" value="<?php print $_GET["id"] ?? '' ?>">
      <div style="margin-top:12px;display:flex;gap:8px;">
        <?php if (!empty($_GET["id"])): ?>
          <script language="JavaScript">buttonMagicSubmit('<?php print LANGBT50 ?>','suppregime');</script>
          <script language="JavaScript">buttonMagicSubmit('<?php print LANGPER30 ?>','updateregime');</script>
        <?php else: ?>
          <script language="JavaScript">buttonMagicSubmit('<?php print LANGENR ?>','createregime');</script>
        <?php endif; ?>
      </div>
    </form>
    <p style="margin-top:10px;font-size:11px;color:#666;font-style:italic;">
      Vous pouvez créer les régimes <strong>"demi-pension"</strong>, <strong>"demi pension"</strong>, <strong>"interne"</strong> et <strong>"externe"</strong>.
    </p>
  </div>
</div>

<?php
$data = listingRegime();
$days_short = ['L','L','M','M','M','M','J','J','V','V','S','S','D','D'];
$sub_short  = ['M','S','M','S','M','S','M','S','M','S','M','S','M','S'];
?>
<div class="card">
  <div class="card-header card-header-primary">
    <span>Liste des régimes</span>
  </div>
  <table class="table" style="margin:0;font-size:11px;">
    <thead>
      <tr>
        <th class="cc-th">Libellé</th>
        <?php for ($c = 0; $c < 14; $c++): ?>
        <th class="cc-th" style="text-align:center;padding:4px 3px;"><?php print $days_short[$c] ?><br><span style="color:#888;font-weight:400;"><?php print $sub_short[$c] ?></span></th>
        <?php endfor; ?>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++): $id = $data[$i][15]; ?>
      <tr class="cc-tr-data">
        <td><a href="regime_ajout.php?id=<?php print $id ?>" title="Modifier / Supprimer" style="color:#080A66;font-weight:600;"><?php print htmlspecialchars($data[$i][0]) ?></a></td>
        <?php for ($c = 1; $c <= 14; $c++): ?>
        <td style="text-align:center;">
          <?php print ($data[$i][$c] == 1) ? '<span style="color:#080A66;font-weight:700;">✓</span>' : '<span style="color:#ccc;">—</span>' ?>
        </td>
        <?php endfor; ?>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
</div>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
