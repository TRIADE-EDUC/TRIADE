<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Exportation des entreprises</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
require_once "./librairie_php/class.writeexcel_workbook.inc.php";
require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

$fichier="./data/fichier_ASCII/export_entreprise.xls";
@unlink($fichier);

$workbook = new writeexcel_workbook($fichier);
$worksheet1 =& $workbook->addworksheet('Listing Entreprrise');

$header =& $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('blue');

$center =& $workbook->addformat();
$center->set_align('left');

$worksheet1->set_selection('A0');
$j=0;
$worksheet1->write(0, $j++, "Entreprise", $header);
$worksheet1->write(0, $j++, "adresse",    $header);
$worksheet1->write(0, $j++, "code postal",$header);
$worksheet1->write(0, $j++, "ville",      $header);
$worksheet1->write(0, $j++, "pays",       $header);
$worksheet1->write(0, $j++, "contact",    $header);
$worksheet1->write(0, $j++, "tel",        $header);
$worksheet1->write(0, $j++, "email",      $header);

$datalisting=listingEntreprise();
$ii=1;
for ($i=0; $i<countTriade($datalisting); $i++) {
	$j=0;
	$worksheet1->write($ii, $j++, $datalisting[$i][0],  $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][2],  $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][3],  $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][4],  $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][13], $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][1],  $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][7],  $center);
	$worksheet1->write($ii, $j++, $datalisting[$i][9],  $center);
	$ii++;
}
$workbook->close();
?>

<div class="na-foot" style="justify-content:center;padding:20px 0;">
  <button type="button" class="btn-retour" onclick="open('gestion_stage.php','_self','')">Retour</button>
  <button type="button" class="btn-enr" onclick="open('visu_document.php?fichier=<?php print $fichier ?>','_blank','')">Récupérer l'exportation XLS</button>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION['membre'].'2.js'?>"></SCRIPT>
</BODY></HTML>
