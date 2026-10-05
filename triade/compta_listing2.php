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
<title>Triade - Listing paiements <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]; ?></title>
<style>
.cl2-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.cl2-card-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:10px 16px; font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.cl2-card-badge { background:rgba(255,255,255,.22); border-radius:20px; padding:2px 12px; font-size:11px; font-weight:700; text-transform:none; letter-spacing:0; white-space:nowrap; }
.cl2-body { padding:20px 20px 16px; display:flex; flex-direction:column; align-items:center; gap:14px; }
.cl2-info { font-size:13px; color:#333; text-align:center; }
.cl2-info strong { color:#080A66; }
.cl2-icon { font-size:36px; color:#c5cae9; }
.cl2-btn-row { display:flex; gap:10px; align-items:center; justify-content:center; }
.cl2-dl-btn { padding:8px 20px; background:#080A66; color:#fff; border:none; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer; }
.cl2-dl-btn:hover { background:#1e4d8c; }
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
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'1.js'; ?>"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Listing des paiements par classe</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top style="padding:8px 4px;">

<?php
include_once("./librairie_php/class.writeexcel_workbook.inc.php");
include_once("./librairie_php/class.writeexcel_worksheet.inc.php");

$anneescolairefiltre = $_POST["anneescolairefiltre"];
$idclasse  = $_POST["sClasseGrp"];
$nomclasse = chercheClasse_nom($idclasse);

$fichier = "./data/fichier_ASCII/export_compta_".$_SESSION["id_pers"].".xls";
@unlink($fichier);

$workbook  = new writeexcel_workbook($fichier);
$worksheet1 =& $workbook->addworksheet("Listing $nomclasse");

$header =& $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('blue');

$center =& $workbook->addformat();
$center->set_align('left');

$worksheet1->set_selection('A0');
$worksheet1->write(0, 0, "Nom",              $header);
$worksheet1->write(0, 1, "Prénom",           $header);
$worksheet1->write(0, 2, "Montant à payer",  $header);
$worksheet1->write(0, 3, "Mode de paiement", $header);
$worksheet1->write(0, 4, "Année",            $header);

$sql = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves,{$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
$res = execSql($sql);
$datalisting = chargeMat($res);

for ($i = 1; $i < countTriade($datalisting); $i++) {
    $ideleve = $datalisting[$i][1];
    $nom     = $datalisting[$i][2];
    $prenom  = $datalisting[$i][3];
    $montant = "";
    $data = recupConfigVersementEleveEtClasse($ideleve, $anneescolairefiltre);
    for ($j = 0; $j < countTriade($data); $j++) {
        $dateVersement = $data[$j][4];
        if ($dateVersement != "") { $dateVersement = dateForm($dateVersement); }
        $montant      = number_format($data[$j][3], 2, '.', '');
        $modepaiement = $data[$j][5];
        $worksheet1->write($i, 0, "$nom",                  $center);
        $worksheet1->write($i, 1, "$prenom",               $center);
        $worksheet1->write($i, 2, "$montant",              $center);
        $worksheet1->write($i, 3, "$modedepaiement",       $center);
        $worksheet1->write($i, 4, "$anneescolairefiltre",  $center);
    }
}
$workbook->close();
?>

<div class="cl2-card">
  <div class="cl2-body">
    <div class="cl2-icon">&#128196;</div>
    <div class="cl2-info">
      Fichier g&eacute;n&eacute;r&eacute; pour la classe <strong><?php print htmlspecialchars(ucwords($nomclasse)) ?></strong><br>
      Ann&eacute;e scolaire <strong><?php print htmlspecialchars($anneescolairefiltre) ?></strong>
    </div>
    <div class="cl2-btn-row">
      <input type=button
             onclick="open('visu_document.php?fichier=<?php print $fichier ?>','_blank','');"
             value="T&eacute;l&eacute;charger l'export"
             class="cl2-dl-btn">
      <script language=JavaScript>buttonMagicRetour('compta_listing.php','_self')</script>
    </div>
  </div>
</div>

</td></tr></table>

<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'2.js'; ?>"></SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
