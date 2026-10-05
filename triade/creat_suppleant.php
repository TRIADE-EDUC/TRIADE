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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
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
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE39; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <img src="image/commun/personne.png" alt="" class="ca-nav-img">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGBT11 ?>","list_suppleant.php","liste_suppleant","","");</script>
    <script language="JavaScript">buttonMagic("Supprimer affectation suppléant","suppression_compte_suppleant.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<?php
include_once('librairie_php/db_triade.php');
$cnx = cnx();
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE39; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" onsubmit="return verifsuppleant()" name="formulaire">
<div class="ca-form-wrap">

  <br>
  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGMODIF5; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl">Civ</label>
      <select name="saisie_intitule" class="ca-select"><?php listingCiv(); ?></select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA1; ?></label>
      <input type="text" name="saisie_creat_nom" size="25" maxlength="40" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA2; ?></label>
      <input type="text" name="saisie_creat_prenom" size="25" maxlength="40" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA3; ?></label>
      <input type="text" name="saisie_creat_password" size="15" maxlength="50" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA5; ?></label>
      <select name="saisie_remplacement" class="ca-select">
        <option value=""><?php echo LANGCHOIX; ?></option>
        <?php select_personne_2('ENS', 30); ?>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo ucwords(LANGTE2); ?></label>
      <input type="text" name="saisie_date_entree" size="12" maxlength="10" class="ca-input" value="<?php echo dateDMY(); ?>" style="max-width:110px;">
      <span style="margin:0 8px;font-size:12px;color:#444;"><?php echo ucwords(LANGTE10); ?></span>
      <input type="text" name="saisie_date_sortie" size="12" maxlength="10" class="ca-input" value="inconnu" onclick="this.value='jj/mm/aaaa'" style="max-width:110px;">
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

<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>

<?php
if (isset($_POST["create"])):
    $cr = create_suppleant($_POST["saisie_creat_nom"], $_POST["saisie_creat_prenom"], $_POST["saisie_creat_password"], $_POST["saisie_remplacement"], $_POST["saisie_date_entree"], $_POST["saisie_date_sortie"], $_POST["saisie_intitule"]);
    if ($cr == 1) {
        print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGNA4)."'); });</script>";
    } elseif ($cr == -3) {
        alertJs(LANGPASSG2);
    } else {
        alertJs(LANGPASSG3);
    }
endif;
Pgclose();
?>
</body></html>
