<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
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
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom']).' '.htmlspecialchars($_SESSION['prenom']) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form method="post" onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMODIF4.' '.LANGADMIN ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
error($cnx);

if (isset($_POST["offline"])) {
    modif_personnel_actif_desactif($_POST["id_pers"], "1");
    history_cmd($_SESSION["nom"], "DESACTIVE", " de ".$_POST["saisie_creat_nom"]);
}
if (isset($_POST["online"])) {
    modif_personnel_actif_desactif($_POST["id_pers"], "0");
    history_cmd($_SESSION["nom"], "ACTIVE", " de ".$_POST["saisie_creat_nom"]);
}
if (isset($_POST["create"])) {
    $_POST["saisie_creat_nom"]    = stripslashes($_POST["saisie_creat_nom"]);
    $_POST["saisie_creat_prenom"] = stripslashes($_POST["saisie_creat_prenom"]);
    $cr = modif_personnel(
        $_POST["id_pers"], $_POST["saisie_creat_nom"], $_POST["saisie_creat_prenom"],
        $_POST["saisie_intitule"], $_POST["saisie_creat_adr"], $_POST["saisie_creat_code"],
        $_POST["saisie_creat_tel"], $_POST["saisie_creat_mail"], $_POST["saisie_creat_commune"],
        $_POST["saisie_creat_tel_port"], 0, $_POST['saisie_pays'], $_POST["saisie_indice_salaire"]
    );
    if ($cr == 1) {
        alertJs(LANGMODIF14);
        history_cmd($_SESSION["nom"], "MODIFICATION", " de ".$_POST["saisie_creat_nom"]);
    }
    $saisie_id = $_POST["id_pers"];
} else {
    $saisie_id = $_GET["saisie_id"];
}

$data          = recherche_personne_modif($saisie_id);
$nom_admin     = htmlspecialchars(trim(stripslashes($data[0][1])));
$prenom_admin  = htmlspecialchars(trim(stripslashes($data[0][2])));
$intitule_admin = $data[0][4];
$mail          = htmlspecialchars(trim($data[0][5]));
$adr           = htmlspecialchars(trim(stripslashes($data[0][6])));
$code_post     = htmlspecialchars(trim(stripslashes($data[0][7])));
$commune       = htmlspecialchars(trim(stripslashes($data[0][8])));
$tel           = htmlspecialchars(trim($data[0][9]));
$telPort       = htmlspecialchars(trim($data[0][10]));
$offline       = trim($data[0][12]);
$pays          = htmlspecialchars(trim($data[0][14]));
$indice_salaire = htmlspecialchars(trim($data[0][15]));
$saisie_id_int = intval($saisie_id);
?>

<div class="ma-form-wrap">

<?php if ($offline == 1) { ?>
  <div class="ma-inactive-banner">
    <img src="./image/commun/warning.png" alt="warning"> Ce compte est actuellement inactif !
  </div>
<?php } ?>

  <br><br>
  <!-- Identité -->
  <fieldset class="ma-fieldset">
    <legend class="ma-legend"><?php print LANGMODIF5 ?></legend>

    <div class="ma-field-row">
      <label class="ma-lbl">Civ</label>
      <select name="saisie_intitule" class="ma-select">
        <option value="<?php print htmlspecialchars($intitule_admin) ?>"><?php print civ($intitule_admin) ?></option>
        <?php listingCiv() ?>
      </select>
    </div>

    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGNA1 ?></label>
      <input type="text" name="saisie_creat_nom" value="<?php print $nom_admin ?>"
             size="33" maxlength="30" class="ma-input ma-input-wide">
    </div>

    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGNA2 ?></label>
      <input type="text" name="saisie_creat_prenom" value="<?php print $prenom_admin ?>"
             size="33" maxlength="30" class="ma-input ma-input-wide">
    </div>

    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGNA3 ?></label>
      <input type="button" class="ma-btn-pass" value="<?php print LANGPER30 ?>"
             onclick="open('modif_pers_pass.php?id=<?php print $saisie_id_int ?>&type=ADM','pass','width=400,height=300')">
    </div>

  </fieldset>

  <br><br>
  <!-- Photo -->
  <fieldset class="ma-fieldset">
    <legend class="ma-legend"><?php print LANGMODIF6 ?></legend>

    <div class="ma-photo-row">
      <img src="image_trombi.php?idP=<?php print $saisie_id_int ?>" border="0" alt="photo" class="ma-photo">
      <div class="ma-photo-links">
        <button type="button" class="ma-btn-photo-pill"
                onclick="open('photoajoutpers.php?idpers=<?php print $saisie_id_int ?>','photo','width=450,height=280')">
          ✏️ <?php print LANGMODIF20 ?>
        </button>
        <button type="button" class="ma-btn-photo-pill ma-btn-photo-refresh"
                onclick="window.location.reload(true)">
          🔄 <?php print LANGMODIF18 ?>
        </button>
      </div>
    </div>

  </fieldset>

  <br><br>
  <!-- Coordonnées -->
  <fieldset class="ma-fieldset">
    <legend class="ma-legend"><?php print LANGMODIF7 ?></legend>

    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF8 ?></label>
      <input type="text" name="saisie_creat_adr" value="<?php print $adr ?>"
             size="33" maxlength="100" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF9 ?></label>
      <input type="text" name="saisie_creat_code" value="<?php print $code_post ?>"
             size="33" maxlength="15" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF10 ?></label>
      <input type="text" name="saisie_creat_commune" value="<?php print $commune ?>"
             size="33" maxlength="40" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGAGENDA73 ?></label>
      <input type="text" name="saisie_pays" value="<?php print $pays ?>"
             size="33" maxlength="50" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF11 ?></label>
      <input type="text" name="saisie_creat_tel" value="<?php print $tel ?>"
             size="33" maxlength="18" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGAGENDA76 ?></label>
      <input type="text" name="saisie_creat_tel_port" value="<?php print $telPort ?>"
             size="33" maxlength="18" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF12 ?></label>
      <input type="text" name="saisie_creat_mail" value="<?php print $mail ?>"
             size="33" maxlength="150" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMESS179 ?></label>
      <input type="text" name="saisie_indice_salaire" value="<?php print $indice_salaire ?>"
             size="33" maxlength="150" class="ma-input ma-input-wide">
    </div>

  </fieldset>

  <input type="hidden" name="id_pers" value="<?php print $saisie_id_int ?>">

  <div class="ma-actions">
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGMODIF13 ?>","create");</script>
    <script language="JavaScript">buttonMagic("<?php print LANGBT8 ?>","list_admin.php","_parent","","");</script>
    <?php if ($mail != "support@triade-educ.org") { ?>
      <?php if ($offline == 0) { ?>
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGMESS398 ?>","offline");</script>
      <?php } else { ?>
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGMESS399 ?>","online");</script>
      <?php } ?>
    <?php } ?>
  </div>

</div>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY></HTML>
