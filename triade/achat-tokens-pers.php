<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
    <?php include_once("./common/config5.inc.php") ?>
    <meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
    <meta http-equiv="CacheControl" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="Copyright" content="Triade©, 2001">
    <LINK REL="SHORTCUT ICON" HREF="./favicon.ico">
    <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
    <link rel="stylesheet" href="./librairie_css/css-v4.css">
    <link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
    <title>TRIADE-COACH — Acheter des tokens IA</title>
    <style>
    .at-wrap{display:flex;flex-direction:column;gap:14px;padding:10px 6px}
    .at-moteur-card{background:#fff;border:1px solid #dde0f0;border-radius:10px;padding:14px 18px;border-top:4px solid #080A66}
    .at-moteur-title{font-size:13px;font-weight:800;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;display:flex;align-items:center;gap:8px;margin-bottom:10px}
    .at-moteur-opts{display:flex;gap:20px;flex-wrap:wrap;align-items:center}
    .at-moteur-opt{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:600;cursor:pointer;color:#333}
    .at-moteur-opt input{width:16px;height:16px;cursor:pointer;accent-color:#080A66}
    .at-moteur-btn{margin-left:10px;background:#080A66;color:#fff;border:none;border-radius:7px;padding:7px 16px;font-size:12px;font-weight:700;cursor:pointer}
    .at-moteur-btn:hover{background:#0b0f8a}
    .at-moteur-ok{display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#2e7d32;font-weight:600;margin-left:10px}
    .at-plans{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:12px}
    .at-plan{background:#fff;border:1px solid #dde0f0;border-radius:10px;padding:14px 14px;display:flex;flex-direction:column;gap:8px;border-top:4px solid #080A66;opacity:.5;pointer-events:none;transition:opacity .2s,border-color .15s}
    .at-plan.active{opacity:1;pointer-events:auto;cursor:pointer}
    .at-plan.active:hover{border-color:#080A66;background:#f0f2fa}
    .at-plan.active.selected{border-color:#080A66;background:#f0f2fa;border-width:2px}
    .at-plan-label{font-size:10px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.5px;font-family:Electrolize,Trebuchet MS,Arial}
    .at-plan-tokens{font-size:13px;font-weight:800;color:#333}
    .at-plan-price{font-size:15px;font-weight:800;color:#080A66}
    .at-plan-frais{font-size:10px;color:#aaa}
    .at-note{font-size:11px;color:#aaa;text-align:center}
    .at-pay-box{background:#fff;border:1px solid #dde0f0;border-radius:10px;padding:14px 18px;border-top:4px solid #635bff;display:none}
    .at-pay-box.visible{display:block}
    .at-pay-label{font-size:11px;color:#555;font-weight:700;margin-bottom:3px;display:block}
    .at-pay-input{width:100%;padding:6px 8px;border:1px solid #c5caee;border-radius:5px;font-size:12px;box-sizing:border-box;margin-bottom:10px}
    .at-btn-stripe{background:#635bff;color:#fff;border:none;padding:9px 22px;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:7px}
    .at-btn-stripe:hover{background:#4f46e5}
    </style>
    <script language="JavaScript" src="./librairie_js/function.js"></script>
    <script type="text/javascript" src="./librairie_js/prototype.js"></script>
    <script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
</HEAD>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<?php
include_once("./librairie_php/lib_rss.php");
include_once("./librairie_php/lib_licence.php");
include_once("./common/productId.php");
include_once("./common/config-ia-achat.php");
include_once("./librairie_php/db_triade.php");
include_once("./common/config-module.php");

if (empty($_SESSION['id_pers']) && empty($_SESSION['membre'])) {
    print "<script>location.href='./index.php';</script>";
    exit;
}

$cnx = cnx();

// Email par défaut du compte
$emailCompte = '';
if (!empty($_SESSION['id_pers'])) {
    global $prefixe;
    $idp = intval($_SESSION['id_pers']);
    $res = execSql("SELECT email FROM {$prefixe}personnel WHERE pers_id=$idp");
    if ($res && !DB::isError($res)) {
        $mat = chargeMat($res);
        if (!empty($mat[0][0])) $emailCompte = $mat[0][0];
    }
}

$idkeypers = htmlspecialchars($_GET['idkeypers'] ?? '');
$productid  = htmlspecialchars($_GET['productid'] ?? (defined('PRODUCTID') ? PRODUCTID : ''));
$url        = htmlspecialchars($_GET['url']        ?? $_SERVER['SERVER_NAME']);

// Sauvegarde moteur en session + appel API centrale
// Messages retour Stripe
$stripeMsg = '';
$stripeMsgType = '';
if (isset($_GET['stripe'])) {
    switch ($_GET['stripe']) {
        case 'success': $stripeMsg = 'Paiement accepté. Vos tokens sont en cours de crédit.'; $stripeMsgType = 'success'; break;
        case 'cancel':  $stripeMsg = 'Paiement annulé. Aucun prélèvement effectué.'; $stripeMsgType = 'error'; break;
        case 'error':   $stripeMsg = 'Erreur : ' . htmlspecialchars($_GET['msg'] ?? 'impossible de créer la session.'); $stripeMsgType = 'error'; break;
    }
}

$moteurSaved = false;
if (!empty($_POST['moteur']) && in_array($_POST['moteur'], ['gpt-5-mini','mistral-large-2512'])) {
    $_SESSION['ia_moteur'] = $_POST['moteur'];
    // Sauvegarde sur serveur central via API
    $apiUrl = "https://support.triade-educ.org/support/ia-api.php"
            . "?action=save-moteur&iakey=" . urlencode($idkeypers)
            . "&moteur=" . urlencode($_POST['moteur']);
    @file_get_contents($apiUrl);
    $moteurSaved = true;
}
$moteurCourant = $_SESSION['ia_moteur'] ?? '';
$moteurActif   = !empty($moteurCourant);

// Tarifs et buy-button-ids depuis config-tarif.php
// Récupération des tarifs depuis le serveur central (mis en cache en session)
$frais    = 8;
$stripePK = '';
$plans    = [];
$apiError = false;

if (!isset($_SESSION['ia_plans_cache']) || !isset($_SESSION['ia_plans_ts']) || (time() - $_SESSION['ia_plans_ts']) > 300) {
    $apiResp = @file_get_contents(IA_ACHAT_API . '?action=get-plans');
    if ($apiResp) {
        $apiData = json_decode($apiResp, true);
        if (!empty($apiData['ok'])) {
            $_SESSION['ia_plans_cache'] = $apiData;
            $_SESSION['ia_plans_ts']    = time();
        }
    }
}

if (!empty($_SESSION['ia_plans_cache'])) {
    $cache    = $_SESSION['ia_plans_cache'];
    $frais    = intval($cache['frais']);
    $stripePK = $cache['stripe_pk'];
    foreach ($cache['plans'] as $p) {
        $plans[$p['offre']] = $p;
    }
} else {
    $apiError = true;
}
?>

<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Acheter des tokens IA</font></b></td></tr>
<tr id='cadreCentral0'><td>
<!-- // fin  -->

<div class="at-wrap">

  <!-- Sélection moteur IA -->
  <div class="at-moteur-card">
    <div class="at-moteur-title"><i class="bi bi-cpu"></i> Moteur IA</div>
    <form method="post" action="achat-tokens-pers.php?idkeypers=<?php print urlencode($idkeypers) ?>&productid=<?php print urlencode($productid) ?>&url=<?php print urlencode($url) ?>">
      <div class="at-moteur-opts">
        <label class="at-moteur-opt">
          <input type="radio" name="moteur" value="gpt-5-mini" <?php if($moteurCourant==='gpt-5-mini') print 'checked' ?>>
          Open AI
        </label>
        <label class="at-moteur-opt">
          <input type="radio" name="moteur" value="mistral-large-2512" <?php if($moteurCourant==='mistral-large-2512') print 'checked' ?>>
          Mistral AI
        </label>
        <button type="submit" class="at-moteur-btn">Valider</button>
        <?php if ($moteurActif): ?>
        <span class="at-moteur-ok"><i class="bi bi-check-circle-fill"></i>
          <?php print ($moteurCourant==='gpt-5-mini') ? 'Open AI sélectionné' : 'Mistral AI sélectionné' ?>
        </span>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <?php if (!$moteurActif): ?>
  <div style="font-size:12px;color:#888;text-align:center;padding:8px 0;font-style:italic">
    <i class="bi bi-arrow-up"></i> Veuillez d'abord sélectionner votre moteur IA pour activer les offres.
  </div>
  <?php endif; ?>

  <?php if ($apiError): ?>
  <div style="background:#fce4ec;border:1px solid #f48fb1;border-radius:8px;padding:12px 16px;font-size:12px;color:#c62828">
    <i class="bi bi-exclamation-triangle-fill"></i> Impossible de charger les offres depuis le serveur central. Veuillez réessayer dans quelques instants.
  </div>
  <?php endif; ?>

  <!-- Plans tarifaires -->
  <div class="at-plans">
    <?php foreach ($plans as $offre => $p):
        $total  = $p['prix'] + $frais;
        $tokFmt = number_format($p['tokens'], 0, ',', ' ');
        $cls    = $moteurActif ? 'at-plan active' : 'at-plan';
    ?>
    <div class="<?php print $cls ?>" onclick="selectionnerPlan('<?php print $offre ?>',<?php print $total ?>,'<?php print addslashes($p['label']) ?> &mdash; <?php print number_format($total,2,',','') ?>&nbsp;&euro;', event)">
      <div class="at-plan-label"><?php print $p['label'] ?></div>
      <div class="at-plan-tokens"><?php print $tokFmt ?> tokens</div>
      <div class="at-plan-price"><?php print number_format($total, 2, ',', ' ') ?>&nbsp;€</div>
      <div class="at-plan-frais"><?php print number_format($p['prix'], 2, ',', ' ') ?>&nbsp;€ + <?php print $frais ?>&nbsp;€ frais</div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Zone paiement (apparaît après sélection) -->
  <div class="at-pay-box" id="at-pay-box">
    <label class="at-pay-label">Email (reçu Stripe)</label>
    <input type="email" id="at-email" class="at-pay-input" placeholder="votre@email.fr" value="<?php print htmlspecialchars($emailCompte) ?>">
    <button class="at-btn-stripe" onclick="lancerStripe()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13.97 9.056c0-.78.64-1.08 1.7-1.08 1.52 0 3.44.46 4.96 1.28V5.16C19.1 4.56 17.6 4 15.67 4 11.8 4 9.2 6.04 9.2 9.3c0 5.12 7.04 4.3 7.04 6.5 0 .92-.8 1.22-1.92 1.22-1.66 0-3.78-.68-5.46-1.6v4.06c1.86.8 3.74 1.14 5.46 1.14 4 0 6.74-1.98 6.74-5.3 0-5.52-7.09-4.54-7.09-6.22z"/></svg>
      Payer avec Stripe — <span id="at-pay-label-prix"></span>
    </button>
    <form method="POST" action="achat-tokens-pers-checkout.php" id="at-form-stripe">
      <input type="hidden" name="plan"       id="at-stripe-plan"   value="">
      <input type="hidden" name="contact"    id="at-stripe-contact" value="">
      <input type="hidden" name="idkeypers"  value="<?php print htmlspecialchars($idkeypers) ?>">
      <input type="hidden" name="productid"  value="<?php print htmlspecialchars($productid) ?>">
      <input type="hidden" name="url"        value="<?php print htmlspecialchars($url) ?>">
    </form>
  </div>

  <div class="at-note"><i class="bi bi-lock-fill"></i> Paiements sécurisés par Stripe. Tokens valables sans limite de durée.</div>

</div>

<script>
function selectionnerPlan(offre, total, label, e) {
    document.querySelectorAll('.at-plan').forEach(function(c){ c.classList.remove('selected'); });
    (e.currentTarget || e.target).classList.add('selected');
    document.getElementById('at-stripe-plan').value = offre;
    document.getElementById('at-pay-label-prix').innerHTML = label;
    document.getElementById('at-pay-box').classList.add('visible');
}
function lancerStripe() {
    var email = document.getElementById('at-email').value.trim();
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(email)) { alertify.error('Entrez un email valide.'); return; }
    document.getElementById('at-stripe-contact').value = email;
    document.getElementById('at-form-stripe').submit();
}
<?php if ($stripeMsg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php print $stripeMsgType === 'success' ? 'success' : 'error'; ?>('<?php print addslashes($stripeMsg); ?>');
    if (window.history && window.history.replaceState) {
        var url = window.location.pathname + '?idkeypers=<?php print urlencode($idkeypers) ?>&productid=<?php print urlencode($productid) ?>&url=<?php print urlencode($url) ?>';
        window.history.replaceState({}, '', url);
    }
});
<?php endif; ?>
</script>

<!-- // fin  -->
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY></HTML>
