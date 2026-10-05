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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id = php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(300);
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importation d'un fichier</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("librairie_php/db_triade.php");

$fichier  = $_FILES["fichier1"]["name"];
$type     = $_FILES["fichier1"]["type"];
$tmp_name = $_FILES["fichier1"]["tmp_name"];

$optionligne = 1;
if ($_POST['optionligne'] == 1) { $optionligne = 0; }
if ( (!empty($fichier)) && (($type == "application/octet-stream") || ($type == "application/vnd.ms-excel"))) {
	move_uploaded_file($tmp_name, "data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement.xls");
	@unlink("data/fichier_gep/$fichier");
	$fic_xls     = "data/fichier_gep/traitement.xls";
	$typefichier = "excel";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);

	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			if ($i == $optionligne) { continue; }
			if ($i == 1) { continue; }
			if (strtolower($data->sheets[0]['cells'][$i][35]) == "g") { continue; }
			$donnee = $data->sheets[0]['cells'][$i][34];
			$tab11[$donnee] = $donnee;
		}
	}

	if ($_POST["vide_eleve"] == "oui") {
		$cnx = cnx();
		purge_element_eleve();
		Pgclose();
	}

	@ksort($tab11);
	$ligne = 0;
	print "<br><form method='post' action='base_de_donne_importation320.php'>";
	print "<input type=hidden name='fichier' value=\"$fic_xls\">";
	print "<input type=hidden name='typefichier' value=\"$typefichier\">";
	print "<div class='na-card'><p style='font-size:12px;color:#555;margin:0 0 8px;'>".LANGIMP42."</p>";
	print "<table style='border-collapse:collapse;width:100%;'><tbody>";
	$cnx = cnx();
	foreach ($tab11 as $clef => $b) {
		if (strlen(trim($clef))) {
			?>
			<tr class="cc-tr-data">
			<td style="border:1px solid #d0d4f0;padding:4px 8px;"><input type="text" name="saisie_ref[]" value="<?php print $clef ?>" size="20" class="cc-select" style="width:120px;" onfocus="this.blur()"></td>
			<td style="border:1px solid #d0d4f0;padding:4px 8px;"><select name="saisie_classe[]" class="cc-select">
			<?php
				print "<option value='-1' style='color:#000066;background-color:#FCE4BA'>".LANGCHOIX."</option>";
				$id_classe = recherche_gep_classe($clef);
				if ($id_classe != "") {
					$nom_classe = chercheClasse_nom($id_classe);
					if ($nom_classe != "") {
						print "<option selected value='$id_classe'>$nom_classe</option>";
					}
				}
				select_classe_gep();
			?>
			</select></td>
			</tr>
			<?php
		}
	}
	Pgclose();
	print "</tbody></table></div>";
?>
<input type="hidden" value="<?php print $_POST['update'] ?>" name="update">
<input type="hidden" value="<?php print $_POST['updatepasswd'] ?>" name="updatepasswd">
<input type="hidden" value="<?php print $_POST['updatevide'] ?>" name="updatevide">
<input type="hidden" value="<?php print $_POST['optionligne'] ?>" name="optionligne">
<div class="na-foot">
  <button type="button" class="btn-retour" onclick="location.reload()">Actualiser</button>
  <button type="button" class="btn-enr" onclick="open('creat_classe_gep.php','ajclass','width=450,height=150')"><?php print LANGBT26 ?></button>
  <button type="submit" class="btn-enr" onclick="attente()"><?php print LANGCHER9 ?> &gt;</button>
</div>
<p style="font-size:11px;color:#888;margin-top:6px;"><?php print LANGbasededon21 ?></p>
</form>
<?php
} else {
?>
<div style="margin:10px;padding:10px 14px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:13px;color:#c62828;text-align:center;">
  <?php print LANGbasededon203 ?><br><br>Le fichier doit être au format xls<br><br>
  <em style="font-size:11px;color:#888;">Information support : <?php print $type ?></em>
</div>
<div class="na-foot">
  <button type="button" class="btn-retour" onclick="history.go(-1)"><?php print LANGBT24 ?></button>
</div>
<?php
}
?>

</td>
</tr>
</table>

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
