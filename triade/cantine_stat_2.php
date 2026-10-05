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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Statistiques cantine - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Relevé complet des passages à la cantine</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$cnx = cnx();
if ((verifDroit($_SESSION["id_pers"], "cantine")) || ($_SESSION["membre"] == "menuadmin")) {

    $dateDebut = $_POST["saisie_date_debut"];
    $dateFin   = $_POST["saisie_date_fin"];

    require_once "./librairie_php/class.writeexcel_workbook.inc.php";
    require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

    if (!is_dir("./data/cantine/")) {
        mkdir("./data/cantine/");
        htaccess("./data/cantine/");
    }

    $fichier = "./data/cantine/export_cantine_".$_SESSION["id_pers"].".xls";
    @unlink($fichier);

    $workbook  = new writeexcel_workbook($fichier);
    $worksheet1 = $workbook->addworksheet("Listing");

    $header = $workbook->addformat();
    $header->set_color('white');
    $header->set_align('center');
    $header->set_align('vcenter');
    $header->set_pattern();
    $header->set_fg_color('blue');

    $header1 = $workbook->addformat();
    $header1->set_color('black');
    $header1->set_align('vcenter');
    $header1->set_pattern();
    $header1->set_fg_color('yellow');

    $center = $workbook->addformat();
    $center->set_align('left');

    $worksheet1->set_selection('A0');
    $worksheet1->write(0, 0, utf8_decode("Période du $dateDebut au $dateFin"), $header1);
    $worksheet1->write(1, 0, "Plateau",         $header);
    $worksheet1->write(1, 1, "Prix du Plateau", $header);
    $worksheet1->write(1, 2, "Nb de Plateau",   $header);
    $worksheet1->write(1, 3, "Total",            $header);

    $a = 2;
    $datalisting = listingCantine($dateDebut, $dateFin);
    for ($i = 0; $i < countTriade($datalisting); $i++) {
        if (trim($datalisting[$i][1]) == "") continue;
        $worksheet1->write($a, 0, utf8_decode(urldecode($datalisting[$i][1])), $center);
        $worksheet1->write($a, 1, utf8_decode(preg_replace("/^-/", '', $datalisting[$i][3])), $center);
        $worksheet1->write($a, 2, utf8_decode($datalisting[$i][2]), $center);
        $worksheet1->write($a, 3, utf8_decode(preg_replace("/^-/", '', $datalisting[$i][0])), $center);
        $a++;
    }
    $workbook->close();
?>

<div class="card">
  <div class="card-header card-header-primary">
    <span>Export statistiques — du <?php print $dateDebut ?> au <?php print $dateFin ?></span>
  </div>
  <div class="card-body" style="text-align:center;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
    <button type="button" class="btn btn-primary"
      onclick="open('visu_document.php?fichier=<?php print $fichier ?>','_blank','')">
      Récupérer l'exportation
    </button>
    <script language="JavaScript">buttonMagicRetour('cantine.php','_self')</script>
  </div>
</div>

<?php } else { ?>
<div class="alert alert-danger">Accès réservé</div>
<?php } ?>

</td></tr></table>
<?php
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
