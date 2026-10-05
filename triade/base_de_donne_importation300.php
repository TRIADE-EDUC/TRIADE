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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
@unlink("./data/fichier_gep/traitement.xls");
if (empty($_SESSION["adminplus"])) {
	print "<script>location.href='./base_de_donne_importation.php'</script>";
	exit;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importation du fichier</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" action="./base_de_donne_importation310.php" name="formulaire" enctype="multipart/form-data">
<div class="na-card">

  <div style="margin-bottom:12px;">
    <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;"><?php print LANGGEP2 ?> (<b>xls</b>) :</label>
    <input type="file" name="fichier1" size="25" style="font-size:12px;">
  </div>

  <script>
  function fonc1() {
    if (document.formulaire.update.checked) {
      document.formulaire.vide_eleve.checked = false;
      document.formulaire.vide_eleve.disabled = true;
      document.getElementById('up1').style.display = 'block';
    } else {
      document.formulaire.vide_eleve.checked = false;
      document.formulaire.vide_eleve.disabled = false;
      document.formulaire.updatevide.checked = false;
      document.formulaire.updatepasswd.checked = false;
      document.getElementById('up1').style.display = 'none';
    }
  }
  </script>

  <div style="font-size:12px;color:#444;margin-bottom:6px;">
    <label><input type="checkbox" name="update" value="1" onclick="fonc1()"> <?php print "Effectuer une mise à jour" ?> (<?php print LANGOUI ?>)</label>
  </div>
  <div id="up1" style="display:none;margin-left:18px;margin-bottom:6px;font-size:12px;color:#555;">
    <label><input type="checkbox" name="updatevide" value="1"> <?php print "Prendre en compte les champs vides du fichier" ?> (<?php print LANGNON ?>)</label><br>
    <label><input type="checkbox" name="updatepasswd" value="1"> <?php print "Affecter un nouveau mot de passe pour les élèves déjà inscrits" ?> (<?php print LANGNON ?>)</label>
  </div>
  <div style="font-size:12px;color:#444;margin-bottom:6px;">
    <label><input type="checkbox" name="optionligne" value="1"> <?php print "Prendre la première ligne du fichier" ?> (<?php print LANGOUI ?>)</label>
  </div>
  <div style="font-size:12px;color:#444;margin-bottom:6px;">
    <label><input type="checkbox" name="vide_eleve" value="oui"> <?php print LANGBASE41 ?> (<?php print LANGOUI ?>)</label>
  </div>

  <?php
  $annee = (date("Y")-1)."-".date("Y");
  if (file_exists("./data/archive/$annee.sqlite")) {
    print "<div style='margin-top:8px;padding:8px 12px;background:#ffebee;border:1px solid #ef9a9a;border-radius:6px;font-size:12px;color:#c62828;'>Attention : la suppression des élèves supprimera toutes les archives !</div>";
  }
  ?>

  <div style="font-size:11px;color:#888;margin-top:8px;"><?php print LANGbasededon21 ?></div>

</div>
<div class="na-foot">
  <button type="submit" class="btn-enr"><?php print LANGBT23 ?></button>
</div>
</form>

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
