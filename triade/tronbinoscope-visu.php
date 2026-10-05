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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"], "trombinoscopeRead")) {
		validerequete("2");
	}
} else {
	validerequete("2");
	$visu  = 1;
	$visu2 = 1;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<form method="post" onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTITRE32 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div class="na-card">
  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <label style="font-size:13px;color:#555;font-weight:600;"><?php print LANGELE4 ?> :</label>
    <select id="saisie_classe" name="saisie_classe" class="cc-select" style="width:200px;">
      <option><?php print LANGCHOIX ?></option>
      <?php select_classe(); ?>
    </select>
    <button type="submit" name="consult" class="btn-enr"><?php print LANGBT28 ?></button>
    <?php if (isset($_POST["consult"])) : ?>
    <button type="button" class="btn-enr" onclick="open('tronbinoscope-impr.php?idclasse=<?php print $_POST['saisie_classe'] ?>','impr','width=800,height=600,scrollbars=yes,menubar=yes')"><?php print LANGaffec_cre41 ?></button>
    <?php endif; ?>
  </div>
</div>

</td></tr></table>
</form>

<?php
if (isset($_POST["consult"])) {
	$saisie_classe = $_POST["saisie_classe"];
	$sql = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res  = execSql($sql);
	$data = chargeMat($res);
	Pgclose();
	$cl = $data[0][0];
?>
<br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGELE4 ?> : <font id='color2'><?php print $cl ?></font></font></b></td></tr>
<tr id='cadreCentral0'><td>
<?php if (countTriade($data) <= 0) : ?>
  <div class="na-card"><p style="font-size:13px;color:#888;margin:0;text-align:center;"><?php print LANGRECH1 ?></p></div>
<?php else : ?>
  <div class="na-card" style="padding:12px;">
    <div style="display:flex;flex-wrap:wrap;gap:16px;">
<?php
	for ($i = 0; $i < countTriade($data); $i++) {
		$nom    = strtoupper($data[$i][2]);
		$prenom = ucwords($data[$i][3]);
?>
      <div style="text-align:center;width:110px;">
        <a href="#" onclick="open('photoajouteleve.php?ideleve=<?php print $data[$i][1] ?>','photo','width=450,height=280'); return false;">
          <img src="image_trombi.php?idE=<?php print $data[$i][1] ?>" border="0" style="max-width:100px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.25);">
        </a>
        <p style="font-size:11px;font-weight:700;margin:6px 0 1px;color:#1a2340;"><?php print $nom ?></p>
        <p style="font-size:11px;color:#666;margin:0;"><?php print $prenom ?></p>
      </div>
<?php
	}
?>
    </div>
  </div>
<?php endif; ?>
</td></tr></table>
<?php } ?>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
</BODY>
</HTML>
