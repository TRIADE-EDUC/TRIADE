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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Entretiens individuels des enseignants</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="padding:16px;">
<br>

<!-- Entretien individuel -->
<div class="card">
  <div class="card-header"><span class="card-title">Entretien individuel</span></div>
  <div class="card-body">
    <form method="post" action="gestion_entretient_enseignant2.php" name="formulaire">
      <div class="form-row">
        <label class="form-label"><?php print LANGNA1." ".LANGNA2 ?></label>
        <select name="idpers" class="form-control" style="max-width:280px;">
          <option><?php print LANGCHOIX ?></option>
          <?php select_personne('ENS'); ?>
        </select>
      </div>
      <div style="padding:10px 14px;">
        <script language=JavaScript>buttonMagicSubmit("Accèder","supp");</script>
      </div>
    </form>
  </div>
</div>

<br>

<!-- Temps d'accompagnement -->
<div class="card">
  <div class="card-header"><span class="card-title">Temps d'accompagnement</span></div>
  <div class="card-body">
    <form method="post" action="gestion_entretient_enseignant_recap.php" name="formulaire2">
      <div class="form-row">
        <label class="form-label"><?php print LANGNA1." ".LANGNA2 ?></label>
        <select name="idpers" class="form-control" style="max-width:280px;">
          <option><?php print LANGCHOIX ?></option>
          <?php select_personne('ENS'); ?>
        </select>
      </div>
      <div style="padding:10px 14px;">
        <script language=JavaScript>buttonMagicSubmit("Accèder","supp");</script>
      </div>
    </form>
  </div>
</div>

<br>

<!-- Statistique par classe -->
<div class="card">
  <div class="card-header"><span class="card-title">Statistique par classe</span></div>
  <div class="card-body">
<?php
$data = listingEntretienEnseignant();
if (countTriade($data) > 0): ?>
    <div style="text-align:center;margin-bottom:16px;">
      <img src="ajax-graph-entretien-prof.php">
    </div>
<?php endif; ?>
<?php
$tabProf = [];
for ($i = 0; $i < countTriade($data); $i++) {
  $idprof  = $data[$i][0];
  $seconde = conv_en_seconde($data[$i][1]);
  if ($seconde != "") {
    $tabProf[$idprof] += $seconde;
  }
}
?>
    <table class="table table-hover" style="width:100%;">
      <thead><tr>
        <th class="cc-th">Enseignant</th>
        <th class="cc-th" style="width:160px;">Durée totale</th>
      </tr></thead>
      <tbody>
<?php foreach ($tabProf as $idprof => $duree): ?>
        <tr class="cc-tr-data">
          <td><?php print recherche_personne($idprof) ?></td>
          <td><?php print convert_sec($duree) ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

</div>

</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
