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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS346 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
include_once('./librairie_php/db_triade.php');
include("./librairie_php/fonctions_vatel.php");
$cnx = cnx();
$anneeScolaire = isset($_COOKIE["anneeScolaire"]) ? $_COOKIE["anneeScolaire"] : "";
if (isset($_POST["annee_scolaire"])) $anneeScolaire = $_POST["annee_scolaire"];
?>

<div style="padding:16px;">

<!-- Filtres -->
<form method="post" name="formulaire">
<div class="toolbar">
  <label class="form-label" style="min-width:auto;"><?php print LANGBULL3 ?>&nbsp;:</label>
  <select name='annee_scolaire' class="form-control" style="max-width:160px;" onChange="this.form.submit()">
    <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
  </select>
  <label class="form-label" style="min-width:auto;"><?php print LANGMESS347 ?></label>
  <select name='idclasse' class="form-control" style="max-width:200px;" onChange="this.form.submit()">
    <option style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
    <?php select_classe(isset($_POST["idclasse"]) ? $_POST["idclasse"] : 0); ?>
  </select>
</div>
</form>

<?php if (isset($_POST["idclasse"])): ?>

<div class="card">
  <div class="card-header">Unités d'enseignement</div>
  <div class="card-body" style="padding:0;">
    <table class="table table-hover">
      <thead>
        <tr>
          <th><?php print LANGELE4 ?></th>
          <th><?php print LANGMESS351 ?></th>
          <th><?php print LANGMESS350 ?></th>
          <th style="width:80px;"></th>
          <th style="width:80px;"></th>
        </tr>
      </thead>
      <tbody>
      <?php
      $data = vatel_liste_ueViaIdClasse($_POST["idclasse"], $_POST["annee_scolaire"]);
      for ($i = 0; $i < countTriade($data); $i++) {
          if ($data[$i][1] != "") {
              $classe = Vatel_affUneClasse($data[$i][1]);
              $sem = ($data[$i][2] == 0) ? "1&nbsp;et&nbsp;2" : $data[$i][2];
              print "<tr>";
              print "<td>&nbsp;".$classe[0][0]."&nbsp;</td>";
              print "<td>&nbsp;".$sem."&nbsp;</td>";
              print "<td>&nbsp;".stripslashes($data[$i][4])."&nbsp;</td>";
              print "<td><button class='btn btn-primary' onclick=\"open('vatel_modif_ue.php?id=".$data[$i][0]."','_parent','');\">Modifier</button></td>";
              print "<td><button class='btn btn-danger' onclick=\"open('vatel_supp_ue.php?id=".$data[$i][0]."','_parent','');\">Supprimer</button></td>";
              print "</tr>";
          }
      }
      if (countTriade($data) == 0) {
          print "<tr><td colspan='5' class='text-center text-muted' style='padding:20px;'>Aucune unité d'enseignement pour cette classe</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</div>

<div style="padding:4px 0;">
  <script language=JavaScript>buttonMagic("<?php print LANGMESS352 ?>","vatel_creat_ue.php","_parent","","");</script>
</div>

<?php endif; ?>

</div>

<?php brmozilla($_SESSION["navigateur"]); ?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
