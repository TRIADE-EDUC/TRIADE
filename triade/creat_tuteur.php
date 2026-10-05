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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGMESS180; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <img src="image/commun/personne.png" alt="" class="ca-nav-img">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGMESS181 ?>","list_tuteur.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGAGENDA86 ?>","base_de_donne_importation20.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGCREAT2 ?>","suppression_compte_tuteur.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<?php
include_once("librairie_php/db_triade.php");
$cnx = cnx();
$affiche = affichageMessageSecurite();
$txt2 = preg_replace('/\<b\>/', '', $affiche);
$txt2 = preg_replace('/\<\/b\>/', '', $txt2);
$txt2 = preg_replace('/\<br \/\>/', '', $txt2);
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGMESS180; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" action="creat_tuteur.php" onsubmit="return verifcommun('<?php echo $txt2; ?>')" name="formulaire">
<?php
if ($_SESSION["nav"] != "IE") {
    print "<SCRIPT language=\"JavaScript\">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>";
}
?>
<div class="ca-form-wrap">

  <br>
  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGMODIF5; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS178; ?></label>
      <select name="saisie_intitule" class="ca-select"><?php listingCiv(); ?></select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA1; ?></label>
      <input type="text" name="saisie_creat_nom" size="25" maxlength="30" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA2; ?></label>
      <input type="text" name="saisie_creat_prenom" size="25" maxlength="30" class="ca-input">
      <span class="ca-required">*</span>
      <a href="#"
         onMouseOver="AffBulle3('Info','image/commun/warning.jpg','<font face=Verdana size=1>Si pas de prénom, indiquer \'inconnu\'</FONT>'); window.status=''; return true;"
         onMouseOut="HideBulle()"><img src="./image/help.gif" align="center" border="0" alt="aide"></a>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA3; ?></label>
      <input type="text" name="saisie_creat_password" size="15" maxlength="50" class="ca-input">
      <span class="ca-required">*</span>
      <a href="#"
         onMouseOver="AffBulle3('ATTENTION','image/commun/warning.jpg','<font face=Verdana size=1><?php echo $affiche ?></FONT>'); window.status=''; return true;"
         onMouseOut="HideBulle()"><img src="./image/help.gif" align="center" border="0" alt="aide"></a>
      <?php if ($_SESSION["nav"] == "IE") { print "<SCRIPT language=\"JavaScript\">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>"; } ?>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS183; ?></label>
      <select name="id_societe" class="ca-select">
        <option value="0"><?php echo LANGCHOIX; ?></option>
        <?php select_entreprise_limit(25); ?>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS184; ?></label>
      <input type="text" name="saisie_qualite" size="33" maxlength="100" class="ca-input ca-input-wide">
    </div>
  </fieldset>

  <br><br>

  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGMODIF7; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMODIF8; ?></label>
      <input type="text" name="saisie_creat_adr" size="33" maxlength="100" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMODIF9; ?></label>
      <input type="text" name="saisie_creat_code" size="10" maxlength="15" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMODIF10; ?></label>
      <input type="text" name="saisie_creat_commune" size="33" maxlength="40" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGAGENDA73; ?></label>
      <input type="text" name="saisie_pays" size="33" maxlength="50" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMODIF11; ?></label>
      <input type="text" name="saisie_creat_tel" size="20" maxlength="18" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGAGENDA76; ?></label>
      <input type="text" name="saisie_creat_tel_port" size="20" maxlength="18" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMODIF12; ?></label>
      <input type="text" name="saisie_creat_mail" size="33" maxlength="150" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS179; ?></label>
      <input type="text" name="saisie_indice_salaire" size="33" maxlength="150" class="ca-input ca-input-wide">
    </div>
  </fieldset>

  <div class="ca-actions">
    <?php brmozilla($_SESSION["navigateur"]); ?>
    <script language="JavaScript">buttonMagicSubmit("<?php echo LANGBT7 ?>","create");</script>
    <?php brmozilla($_SESSION["navigateur"]); ?>
  </div>

</div>
</form>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}

if (isset($_POST["create"])):
    include_once("librairie_php/db_triade.php");
    validerequete("3");
    $prenom = ($_POST["saisie_creat_prenom"] == "inconnu") ? " " : $_POST["saisie_creat_prenom"];
    $cr = create_personnel($_POST["saisie_creat_nom"], $prenom, $_POST["saisie_creat_password"], 'TUT', $_POST["saisie_intitule"], '', $_POST["saisie_creat_adr"], $_POST["saisie_creat_code"], $_POST["saisie_creat_tel"], $_POST["saisie_creat_mail"], $_POST["saisie_creat_commune"], $_POST["saisie_creat_tel_port"], $_POST["id_societe"], $_POST["saisie_pays"], $_POST["saisie_indice_salaire"], $_POST["saisie_qualite"]);
    if ($cr == 1) {
        print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGNA4)."'); });</script>";
    } elseif ($cr == -3) {
        $affiche = affichageMessageSecurite2();
        alertJs($affiche);
    } elseif ($cr == -1) {
        alertJs(LANGCREAT1);
    } else {
        alertJs(LANGPASSG3);
    }
    Pgclose();
endif;
?>
</body></html>
