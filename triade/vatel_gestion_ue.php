<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E.
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
 ***************************************************************************
/***************************************************************************
Last updated: 09.10.2008   par AMBIS Cyril
     upadted: 18.09.2014   par TRIADE-DEV
****************************************************************************/
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx = cnx();
$anneeScolaire = isset($_COOKIE["anneeScolaire"]) ? $_COOKIE["anneeScolaire"] : "";
$vgu_success = "";

if (isset($_POST["copy"])) {
	$saisie_classe_source        = $_POST["saisie_classe_source"];
	$anneeScolaireSource         = $_POST["anneeScolaireSource"];
	$saisie_classe_destination   = $_POST["saisie_classe_destination"];
	$anneeScolaireDest           = $_POST["anneeScolaireDest"];
	copyUniteEnseignement($saisie_classe_source, $anneeScolaireSource, $saisie_classe_destination, $anneeScolaireDest);
	$vgu_success = LANGDONENR;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS222 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>

<?php if ($vgu_success): ?>
<script>document.addEventListener('DOMContentLoaded', function(){ alertify.success(<?php echo json_encode($vgu_success); ?>); });</script>
<?php endif; ?>

<div style="padding:16px;">

<div style="margin-bottom:14px;">
  <script language=JavaScript>buttonMagic("<?php print LANGMESS223 ?>","vatel_creat_ue.php","_parent","","");</script>
  <script language=JavaScript>buttonMagic("<?php print LANGTMESS426 ?>","vatel_list_ue.php","_parent","","");</script>
  <script language=JavaScript>buttonMagic("<?php print "Copier Unité Enseignement" ?>","vatel_gestion_ue.php?copy","_parent","","");</script>
</div>
<br><br>
<?php if (isset($_GET["copy"])): ?>
<div class="card">
  <div class="card-header">Copie d'unité d'enseignement</div>
  <div class="card-body">
    <div class="alert alert-warning" style="margin-bottom:14px;">
      IMPORTANT : LA COPIE D'UNITÉ D'ENSEIGNEMENT SUPPRIME L'ANCIENNE VERSION !
    </div>
    <form method='post' action='vatel_gestion_ue.php'>
      <div class="form-row">
        <label class="form-label">Classe source</label>
        <select name="saisie_classe_source" class="form-control">
          <option value="0" id='select0'><?php print LANGCHOIX?></option>
          <optgroup label="Classe">
          <?php select_classe(); ?>
        </select>
      </div>
      <div class="form-row">
        <label class="form-label">Année scolaire source</label>
        <select name="anneeScolaireSource" class="form-control">
          <?php
          print "<option value='' id='select0'>".LANGCHOIX."</option>";
          filtreAnneeScolaireSelectNote($anneeScolaire, 3);
          ?>
        </select>
      </div>
      <div class="form-row">
        <label class="form-label">Classe destination</label>
        <select name="saisie_classe_destination" class="form-control">
          <option value="0" id='select0'><?php print LANGCHOIX?></option>
          <optgroup label="Classe">
          <?php select_classe(); ?>
        </select>
      </div>
      <div class="form-row">
        <label class="form-label">Année scolaire destination</label>
        <select name="anneeScolaireDest" class="form-control">
          <?php
          print "<option value='' id='select0'>".LANGCHOIX."</option>";
          filtreAnneeScolaireSelectNote($anneeScolaire, 3);
          ?>
        </select>
      </div>
      <div style="padding:10px 14px;">
        <script language=JavaScript>buttonMagicSubmit("<?php print 'Valider la copie' ?>","copy");</script>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

</div>

<?php brmozilla($_SESSION["navigateur"]); ?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
