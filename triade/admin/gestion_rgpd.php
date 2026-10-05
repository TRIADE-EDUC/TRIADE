<?php
session_start();
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("../librairie_php/timezone.php");
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<title>Triade — Gestion RGPD</title>
</HEAD>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion RGPD</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php

$saved = false;

if (isset($_POST['creatergpd'])) {
    $fichierInstall = "../data/install_log/install.inc";
    $date_installe  = "";
    if (file_exists($fichierInstall)) {
        $fh   = fopen($fichierInstall, "r");
        $tab  = explode(" ", fread($fh, 10000));
        fclose($fh);
        $date_installe = stripslashes($tab[2] ?? "");
    }
    $date_update_info = dateDMY();

    $texte  = "<?php\n";
    $texte .= "define(\"RGPD_NOM_ETAB\",\"".$_POST['nom_etablissement']."\");\n";
    $texte .= "define(\"RGPD_NOM_ACAD\",\"".$_POST['nom_academie']."\");\n";
    $texte .= "define(\"RGPD_NOM_RESP\",\"".$_POST['nom_resp']."\");\n";
    $texte .= "define(\"RGPD_PRENOM_RESP\",\"".$_POST['prenom_resp']."\");\n";
    $texte .= "define(\"RGPD_ADRESSE_RESP\",\"".$_POST['adresse_resp']."\");\n";
    $texte .= "define(\"RGPD_CCP_RESP\",\"".$_POST['ccp_resp']."\");\n";
    $texte .= "define(\"RGPD_VILLE_RESP\",\"".$_POST['ville_resp']."\");\n";
    $texte .= "define(\"RGPD_TEL_RESP\",\"".$_POST['tel_resp']."\");\n";
    $texte .= "define(\"RGPD_EMAIL_RESP\",\"".$_POST['email_resp']."\");\n";
    $texte .= "define(\"RGPD_NOM_DPO\",\"".$_POST['nom_dpo']."\");\n";
    $texte .= "define(\"RGPD_PRENOM_DPO\",\"".$_POST['prenom_dpo']."\");\n";
    $texte .= "define(\"RGPD_SOCIETE_DPO\",\"".$_POST['societe_dpo']."\");\n";
    $texte .= "define(\"RGPD_ADRESSE_DPO\",\"".$_POST['adresse_dpo']."\");\n";
    $texte .= "define(\"RGPD_CCP_DPO\",\"".$_POST['ccp_dpo']."\");\n";
    $texte .= "define(\"RGPD_VILLE_DPO\",\"".$_POST['ville_dpo']."\");\n";
    $texte .= "define(\"RGPD_TEL_DPO\",\"".$_POST['tel_dpo']."\");\n";
    $texte .= "define(\"RGPD_EMAIL_DPO\",\"".$_POST['email_dpo']."\");\n";
    $texte .= "define(\"RGPD_DATE_INSTALLE\",\"".$date_installe."\");\n";
    $texte .= "define(\"RGPD_UPDATE\",\"".$date_update_info."\");\n";
    $texte .= "define(\"RGPD_TRANS\",\"".$_POST['transf_hors_europe']."\");\n";
    $texte .= "define(\"RGPD_SAVE\",\"".$_POST['sauvegarde_manuel']."\");\n";
    $texte .= "define(\"RGPD_HTTPS\",\"".$_POST['https_utiliser']."\");\n";
    $texte .= "define(\"RGPD_PROXY\",\"".$_POST['proxy_utiliser']."\");\n";
    $texte .= "?>\n";

    $fp = fopen("../common/config-rgpd.php", "w");
    fwrite($fp, $texte);
    fclose($fp);
    $saved = true;
}

if (file_exists("../common/config-rgpd.php")) {
    include_once("../common/config-rgpd.php");
    $nom_etab      = RGPD_NOM_ETAB;
    $nom_acad      = RGPD_NOM_ACAD;
    $nom_resp      = RGPD_NOM_RESP;
    $prenom_resp   = RGPD_PRENOM_RESP;
    $adresse_resp  = RGPD_ADRESSE_RESP;
    $ccp_resp      = RGPD_CCP_RESP;
    $ville_resp    = RGPD_VILLE_RESP;
    $tel_resp      = RGPD_TEL_RESP;
    $email_resp    = RGPD_EMAIL_RESP;
    $nom_dpo       = RGPD_NOM_DPO;
    $prenom_dpo    = RGPD_PRENOM_DPO;
    $societe_dpo   = RGPD_SOCIETE_DPO;
    $adresse_dpo   = RGPD_ADRESSE_DPO;
    $ccp_dpo       = RGPD_CCP_DPO;
    $ville_dpo     = RGPD_VILLE_DPO;
    $tel_dpo       = RGPD_TEL_DPO;
    $email_dpo     = RGPD_EMAIL_DPO;
    $date_installe = RGPD_DATE_INSTALLE;
    $date_update   = RGPD_UPDATE;
    $transfert     = RGPD_TRANS;
    $sauvegarde    = RGPD_SAVE;
    $https         = RGPD_HTTPS;
    $proxy         = RGPD_PROXY;
}

?>

<div style="display:flex;flex-direction:column;gap:8px;">

<!-- Info RGPD -->
<div class="alert alert-info" style="display:flex;align-items:flex-start;gap:10px;">
  <i class="bi bi-shield-check" style="font-size:20px;flex-shrink:0;margin-top:2px;"></i>
  <span>
    Le règlement général sur la protection des données (<strong>RGPD</strong>) renforce et unifie la protection des données pour tous les individus au sein de l'Union européenne.
    Si vous traitez des données personnelles de citoyens européens via Triade, vous devez remplir les obligations du RGPD.
    Exercez les droits de vos utilisateurs grâce à nos procédures adaptées RGPD.
  </span>
</div>

<form method="post">

<!-- Établissement -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-building" style="margin-right:6px;"></i>Établissement</span>
  </div>
  <div class="card-body">
    <div class="form-row">
      <label class="form-lbl">Nom de l'établissement :</label>
      <input type="text" name="nom_etablissement" size="30" class="bouton2" value="<?php print htmlspecialchars($nom_etab ?? '') ?>">
    </div>
    <div class="form-row" style="margin-top:6px;">
      <label class="form-lbl">Nom de l'académie :</label>
      <input type="text" name="nom_academie" size="30" class="bouton2" value="<?php print htmlspecialchars($nom_acad ?? '') ?>">
    </div>
  </div>
</div>

<!-- Responsable -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-person-badge" style="margin-right:6px;"></i>Responsable de l'organisme</span>
  </div>
  <div class="card-body">
    <?php
    $fieldsResp = [
        'Nom'          => ['nom_resp',      $nom_resp      ?? ''],
        'Prénom'       => ['prenom_resp',   $prenom_resp   ?? ''],
        'Adresse'      => ['adresse_resp',  $adresse_resp  ?? ''],
        'Code postal'  => ['ccp_resp',      $ccp_resp      ?? ''],
        'Ville'        => ['ville_resp',    $ville_resp    ?? ''],
        'Téléphone'    => ['tel_resp',      $tel_resp      ?? ''],
        'Email'        => ['email_resp',    $email_resp    ?? ''],
    ];
    $first = true;
    foreach ($fieldsResp as $label => [$name, $val]):
    ?>
    <div class="form-row" <?php print $first ? '' : 'style="margin-top:6px;"' ?>>
      <label class="form-lbl"><?php print $label ?> :</label>
      <input type="text" name="<?php print $name ?>" size="30" class="bouton2" value="<?php print htmlspecialchars($val) ?>">
    </div>
    <?php $first = false; endforeach; ?>
  </div>
</div>

<!-- DPO -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-person-lock" style="margin-right:6px;"></i>Délégué à la protection des données (DPO)</span>
  </div>
  <div class="card-body">
    <?php
    $fieldsDpo = [
        'Nom'                    => ['nom_dpo',      $nom_dpo      ?? ''],
        'Prénom'                 => ['prenom_dpo',   $prenom_dpo   ?? ''],
        'Société (DPO externe)'  => ['societe_dpo',  $societe_dpo  ?? ''],
        'Adresse'                => ['adresse_dpo',  $adresse_dpo  ?? ''],
        'Code postal'            => ['ccp_dpo',      $ccp_dpo      ?? ''],
        'Ville'                  => ['ville_dpo',    $ville_dpo    ?? ''],
        'Téléphone'              => ['tel_dpo',      $tel_dpo      ?? ''],
        'Email'                  => ['email_dpo',    $email_dpo    ?? ''],
    ];
    $first = true;
    foreach ($fieldsDpo as $label => [$name, $val]):
    ?>
    <div class="form-row" <?php print $first ? '' : 'style="margin-top:6px;"' ?>>
      <label class="form-lbl"><?php print $label ?> :</label>
      <input type="text" name="<?php print $name ?>" size="30" class="bouton2" value="<?php print htmlspecialchars($val) ?>">
    </div>
    <?php $first = false; endforeach; ?>
  </div>
</div>

<!-- Paramètres techniques -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-gear" style="margin-right:6px;"></i>Paramètres techniques</span>
  </div>
  <div class="card-body">
    <div class="form-row">
      <label class="form-lbl">Date d'installation de Triade :</label>
      <input type="text" name="date_installe" size="14" class="bouton2"
             value="<?php print htmlspecialchars($date_installe ?? '') ?>" readonly style="background:#f5f7ff;color:#555;">
    </div>
    <div class="form-row" style="margin-top:6px;">
      <label class="form-lbl">Dernière mise à jour :</label>
      <input type="text" name="date_update_info" size="14" class="bouton2"
             value="<?php print htmlspecialchars($date_update ?? '') ?>" readonly style="background:#f5f7ff;color:#555;">
    </div>
    <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
      <?php
      $checks = [
          'Transferts de données hors UE'  => ['transf_hors_europe', $transfert  ?? ''],
          'Backups réalisés manuellement'  => ['sauvegarde_manuel',  $sauvegarde ?? ''],
          'Utilisation de l\'HTTPS'        => ['https_utiliser',     $https      ?? ''],
          'Utilisation d\'un proxy'        => ['proxy_utiliser',     $proxy      ?? ''],
      ];
      foreach ($checks as $label => [$name, $val]):
      ?>
      <label style="display:flex;align-items:center;gap:8px;font-size:12px;cursor:pointer;">
        <input type="checkbox" name="<?php print $name ?>" value="oui" <?php print ($val == "oui") ? "checked" : "" ?>>
        <span><?php print $label ?></span>
      </label>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div style="padding:4px 0;">
  <script language="JavaScript">buttonMagicSubmit('Enregistrer','creatergpd');</script>
</div>

</form>

<?php if (file_exists("../common/config-rgpd.php")):
    $fic = "../data/parametrage/registre_RGPD_triade.rtf";
    @unlink($fic);
    $TempFilename = "./lib/registre_rgpd_triade.rtf";
    if (file_exists($TempFilename)) {
        $fh   = fopen($TempFilename, "r");
        $data = fread($fh, 90000000);
        fclose($fh);
        $data = preg_replace('#-NomEtablissement-#', $nom_etab ?? '',    $data);
        $data = preg_replace('#-Nom-#',              $nom_resp ?? '',    $data);
        $data = preg_replace('#-nomacademie-#',       $nom_acad ?? '',   $data);
        $data = preg_replace('#-Prenom-#',            $prenom_resp ?? '', $data);
        $data = preg_replace('#-Adresse-#',           $adresse_resp ?? '',$data);
        $data = preg_replace('#-CodePostal-#',        $ccp_resp ?? '',   $data);
        $data = preg_replace('#-Ville-#',             $ville_resp ?? '', $data);
        $data = preg_replace('#-telephone-#',         $tel_resp ?? '',   $data);
        $data = preg_replace('#-email-#',             $email_resp ?? '', $data);
        $data = preg_replace('#-NomDPO-#',            $nom_dpo ?? '',    $data);
        $data = preg_replace('#-PrenomDPO-#',         $prenom_dpo ?? '', $data);
        $data = preg_replace('#-SocieteDPO-#',        $societe_dpo ?? '',$data);
        $data = preg_replace('#-AdresseDPO-#',        $adresse_dpo ?? '',$data);
        $data = preg_replace('#-CodePostalDPO-#',     $ccp_dpo ?? '',   $data);
        $data = preg_replace('#-VilleDPO-#',          $ville_dpo ?? '', $data);
        $data = preg_replace('#-TelephoneDPO-#',      $tel_dpo ?? '',   $data);
        $data = preg_replace('#-emailDPO-#',          $email_dpo ?? '', $data);
        $data = preg_replace('#-InstalleTriade-#',    $date_installe ?? '',$data);
        $data = preg_replace('#-UpdateFiche-#',       $date_update ?? '',$data);
        $data = preg_replace('#-backup-#',            $sauvegarde ?? '', $data);
        $data = preg_replace('#-https-#',             $https ?? '',      $data);
        $data = preg_replace('#-proxy-#',             $proxy ?? '',      $data);
        $data = preg_replace('#-transhorsEU-#',       $transfert ?? '',  $data);
        $fh   = fopen($fic, "a");
        fwrite($fh, $data);
        fclose($fh);
    }
?>
<div class="card" style="margin-top:8px;">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-file-earmark-text" style="margin-right:6px;"></i>Registre RGPD</span>
  </div>
  <div class="card-body" style="text-align:center;">
    <button type="button" class="btn btn-primary"
      onclick="open('telecharger.php?fichier=/data2/parametrage/registre_RGPD_triade.rtf','_blank','')">
      <i class="bi bi-download" style="margin-right:6px;"></i>Télécharger le registre RGPD (.rtf)
    </button>
  </div>
</div>
<?php endif; ?>

</div><!-- /flex column -->

<?php if ($saved): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    alertify.success('Informations RGPD enregistrées.');
});
</script>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
