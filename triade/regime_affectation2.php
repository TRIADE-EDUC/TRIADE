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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Affectation régimes - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
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
<?php
if (isset($_POST["createregime"])) {
    $ideleve = $_POST["ideleve"];
    $regime  = $_POST["regime"];
    $nb      = $_POST["nb"];
    for ($i = 0; $i <= $nb; $i++) {
        miseAjourRegime($ideleve[$i], $regime[$i]);
    }
}

if (isset($_POST["sClasseGrp"])) {
    $saisie_classe = $_POST["sClasseGrp"];
    $sql = "SELECT libelle,elev_id,nom,prenom,regime FROM {$prefixe}eleves ,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
    $res  = execSql($sql);
    $data = chargeMat($res);
    $cl   = $data[0][0];
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGELE4 ?> : <font id="color2"><b><?php print $cl ?></b></font> / <?php print LANGCOM3 ?> <font id="color2"><?php print countTriade($data) ?></font></font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div class="card">
  <div class="card-header card-header-primary">
    <span>Affectation des régimes</span>
  </div>
  <?php if (countTriade($data) <= 0): ?>
  <div class="card-body" style="color:#888;font-style:italic;"><?php print LANGPROJ6 ?></div>
  <?php else: ?>
  <form method="post" action="regime_affectation2.php">
    <table class="table" style="margin:0;">
      <thead>
        <tr>
          <th class="cc-th"><?php print LANGELE2 ?></th>
          <th class="cc-th"><?php print LANGELE3 ?></th>
          <th class="cc-th">Régime</th>
        </tr>
      </thead>
      <tbody>
      <?php for ($i = 0; $i < countTriade($data); $i++): $regime = $data[$i][4]; ?>
        <tr class="cc-tr-data">
          <td>
            <?php print strtoupper($data[$i][2]) ?>
            <input type="hidden" name="ideleve[]" value="<?php print $data[$i][1] ?>">
          </td>
          <td><?php print ucwords($data[$i][3]) ?></td>
          <td>
            <select name="regime[]" class="cc-select" style="font-size:11px;padding:3px 6px;">
              <?php if ($regime != ""): ?>
              <option value="<?php print $regime ?>" selected><?php print $regime ?></option>
              <?php endif; ?>
              <option value=""></option>
              <?php select_regime2() ?>
            </select>
          </td>
        </tr>
      <?php endfor; ?>
      </tbody>
    </table>
    <div style="padding:10px;display:flex;gap:8px;">
      <script language="JavaScript">buttonMagicSubmit('Enregistrer','createregime');</script>
      <script language="JavaScript">buttonMagicRetour2('regime_affectation.php','_parent','Retour');</script>
    </div>
    <input type="hidden" name="sClasseGrp" value="<?php print htmlspecialchars($_POST["sClasseGrp"]) ?>">
    <input type="hidden" name="nb" value="<?php print countTriade($data) ?>">
  </form>
  <?php endif; ?>
</div>

</td></tr></table>
<?php
}
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY></HTML>
