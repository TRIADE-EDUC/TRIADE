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
?>
<html>
<head>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/ajax_codebarre.js"></script>
<script language="JavaScript">
function newkey(form,variable){
	form.text2display.value += variable;
}
function newkeyCode(form,variable){
	form.text2display.value += String.fromCharCode(variable);
}
</script>
<style>
body { background:#f4f6fb; margin:0; padding:12px; font-family:Arial,sans-serif; font-size:12px; }
.cb-toolbar { padding:0 0 12px; }
.cb-grid { display:flex; flex-wrap:wrap; gap:12px; }
.cb-card {
  background:#fff;
  border:1px solid #dde2f4;
  border-radius:8px;
  padding:12px 14px;
  width:220px;
  box-sizing:border-box;
  transition:box-shadow .15s, border-color .15s;
}
.cb-card:hover { border-color:#9ba8d8; box-shadow:0 3px 10px rgba(60,74,138,.13); }
.cb-card-head {
  display:flex;
  align-items:center;
  gap:6px;
  margin-bottom:8px;
  border-bottom:1px solid #eef0fa;
  padding-bottom:6px;
}
.cb-name { font-weight:700; font-size:12px; color:#1a2340; line-height:1.3; flex:1; }
.cb-badge-actif   { width:8px;height:8px;border-radius:50%;background:#43a047;flex-shrink:0; }
.cb-badge-inactif { width:8px;height:8px;border-radius:50%;background:#e53935;flex-shrink:0; }
.cb-card-actions { display:flex; gap:6px; align-items:center; margin-bottom:10px; }
.cb-card-actions a { opacity:.8; line-height:0; }
.cb-card-actions a:hover { opacity:1; }
.cb-card-barcode { text-align:center; min-height:40px; }
.cb-card-barcode img { max-width:100%; }
.cb-input { font-size:12px;padding:2px 4px;border:1px solid #b0b8e0;border-radius:3px;width:100%;box-sizing:border-box;margin-top:4px; }
.cb-warn { margin:0 0 12px;padding:8px 12px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:12px;color:#c62828;text-align:center; }
</style>
</head>
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx = cnx();
?>
<body>
<?php
$version = phpversion();

if (!preg_match('/^8/', $version)) {
	print "<div class='cb-warn'><strong>ATTENTION</strong> : " . LANGCODEBAR2 . "</div>";
}

$saisie_classe = $_GET["idclasse"];
$sql = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res  = execSql($sql);
$data = chargeMat($res);

if (preg_match('/^8/', $version)) {
	print "<div class='cb-toolbar'>";
	print "<button type='button' class='btn-enr' style='font-size:11px;padding:4px 10px;' onclick=\"open('codebarimpr.php?idclasse=$saisie_classe&codebase=" . $_GET["codebase"] . "','_blank','')\">&#128438; Imprimer</button>";
	print "</div>";
}

if (isset($_GET["idvalide"])) {
	valideIdCodeBar($_GET["idvalide"], "menueleve", $_GET["valide"]);
}
if (isset($_GET["idsupp"])) {
	suppIdCodeBar($_GET["idsupp"], "menueleve");
}

print "<form><div class='cb-grid'>";

for ($i = 0; $i < countTriade($data); $i++) {
	$texte  = recupIdCodeBar($data[$i][1], "menueleve");
	$nom    = strtoupper(trim(stripslashes($data[$i][2])));
	$prenom = ucfirst(trim(stripslashes($data[$i][3])));
	$actif  = verifCodebarre($data[$i][1], "menueleve");
	$actvalide = $actif ? 0 : 1;

	print "<div class='cb-card'>";

	print "<div class='cb-card-head'>";
	print "<span class='" . ($actif ? "cb-badge-actif" : "cb-badge-inactif") . "' title='" . ($actif ? "actif" : "non actif") . "'></span>";
	print "<span class='cb-name'>$nom<br><span style='font-weight:400;color:#555;'>$prenom</span></span>";
	print "</div>";

	print "<div class='cb-card-actions'>";
	print "<a href='codebar.php?idclasse=$saisie_classe&idvalide=" . $data[$i][1] . "&valide=$actvalide&codebase=" . $_GET["codebase"] . "' title='Bloquer / Activer'>&#128164;</a>";
	print "<a href='codebar.php?idclasse=$saisie_classe&idsupp=" . $data[$i][1] . "&codebase=" . $_GET["codebase"] . "' title='Nouveau code'><img src='./image/commun/recycle.jpg' border='0'></a>";
	print "<a href='#' onclick=\"document.getElementById('codeim$i').style.display='none'; document.getElementById('codeinput$i').style.display='block'; return false;\" title='Modifier code'><img src='./image/commun/editer.gif' border='0'></a>";
	print "</div>";

	if (preg_match('/^8/', $version)) {
		print "<div class='cb-card-barcode'>";
		print "<img id='codeim$i' src='./codebar/image.php?code=" . $_GET["codebase"] . "&text=$texte'>";
		print "<span id='codespan$i'><input type='text' id='codeinput$i' class='cb-input' style='display:none' onchange=\"enrModifCodebarre(this.value,'" . $data[$i][1] . "','codespan$i','menueleve')\"></span>";
		print "</div>";
	}

	print "</div>";
}

print "</div></form>";
Pgclose();
?>
</body>
</html>
