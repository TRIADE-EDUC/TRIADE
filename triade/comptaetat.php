<?php
session_start();
if (isset($_COOKIE["anneescolairefiltre"])) $anneeScolaire=$_COOKIE["anneescolairefiltre"];
?>
<HTML>
<HEAD>
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
<META http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]"; ?></title>
<style>
.ce-menu-item { display:flex; align-items:center; justify-content:space-between; padding:8px 14px; border-bottom:1px solid #e8eaf6; }
.ce-menu-item:last-child { border-bottom:none; }
.ce-menu-label { font-size:13px; color:#1a237e; font-weight:600; }
.ce-filter-bar { padding:8px 12px; background:#f5f7ff; border-bottom:1px solid #e8eaf6; display:flex; align-items:center; gap:8px; }
.ce-filter-bar select { padding:4px 8px; border:1px solid #c5cae9; border-radius:6px; font-size:12px; }
.ce-total-row td { background:#eef1ff !important; }
.ce-pwd-box { padding:24px; text-align:center; }
.ce-pwd-box input[type=password] { width:90px; padding:4px 8px; border:1px solid #c5cae9; border-radius:6px; font-size:12px; margin:0 4px; }
.ce-sep { color:#aaa; margin:0 6px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
?>

<!-- ── Accès rapide ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'><?php print LANGMESS74 ?></font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:0;">

<div class="ce-menu-item">
  <span class="ce-menu-label"><?php print LANGMESS71 ?></span>
  <form action='compta_consulte.php' method='post' style="margin:0;">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
  </form>
</div>
<div class="ce-menu-item">
  <span class="ce-menu-label"><?php print LANGMESS72 ?></span>
  <form action='compta_fiche.php' method='post' style="margin:0;">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
  </form>
</div>
<div class="ce-menu-item">
  <span class="ce-menu-label"><?php print LANGMESS73 ?></span>
  <form action='compta_consulte_retard.php' method='post' style="margin:0;">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
  </form>
</div>
<div class="ce-menu-item">
  <span class="ce-menu-label">Exportation (format xls)</span>
  <form action='compta_export.php' method='post' style="margin:0;">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
  </form>
</div>
<div class="ce-menu-item">
  <span class="ce-menu-label">Listing des paiements par classe (format xls)</span>
  <form action='compta_listing.php' method='post' style="margin:0;">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien");</script>
  </form>
</div>

</td></tr></table>

<br>

<!-- ── Bilan financier ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Bilan financier</font></b></td></tr>
<tr id='cadreCentral0'><td valign='top' style="padding:0;">
<?php
$visu=0;
if (PASSMODULEBILANFINANCIER != "oui") { $visu=1; }
if (isset($_SESSION["adminplus"])) { $visu=1; }
if ($visu == 0) {
    $erreurCnx = ($_GET["saisie_resultat"] == "erreur");
?>
<form method=post action='./base_de_donne_central.php'>
<div class="ce-pwd-box">
  <p style="font-size:13px; font-weight:600; color:#1a237e; margin-bottom:14px;"><?php print LANGPER12 ?></p>
  <input type=password name='saisie_code1' size=10>
  <span class="ce-sep">—</span>
  <input type=password name='saisie_code2' size=10>
  <span class="ce-sep">—</span>
  <input type=password name='saisie_code3' size=10>
  <br><br>
  <input type=hidden name='base' value="bilanfinancier">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGPER13 ?>","rien");</script>
</div>
</form>
<?php if ($erreurCnx): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error('Erreur de connexion'); });</script>
<?php endif; ?>

<?php
}else{
    if (isset($_POST["anneescolairefiltre"])) {
        $anneescolairefiltre=$_POST["anneescolairefiltre"];
    }
?>
<div class="ce-filter-bar">
  <form method='post' action="comptaetat.php" style="margin:0; display:flex; align-items:center; gap:8px;">
    <span style="font-size:12px; font-weight:600; color:#1a237e;">Filtre :</span>
    <select onChange='this.form.submit()' name='anneescolairefiltre'>
      <option value=''><?php print LANGCHOIX ?></option>
      <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?>
    </select>
  </form>
</div>
<table class="cc-table" width="100%" style="border-collapse:collapse;">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th">Classe</th>
  <th class="cc-th" style="text-align:right; width:110px;">Total</th>
  <th class="cc-th" style="text-align:right; width:110px;">Re&ccedil;u</th>
  <th class="cc-th" style="text-align:right; width:80px;">Moy.&nbsp;%</th>
</tr>
</thead>
<tbody>
<?php
$data=visu_affectation($anneescolairefiltre);
for($i=0;$i<countTriade($data);$i++) {
	$idclasse=$data[$i][0];
	$dataV=recupConfigVersement($idclasse,$anneescolairefiltre);
	if ($dataV == "") { $dataV=array(); }
	$sql="SELECT elev_id FROM {$prefixe}eleves WHERE classe='$idclasse'";
	$res=execSql($sql);
	$dataEl=chargeMat($res);
	for($h=0;$h<countTriade($dataEl);$h++) {
		$ideleve=$dataEl[$h][0];
		$dataVE=recupConfigVersementEleve($ideleve,$anneescolairefiltre);
		if ($dataVE == "") { $dataVE=array(); }
		$dataVI=array_merge($dataV,$dataVE);
		for($j=0;$j<countTriade($dataVI);$j++) {
			$id=$dataVI[$j][0];
			if (verifcomptaExclu($id,$ideleve)) continue;
			$dataO=recupInfoVersement($ideleve,$id);
			$montantVers+=$dataO[0][2];
			$montantavers+=$dataVI[$j][3];
		}
		unset($dataVE);
	}
	if ($montantavers > 0) $pourcentage=($montantVers/$montantavers)*100;

	print "<tr class='cc-tr-data'>";
	print "<td class='cc-td'>".$data[$i][1]."</td>";
	print "<td class='cc-td' style='text-align:right;'>&nbsp;".number_format($montantavers,2,'.','')."&nbsp;".unitemonnaie()."&nbsp;</td>";
	print "<td class='cc-td' style='text-align:right;'>&nbsp;".number_format($montantVers,2,'.','')."&nbsp;".unitemonnaie()."&nbsp;</td>";
	print "<td class='cc-td' style='text-align:right;'>&nbsp;".number_format($pourcentage,2,'.','')."%&nbsp;</td>";
	print "</tr>";

	$sommeVers+=$montantVers;
	$sommeAVers+=$montantavers;
	$sommepourcentage+=number_format($pourcentage,2,'.','');
	$nbpourcentage++;

	unset($dataV);
	unset($dataVE);
	unset($montantVers);
	unset($montantavers);
}
if ($nbpourcentage != 0) { $sommepourcentage=$sommepourcentage/$nbpourcentage; }
print "<tr class='ce-total-row'>";
print "<td class='cc-td' style='text-align:right;'><b>Total</b></td>";
print "<td class='cc-td' style='text-align:right;'><b>&nbsp;".number_format($sommeAVers,'2',',',' ')."&nbsp;".unitemonnaie()."</b></td>";
print "<td class='cc-td' style='text-align:right;'><b>&nbsp;".number_format($sommeVers,'2',',',' ')."&nbsp;".unitemonnaie()."</b></td>";
print "<td class='cc-td' style='text-align:right;'><b>&nbsp;".number_format($sommepourcentage,2,'.','')."%</b></td>";
print "</tr>";
?>
</tbody>
</table>
<?php } ?>
</td></tr></table>

<br><br>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
PgClose();
?>
</BODY></HTML>
