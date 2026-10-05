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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION["nom"])." ".htmlspecialchars($_SESSION["prenom"]) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$sql_ngrp = "SELECT trim(libelle) FROM {$prefixe}groupes ORDER by 1";
$res = execSql($sql_ngrp);
genMatJs("liste_grp", chargeMat($res));
?>
<script language="JavaScript">
function verif_nom_grp(mat) {
    for (i = 0; i < mat.length; i++) {
        if ((mat[i][0] == document.formulaire.saisie_intitule.value) && (document.formulaire.saisie_intitule.value != "")) {
            alert("<?php print LANGGRP46 ?>");
            document.formulaire.saisie_intitule.focus();
            document.formulaire.saisie_intitule.select();
            document.formulaire.rien.disabled = true;
            return false;
        } else {
            document.formulaire.rien.disabled = false;
        }
    }
    return true;
}
function verif_nom_grp2(mat) {
    for (i = 0; i < mat.length; i++) {
        if ((mat[i][0] == document.formulaire2.saisie_intitule.value) && (document.formulaire2.saisie_intitule.value != "")) {
            alert("<?php print LANGGRP46 ?>");
            document.formulaire2.saisie_intitule.focus();
            document.formulaire2.saisie_intitule.select();
            document.formulaire2.rien.disabled = true;
            return false;
        } else {
            document.formulaire2.rien.disabled = false;
        }
    }
    return true;
}
</script>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTITRE11 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<!-- ── Section 1 : Création manuelle ── -->
<form method="post" onsubmit="return validecreatgroupe()" name="formulaire" action="./creat_groupe_suite.php">
<div class="cg-card">

  <div class="cg-field-row">
    <label class="cg-lbl"><?php print LANGGRP1 ?></label>
    <input onchange="return verif_nom_grp(liste_grp);"
           type="text" name="saisie_intitule" size="20" maxlength="30"
           class="cg-input">
    <a href="modifier_groupe.php" target="_parent" class="cg-btn-link"><?php print LANGGRP50 ?></a>
  </div>

  <div class="cg-field-row">
    <label class="cg-lbl"><?php print LANGBULL3 ?></label>
    <select name="annee_scolaire" size="1" class="cg-select">
      <?php filtreAnneeScolaireSelectNote('', 3); ?>
    </select>
  </div>

  <div class="cg-section-title"><?php print LANGGRP2 ?></div>

  <div class="cg-classes-row">
    <select name="saisie_liste[]" size="8" multiple="multiple" class="cg-multiselect">
      <?php select_classe(); ?>
    </select>
    <div class="cg-classes-hint">
      <?php print LANGGRP3 ?> <strong><?php print LANGGRP4 ?></strong> <?php print LANGGRP5 ?>
    </div>
  </div>

  <div class="cg-actions">
    <a href="liste_groupe.php" target="_parent" class="cg-btn"><?php print LANGBT12 ?></a>
    <button type="submit" name="rien" class="cg-btn cg-btn-primary"><?php print LANGBT13 ?></button>
    <a href="suppression_groupe.php" target="_parent" class="cg-btn cg-btn-danger"><?php print LANGGRP44 ?></a>
  </div>

</div>
</form>

<hr class="cg-sep">

<!-- ── Section 2 : Import CSV ── -->
<form method="post" action="./creat_groupe_import.php" name="formulaire2"
      enctype="multipart/form-data" onsubmit="return validecreatgroupe3()">
<div class="cg-card">

  <div class="cg-field-row">
    <label class="cg-lbl"><?php print LANGGRP1 ?></label>
    <input onchange="return verif_nom_grp2(liste_grp);"
           type="text" name="saisie_intitule" size="25" maxlength="30"
           class="cg-input">
  </div>

  <div class="cg-field-row">
    <label class="cg-lbl"><?php print LANGBULL3 ?></label>
    <select name="annee_scolaire" size="1" class="cg-select">
      <?php filtreAnneeScolaireSelectNote('', 3); ?>
    </select>
  </div>

  <div class="cg-field-row">
    <label class="cg-lbl"><?php print LANGMESS353 ?></label>
    <input type="file" name="fichier" class="cg-file-input">
  </div>

  <div class="cg-actions">
    <button type="submit" name="rien" class="cg-btn cg-btn-primary"><?php print LANGGRP45 ?></button>
  </div>

</div>
</form>

<!-- ── Format CSV attendu ── -->
<div class="cg-csv-info">
  <div class="cg-csv-title"><?php print LANGMESS354 ?></div>
  <table class="cg-csv-table">
    <tr>
      <th class="cg-csv-th">1) <?php print LANGIMP48 ?></th>
      <th class="cg-csv-th">2) <?php print LANGIMP46 ?></th>
      <th class="cg-csv-th">3) <?php print LANGELE10 ?></th>
    </tr>
  </table>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
