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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
<script>
function copieAdresse() {
    document.formulaire.saisie_nomtuteur.value = document.formulaire.saisie_nom.value;
    document.formulaire.saisie_adr1.value = document.formulaire.saisie_adr_eleve.value;
    document.formulaire.saisie_code_post_adr1.value = document.formulaire.saisie_code_post_adr_eleve.value;
    document.formulaire.saisie_commune_adr1.value = document.formulaire.saisie_commune_adr_eleve.value;
    document.formulaire.saisie_telephone.value = document.formulaire.saisie_tel_fixe_eleve.value;
    document.formulaire.saisie_nomtuteur2.value = document.formulaire.saisie_nom.value;
    document.formulaire.saisie_adr2.value = document.formulaire.saisie_adr_eleve.value;
    document.formulaire.saisie_code_post_adr2.value = document.formulaire.saisie_code_post_adr_eleve.value;
    document.formulaire.saisie_commune_adr2.value = document.formulaire.saisie_commune_adr_eleve.value;
}
</script>
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

<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
?>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE43; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <img src="image/commun/etudiant.png" alt="" class="ca-nav-img">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGMESS187 ?>","recherche_eleve.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGMESS188 ?>","base_de_donne_importation.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGMESS189 ?>","suppression_compte_eleve.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE43; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" onsubmit="return valide_creat_eleve()" name="formulaire">
<div class="ca-form-wrap">

  <br>
  <!-- ── Élève ── -->
  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGELE1; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE2; ?></label>
      <input type="text" name="saisie_nom" size="25" maxlength="50" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE3; ?></label>
      <input type="text" name="saisie_prenom" size="25" maxlength="50" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGBULL3; ?></label>
      <select name="annee_scolaire" class="ca-select">
        <?php filtreAnneeScolaireSelectFutur(); ?>
      </select>
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE4; ?></label>
      <select name="saisie_classe" class="ca-select">
        <option value="0"><?php echo LANGCHOIX; ?></option>
        <?php select_classe(); ?>
      </select>
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS190; ?></label>
      <select name="saisie_lv1" class="ca-select">
        <option value=""> </option><?php select_matiere2(); ?>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS191; ?></label>
      <select name="saisie_lv2" class="ca-select">
        <option value=""> </option><?php select_matiere2(); ?>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE5; ?></label>
      <select name="saisie_option" class="ca-select">
        <option value=""> </option><?php select_matiere2(); ?>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE6; ?></label>
      <select name="saisie_regime" class="ca-select">
        <option value=""><?php echo LANGCHOIX; ?></option>
        <option value="Interne"><?php echo LANGELE7; ?></option>
        <option value="Demi-pension"><?php echo LANGELE8; ?></option>
        <option value="Externe"><?php echo LANGELE9; ?></option>
        <optgroup label="Personnalisé"><?php selectRegime(); ?></optgroup>
      </select>
      <a href="regime_ajout.php" style="margin-left:8px;font-size:11px;color:#080A66;"><?php echo LANGTMESS440; ?></a>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGTMESS441; ?></label>
      <input type="text" name="saisie_rangement" size="33" maxlength="200" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE10; ?></label>
      <?php include_once("librairie_php/calendar.php"); ?>
      <input type="text" name="saisie_date_naissance" size="12" class="ca-input" style="max-width:130px;">
      <?php calendarpopupDim('id1', 'document.formulaire.saisie_date_naissance', $_SESSION["langue"], "1", "0"); ?>
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS293; ?></label>
      <div style="display:flex;align-items:center;gap:12px;font-size:12px;">
        M <input type="radio" name="saisie_sexe" value="m">
        &nbsp;F <input type="radio" name="saisie_sexe" value="f">
      </div>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS192; ?></label>
      <div style="display:flex;align-items:center;gap:12px;font-size:12px;">
        Oui <input type="radio" name="saisie_boursier" value="1">
        &nbsp;<?php echo LANGNON; ?> <input type="radio" name="saisie_boursier" value="0" checked>
      </div>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS193; ?></label>
      <div style="display:flex;align-items:center;gap:12px;font-size:12px;">
        Oui <input type="radio" name="saisie_BDE" value="1">
        &nbsp;<?php echo LANGNON; ?> <input type="radio" name="saisie_BDE" value="0" checked>
      </div>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS194; ?></label>
      <div style="display:flex;align-items:center;gap:12px;font-size:12px;">
        Oui <input type="radio" name="saisie_CDI" value="1">
        &nbsp;<?php echo LANGNON; ?> <input type="radio" name="saisie_CDI" value="0" checked>
      </div>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS195; ?></label>
      <input type="text" name="saisie_boursier_montant" size="15" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS196; ?></label>
      <input type="text" name="saisie_indemnite_stage" size="4" class="ca-input" style="max-width:60px;">
      <span style="font-size:12px;margin:0 6px;"><?php echo LANGTMESS442; ?>. <?php echo LANGTMESS443; ?> :</span>
      <input type="text" name="saisie_nbmoisindemnite_stage" size="2" class="ca-input" style="max-width:40px;">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE11; ?></label>
      <input type="text" name="saisie_nationalite" size="20" maxlength="20" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGEDIT6; ?></label>
      <input type="text" name="saisie_lieunais" size="25" maxlength="40" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Département de naissance</label>
      <input type="text" name="saisie_departementnais" size="15" maxlength="40" class="ca-input">
      <em style="font-size:11px;color:#666;margin-left:6px;">(ex : 75013)</em>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA3ter; ?></label>
      <input type="password" name="saisie_passwd_eleve" size="15" maxlength="50" class="ca-input">
      <span class="ca-required">*</span>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE12; ?></label>
      <input type="text" name="saisie_numnational" size="20" maxlength="30" class="ca-input"
             <?php if (AUTOINE == "oui") echo "disabled='disabled' value='auto-création'"; ?>>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Numéro INE</label>
      <input type="text" name="saisie_ine" size="20" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGTMESS444; ?></label>
      <input type="text" name="saisie_codecompta" size="20" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGAGENDA63; ?></label>
      <input type="text" name="saisie_adr_eleve" size="33" maxlength="100" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE15; ?></label>
      <input type="text" name="saisie_code_post_adr_eleve" size="8" maxlength="6" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE16; ?></label>
      <input type="text" name="saisie_commune_adr_eleve" size="25" maxlength="40" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGAGENDA73; ?></label>
      <input type="text" name="saisie_pays_eleve" size="25" maxlength="50" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS199; ?></label>
      <input type="text" name="saisie_tel_fixe_eleve" size="20" maxlength="25" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS200.' '.LANGTITRE40; ?></label>
      <input type="text" name="saisie_portable_eleve" size="20" maxlength="25" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE244.' '.LANGTITRE40; ?></label>
      <input type="text" name="saisie_email_eleve" size="33" maxlength="48" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE244.' '.LANGTMESS445; ?></label>
      <input type="text" name="saisie_emailpro_eleve" size="33" maxlength="48" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Série du bac</label>
      <input type="text" name="saisie_serie_bac" size="20" maxlength="48" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Année d'obtention du bac</label>
      <input type="text" name="saisie_annee_bac" size="8" maxlength="48" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Département du bac</label>
      <input type="text" name="saisie_departement_bac" size="20" maxlength="48" class="ca-input">
    </div>
    <div class="ca-field-row" style="align-items:flex-start;">
      <label class="ca-lbl" style="padding-top:4px;"><?php echo LANGRESA40.' '.LANGTITRE40; ?></label>
      <textarea name="saisie_info_eleve" cols="30" rows="3" class="ca-input ca-input-wide" style="height:auto;"></textarea>
    </div>
  </fieldset>

  <br><br>

  <!-- ── Responsable 1 ── -->
  <fieldset class="ca-fieldset">
    <legend class="ca-legend">Responsable 1</legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS203; ?></label>
      <select name="situation_familiale" class="ca-select">
        <option value=""></option>
        <option value="<?php echo LANGSITU1; ?>"><?php echo LANGSITU1; ?></option>
        <option value="<?php echo LANGSITU2; ?>"><?php echo LANGSITU2; ?></option>
        <option value="<?php echo LANGSITU3; ?>"><?php echo LANGSITU3; ?></option>
        <option value="<?php echo LANGSITU4; ?>"><?php echo LANGSITU4; ?></option>
        <option value="<?php echo LANGSITU5; ?>"><?php echo LANGSITU5; ?></option>
        <option value="<?php echo LANGSITU6; ?>"><?php echo LANGSITU6; ?></option>
        <option value="<?php echo LANGSITU7; ?>"><?php echo LANGSITU7; ?></option>
        <option value="Décédé(e)">Décédé(e)</option>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS178; ?> 1</label>
      <select name="saisie_civ1" class="ca-select"><?php listingCiv(); ?></select>
      <a href="#" onclick="copieAdresse(); return false;" style="margin-left:10px;font-size:11px;color:#080A66;"><?php echo LANGMESS204; ?></a>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE2.' '.LANGTITRE41; ?> 1</label>
      <input type="text" name="saisie_nomtuteur" size="25" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE3.' '.LANGTITRE41; ?> 1</label>
      <input type="text" name="saisie_prenomtuteur" size="25" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE14; ?></label>
      <input type="text" name="saisie_adr1" size="33" maxlength="100" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE15; ?> 1</label>
      <input type="text" name="saisie_code_post_adr1" size="8" maxlength="6" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE16; ?> 1</label>
      <input type="text" name="saisie_commune_adr1" size="25" maxlength="40" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS200; ?> 1</label>
      <input type="text" name="saisie_tel_port_1" size="20" maxlength="25" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE244.' '.LANGTITRE42; ?> 1</label>
      <input type="text" name="saisie_email" size="33" maxlength="150" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA3bis; ?> 1</label>
      <input type="password" name="saisie_passwd" size="15" maxlength="50" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Profession resp. 1</label>
      <input type="text" name="saisie_profession_pere" size="20" maxlength="20" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Téléphone resp. 1</label>
      <input type="text" name="saisie_tel_prof_pere" size="20" maxlength="18" class="ca-input">
    </div>
  </fieldset>

  <br><br>

  <!-- ── Responsable 2 ── -->
  <fieldset class="ca-fieldset">
    <legend class="ca-legend">Responsable 2</legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS178; ?> 2</label>
      <select name="saisie_civ2" class="ca-select"><?php listingCiv(); ?></select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE2.' '.LANGTITRE41; ?> 2</label>
      <input type="text" name="saisie_nomtuteur2" size="25" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE3.' '.LANGTITRE41; ?> 2</label>
      <input type="text" name="saisie_prenomtuteur2" size="25" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE17; ?></label>
      <input type="text" name="saisie_adr2" size="33" maxlength="100" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE15; ?> 2</label>
      <input type="text" name="saisie_code_post_adr2" size="8" maxlength="6" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE16; ?> 2</label>
      <input type="text" name="saisie_commune_adr2" size="25" maxlength="40" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS200; ?> 2</label>
      <input type="text" name="saisie_tel_port_2" size="20" maxlength="25" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE244.' '.LANGTITRE42; ?> 2</label>
      <input type="text" name="saisie_email_2" size="33" maxlength="150" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGNA3bis; ?> 2</label>
      <input type="password" name="saisie_passwd_parent2" size="15" maxlength="50" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE20; ?></label>
      <input type="text" name="saisie_telephone" size="20" maxlength="18" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Profession resp. 2</label>
      <input type="text" name="saisie_profession_mere" size="20" maxlength="20" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl">Téléphone resp. 2</label>
      <input type="text" name="saisie_tel_prof_mere" size="20" maxlength="18" class="ca-input">
    </div>
  </fieldset>

  <br><br>

  <!-- ── Établissement précédent ── -->
  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGELE25; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE26; ?></label>
      <input type="text" name="saisie_nom_etablissement" size="25" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE27; ?></label>
      <input type="text" name="saisie_numero_etablissement" size="20" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGbasededoni41; ?></label>
      <input type="text" name="saisie_classe_ant" size="20" maxlength="30" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGbasededoni42; ?></label>
      <input type="text" name="saisie_date_ant" size="12" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE15; ?></label>
      <input type="text" name="saisie_code_postal_etablissement" size="8" maxlength="6" class="ca-input">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGELE16; ?></label>
      <input type="text" name="saisie_commune_etablissement" size="25" maxlength="30" class="ca-input">
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
if (isset($_POST["create"])):
    if (verif_compte_cree($_POST["saisie_nom"], $_POST["saisie_prenom"])) {
        $params = [
            'ne'  => $_POST["saisie_nom"],         'pe'  => $_POST["saisie_prenom"],
            'ce'  => $_POST["saisie_classe"],       'lv1' => $_POST["saisie_lv1"],
            'lv2' => $_POST["saisie_lv2"],          'option' => $_POST["saisie_option"],
            'regime' => $_POST["saisie_regime"],    'naiss' => $_POST["saisie_date_naissance"],
            'nat' => $_POST["saisie_nationalite"],  'mdp'  => $_POST["saisie_passwd"],
            'mdp2' => $_POST["saisie_passwd_parent2"], 'mdpeleve' => $_POST["saisie_passwd_eleve"],
            'nt'  => $_POST["saisie_nomtuteur"],    'pt'   => $_POST["saisie_prenomtuteur"],
            'adr1' => $_POST["saisie_adr1"],        'cpadr1' => $_POST["saisie_code_post_adr1"],
            'commadr1' => $_POST["saisie_commune_adr1"], 'nadr2' => $_POST["saisie_num_adr2"] ?? '',
            'adr2' => $_POST["saisie_adr2"],        'cpadr2' => $_POST["saisie_code_post_adr2"],
            'commadr2' => $_POST["saisie_commune_adr2"], 'tel'  => $_POST["saisie_telephone"],
            'profp' => $_POST["saisie_profession_pere"], 'telprofp' => $_POST["saisie_tel_prof_pere"],
            'profm' => $_POST["saisie_profession_mere"], 'telprofm' => $_POST["saisie_tel_prof_mere"],
            'nomet' => $_POST["saisie_nom_etablissement"], 'numet' => $_POST["saisie_numero_etablissement"],
            'cpet'  => $_POST["saisie_code_postal_etablissement"], 'commet' => $_POST["saisie_commune_etablissement"],
            'numero_eleve' => $_POST["saisie_numnational"], 'email' => $_POST["saisie_email"],
            'classe_ant' => $_POST["saisie_classe_ant"], 'annee_ant' => $_POST["saisie_date_ant"],
            'civ_1' => $_POST["saisie_civ1"],       'civ_2' => $_POST["saisie_civ2"],
            'tel_eleve' => $_POST["saisie_portable_eleve"], 'mail_eleve' => $_POST["saisie_email_eleve"],
            'mailpro_eleve' => $_POST["saisie_emailpro_eleve"], 'nom_resp2' => $_POST["saisie_nomtuteur2"],
            'prenom_resp2' => $_POST["saisie_prenomtuteur2"], 'lieunais' => $_POST["saisie_lieunais"],
            'tel_port_1' => $_POST["saisie_tel_port_1"], 'tel_port_2' => $_POST["saisie_tel_port_2"],
            'email_2' => $_POST["saisie_email_2"],  'codecompta' => $_POST["saisie_codecompta"],
            'sexe' => $_POST["saisie_sexe"],         'information' => $_POST["saisie_info_eleve"],
            'adr_eleve' => $_POST["saisie_adr_eleve"], 'commune_eleve' => $_POST["saisie_commune_adr_eleve"],
            'ccp_eleve' => $_POST["saisie_code_post_adr_eleve"], 'tel_fixe_eleve' => $_POST["saisie_tel_fixe_eleve"],
            'pays_eleve' => $_POST["saisie_pays_eleve"], 'boursier' => $_POST["saisie_boursier"],
            'cdi' => $_POST["saisie_CDI"],           'bde' => $_POST["saisie_BDE"],
            'boursier_montant' => $_POST["saisie_boursier_montant"], 'indemnite_stage' => $_POST["saisie_indemnite_stage"],
            'nbmoisindemnite_stage' => $_POST["saisie_nbmoisindemnite_stage"], 'rangement' => $_POST["saisie_rangement"],
            'situation_familiale' => $_POST["situation_familiale"], 'annee_scolaire' => $_POST["annee_scolaire"],
            'saisie_serie_bac' => $_POST["saisie_serie_bac"], 'saisie_annee_bac' => $_POST["saisie_annee_bac"],
            'saisie_departement_bac' => $_POST["saisie_departement_bac"],
            'saisie_departementnais' => $_POST["saisie_departementnais"], 'saisie_ine' => $_POST["saisie_ine"],
        ];
        $cr = create_eleve($params, 1);
        if ($cr == 1) {
            history_cmd($_SESSION["nom"], "CREATION", "élève ".$_POST["saisie_nom"]);
            print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGELE28)."'); });</script>";
            if (FINANCIERVATEL == "oui") {
                $sql_eleve  = "SELECT elev_id FROM ".PREFIXE."eleves ";
                $sql_eleve .= "WHERE nom='".$params['ne']."' AND prenom='".$params['pe']."' ";
                $sql_eleve .= "AND classe=".$params['ce']." AND date_naissance='".dateFormBase($params['naiss'])."'";
                $res_eleve = execSql($sql_eleve);
                if ($res_eleve->numRows() > 0) {
                    $ligne_eleve = &$res_eleve->fetchRow();
                    ?>
                    <script language="javascript">
                    if (confirm("<?php echo LANGTMESS446; ?>")) {
                        open('module_financier/rib_editer.php?elev_id=<?php echo $ligne_eleve[0]; ?>','rib','width=550,height=320');
                    }
                    </script>
                    <?php
                }
            }
        } elseif ($cr == -3) {
            $affiche = affichageMessageSecurite2();
            alertJs($affiche);
        } else {
            alertJs(LANGPASSG3);
        }
    } else {
        alertJs(LANGELE29);
    }
endif;
Pgclose();
?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>
</body></html>
