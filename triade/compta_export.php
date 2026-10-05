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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]; ?></title>
<style>
.ce-filter-bar  { padding:10px 14px; background:#f5f7ff; border-bottom:1px solid #e8eaf6; display:flex; align-items:center; gap:8px; }
.ce-filter-bar select { padding:5px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:13px; background:#fff; }
.ce-filter-label { font-size:13px; font-weight:600; color:#1a237e; }
.ce-dl-wrap { padding:16px 20px; display:flex; gap:10px; align-items:center; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'.js'; ?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php $today = date("j M, Y"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'1.js'; ?>"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Exportation des donn&eacute;es comptabilit&eacute;s &eacute;l&egrave;ves</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top style="padding:0;">

<?php
$filtre = "non précisé";
if (isset($_POST["anneescolairefiltre"])) {
    $filtre = $_POST["anneescolairefiltre"];
    $anneeScolaire = $_POST["anneescolairefiltre"];
}
?>

<div class="ce-filter-bar">
  <form method=post action="compta_export.php" style="margin:0; display:flex; align-items:center; gap:8px;">
    <span class="ce-filter-label">Filtre :</span>
    <select onChange='this.form.submit()' name='anneescolairefiltre'>
      <option><?php print LANGCHOIX ?></option>
      <?php filtreAnneeScolaireSelect($filtre) ?>
    </select>
  </form>
</div>

<?php
require_once "./librairie_php/class.writeexcel_workbook.inc.php";
require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

$fichier = "./data/fichier_ASCII/export_compta_".$_SESSION["id_pers"].".xls";
@unlink($fichier);

$workbook  = new writeexcel_workbook($fichier);
$worksheet1 =& $workbook->addworksheet('Listing');

$header =& $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('blue');

$center =& $workbook->addformat();
$center->set_align('left');

$worksheet1->set_selection('A0');
$worksheet1->write(0, 0, "Nom", $header);
$worksheet1->write(0, 1, utf8_decode("Prénom"), $header);
$worksheet1->write(0, 2, "Classe", $header);
$worksheet1->write(0, 3, utf8_decode("Montant à payer"), $header);
$worksheet1->write(0, 4, utf8_decode("Reste à payer"), $header);
$worksheet1->write(0, 5, utf8_decode("Année"), $header);

$datalisting = listingEleve();
for ($i = 1; $i < countTriade($datalisting); $i++) {
    $ideleve = $datalisting[$i][0];
    $donnee  = $datalisting[$i][1];
    $worksheet1->write($i, 0, "$donnee", $center);
    $donnee = $datalisting[$i][2];
    $worksheet1->write($i, 1, "$donnee", $center);
    $donnee = chercheClasse_nom($datalisting[$i][3]);
    $worksheet1->write($i, 2, "$donnee", $center);

    $montant = 0;
    $montantnonpayer = 0;
    $idclasse = recupIdClasseEleve($ideleve, $anneeScolaire);
    $dataV = recupConfigVersement($idclasse, $filtre);
    if ($dataV == "") { $dataV = array(); }
    $dataVE = recupConfigVersementEleve($ideleve, $filtre);
    if ($dataVE == "") { $dataVE = array(); }
    $dataV = array_merge($dataV, $dataVE);
    for ($j = 0; $j < countTriade($dataV); $j++) {
        $nb++;
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
        if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
            $montantnonpayer += $dataV[$j][3];
        }
        if (($montantVers < $dataV[$j][3]) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00")) {
            $montantnonpayer += $dataV[$j][3] - $montantVers;
        }
        $montant += $dataV[$j][3];
    }
    $worksheet1->write($i, 3, "$montant", $center);
    $worksheet1->write($i, 4, "$montantnonpayer", $center);
    $worksheet1->write($i, 5, "$filtre", $center);
}
$workbook->close();
?>

<?php if ($filtre != "non précisé"): ?>
<div class="ce-dl-wrap">
  <input type=button
         onclick="open('visu_document.php?fichier=<?php print $fichier; ?>','_blank','');"
         value="R&eacute;cup&eacute;ration de l'exportation"
         class="btn-primary">
  <script>buttonMagicRetour('compta_listing.php','_self')</script>
</div>
<?php endif; ?>

</td></tr></table>

<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'2.js'; ?>"></SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
