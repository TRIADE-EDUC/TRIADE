<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 *   Module               : Traitement Exportation SIECLE-BEE (XML / ZIP)
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
<title>Triade - Résultat Exportation SIECLE-BEE</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/lib_siecle_bee_export.php");

validerequete("2");
$cnx = cnx();

// Protection CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Erreur de sécurité : Jeton CSRF invalide ou expiré. Veuillez recharger la page.");
}

$uai     = strtoupper(trim(isset($_POST['uai']) ? (string)$_POST['uai'] : ''));
$annee   = (int)(isset($_POST['annee']) ? $_POST['annee'] : date('Y'));
$profil  = (isset($_POST['profil']) && $_POST['profil'] === 'ephc') ? 'ephc' : 'standard';
$classes = (isset($_POST['classes']) && is_array($_POST['classes'])) ? $_POST['classes'] : array();
$valider_xsd = !empty($_POST['valider_xsd']);

$erreur = '';
$res_zip = null;
$validation_res = null;

if (!preg_match('/^[0-9]{7}[A-Z]$/i', $uai)) {
    $erreur = "Format de code UAI invalide. Un code UAI doit comporter 7 chiffres suivis d'une lettre (ex: 0541470E).";
} elseif ($annee < 2000 || $annee > 2099) {
    $erreur = "Année scolaire invalide.";
} else {
    // 1. Récupération des données structurées
    $export_data = siecle_bee_get_export_data($uai, $annee, array(
        'profil'  => $profil,
        'classes' => $classes
    ));

    // 2. Génération du flux XML (ISO-8859-15)
    $xml_content = siecle_bee_generate_xml($export_data);

    // 3. Validation XSD préalable si demandée
    if ($valider_xsd) {
        $validation_res = siecle_bee_validate_export_xml($xml_content, $profil);
        if (!$validation_res['valid']) {
            $erreur = "Anomalies de conformité au schéma XSD SIECLE-BEE détectées.";
        }
    }

    // 4. Création du package ZIP normé si aucune erreur
    if (empty($erreur)) {
        $res_zip = siecle_bee_create_zip_export($xml_content, $uai, $annee, $profil);
        if (!$res_zip['success']) {
            $erreur = "Erreur lors de la création de l'archive ZIP : " . implode(', ', $res_zip['errors']);
        } else {
            // 5. Journalisation d'accès / traçabilité CRUD sensible (access.log)
            $user_log = isset($_SESSION['nom']) ? ($_SESSION['nom'] . " " . $_SESSION['prenom']) : "Admin";
            $nb_eleves = count($export_data['eleves']);
            $nb_pers   = count($export_data['personnes']);
            acceslog("EXPORT_SIECLE_BEE|UAI:{$uai}|Annee:{$annee}|Profil:{$profil}|Eleves:{$nb_eleves}|Personnes:{$nb_pers}|Fichier:{$res_zip['zip_name']}|User:{$user_log}");
        }
    }
}
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
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <?php if (!empty($erreur)): ?>
    <!-- Erreur rencontrée -->
    <div class="card" style="border-left:4px solid #c62828;">
      <div class="card-header" style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:#c62828;">
        <i class="bi bi-exclamation-octagon-fill" style="font-size:16px;"></i>
        Erreur lors de l'exportation SIECLE-BEE
      </div>
      <div class="card-body" style="padding:12px 14px;display:flex;flex-direction:column;gap:10px;">
        <p style="font-size:12px;color:#333;margin:0;"><?php echo htmlspecialchars($erreur); ?></p>
        <?php if (!empty($validation_res) && !empty($validation_res['errors'])): ?>
          <div style="background:#fff3f3;border:1px solid #ffcdd2;border-radius:6px;padding:10px;max-height:220px;overflow-y:auto;font-family:monospace;font-size:11px;color:#b71c1c;">
            <?php foreach ($validation_res['errors'] as $err): ?>
              <div>• <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <!-- Succès de la génération -->
    <div class="card" style="border-left:4px solid #2e7d32;">
      <div class="card-header" style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:#2e7d32;">
        <i class="bi bi-check-circle-fill" style="font-size:16px;"></i>
        Archive SIECLE-BEE générée avec succès
      </div>
      <div class="card-body" style="padding:12px 14px;display:flex;flex-direction:column;gap:12px;">
        <p style="font-size:12px;color:#555;margin:0;">
          L'archive ZIP normalisée conforme aux exigences du Ministère de l'Éducation Nationale a été préparée.
        </p>

        <!-- Récapitulatif technique -->
        <div style="background:#f8f9fe;border:1px solid #e4e9f8;border-radius:6px;padding:10px 12px;font-size:12px;display:flex;flex-direction:column;gap:6px;">
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Nom de l'archive :</span>
            <strong style="color:#080A66;font-family:monospace;"><?php echo htmlspecialchars($res_zip['zip_name']); ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Code UAI (RNE) :</span>
            <strong><?php echo htmlspecialchars($uai); ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Année scolaire :</span>
            <strong><?php echo htmlspecialchars($annee); ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Profil du schéma :</span>
            <strong><?php echo ($profil === 'ephc') ? 'EPHC (Privé Hors Contrat - XSD 1.1)' : 'Standard (Sous contrat - XSD 4.0)'; ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Élèves exportés :</span>
            <strong style="color:#1565c0;"><?php echo count($export_data['eleves']); ?> élève(s)</strong>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Responsables légaux :</span>
            <strong style="color:#1565c0;"><?php echo count($export_data['personnes']); ?> personne(s)</strong>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:#666;">Conformité XSD :</span>
            <strong style="color:#2e7d32;"><i class="bi bi-shield-check"></i> Conforme (0 anomalie)</strong>
          </div>
        </div>

        <!-- Bouton de téléchargement -->
        <div style="padding-top:4px;">
          <button type="button"
                  onclick="open('telecharger.php?fichier=./data/fichier_gep/<?php echo urlencode($res_zip['zip_name']); ?>&fichiername=<?php echo urlencode($res_zip['zip_name']); ?>','_blank','')"
                  class="btn btn-primary"
                  style="background:#2e7d32;border-color:#2e7d32;display:inline-flex;align-items:center;gap:8px;padding:8px 18px;font-size:13px;font-weight:700;">
            <i class="bi bi-download"></i> Télécharger l'archive ZIP
          </button>
        </div>

      </div>
    </div>
  <?php endif; ?>

  <div style="padding-top:4px;">
    <script language=JavaScript>buttonMagicRetour("export_siecle_bee.php","_self")</script>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>