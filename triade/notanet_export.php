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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Export Notanet</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<!-- // debut form  -->
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();


$datap=config_param_visu("contnotanet");
$contnotanet=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("notehistarts");
$notehistarts=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("notehistgeo");
$notehistgeo=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("noteeducivi");
$noteeducivi=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("noteA2");
$noteA2=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("epsviaexamen");
$checkepsviaexamen=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("noteeviescolaire");
$noteeviescolaire=($datap[0][0] == "1") ? "checked='checked'" : "";
$datap=config_param_visu("prevsanteenv");
$checkprev_sante_envviaexamen=($datap[0][0] == "1") ? "checked='checked'" : "";

?>
<form method="post" action="notanet_export2.php" onsubmit="return valide_consul_classe()" name="formulaire">
<input type="hidden" name="typebull" value="brevetcollege3">

<div class="na-card">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <span style="font-size:12px;color:#080A66;font-weight:600;"><?php print $_COOKIE["anneeScolaire"] ?></span>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select">
      <option><?php print LANGCHOIX ?></option>
      <?php select_classe(); ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Série :</span>
    <select name="serie" class="cc-select">
      <option value="LV2">Collège, option de série LV2</option>
      <option value="DP6">Collège, option Découverte Prof. 6h (DP6)</option>
      <option value="STA">Collège, Série Professionnelle option agricole</option>
    </select>
  </div>
</div>

<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Options de prise en compte</div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" <?php print $notehistarts ?> value="1" name="notehistarts"> Prendre en compte l'histoire des arts</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" <?php print $noteA2 ?> value="1" name="noteA2"> Prendre en compte le niveau A2</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" <?php print $notehistgeo ?> value="1" name="notehistgeo"> Prendre en compte l'histoire géographie</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" <?php print $noteeducivi ?> value="1" name="noteeducivi"> Prendre en compte l'éducation civique</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" <?php print $noteeviescolaire ?> value="1" name="noteviescolaire"> Prendre en compte la vie scolaire</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" <?php print $contnotanet ?> value="1" name="controle"> Effectuer un contrôle avant l'extraction</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" name="epsviaexamen" value="1" <?php print $checkepsviaexamen ?>> Matière "EPS" via "Examen Brevet"</label></div>
  <div class="na-row"><label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;"><input type="checkbox" name="prev_sante_envviaexamen" value="1" <?php print $checkprev_sante_envviaexamen ?>> Matière "SANTE ENV." via "Exam. Brevet"</label></div>
</div>

<br>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create");</script>
</div>
<br>
</form>

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
