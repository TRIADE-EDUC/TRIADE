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
<title>Triade - Export cantine - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Exportation des données cantine</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$cnx = cnx();
if ((verifDroit($_SESSION["id_pers"], "cantine")) || ($_SESSION["membre"] == "menuadmin")) {

    require_once "./librairie_php/xlsxwriter.class.php";
    if (!is_dir("./data/cantine/")) { mkdir("./data/cantine/"); htaccess("./data/cantine/"); }
    $fichier = "./data/cantine/export_cantine_" . $_SESSION["id_pers"] . ".xlsx";
    @unlink($fichier);
    $writer = new XLSXWriter();
    $writer->writeSheetHeader('Compta', ['NOM' => 'string', 'PRENOM' => 'string', 'CLASSE' => 'string', 'CREDIT' => 'string']);
    $data = recupCreditCantine();
    for ($i = 0; $i < countTriade($data); $i++) {
        $idpers  = $data[$i][0];
        $membre  = $data[$i][2];
        $credit  = number_format(recupSumCreditCantine($idpers, $membre), 2, ',', '');
        if ($membre == "menueleve") {
            $nom    = recherche_eleve_nom($idpers, $membre);
            $prenom = recherche_eleve_prenom($idpers, $membre);
            $classe = chercheClasse_nom(chercheIdClasseDunEleve($idpers));
        } else {
            $nom    = recherche_personne_nom($idpers, $membre);
            $prenom = recherche_personne_prenom($idpers, $membre);
            $classe = "Staff";
        }
        $writer->writeSheetRow('Compta', [$nom, $prenom, $classe, $credit]);
    }
    $writer->writeToFile($fichier);
?>

<div class="card">
  <div class="card-header card-header-primary">
    <span>Export comptabilité cantine</span>
  </div>
  <div class="card-body" style="text-align:center; padding:16px;">
    <p style="margin-bottom:14px;color:#555;font-size:12px;">Le fichier Excel a été généré avec succès.</p>
    <div style="display:flex;justify-content:center;gap:10px;">
      <button class="btn btn-primary" onclick="open('visu_document.php?fichier=<?php print $fichier ?>','_blank','')">
        Télécharger l'export XLSX
      </button>
      <script language="JavaScript">buttonMagicRetour('cantine.php','_self')</script>
    </div>
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
