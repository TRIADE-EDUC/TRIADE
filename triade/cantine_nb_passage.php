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
<title>Triade - Passages cantine - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
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

    $titre = "Information au " . dateDMY() . " à " . dateHIS();
    $liste_eleveRegime     = recupListEleveRegime();
    $nb_elevedemipension   = countTriade($liste_eleveRegime);
    $nb_eleve_abs          = nbrEleveAbsCantineAujourdhui($liste_eleveRegime);
    $nb_eleve_en_stage     = nbrEleveEnStageAujourdhui($liste_eleveRegime);
    $total                 = $nb_elevedemipension - $nb_eleve_abs - $nb_eleve_en_stage;

    define('FPDF_FONTPATH', './librairie_pdf/fpdf/font/');
    include_once('./librairie_pdf/fpdf/fpdf.php');
    include_once('./librairie_pdf/html2pdf.php');
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetTitle("Rapport - Passage Cantine");
    $pdf->SetCreator("T.R.I.A.D.E.");
    $pdf->SetSubject("Rapport Passage Cantine");
    $pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com");
    $X = 10; $Y = 10;
    $pdf->SetFont('Arial', 'U', 14);
    $pdf->SetXY($X, $Y);
    $pdf->MultiCell(210, 10, "$titre", 0, 'L', 0);
    $pdf->SetFont('Arial', '', 12);
    foreach ([
        "Nombre d'élève au régime : $nb_eleveRegime",
        "Nombre d'élève absent : $nb_eleve_abs",
        "Nombre d'élève en stage : $nb_eleve_en_stage",
        "Nombre d'élève présent : <b>$total</b>",
    ] as $contenu) {
        $pdf->SetXY($X, $Y += 5);
        $pdf->WriteHTML($contenu);
    }
    $fichier = "./data/pdf_certif/passage_cantine_" . $_SESSION["id_pers"] . ".pdf";
    @unlink($fichier);
    $pdf->output('F', $fichier);
    $pdf->close();
?>

<div class="card">
  <div class="card-header card-header-primary">
    <span>Information au <?php print dateDMY() ?> à <?php print dateHIS() ?></span>
  </div>
  <table class="table" style="margin:0;">
    <tbody>
      <tr class="cc-tr-data">
        <td>Nombre d'élèves au régime</td>
        <td><strong><?php print countTriade($liste_eleveRegime) ?></strong></td>
      </tr>
      <tr class="cc-tr-data">
        <td>Nombre d'élèves absent</td>
        <td><?php print $nb_eleve_abs ?></td>
      </tr>
      <tr class="cc-tr-data">
        <td>Nombre d'élèves en stage</td>
        <td><?php print $nb_eleve_en_stage ?></td>
      </tr>
      <tr class="cc-tr-data" style="font-weight:700;color:#080A66;">
        <td>Nombre d'élèves présents</td>
        <td><?php print $total ?></td>
      </tr>
    </tbody>
  </table>
  <div class="card-body" style="text-align:center;padding:10px;">
    <button class="btn btn-primary" onclick="open('visu_pdf_id.php?id=./data/pdf_certif/passage_cantine_&fichiername=Passage Cantine.pdf','_blank','')">
      Imprimer le relevé PDF
    </button>
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
