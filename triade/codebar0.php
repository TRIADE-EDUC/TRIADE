<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
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
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGCODEBAR1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div class="na-card">
  <div style="display:flex;gap:20px;flex-wrap:wrap;">

    <!-- Codes-barres par classe -->
    <div style="flex:1;min-width:200px;">
      <p style="font-size:11px;font-weight:600;color:#3c4a8a;text-transform:uppercase;letter-spacing:.5px;margin:0 0 10px;border-bottom:1px solid #e0e4f8;padding-bottom:6px;"><?php print LANGELE4 ?></p>
      <form method="post" onsubmit="return valide_consul_classe()" name="formulaire">
        <div style="margin-bottom:10px;">
          <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;"><?php print LANGELE4 ?> :</label>
          <select id="saisie_classe" name="saisie_classe" class="cc-select" style="width:100%;">
            <option><?php print LANGCHOIX ?></option>
            <?php select_classe(); ?>
          </select>
        </div>
        <div style="margin-bottom:14px;">
          <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;"><?php print LANGMESS221 ?></label>
          <select name="codebase" class="cc-select" style="width:100%;">
            <option value="code39">code39</option>
            <option value="qcode">Qcode</option>
          </select>
        </div>
        <button type="submit" name="consult" class="btn-enr"><?php print LANGBT28 ?></button>
      </form>
    </div>

    <div style="width:1px;background:#e0e4f8;flex-shrink:0;"></div>

    <!-- Codes-barres par membre -->
    <div style="flex:1;min-width:200px;">
      <p style="font-size:11px;font-weight:600;color:#3c4a8a;text-transform:uppercase;letter-spacing:.5px;margin:0 0 10px;border-bottom:1px solid #e0e4f8;padding-bottom:6px;"><?php print LANGMESS216 ?></p>
      <form method="post" onsubmit="return valide_consul_membre()" name="formulaire1">
        <div style="margin-bottom:10px;">
          <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;"><?php print LANGMESS216 ?> :</label>
          <select id="membre" name="membre" class="cc-select" style="width:100%;">
            <option><?php print LANGCHOIX ?></option>
            <option value="menuadmin">Direction</option>
            <option value="menuprof">Enseignant</option>
            <option value="menuscolaire">Vie Scolaire</option>
            <option value="menupersonnel">Personnel</option>
          </select>
        </div>
        <div style="margin-bottom:14px;">
          <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;"><?php print LANGMESS221 ?></label>
          <select name="codebase" class="cc-select" style="width:100%;">
            <option value="code39">code39</option>
            <option value="qcode">Qcode</option>
          </select>
        </div>
        <button type="submit" name="consultmembre" class="btn-enr"><?php print LANGBT28 ?></button>
      </form>
    </div>

  </div>
  <?php if (isset($_POST["codebase"])) : ?>
  <p style="font-size:11px;color:#888;margin:10px 0 0;"><?php print LANGCODEBAR4 ?> <strong>code39</strong>.</p>
  <?php endif; ?>
</div>

</td></tr></table>

<?php
if (isset($_POST["consult"])) {
	$saisie_classe = $_POST["saisie_classe"];
	$sql = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res  = execSql($sql);
	$data = chargeMat($res);
	$cl   = $data[0][0];
?>
<br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGELE4 ?> : <font id="color2"><?php print $cl ?></font></font></b></td></tr>
<tr id='cadreCentral0'><td>
<?php if (countTriade($data) <= 0) : ?>
  <div class="na-card"><p style="font-size:13px;color:#888;margin:0;text-align:center;"><?php print LANGRECH1 ?></p></div>
<?php else : ?>
  <?php history_cmd($_SESSION["nom"], "VISUALISA.", "Code Barre"); ?>
  <iframe src="./codebar.php?idclasse=<?php print $saisie_classe ?>&codebase=<?php print $_POST["codebase"] ?>" width="100%" height="700" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="yes" name="codebar"></iframe>
<?php endif; ?>
</td></tr></table>
<?php } ?>

<?php
if (isset($_POST["consultmembre"])) {
	$membre = $_POST["membre"];
?>
<br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Membre : <font id="color2"><?php print renvoiMembreFormatePersonne($membre) ?></font></font></b></td></tr>
<tr id='cadreCentral0'><td>
  <iframe src="./codebarmembre.php?membre=<?php print $membre ?>&codebase=<?php print $_POST["codebase"] ?>" width="100%" height="700" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="yes" name="codebar"></iframe>
</td></tr></table>
<?php } ?>

<?php
Pgclose();
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY>
</HTML>
