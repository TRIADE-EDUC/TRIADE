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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGBASE42 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("librairie_php/db_triade.php");

$fichier  = $_FILES["fichier1"]["name"];
$type     = $_FILES["fichier1"]["type"];
$tmp_name = $_FILES["fichier1"]["tmp_name"];

if ( (!empty($fichier)) && (($type == "application/octet-stream") || ($type == "application/vnd.ms-excel"))) {
	move_uploaded_file($tmp_name, "data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement1.xls");
	@unlink("data/fichier_gep/$fichier");
	$fic_xls = "data/fichier_gep/traitement1.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
	$cnx = cnx();
	$nbeleveaffecte = 0;
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		$params["nomparent"]      = strtolower(trim(addslashes($data->sheets[0]['cells'][$i][2])));
		$params["prenomparent"]   = strtolower(trim(addslashes($data->sheets[0]['cells'][$i][3])));
		$params["teltuteur1"]     = $data->sheets[0]['cells'][$i][4];
		$params["telportable1"]   = $data->sheets[0]['cells'][$i][5];
		$params["telprofession1"] = $data->sheets[0]['cells'][$i][6];
		$params["email1"]         = $data->sheets[0]['cells'][$i][7];
		$params["adresse1"]       = preg_replace('/,/', '', trim(addslashes($data->sheets[0]['cells'][$i][9])));
		$params["adresse1"]      .= ", ".preg_replace('/,/', '', trim(addslashes($data->sheets[0]['cells'][$i][10])));
		$params["adresse1"]      .= ", ".preg_replace('/,/', '', trim(addslashes($data->sheets[0]['cells'][$i][11])));
		$params["ville1"]         = trim(addslashes($data->sheets[0]['cells'][$i][13]));
		$params["codepostal1"]    = trim(addslashes($data->sheets[0]['cells'][$i][14]));
		$params["emploi"]         = ucfirst(strtolower(trim(addslashes($data->sheets[0]['cells'][$i][19]))));
		$respFinancier            = trim(addslashes($data->sheets[0]['cells'][$i][21]));
		$params["responsable"]    = trim(addslashes($data->sheets[0]['cells'][$i][24]));
		$params["parente"]        = trim(addslashes($data->sheets[0]['cells'][$i][25]));
		$params["sexe"]           = trim(addslashes($data->sheets[0]['cells'][$i][26]));
		$params["numEleve"]       = $data->sheets[0]['cells'][$i][36];
		$params["maileleve"]      = trim($data->sheets[0]['cells'][$i][43]);
		$params["regime"]         = trim($data->sheets[0]['cells'][$i][53]);
		$respLegal                = trim(addslashes($data->sheets[0]['cells'][$i][24]));

		if (($respLegal == 1) || ($respLegal == 2)) {
			$cr = modif_eleve_sconet($params);
			if ($cr) {
				$nbeleveaffecte++;
			}
			unset($params);
		}
	}

	Pgclose();
	@unlink("data/fichier_gep/traitement1.xls");
?>
<div class="na-card">
  <p style="font-size:13px;color:#555;margin:0;">— <?php print "Nombre d'éléments mis à jour" ?> : <strong><?php print $nbeleveaffecte ?></strong></p>
</div>
<?php
} else {
?>
<div style="margin:10px;padding:10px 14px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:13px;color:#c62828;text-align:center;">
  <?php print LANGbasededon203 ?><br><br>
  <?php print LANGDISP26 ?><br><br>
  <?php print "Information Support : $type" ?>
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
