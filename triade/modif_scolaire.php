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
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom']).' '.htmlspecialchars($_SESSION['prenom']); ?></title>
<style>
#coulBar0 { background-image: none; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<form method="post" onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMODIF13 ?> <?php print LANGASS6 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

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
        $cr = modif_personnel(
            $_POST["id_pers"], $_POST["saisie_creat_nom"], $_POST["saisie_creat_prenom"],
            $_POST["saisie_intitule"], $_POST["saisie_creat_adr"], $_POST["saisie_creat_code"],
            $_POST["saisie_creat_tel"], $_POST["saisie_creat_mail"], $_POST["saisie_creat_commune"],
            $_POST["saisie_creat_tel_port"], '0', '', $_POST["saisie_indice_salaire"]
        );
        if ($cr == 1) {
            alertJs(LANGMODIF14);
            history_cmd($_SESSION["nom"], "MODIFICATION", " de ".$_POST["saisie_creat_nom"]);
        }
        $saisie_id = $_POST["id_pers"];
    } else {
        $saisie_id = $_GET["saisie_id"];
    }

    $passage_argument = "oui";
    $data           = recherche_personne_modif($saisie_id);
    $nom_admin      = htmlspecialchars(trim(stripslashes($data[0][1])));
    $prenom_admin   = htmlspecialchars(trim(stripslashes($data[0][2])));
    $intitule_admin = $data[0][4];
    $mail           = htmlspecialchars(trim(stripslashes($data[0][5])));
    $adr            = htmlspecialchars(trim(stripslashes($data[0][6])));
    $code_post      = htmlspecialchars(trim(stripslashes($data[0][7])));
    $commune        = htmlspecialchars(trim(stripslashes($data[0][8])));
    $tel            = htmlspecialchars(trim($data[0][9]));
    $telPort        = htmlspecialchars(trim($data[0][10]));
    $offline        = trim($data[0][12]);
    $indice_salaire = htmlspecialchars(trim($data[0][15]));
    $saisie_id_int  = intval($saisie_id);
?>

<div class="ma-form-wrap">

<?php if ($offline == 1): ?>
  <div class="ma-inactive-banner">
    <i class="bi bi-exclamation-triangle-fill"></i> Ce compte est actuellement inactif !
  </div>
<?php endif; ?>

  <br>
  <!-- Identité -->
  <fieldset class="ma-fieldset">
    <legend class="ma-legend"><?php print LANGMODIF5 ?></legend>

    <div class="ma-field-row">
      <label class="ma-lbl">Civ.</label>
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
             onclick="open('modif_pers_pass.php?id=<?php print $saisie_id_int ?>&type=MVS','pass','width=400,height=300')">
    </div>

  </fieldset>

  <br>
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

  <br>
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
             size="12" maxlength="15" class="ma-input">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF10 ?></label>
      <input type="text" name="saisie_creat_commune" value="<?php print $commune ?>"
             size="33" maxlength="40" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF11 ?></label>
      <input type="text" name="saisie_creat_tel" value="<?php print $tel ?>"
             size="20" maxlength="18" class="ma-input">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGAGENDA76 ?></label>
      <input type="text" name="saisie_creat_tel_port" value="<?php print $telPort ?>"
             size="20" maxlength="18" class="ma-input">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl"><?php print LANGMODIF12 ?></label>
      <input type="text" name="saisie_creat_mail" value="<?php print $mail ?>"
             size="33" maxlength="150" class="ma-input ma-input-wide">
    </div>
    <div class="ma-field-row">
      <label class="ma-lbl">Indice salaire</label>
      <input type="text" name="saisie_indice_salaire" value="<?php print $indice_salaire ?>"
             size="33" maxlength="150" class="ma-input ma-input-wide">
    </div>

  </fieldset>

  <input type="hidden" name="id_pers" value="<?php print $saisie_id_int ?>">

  <div class="ma-actions">
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGMODIF13 ?>","create");</script>
    <script language="JavaScript">buttonMagic("<?php print LANGBT9 ?>","list_scolaire.php","_parent","","");</script>
    <?php if ($offline == 0): ?>
      <script language="JavaScript">buttonMagicSubmit("<?php print LANGMESS398 ?>","offline");</script>
    <?php else: ?>
      <script language="JavaScript">buttonMagicSubmit("<?php print LANGMESS399 ?>","online");</script>
    <?php endif; ?>
  </div>

</div>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY></HTML>
