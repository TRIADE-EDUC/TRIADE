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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom']).' '.htmlspecialchars($_SESSION['prenom']) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTITRE36 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <img src="image/commun/personne.png" alt="" class="ca-nav-img">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php print LANGBT8 ?>","list_admin.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php print LANGAGENDA86 ?>","base_de_donne_importation.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php print LANGCREAT2 ?>","suppression_compte_admin.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<?php
include_once("librairie_php/db_triade.php");
$affiche = affichageMessageSecurite();
$txt2 = preg_replace('/\<b\>/', "", $affiche);
$txt2 = preg_replace('/\<\/b\>/', "", $txt2);
$txt2 = preg_replace('/\<br \/\>/', "", $txt2);
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTITRE6 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" onsubmit="return verifcommun('<?php print $txt2 ?>')" name="formulaire">
<?php
if ($_SESSION["nav"] != "IE") {
    print "<SCRIPT language=\"JavaScript\">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>";
}
?>
<div class="ca-form-wrap">

  <br>
  <!-- Coordonnées -->
  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php print LANGMODIF7 ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMODIF8 ?></label>
      <input type="text" name="saisie_creat_adr" size="33" maxlength="100" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMODIF9 ?></label>
      <input type="text" name="saisie_creat_code" size="33" maxlength="15" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMODIF10 ?></label>
      <input type="text" name="saisie_creat_commune" size="33" maxlength="40" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGAGENDA73 ?></label>
      <input type="text" name="saisie_pays" size="33" maxlength="50" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMODIF11 ?></label>
      <input type="text" name="saisie_creat_tel" size="33" maxlength="18" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGAGENDA76 ?></label>
      <input type="text" name="saisie_creat_tel_port" size="33" maxlength="18" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMODIF12 ?></label>
      <input type="text" name="saisie_creat_mail" size="33" maxlength="150" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMESS179 ?></label>
      <input type="text" name="saisie_indice_salaire" size="33" maxlength="150" class="ca-input ca-input-wide">
    </div>

  </fieldset>
<br><br>
  <!-- Identité / Connexion -->
  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php print LANGMODIF5 ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGMESS178 ?></label>
      <select name="saisie_intitule" class="ca-select">
        <?php listingCiv() ?>
      </select>
    </div>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGNA1 ?></label>
      <input type="text" name="saisie_creat_nom" size="25" maxlength="30" class="ca-input">
      <span class="ca-required">*</span>
    </div>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGNA2 ?></label>
      <input type="text" name="saisie_creat_prenom" size="25" maxlength="30" class="ca-input">
      <span class="ca-required">*</span>
    </div>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php print LANGNA3 ?></label>
      <input type="text" name="saisie_creat_password" size="15" maxlength="50" class="ca-input">
      <span class="ca-required">*</span>
      <a href="#"
         onMouseOver="AffBulle3('ATTENTION','image/commun/warning.jpg','<font face=Verdana size=1><?php print $affiche ?></FONT>'); window.status=''; return true;"
         onMouseOut="HideBulle()"><img src="./image/help.gif" align="center" border="0" alt="aide"></a>
      <?php
      if ($_SESSION["nav"] == "IE") {
          print "<SCRIPT language=\"JavaScript\">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>";
      }
      ?>
    </div>

  </fieldset>

  <div class="ca-actions">
    <?php brmozilla($_SESSION["navigateur"]); ?>
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT7 ?>","create");</script>
    <?php brmozilla($_SESSION["navigateur"]); ?>
  </div>

</div>
</form>

</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php
if (isset($_POST["create"])):
    include_once("librairie_php/db_triade.php");
    validerequete("menuadmin");
    $cr = create_personnel(
        $_POST["saisie_creat_nom"], $_POST["saisie_creat_prenom"],
        $_POST["saisie_creat_password"], 'ADM', $_POST["saisie_intitule"], '',
        $_POST["saisie_creat_adr"], $_POST["saisie_creat_code"],
        $_POST["saisie_creat_tel"], $_POST["saisie_creat_mail"],
        $_POST["saisie_creat_commune"], $_POST["saisie_creat_tel_port"],
        '0', $_POST["saisie_pays"], $_POST["saisie_indice_salaire"], ''
    );
    if ($cr == 1) {
        history_cmd($_SESSION["nom"], "CREATION", "administration ".$_POST["saisie_creat_nom"]);
        alertJs(LANGNA4);
    } elseif ($cr == 2) {
        $code = "window.location.replace('./creat_admin.php')";
        codeJS($code);
    } elseif ($cr == -3) {
        $affiche = affichageMessageSecurite2();
        alertJs($affiche);
    } elseif ($cr == -1) {
        alertJs(LANGCREAT1);
    }
    Pgclose();
endif;
?>
</BODY></HTML>
