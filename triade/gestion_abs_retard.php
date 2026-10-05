<?php
session_start();
if (isset($_COOKIE["anneeScolaire"])) $anneeScolaire=$_COOKIE["anneeScolaire"];
include_once("./librairie_php/verifEmailEnregistre.php");
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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<!-- ══════════════════════════════════════════════════════
     SECTION 1 — Saisie absences / retards
     ══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('librairie_php/db_triade.php');

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}

$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>

<!-- Form lecteur code-barres -->
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:8px 5px 4px;">
  <span class="na-lbl"><?php print LANGMESS433 ?> :</span>
  <form name='formulaire00' method='post' action='gestion_abs_retard_codebar.php' style="margin:0;">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis ?>","rien");</script></span>
  </form>
</div>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Form classe -->
<form name='formulaire' onsubmit='return valide_consul_classe()' method='post' action='gestion_abs_retard_suite.php'>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print ucwords(LANGIMP10) ?> :</span>
    <select name='saisie_classe' class="cc-select">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
      <?php select_classe2(25); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis ?>","class");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Form groupe -->
<form name=formulaire_5 onsubmit='return valide_consul_classe2()' method="post" action='gestion_abs_retard_suite.php'>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print ucwords(LANGPROF4) ?> :</span>
    <select name='saisie_groupe' class="cc-select">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
      <?php select_groupe_id(); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis ?>","grp");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Form étude -->
<form name=formulaire_55 onsubmit='return valide_consul_classe22()' method="post" action='gestion_abs_retard_suite.php'>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print ucwords(LANGGRP62) ?> :</span>
    <select name='saisie_etude' class="cc-select">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
      <?php select_etude(); ?>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT27bis ?>","etude");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Form feuille présence -->
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:8px 5px;">
  <span class="na-lbl"><?php print LANGMESS434 ?> :</span>
  <form name=formulaire_56 method="post" action='gestion_abs_present.php' style="margin:0;">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","rien");</script></span>
  </form>
</div>
<br>

<?php PgClose(); ?>
</td></tr></table>

<br><br>

<!-- ══════════════════════════════════════════════════════
     SECTION 2 — Listings et rapports
     ══════════════════════════════════════════════════════ -->
<?php
if ((LAN == "oui") && (file_exists("./common/config-sms.php"))) {
	$disabled="";
	$textdisabled="";
}else{
	$disabled="disabled";
	$textdisabled=" / Non Abonn&eacute;";
}
$disabledAttr = $disabled ? "disabled" : "";
$disabledClass = $disabled ? "btn-dest-disabled" : "";
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS4bis ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="dest-list" style="margin:5px;">

  <div class="dest-row">
    <div class="dest-row-label">Listing absences</div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('liste_abs.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('liste_abs_impr.php','_parent','')">Envoi Mail</button>
      <form method="post" action="sms-abs.php" style="margin:0;">
        <button type="submit" name="sms" value="1" class="btn-dest <?php print $disabledClass ?>" <?php print $disabledAttr ?>>Envoi SMS<?php print $textdisabled ?></button>
      </form>
    </div>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Listing retards</div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('liste_rtd.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('liste_rtd_impr.php','_parent','')">Envoi Mail</button>
      <form method="post" action="sms-rtd.php" style="margin:0;">
        <button type="submit" name="sms" value="1" class="btn-dest <?php print $disabledClass ?>" <?php print $disabledAttr ?>>Envoi SMS<?php print $textdisabled ?></button>
      </form>
    </div>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGABS68 ?></div>
    <button type="button" class="btn-dest" onclick="open('liste_abs_rtd_classe.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS436 ?></div>
    <button type="button" class="btn-dest" onclick="open('liste_abs_rtd_aucun.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGTMESS488 ?></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('liste_rattrapage.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('export_rattrapage.php','_parent','')">Export rattrapage</button>
    </div>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS437 ?></div>
    <button type="button" class="btn-dest" onclick="open('releve_abs_rtd_classe.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS438 ?></div>
    <button type="button" class="btn-dest" onclick="open('releve_abs_rtd_semaine_classe.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS439 ?></div>
    <button type="button" class="btn-dest" onclick="open('impr_abs_rtd_eleve.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS440 ?></div>
    <button type="button" class="btn-dest" onclick="open('listePresent.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <?php if ($_SESSION['membre'] != "menuprof") { ?>
  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS441 ?></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('gestion_abs_sconet.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('base_de_donne_importation700.php','_parent','')">Import</button>
    </div>
  </div>
  <?php } ?>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGABS69 ?></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('cumul_abs_rtd_classe.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('cumul_rtd_impr.php','_parent','')"><?php print LANGaffec_cre41 ?></button>
    </div>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS442 ?></div>
    <button type="button" class="btn-dest" onclick="open('gestion_abs_statistique.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

</div>
<br><br>
</td></tr></table>

<br><br>

<!-- ══════════════════════════════════════════════════════
     SECTION 3 — Recherche par élève
     ══════════════════════════════════════════════════════ -->
<form method="post" onsubmit="return valide_recherche_eleve_1()" name="formulaire_1" id="formulaire_1" action="gestion_abs_retard_modif_donne.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS443 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Mode :</span>
    <span>
      <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
        <input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_planifier.php'" />
        <?php print LANGMESS444 ?>
      </label>
      &nbsp;&nbsp;
      <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
        <input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_modif_donne.php'" checked='checked' />
        <?php print LANGMESS445 ?>
      </label>
      &nbsp;&nbsp;
      <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
        <input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_modif.php'" />
        <?php print LANGMESS446 ?>
      </label>
    </span>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <select name='anneeScolaire' class="cc-select">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,5); ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGABS3 ?> :</span>
    <input type="text" name="saisie_nom_eleve" id="search" autocomplete="off" class="cc-select" style="width:15em;" onkeyup="searchRequest(this,'eleve','target0','formulaire_1','saisie_nom_eleve')">
  </div>
  <div style="padding-left:130px;"><div id="target0" style="width:16em;"></div></div>
</div>
<br>
<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin:0 5px;">
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS447 ?>","rien");</script></span>
  <label style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
    <input type='radio' name='act' onclick="document.getElementById('formulaire_1').action='gestion_abs_retard_conv.php'" />
    <?php print LANGMESS448 ?>
  </label>
</div>
<br><br>
</td></TR></TABLE>
</form>

<BR>

<!-- ══════════════════════════════════════════════════════
     SECTION 4 — Configuration (non accessible menuprof)
     ══════════════════════════════════════════════════════ -->
<?php if ($_SESSION['membre'] != "menuprof") { ?>
<BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS449 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<?php
if (file_exists("./common/config-sms.php")) {
	include_once("./common/config-sms.php");
	$idsms=SMSKEY;
	$inc=GRAPH;
}else{
	$idsms="";
	$inc=defined("GRAPH") ? GRAPH : "";
}
?>
<div class="dest-list" style="margin:5px;">

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGABS70 ?></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('gestion_abs_config.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('gestion_abs_config_alerte.php','_parent','')"><?php print LANGMESS450 ?></button>
    </div>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS451 ?></div>
    <button type="button" class="btn-dest" onclick="open('gestion_crenau_config.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label"><?php print LANGMESS452 ?></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
      <button type="button" class="btn-dest" onclick="open('gestion_sms_config.php','_parent','')"><?php print LANGCONFIG ?></button>
      <button type="button" class="btn-dest" onclick="open('https://support.triade-educ.com/support/sms-compte.php?idsms=<?php print $idsms ?>&inc=<?php print $inc ?>','','width=550,height=600')"><?php print LANGMESS453 ?></button>
    </div>
  </div>

</div>
<br>
</td></tr></table>
<?php } ?>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
