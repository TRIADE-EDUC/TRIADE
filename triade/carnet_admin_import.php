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
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

$taille=2000000;
$taille2="2Mo";

include_once("librairie_php/lib_get_init.php");
include_once("common/config6.inc.php");

if (MAXUPLOAD == "oui") {
	$id=php_ini_get("safe_mode");
	if ($id != 1) {
		set_time_limit(600);
		$taille=8000000;
		$taille2="8Mo";
	}
}

$erreurSQLITE="";
$disabled="";
if (php_module_load("SQLite") != 1) {
	$erreurSQLITE="<div style='color:#c00;font-weight:700;padding:8px 10px;margin:8px 5px;background:#fff0f0;border-radius:6px;border:1px solid #fcc;'>".LANGEDT10."</div>";
	$disabled="disabled='disabled'";
}

$result_html="";
if (isset($_POST["create"])) {
	$fichier=$_FILES['fichier']['name'];
	$type=$_FILES['fichier']['type'];
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	if ((!empty($fichier)) && ($size <= $taille) && ($type == "text/xml")) {
		@unlink("data/fichier_ASCII/cds.xml");
		move_uploaded_file($tmp_name,"data/fichier_ASCII/cds.xml");
		$xml=simplexml_load_file("data/fichier_ASCII/cds.xml");
		foreach ($xml->PARAMETRAGE as $PARAM) {
			$versionTRIADE=$PARAM->VERSION_TRIADE;
			$versionPATCH=$PARAM->VERSION_PATCH;
			$versionXMLCDS=$PARAM->VERSION_XML_CDS;
			$dateCreationXML=$PARAM->DATE_CREATION_XML;
		}
		foreach ($xml->LES_CARNETS->UN_CARNET as $UN_CARNET) {
			$cr=create_carnet(accent_export($UN_CARNET->NOM_CARNET),$UN_CARNET->CODE_LETTRE,$UN_CARNET->CODE_CHIFFRE,$UN_CARNET->CODE_COULEUR,$UN_CARNET->CODE_NOTE,'',$UN_CARNET->NB_PERIODE);
			$nom_carnet=accent_export($UN_CARNET->NOM_CARNET);
		}
		if ($cr == -1) {
			$result_html="<div style='padding:8px 10px;margin:8px 5px;background:#fff8e1;border-radius:6px;border:1px solid #f5c842;'>".LANGCARNET66."</div>";
		} else {
			$idcarnet=chercheIdCarnet($nom_carnet);
			foreach ($xml->LES_COMPETENCES->UNE_COMPETENCE as $UNE_COMPETENCE) {
				$nomCompetence=accent_export($UNE_COMPETENCE->NOM_COMPETENCE);
				$ordre=$UNE_COMPETENCE->ORDRE;
				$idcompetence=enr_competence_import($idcarnet,$nomCompetence,$ordre);
				foreach ($UNE_COMPETENCE->DES_DESCRIPTIFS->UN_DESCRIPTIF as $UN_DESCRIPTIF) {
					$descriptif=accent_export($UN_DESCRIPTIF->LIBELLE);
					$titre=accent_export($UN_DESCRIPTIF->TITRE);
					$ordre=$UN_DESCRIPTIF->ORDRE;
					enr_descriptif_import($idcarnet,$idcompetence,$titre,$descriptif,$ordre);
				}
			}
			Pgclose();
			unlink("data/fichier_ASCII/cds.xml");
			$result_html="<div style='text-align:center;padding:12px 10px;margin:8px 5px;background:#d4edda;border-radius:6px;border:1px solid #b8dac4;font-weight:700;color:#155724;'>".LANGCARNET633."</div>";
		}
	}
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGCARNET63 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<?php print $erreurSQLITE; ?>

<form method="post" name="formulaire" enctype="multipart/form-data">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGCARNET64 ?> :</span>
    <span style="display:flex;align-items:center;gap:8px;">
      <input type="file" name="fichier">
      <span class="htip-wrap"><img src="./image/help.gif" width="15" height="15" border="0" align="middle"><span class="htip"><?php print LANGEDT1 ?> — <?php print LANGEDT1bis ?> <b><?php print $taille2 ?></b></span></span>
    </span>
  </div>
</div>
<br>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 5px;">
  <button type="button" class="btn-retour" onclick="open('carnet_admin.php','_parent','')"><?php print LANGCIRCU14 ?></button>
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGAGENDA86 ?>","create","<?php print $disabled ?>");</script></span>
</div>
<br><br>
</form>

<?php print $result_html; ?>

</td></tr></table>

<?php
if ($_SESSION["membre"] == "menuadmin") {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
