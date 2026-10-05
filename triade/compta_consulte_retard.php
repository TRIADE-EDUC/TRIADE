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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]"; ?></title>
<style>
.ce-filters { display:flex; align-items:center; gap:16px; padding:10px 14px; background:#f5f7ff; border-bottom:1px solid #e8eaf6; flex-wrap:wrap; }
.ce-filter-label { font-size:13px; font-weight:600; color:#1a237e; }
.ce-filter-select { padding:4px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:13px; background:#fff; }
.ce-actions { display:flex; align-items:center; gap:10px; padding:12px 14px; }
.ce-sms-btn { padding:6px 16px; background:#1a237e; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; }
.ce-sms-btn:disabled { background:#9fa8da; cursor:not-allowed; }
.ce-sms-note { font-size:12px; color:#c0392b; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
$action = "compta_consulte_retard_sms.php";
if (LAN == "non") { $disabledSMS = "disabled='disabled'"; $action = ""; $valideSMS = ERREUR1; }
if (!file_exists("./common/config-sms.php")) { $disabledSMS = "disabled='disabled'"; $action = ""; $valideSMS = LANGMESS37; }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'><?php print LANGMESS73 ?></font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:0;">

<!-- Filtres -->
<form method=post action="compta_consulte_retard.php">
<div class="ce-filters">
  <span class="ce-filter-label">Classe :</span>
  <?php
  if (isset($_POST["filtre"])) {
      $saisie_classe = $_POST["filtre"];
      if ($saisie_classe != "") {
          $option = "<option value='$saisie_classe' id='select0'>".chercheClasse_nom($saisie_classe)."</option>";
      }
  }
  ?>
  <select name='filtre' onchange="this.form.submit();" class="ce-filter-select">
    <?php print $option ?? ''; ?>
    <option value="">Aucun</option>
    <?php select_classe(); ?>
  </select>

  <span class="ce-filter-label">Ann&eacute;e :</span>
  <?php
  if (isset($_POST["anneescolairefiltre"])) {
      $anneescolairefiltre = $_POST["anneescolairefiltre"];
  }
  ?>
  <select name='anneescolairefiltre' onchange="this.form.submit();" class="ce-filter-select">
    <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?>
  </select>
</div>
</form>

<!-- Tableau des retards -->
<form name="formulaire" method="post" action="<?php print $action ?>">
<table class="cc-table" width="100%" style="border-collapse:collapse;">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th">Nom Pr&eacute;nom</th>
  <th class="cc-th" style="width:35%;">Versement</th>
  <th class="cc-th" style="width:90px; text-align:right;">Montant</th>
  <th class="cc-th" style="width:90px; text-align:center;">&Eacute;ch&eacute;ance</th>
  <th class="cc-th" style="width:40px; text-align:center;"><input type='checkbox' onclick='checktous();' name='tous'></th>
</tr>
</thead>
<tbody>
<?php
if (isset($_POST['filtre'])) {
    $sqlsuite = ($_POST['filtre'] != '') ? "WHERE classe='".$_POST['filtre']."'" : "";
} else {
    $sqlsuite = "";
}
$sql  = "SELECT elev_id,nom,prenom,classe FROM {$prefixe}eleves $sqlsuite ORDER BY nom";
$res  = execSql($sql);
$dataE = ChargeMat($res);
$nb = 0; $a = 0; $total = 0;
for ($o = 0; $o < countTriade($dataE); $o++) {
    $ideleve    = $dataE[$o][0];
    $nomeleve   = $dataE[$o][1];
    $prenomeleve = $dataE[$o][2];
    $idclasse   = $dataE[$o][3];
    $dataV  = recupConfigVersement($idclasse, $anneescolairefiltre);
    if ($dataV == "") { $dataV = array(); }
    $dataVE = recupConfigVersementEleve($ideleve, $anneescolairefiltre);
    if ($dataVE == "") { $dataVE = array(); }
    $dataV = array_merge($dataV, $dataVE);
    for ($j = 0; $j < countTriade($dataV); $j++) {
        $nb++;
        $affiche = 0;
        $id = $dataV[$j][0];
        if (verifcomptaExclu($id, $ideleve)) { continue; }
        $data = recupInfoVersement($ideleve, $id);
        $dateVersement = $data[0][3];
        $idvers = $data[0][1];
        if ($dateVersement != "") { $dateVersement = dateForm($dateVersement); }
        $montantVers  = number_format($data[0][2], 2, '.', '');
        $modepaiement = nl2br($data[0][4]);
        $dateVersOr   = $dataV[$j][4];
        $montantavers = $dataV[$j][3];
        $dateduJour   = date("Ymd");
        $dateVersOr   = preg_replace('/-/', "", $dateVersOr);
        if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) { $affiche = 1; }
        if (($montantVers < $dataV[$j][3]) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00")) { $affiche = 1; }
        if ($affiche == 1) {
            $a++;
            print "<tr id=\"tr$a\" class='cc-tr-data'>";
            print "<td class='cc-td'>&nbsp;".strtoupper($nomeleve)." ".ucfirst($prenomeleve)."&nbsp;</td>";
            print "<td class='cc-td'>&nbsp;".$dataV[$j][2]."</td>";
            print "<td class='cc-td' style='text-align:right;'>&nbsp;<b>".preg_replace('/ /', '&nbsp;', affichageFormatMonnaie($dataV[$j][3]))."</b></td>";
            print "<td class='cc-td' style='text-align:center;'>&nbsp;".dateForm($dataV[$j][4])."</td>";
            print "<td class='cc-td' style='text-align:center;'><input type=checkbox name='ideleve[]' value='$ideleve' onClick=\"DisplayLigne('tr$a');\" /></td>";
            print "</tr>";
            $total += $dataV[$j][3];
        }
    }
}
print "<tr class='cc-tr-data' style='background:#eef1ff;'>";
print "<td class='cc-td' colspan='2' style='text-align:right;'><b>Total&nbsp;:</b></td>";
print "<td class='cc-td' style='text-align:right;'><b>".preg_replace('/ /', '&nbsp;', affichageFormatMonnaie($total))."</b></td>";
print "<td class='cc-td' colspan='2'></td>";
print "</tr>";
?>
</tbody>
</table>

<div class="ce-actions">
  <input type='submit' name="consult" value='Envoyer un SMS' class='ce-sms-btn' <?php print $disabledSMS ?? ''; ?>>
  <script language=JavaScript>buttonMagicRetour("comptaetat.php","_self");</script>
  <?php if (!empty($valideSMS)): ?>
  <span class="ce-sms-note"><?php print $valideSMS ?></span>
  <?php endif; ?>
</div>
</form>

<script>
function checktous() {
    var nb = <?php print $a ?>;
    for (var i = 1; i <= nb; i++) {
        if (document.formulaire.tous.checked == false) {
            document.formulaire.elements[i].checked = false;
            document.getElementById('tr'+i).style.backgroundColor = '';
        } else {
            document.formulaire.elements[i].checked = true;
            document.getElementById('tr'+i).style.backgroundColor = '#c0c0c0';
        }
    }
}
</script>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
