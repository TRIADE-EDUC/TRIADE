<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 *   Module               : Exportation SIECLE-BEE (XML / ZIP)
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Exportation SIECLE-BEE</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/lib_siecle_bee_export.php");
validerequete("2");
$cnx = cnx();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$etab_info = siecle_bee_get_etablissement_info();
$classes   = siecle_bee_get_classes_list();
$uai_val   = !empty($etab_info['uai']) ? $etab_info['uai'] : '0541470E';
$annee_val = !empty($etab_info['annee_millesime']) ? $etab_info['annee_millesime'] : date('Y');
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print defined('LANG_SIECLE_EXPORT_TITRE') ? LANG_SIECLE_EXPORT_TITRE : "Exportation SIECLE-BEE (XML / ZIP)"; ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" action="./export_siecle_bee_traitement.php" name="formulaire">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

<div class="card" style="margin:8px 4px;">
  <div class="card-header" style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:#080A66;">
    <i class="bi bi-file-earmark-zip" style="font-size:16px;"></i>
    <?php print defined('LANG_SIECLE_EXPORT_TITRE') ? LANG_SIECLE_EXPORT_TITRE : "Exportation SIECLE-BEE"; ?>
  </div>
  <div class="card-body" style="padding:12px 14px;display:flex;flex-direction:column;gap:12px;">
    
    <div style="font-size:11px;color:#555;line-height:1.4;">
      <i class="bi bi-info-circle" style="color:#1565c0;"></i>
      <?php print defined('LANG_SIECLE_EXPORT_DESC') ? LANG_SIECLE_EXPORT_DESC : "Génération de l'archive ZIP conforme aux normes nationales pour l'import dans la Base Élèves Établissement (SIECLE)."; ?>
    </div>

    <!-- Paramètres Établissement -->
    <div style="display:flex;flex-direction:column;gap:10px;background:#f8f9fe;border:1px solid #e4e9f8;border-radius:6px;padding:10px 12px;">
      
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <label style="font-weight:600;font-size:12px;color:#080A66;display:flex;align-items:center;gap:6px;">
          <i class="bi bi-building"></i> <?php print defined('LANG_SIECLE_UAI') ? LANG_SIECLE_UAI : "Code UAI (RNE) de l'établissement"; ?> :
        </label>
        <input type="text" name="uai" value="<?php echo htmlspecialchars($uai_val); ?>" maxlength="8" required style="width:140px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;font-weight:bold;text-transform:uppercase;text-align:center;">
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;">
        <label style="font-weight:600;font-size:12px;color:#080A66;display:flex;align-items:center;gap:6px;">
          <i class="bi bi-calendar3"></i> <?php print defined('LANG_SIECLE_ANNEE') ? LANG_SIECLE_ANNEE : "Année scolaire (Millésime)"; ?> :
        </label>
        <input type="number" name="annee" value="<?php echo htmlspecialchars($annee_val); ?>" min="2000" max="2099" required style="width:140px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;text-align:center;">
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;">
        <label style="font-weight:600;font-size:12px;color:#080A66;display:flex;align-items:center;gap:6px;">
          <i class="bi bi-gear"></i> <?php print defined('LANG_SIECLE_PROFIL') ? LANG_SIECLE_PROFIL : "Profil du schéma SIECLE"; ?> :
        </label>
        <select name="profil" style="width:240px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;">
          <option value="standard" selected><?php print defined('LANG_SIECLE_PROFIL_STANDARD') ? LANG_SIECLE_PROFIL_STANDARD : "Standard (Sous contrat - XSD 4.0)"; ?></option>
          <option value="ephc"><?php print defined('LANG_SIECLE_PROFIL_EPHC') ? LANG_SIECLE_PROFIL_EPHC : "Privé Hors Contrat (EPHC - XSD 1.1)"; ?></option>
        </select>
      </div>

      <div>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;font-weight:600;color:#2e7d32;">
          <input type="checkbox" name="valider_xsd" value="1" checked style="accent-color:#2e7d32;">
          <span><i class="bi bi-shield-check"></i> <?php print defined('LANG_SIECLE_VALIDATION_XSD') ? LANG_SIECLE_VALIDATION_XSD : "Valider la conformité XSD avant export"; ?></span>
        </label>
      </div>

    </div>

    <!-- Filtre par classe -->
    <div style="display:flex;flex-direction:column;gap:6px;">
      <span style="font-size:12px;font-weight:700;color:#080A66;display:flex;align-items:center;gap:5px;">
        <i class="bi bi-collection"></i> <?php print defined('LANG_SIECLE_PERIMETRE') ? LANG_SIECLE_PERIMETRE : "Périmètre de l'exportation"; ?> :
      </span>
      <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:4px;max-height:160px;overflow-y:auto;border:1px solid #e4e9f8;padding:6px;border-radius:6px;background:#fafbfe;">
        <?php foreach ($classes as $id_cl => $cl): ?>
          <label style="display:flex;align-items:center;gap:6px;font-size:11px;padding:3px;background:#fff;border-radius:4px;border:1px solid #edf0fa;cursor:pointer;">
            <input type="checkbox" name="classes[]" value="<?php echo $id_cl; ?>" checked style="accent-color:#080A66;">
            <span><?php echo htmlspecialchars($cl['libelle']); ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 4px 10px 4px;">
  <div>
    <script language=JavaScript>buttonMagicRetour("export.php","_self")</script>
  </div>
  <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;">
    <i class="bi bi-download"></i> <?php print defined('LANG_SIECLE_BTN_EXPORTER') ? LANG_SIECLE_BTN_EXPORTER : "Générer et télécharger l'archive ZIP"; ?>
  </button>
</div>

</form>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>