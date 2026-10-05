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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_POST["create"])){
	$cr=config_param_ajout($_POST["texte"],"sms-message");
	if($cr == 1){ alertJs("Message enregistré -- Equipe Triade"); }
}

if (isset($_POST["smsfiltre"])) {
	config_param_ajout($_POST["numsms"],"SMSFILTRE");
}

if (isset($_POST["smsauto"])) {
	config_param_ajout($_POST["validesmsauto"],"SMSAUTO");
	config_param_ajout($_POST["nbabssms"],"SMSNBABS");
	config_param_ajout($_POST["smsautotel"],'SMSAUTOTEL');
	config_param_ajout($_POST["smsautotelport1"],'SMSAUTOTELPORT1');
	config_param_ajout($_POST["smsautotelport2"],'SMSAUTOTELPORT2');
	config_param_ajout($_POST["smsautoteleleveport"],'SMSAUTOTELELEVEPORT');
	config_param_ajout($_POST["smsautotelevefixe"],'SMSAUTOTELELEVEFIXE');
	config_param_ajout($_POST["smsautojustifier"],'SMSAUTOJUSTIFIER');
}

$SMSAUTO=config_param_visu('SMSAUTO'); $checkedSMSAUTO="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTO="checked='checked'"; }
$a=config_param_visu('SMSNBABS');
$nbsmsauto=$a[0][0];
if ($nbsmsauto == "") $nbsmsauto=2;
$SMSAUTO=config_param_visu('SMSAUTOTEL'); $checkedSMSAUTOTEL="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTOTEL="checked='checked'"; }
$SMSAUTO=config_param_visu('SMSAUTOTELPORT1'); $checkedSMSAUTOTELPORT1="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTOTELPORT1="checked='checked'"; }
$SMSAUTO=config_param_visu('SMSAUTOTELPORT2'); $checkedSMSAUTOTELPORT2="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTOTELPORT2="checked='checked'"; }
$SMSAUTO=config_param_visu('SMSAUTOTELELEVEPORT'); $checkedSMSAUTOTELELEVEPORT="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTOTELELEVEPORT="checked='checked'"; }
$SMSAUTO=config_param_visu('SMSAUTOTELELEVEFIXE'); $checkedSMSAUTOTELELEVEFIXE="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTOTELELEVEFIXE="checked='checked'"; }
$SMSAUTO=config_param_visu('SMSAUTOJUSTIFIER'); $checkedSMSAUTOJUSTIFIER="";
if ($SMSAUTO[0][0] == "1") { $checkedSMSAUTOJUSTIFIER="checked='checked'"; }

$filtreSMS=config_param_visu('smsfiltre');
$message1=config_param_visu("sms-message");
$message=$message1[0][0];
if (trim($message) == "") { $message="Nous vous signalons que votre enfant ELEVE est absent(e) aujourd'hui (DATE)"; }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration Message SMS</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<!-- ── Message SMS ── -->
<div style="font-size:12px;font-weight:700;color:#080A66;margin:8px 5px 4px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">Enregistrement du message SMS envoyé aux parents d'élèves.</div>
<form method="post" name="form">
<div class="na-card" style="margin:5px;">
  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;">Message :</span>
    <div>
      <textarea name="texte" cols="40" rows="4" class="cc-select" onkeypress="compter(this,'140', this.form.CharRestant)"><?php print $message ?></textarea><br>
      <span style="font-size:11px;color:#666;">(140 caractères max) &nbsp;utilisés : <input type="text" name="CharRestant" size="2" disabled="disabled" value="<?php print strlen($message) ?>"></span>
    </div>
  </div>
  <div style="font-size:11px;color:#555;margin:6px 0 2px;font-style:italic;line-height:1.6;">
    ELEVE = prénom+nom, NOM = nom, PRENOM = prénom, DATE = date, CLASSE = classe, TYPE = nature (absent/retard).
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- ── Filtre SMS ── -->
<form method="post">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Filtre SMS :</span>
    <span style="font-size:12px;color:#333;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
      Les numéros commencent par :
      <input type="text" name="numsms" size="3" class="cc-select" style="width:50px;" value="<?php print $filtreSMS[0][0] ?>">
      <span style="font-size:11px;color:#888;">(ex: 06)</span>
    </span>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("Valider","smsfiltre");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- ── AUTO-SMS ── -->
<form method="post">
<div class="na-card" style="margin:5px;">
  <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">AUTO-SMS</div>
  <div class="na-row">
    <span class="na-lbl">Activer :</span>
    <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
      <input type="checkbox" name="validesmsauto" value="1" <?php print $checkedSMSAUTO ?>> (oui)
    </label>
  </div>
  <div class="na-row">
    <span class="na-lbl">Nbr absences cumulées :</span>
    <input type="text" name="nbabssms" value="<?php print $nbsmsauto ?>" class="cc-select" style="width:40px;">
  </div>
  <div style="font-size:12px;color:#333;margin:4px 0 6px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">Envoi sur :</div>
  <?php
  $checkboxes=[
      ['name'=>'smsautotel','label'=>'Téléphone principal (fixe)','checked'=>$checkedSMSAUTOTEL],
      ['name'=>'smsautotelport1','label'=>'Portable tuteur 1','checked'=>$checkedSMSAUTOTELPORT1],
      ['name'=>'smsautotelport2','label'=>'Portable tuteur 2','checked'=>$checkedSMSAUTOTELPORT2],
      ['name'=>'smsautotelevefixe','label'=>'Téléphone Étudiant (fixe)','checked'=>$checkedSMSAUTOTELELEVEFIXE],
      ['name'=>'smsautoteleleveport','label'=>'Portable Étudiant','checked'=>$checkedSMSAUTOTELELEVEPORT],
      ['name'=>'smsautojustifier','label'=>'Valider comme "justifié"','checked'=>$checkedSMSAUTOJUSTIFIER],
  ];
  foreach($checkboxes as $cb) {
  ?>
  <div class="na-row" style="margin-bottom:4px;">
    <span class="na-lbl"></span>
    <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
      <input type="checkbox" name="<?php print $cb['name'] ?>" value="1" <?php print $cb['checked'] ?>>
      <?php print $cb['label'] ?>
    </label>
  </div>
  <?php } ?>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("Valider","smsauto");</script>
<br><br>
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
?>
</BODY></HTML>
