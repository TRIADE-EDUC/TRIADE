<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
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
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Tableau de statistique</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
$dateDebut = $_POST["saisie_date_debut"];
$dateFin   = $_POST["saisie_date_fin"];

require_once "./librairie_php/class.writeexcel_workbook.inc.php";
require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

$fichier = "./data/fichier_ASCII/statistique.xls";
@unlink($fichier);

$workbook = new writeexcel_workbook($fichier);

$header = $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('blue');

$center = $workbook->addformat();
$center->set_align('left');

$worksheet1 = $workbook->addworksheet('Matière');
$worksheet1->write(0, 0, "Matière",      $header);
$worksheet1->write(0, 1, "Nb heure réalisé", $header);
$worksheet1->write(0, 2, "Coût réalisé",     $header);

$worksheet2 = $workbook->addworksheet('Classe');
$worksheet2->write(0, 0, "Classe",       $header);
$worksheet2->write(0, 1, "Nb heure réalisé", $header);
$worksheet2->write(0, 2, "Coût réalisé",     $header);

$worksheet3 = $workbook->addworksheet('Enseignant');
$worksheet3->write(0, 0, "Enseignant",   $header);
$worksheet3->write(0, 1, "Nb heure réalisé", $header);
$worksheet3->write(0, 2, "Coût réalisé",     $header);

$data = affMatiere();
$j = 1;
for ($i = 0; $i < countTriade($data); $i++) {
    $matiere    = $data[$i][1];
    $sousmatiere = $data[$i][2];
    $idmatiere  = $data[$i][0];
    $Nb_heure_realise = nbHeureVacationParIdMatiere($idmatiere, $dateDebut, $dateFin);
    $tab_type_prestation = recupTypePrestationViaIdMatiere($idmatiere);
    $tabtmp = recupNbHeureEffectueViaIdMatiere($idmatiere, $dateDebut, $dateFin);
    $Cout_realise = 0;
    for ($k = 0; $k < countTriade($tabtmp); $k++) {
        $taux = affTauxViaId($tabtmp[$k][1]);
        list($nbheure) = preg_split('/:/', $tabtmp[$k][0]);
        $Cout_realise += $taux * preg_replace('/^0/', '', $nbheure);
    }
    if (empty($Nb_heure_realise)) continue;
    $sousmatiere = preg_replace('/0$/', '', $sousmatiere);
    $worksheet1->write($j, 0, "$matiere $sousmatiere", $center);
    $worksheet1->write($j, 1, "$Nb_heure_realise",     $center);
    $worksheet1->write($j, 2, "$Cout_realise",         $center);
    $j++;
}

unset($Nb_heure_realise);
$data = affClasse();
$j = 1;
for ($i = 0; $i < countTriade($data); $i++) {
    $classe   = $data[$i][1];
    $idclasse = $data[$i][0];
    $Nb_heure_realise = nbHeureVacationParIdClasse($idclasse, $dateDebut, $dateFin);
    $tabtmp = recupNbHeureEffectueViaIdClasse($idclasse, $dateDebut, $dateFin);
    $Cout_realise = 0;
    for ($k = 0; $k < countTriade($tabtmp); $k++) {
        $taux = affTauxViaId($tabtmp[$k][1]);
        list($nbheure) = preg_split('/:/', $tabtmp[$k][0]);
        $Cout_realise += $taux * preg_replace('/^0/', '', $nbheure);
    }
    if (($idclasse > 0) && ($Nb_heure_realise != '')) {
        $worksheet2->write($j, 0, "$classe",          $center);
        $worksheet2->write($j, 1, "$Nb_heure_realise", $center);
        $worksheet2->write($j, 2, "$Cout_realise",     $center);
        $j++;
    }
}

unset($Nb_heure_realise);
$data = affPersActif('ENS');
$j = 1;
for ($i = 0; $i < countTriade($data); $i++) {
    $nomprenom = ucwords($data[$i][2])." ".ucwords($data[$i][3]);
    $idpers    = $data[$i][0];
    $Nb_heure_realise = nbHeureVacationParIdPers($idpers, $dateDebut, $dateFin);
    $tabtmp = recupNbHeureEffectueViaIdPers($idpers, $dateDebut, $dateFin);
    $Cout_realise = 0;
    for ($k = 0; $k < countTriade($tabtmp); $k++) {
        $taux = affTauxViaId($tabtmp[$k][1]);
        list($nbheure) = preg_split('/:/', $tabtmp[$k][0]);
        $Cout_realise += $taux * preg_replace('/^0/', '', $nbheure);
    }
    if (($idpers > 0) && ($Nb_heure_realise != '')) {
        $worksheet3->write($j, 0, "$nomprenom",       $center);
        $worksheet3->write($j, 1, "$Nb_heure_realise", $center);
        $worksheet3->write($j, 2, "$Cout_realise",     $center);
        $j++;
    }
}
$workbook->close();
?>

<div class="alert alert-success" style="margin:14px 16px">
  <i class="bi bi-file-earmark-spreadsheet"></i>
  Fichier généré — période du <b><?php print $dateDebut ?></b> au <b><?php print $dateFin ?></b>
</div>

<div class="toolbar" style="padding:10px 16px;border-top:1px solid #e8eaf6">
  <input type="button" onclick="open('visu_document.php?fichier=<?php print $fichier ?>','_blank','');" value="Récupérer les statistiques (XLS)" class="bouton2">
  <script language="JavaScript">buttonMagicRetour("gestion_statistique.php","_self")</script>
</div>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
