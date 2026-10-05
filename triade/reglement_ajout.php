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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript">
function tout() {
	document.querySelectorAll('input[name="saisie_classe[]"]').forEach(function(cb){ cb.checked = true; });
}
</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS336 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('librairie_php/db_triade.php');
$cnx = cnx();
$data = affclasse();
?>
<form method="post" action="./reglement_ajout2.php" name="formulaire" enctype="multipart/form-data">
<div class="na-card">

  <div style="display:grid;grid-template-columns:180px 1fr;gap:10px 14px;align-items:start;max-width:600px;">

    <div style="text-align:right;padding-top:5px;font-size:13px;color:#555;font-weight:600;"><?php print LANGCIRCU6 ?> :</div>
    <div><input type="text" name="saisie_titre" class="cc-select" style="width:220px;" maxlength="28"></div>

    <div style="text-align:right;padding-top:5px;font-size:13px;color:#555;font-weight:600;"><?php print LANGCIRCU7 ?> :</div>
    <div><input type="text" name="saisie_ref" class="cc-select" style="width:220px;" maxlength="28"></div>

    <div style="text-align:right;padding-top:5px;font-size:13px;color:#555;font-weight:600;"><?php print LANGMESS337 ?> :</div>
    <div style="display:flex;align-items:center;gap:8px;">
      <input type="file" name="fichier" size="30">
      <span class="htip-wrap"><img src='./image/help.gif' width='15' height='15' border='0'><span class="htip">Format PDF (Max 2Mo)</span></span>
    </div>

    <div style="text-align:right;padding-top:5px;font-size:13px;color:#555;font-weight:600;"><?php print LANGCIRCU9 ?> :</div>
    <div style="display:flex;align-items:center;gap:8px;">
      <input type="checkbox" name="saisie_envoi_prof" id="envoi_prof" value="1">
      <label for="envoi_prof" style="font-size:13px;color:#444;cursor:pointer;"><?php print LANGCIRCU9 ?></label>
      <span class="htip-wrap"><img src='./image/help.gif' width='15' height='15' border='0'><span class="htip"><?php print LANGMESS70 ?></span></span>
    </div>

    <div style="text-align:right;padding-top:8px;font-size:13px;color:#555;font-weight:600;"><?php print LANGMESS338 ?> :</div>
    <div>
      <div style="display:flex;flex-wrap:wrap;gap:6px 18px;">
<?php
for ($i = 0; $i < countTriade($data); $i++) {
	print "<label style='font-size:12px;cursor:pointer;white-space:nowrap;'><input type='checkbox' name='saisie_classe[]' value='" . $data[$i][0] . "'> " . trim($data[$i][1]) . "</label>\n";
}
?>
      </div>
      <div style="text-align:right;margin-top:6px;">
        <a href="#" onclick="tout(); return false;" style="font-size:12px;color:#3c4a8a;"><?php print LANGCIRCU13 ?></a>
      </div>
    </div>

  </div>
</div>
<div class="na-foot">
  <button type="button" class="btn-retour" onclick="history.go(-1)"><?php print LANGCIRCU14 ?></button>
  <button type="submit" class="btn-enr" onclick="attente()"><?php print LANGENR ?></button>
</div>
</form>

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
attente();
?>
</BODY></HTML>
