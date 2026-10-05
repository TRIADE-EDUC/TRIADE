<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id = php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importation SIECLE BEE (XML / ZIP)</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
include_once("librairie_php/lib_siecle_bee.php");

validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);

$upload_dir = "data/fichier_gep/";
if (!is_dir($upload_dir)) {
	@mkdir($upload_dir, 0777, true);
}

$uploaded_paths = array();

// 1. Récupération des fichiers téléversés (support upload unique ou multiple)
if (isset($_FILES['fichiers']) && is_array($_FILES['fichiers']['name'])) {
	$count = count($_FILES['fichiers']['name']);
	for ($i = 0; $i < $count; $i++) {
		$fname = $_FILES['fichiers']['name'][$i];
		$ferr  = $_FILES['fichiers']['error'][$i];
		$ftmp  = $_FILES['fichiers']['tmp_name'][$i];

		if ($ferr == UPLOAD_ERR_OK && !empty($fname)) {
			$ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
			if ($ext === 'xml' || $ext === 'zip') {
				$target = $upload_dir . "siecle_up_" . time() . "_" . $i . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fname);
				if (move_uploaded_file($ftmp, $target)) {
					$uploaded_paths[] = $target;
				}
			}
		}
	}
}

// Support rétrocompatibilité champ unique "fichier1"
if (empty($uploaded_paths) && isset($_FILES['fichier1'])) {
	$fname = $_FILES['fichier1']['name'];
	$ferr  = $_FILES['fichier1']['error'];
	$ftmp  = $_FILES['fichier1']['tmp_name'];

	if ($ferr == UPLOAD_ERR_OK && !empty($fname)) {
		$ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
		if ($ext === 'xml' || $ext === 'zip') {
			$target = $upload_dir . "siecle_up_" . time() . "_0_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fname);
			if (move_uploaded_file($ftmp, $target)) {
				$uploaded_paths[] = $target;
			}
		}
	}
}

if (!empty($uploaded_paths)) {

	$options = array(
		'update'       => isset($_POST['update']) ? (int)$_POST['update'] : 0,
		'updatevide'   => isset($_POST['updatevide']) ? (int)$_POST['updatevide'] : 0,
		'updatepasswd' => isset($_POST['updatepasswd']) ? (int)$_POST['updatepasswd'] : 0,
		'vide_eleve'   => isset($_POST['vide_eleve']) ? $_POST['vide_eleve'] : ''
	);

	// Exécution du moteur d'importation
	$result = siecle_bee_import_files($uploaded_paths, $options);

	// Nettoyage des fichiers uploadés initiaux
	foreach ($uploaded_paths as $up) {
		@unlink($up);
	}
?>
<div class="na-card">
  <div class="na-lbl" style="margin-bottom:8px;">Bilan de l'importation SIECLE-BEE :</div>

  <?php if (!empty($result['fichiers_traites'])) : ?>
  <div style="margin-bottom:12px;">
    <div class="dest-row-sub" style="font-weight:bold;margin-bottom:4px;">Fichiers détectés et analysés :</div>
    <ul class="dest-row-sub" style="margin:0;padding-left:18px;">
      <?php foreach ($result['fichiers_traites'] as $ft) : ?>
      <li><strong><?php print htmlspecialchars($ft['fichier']) ?></strong> &mdash; Type : <em><?php print htmlspecialchars($ft['type']) ?></em></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <table class="cc-data-table" style="margin:10px 0;">
    <tr class="cc-thead-row">
      <th class="cc-th">Élément</th>
      <th class="cc-th cc-th-center">Quantité</th>
    </tr>
    <tr class="cc-tr-data">
      <td class="cc-td">Classes créées / identifiées</td>
      <td class="cc-td cc-td-center"><strong><?php print (int)$result['classes_creees'] ?></strong></td>
    </tr>
    <tr class="cc-tr-data">
      <td class="cc-td">Responsables légaux (tuteurs) associés</td>
      <td class="cc-td cc-td-center"><strong><?php print (int)$result['responsables_charges'] ?></strong></td>
    </tr>
    <tr class="cc-tr-data">
      <td class="cc-td">Total élèves traités</td>
      <td class="cc-td cc-td-center"><strong><?php print (int)$result['eleves_total'] ?></strong></td>
    </tr>
    <tr class="cc-tr-data">
      <td class="cc-td">Nouveaux élèves inscrits</td>
      <td class="cc-td cc-td-center" style="color:#2e7d32;"><strong><?php print (int)$result['eleves_crees'] ?></strong></td>
    </tr>
    <tr class="cc-tr-data">
      <td class="cc-td">Élèves mis à jour</td>
      <td class="cc-td cc-td-center" style="color:#0277bd;"><strong><?php print (int)$result['eleves_mis_a_jour'] ?></strong></td>
    </tr>
    <tr class="cc-tr-data">
      <td class="cc-td">Élèves déjà présents (sans mise à jour)</td>
      <td class="cc-td cc-td-center"><strong><?php print (int)$result['eleves_deja_presents'] ?></strong></td>
    </tr>
    <?php if ($result['eleves_supprimes'] > 0) : ?>
    <tr class="cc-tr-data">
      <td class="cc-td">Élèves radiés (date de sortie échue)</td>
      <td class="cc-td cc-td-center" style="color:#c62828;"><strong><?php print (int)$result['eleves_supprimes'] ?></strong></td>
    </tr>
    <?php endif; ?>
    <?php if ($result['eleves_erreurs'] > 0) : ?>
    <tr class="cc-tr-data">
      <td class="cc-td">Erreurs d'insertion</td>
      <td class="cc-td cc-td-center" style="color:#c62828;"><strong><?php print (int)$result['eleves_erreurs'] ?></strong></td>
    </tr>
    <?php endif; ?>
  </table>

  <?php if (!empty($result['erreurs'])) : ?>
  <div style="margin-top:10px;padding:8px 12px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:12px;color:#c62828;">
    <strong>Avertissements :</strong><br>
    <?php foreach ($result['erreurs'] as $err) : ?>
      &bull; <?php print htmlspecialchars($err) ?><br>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="dest-row-sub" style="margin-top:10px;">
    <?php print LANGBASE9 ?> (<?php print LANGBASE8bis ?>)
  </div>
  <div class="dest-row-sub" style="color:#c62828;margin-top:4px;">
    <?php print LANGBASE17 ?>
  </div>

</div>

<div class="na-foot">
  <?php if (file_exists("./data/fic_pass.txt") && filesize("./data/fic_pass.txt") > 0) : ?>
  <button type="button" class="btn-enr" onclick="open('recupepw.php','_blank','')"><?php print LANGBT40 ?></button>
  <?php endif; ?>
  <button type="button" class="btn-enr" onclick="location.href='acces2.php'"><?php print LANGBT41 ?></button>
  <button type="button" class="btn-retour" onclick="location.href='base_de_donne_importation1110.php'"><?php print LANGBT24 ?></button>
</div>

<?php
} else {
?>
<div style="margin:10px;padding:10px 14px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:13px;color:#c62828;text-align:center;">
  <?php print LANGbasededon203 ?><br><br>
  Veuillez fournir un ou plusieurs fichiers valides au format XML (ex: <em>ElevesAvecAdresses.xml</em>, <em>ResponsablesAvecAdresses.xml</em>, <em>Structures.xml</em>) ou une archive <b>.zip</b> globale.
</div>
<div class="na-foot">
  <button type="button" class="btn-retour" onclick="history.go(-1)"><?php print LANGBT24 ?></button>
</div>
<?php
}
?>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
