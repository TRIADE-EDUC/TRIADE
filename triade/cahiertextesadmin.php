<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
	$cnx=cnx();
	if (!verifDroit($_SESSION["id_pers"],"cahiertextes")) {
		accesNonReserveFen();
		exit();
	}
	Pgclose();
} else {
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROF37 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<!-- ── Consultation par classe ── -->
<form method="post" onsubmit="return valide_consul_classe()" name="formulaire" action="cahiertext_visu_global.php" target="devoir">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGCARNET2 ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select">
      <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <?php select_classe(); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult");</script>
<br><br>
</form>

<?php if ($_SESSION["membre"] != "menuscolaire") { ?>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- ── Consultation par enseignant ── -->
<form method="post" action="cahiertext.php" name="formulaire1" onsubmit="return valide_choix_pers('<?php print " un enseignant" ?>')">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <select name="anneeScolaire" class="cc-select">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,8); ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print "Nom de l'enseignant" ?> :</span>
    <select name="saisie_pers" class="cc-select">
      <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <?php select_personne_2('ENS','25'); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult");</script>
<br><br>
</form>

<?php } ?>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- ── Export Word ── -->
<form name="formulaire_55" method="post" action="cahiertexteexport.php">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <select name="anneeScolaire" class="cc-select">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,8); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS240 ?>","export");</script>
<br><br>
</form>

</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
