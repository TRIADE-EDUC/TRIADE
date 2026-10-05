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
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE12; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGCLAS1 ?>","list_classe.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGSUPP21 ?>","suppression_classe.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<form method="post" onsubmit="return verifcreatclasse()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE12; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<br>
<div class="ca-form-wrap">

  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGTITRE12; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGGRP6; ?></label>
      <input type="text" name="saisie_creat_classe" size="20" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS206; ?></label>
      <input type="text" name="saisie_classe_long" size="33" maxlength="250" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS207; ?></label>
      <select name="saisie_site" class="ca-select"><?php select_site(1); ?></select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGTMESS506; ?></label>
      <input type="text" name="specification" size="33" maxlength="200" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGTMESS528; ?></label>
      <select name="saisie_langue" class="ca-select">
        <option value="Français / French">Français / French</option>
        <option value="Anglais / English">Anglais / English</option>
        <option value="Espagnol / Spanish">Espagnol / Spanish</option>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGTMESS512; ?></label>
      <select name="saisie_niveau" class="ca-select">
        <option value=""><?php echo LANGCHOIX; ?></option>
        <optgroup label="Universitaire">
          <option value="A1">1er année</option>
          <option value="A2">2ième année</option>
          <option value="A3">3ième année</option>
          <option value="A4">4ième année</option>
          <option value="A5">5ième année</option>
          <option value="PREPA">PREPA</option>
          <option value="M1">Master 1</option>
          <option value="M2">Master 2</option>
        </optgroup>
        <optgroup label="Livret Scolaire">
          <option value="cycle 2">Cycle 2</option>
          <option value="cycle 3">Cycle 3</option>
          <option value="cycle 4">Cycle 4</option>
        </optgroup>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo defined('LANG_CODE_MEF') ? LANG_CODE_MEF : 'Code MEF (SIECLE)'; ?></label>
      <input type="text" name="code_mef" size="20" maxlength="11" placeholder="Ex: 10010012110" class="ca-input">
    </div>
  </fieldset>

  <div class="ca-actions">
    <?php brmozilla($_SESSION["navigateur"]); ?>
    <script language="JavaScript">buttonMagicSubmit("<?php echo LANGBT14 ?>","create");</script>
    <?php brmozilla($_SESSION["navigateur"]); ?>
  </div>

</div>

</td></tr></table>
</form>

<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>

<?php
if (isset($_POST["create"])):
    $classenom = str_replace(['"', "'"], '', $_POST["saisie_creat_classe"]);
    $saisie_classe_long = str_replace('"', '', $_POST["saisie_classe_long"]);
    $code_mef = isset($_POST["code_mef"]) ? preg_replace('/[^0-9]/', '', (string)$_POST["code_mef"]) : '';
    $cr = create_classe22($classenom, $saisie_classe_long, $_POST["saisie_site"], $_POST["saisie_langue"], $_POST["saisie_niveau"], $_POST["specification"], $code_mef);
    if ($cr):
        print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGGRP7)."'); });</script>";
    else:
        alertJs(LANGTMESS447);
    endif;
    Pgclose();
endif;
?>
</body></html>
