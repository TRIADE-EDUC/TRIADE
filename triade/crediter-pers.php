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

// Auto-création de config-ia-achat.php si absent
$_cfgIaAchat = __DIR__ . '/common/config-ia-achat.php';
if (!file_exists($_cfgIaAchat)) {
    file_put_contents($_cfgIaAchat, "<?php\ndefine(\"IA_ACHAT_API\",\"https://support.triade-educ.org/support/ia-api.php\");\n?>\n");
}
unset($_cfgIaAchat);
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
    <title>TRIADE-COACH — Créditer mon compte IA</title>
    <style>
    .cp-wrap{display:flex;flex-direction:column;gap:14px;padding:10px 6px}
    .cp-card{background:#fff;border:1px solid #dde0f0;border-radius:10px;padding:18px 20px;border-top:4px solid #080A66}
    .cp-card.purple{border-top-color:#7b1fa2}
    .cp-card-title{font-size:13px;font-weight:800;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;display:flex;align-items:center;gap:8px;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #eef0f8}
    .cp-card.purple .cp-card-title{color:#7b1fa2}
    .cp-card-desc{font-size:12px;color:#555;line-height:1.6;margin-bottom:12px}
    .cp-plans{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
    .cp-plan{flex:1;min-width:110px;background:#f8f0ff;border:1px solid #ce93d8;border-radius:8px;padding:10px 12px;text-align:center}
    .cp-plan-name{font-size:10px;font-weight:700;color:#6a1b9a;text-transform:uppercase;letter-spacing:.5px}
    .cp-plan-price{font-size:16px;font-weight:800;color:#7b1fa2;margin:3px 0}
    .cp-plan-tok{font-size:10px;color:#888}
    .cp-btn{display:inline-block;text-align:center;border:none;border-radius:8px;padding:9px 18px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;font-family:Electrolize,Trebuchet MS,Arial}
    .cp-btn-blue{background:#080A66;color:#fff}
    .cp-btn-blue:hover{background:#0b0f8a;color:#fff}
    .cp-btn-purple{background:#7b1fa2;color:#fff}
    .cp-btn-purple:hover{background:#6a1b9a;color:#fff}
    .cp-note{font-size:11px;color:#aaa;text-align:center;margin-top:4px}
    .cp-sub-actif{background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px}
    .cp-sub-badge{background:#2e7d32;color:#fff;border-radius:20px;padding:3px 12px;font-weight:700;font-size:11px;white-space:nowrap}
    .cp-sub-badge.resil{background:#e65100}
    .cp-solde{display:flex;align-items:center;gap:14px;background:#f0f2fa;border:1px solid #c5caee;border-radius:8px;padding:12px 16px;flex-wrap:wrap}
    .cp-solde-label{font-size:11px;color:#666;margin-bottom:3px}
    .cp-solde-badge{padding:4px 14px;border-radius:20px;font-weight:700;font-size:13px}
    .badge-ok{background:#d4edda;color:#155724}
    .badge-low{background:#fff3cd;color:#856404}
    .badge-ko{background:#f8d7da;color:#721c24}
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

$idkeypers = htmlspecialchars($_GET['idkeypers'] ?? '');
$productid  = htmlspecialchars($_GET['productid']  ?? (defined('PRODUCTID') ? PRODUCTID : ''));
$url        = htmlspecialchars($_GET['url']         ?? $_SERVER['SERVER_NAME']);

// Messages retour Stripe
$stripeMsg = '';
$stripeMsgType = '';
if (isset($_GET['stripe'])) {
    switch ($_GET['stripe']) {
        case 'success': $stripeMsg = 'Abonnement activé. Vos tokens seront crédités sous quelques instants.'; $stripeMsgType = 'success'; break;
        case 'cancel':     $stripeMsg = 'Abonnement annulé. Aucun prélèvement effectué.'; $stripeMsgType = 'error'; break;
        case 'cancelled':
            $until = htmlspecialchars($_GET['until'] ?? '');
            $stripeMsg = 'Résiliation programmée.'
                       . ($until ? ' Vos tokens restent disponibles jusqu\'au ' . $until . '.' : '')
                       . ' Votre solde sera remis à zéro à l\'échéance.';
            $stripeMsgType = 'success';
            break;
        case 'error':      $stripeMsg = 'Erreur : ' . htmlspecialchars($_GET['msg'] ?? 'impossible de créer la session.'); $stripeMsgType = 'error'; break;
    }
}

// Email du compte pour pré-remplir le relay
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

$urlRecharge = "./achat-tokens-pers.php"
             . "?idkeypers=" . urlencode($idkeypers)
             . "&productid=" . urlencode($productid)
             . "&url=" . urlencode($url);

// Solde tokens + statut abonnement
$tokensDisp = null;
$subInfo    = null;
if ($idkeypers) {
    $apiKey = 'ia-2026-xJ8kPmR5';
    $balUrl  = "https://triade-educ.org/ia/api.php?action=get_balance_pers&key=" . urlencode($apiKey) . "&idkeypers=" . urlencode($idkeypers);
    $balResp = @file_get_contents($balUrl);
    if ($balResp) {
        $balData = json_decode($balResp, true);
        if (!empty($balData['found'])) $tokensDisp = intval($balData['nbtokencredit']);
    }
    $subUrl  = "https://triade-educ.org/ia/api.php?action=get_sub_pers&key=" . urlencode($apiKey) . "&idkeypers=" . urlencode($idkeypers);
    $subResp = @file_get_contents($subUrl);
    if ($subResp) {
        $subData = json_decode($subResp, true);
        if (!empty($subData['found'])) $subInfo = $subData;
    }
}

// Tarifs abonnement depuis l'API centrale (cache 5min en session)
$subPlans = [];
$stripePK  = '';
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
    $subPlans = $_SESSION['ia_plans_cache']['sub_plans'] ?? [];
    $stripePK = $_SESSION['ia_plans_cache']['stripe_pk']  ?? '';
}
// Fallback si API injoignable — aucun ID Stripe côté école
if (empty($subPlans)) {
    $subPlans = [
        ['plan'=>'P1','label'=>'Essentiel','tokens'=>500000,  'prix'=>3.23, 'actif'=>false],
        ['plan'=>'P2','label'=>'Standard', 'tokens'=>2000000, 'prix'=>8.63, 'actif'=>false],
        ['plan'=>'P3','label'=>'Premium',  'tokens'=>6000000, 'prix'=>16.19,'actif'=>false],
    ];
}
?>

<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Créditer mon compte IA</font></b></td></tr>
<tr id='cadreCentral0'><td>
<!-- // fin  -->

<div class="cp-wrap">

  <!-- Statut abonnement -->
  <?php if ($subInfo && $subInfo['status'] === 'active'): ?>
  <div class="cp-sub-actif">
    <i class="bi bi-patch-check-fill" style="color:#2e7d32;font-size:16px"></i>
    <div style="flex:1">
      <strong>Abonnement <?php print htmlspecialchars($subInfo['plan'] === 'P1' ? 'Essentiel' : ($subInfo['plan'] === 'P2' ? 'Standard' : 'Premium')) ?> actif</strong>
      <?php if ($subInfo['current_period_end']): ?>
      — prochain renouvellement le <strong><?php print date('d/m/Y', $subInfo['current_period_end']) ?></strong>
      <?php endif; ?>
      <?php if ($subInfo['nb_tokens']): ?>
      <div style="margin-top:3px;font-size:11px;color:#555"><?php print number_format($subInfo['nb_tokens'], 0, ',', ' ') ?> tokens/mois</div>
      <?php endif; ?>
    </div>
    <?php if ($subInfo['cancel_at_period_end']): ?>
    <span class="cp-sub-badge resil">Résiliation programmée</span>
    <?php else: ?>
    <span class="cp-sub-badge">Actif</span>
    <button class="cp-btn" style="background:#c62828;color:#fff;font-size:11px;padding:4px 12px"
      onclick="cancelSubPers('<?php print $subInfo['current_period_end'] ? date('d/m/Y', $subInfo['current_period_end']) : '' ?>')">
      <i class="bi bi-x-circle"></i> Résilier
    </button>
    <?php endif; ?>
  </div>
  <form method="POST" action="crediter-pers-cancel.php" id="form-cancel-sub" style="display:none">
    <input type="hidden" name="idkeypers"      value="<?php print htmlspecialchars($idkeypers) ?>">
    <input type="hidden" name="productid"      value="<?php print htmlspecialchars($productid) ?>">
    <input type="hidden" name="url"            value="<?php print htmlspecialchars($url) ?>">
    <input type="hidden" name="period_end_fmt" id="cancel-period-fmt" value="">
  </form>
  <?php endif; ?>

  <!-- Solde tokens -->
  <?php if ($tokensDisp !== null): ?>
  <div class="cp-solde">
    <div style="display:flex;align-items:center;gap:10px">
      <div class="cp-solde-label">Tokens disponibles</div>
      <?php
        $bClass = 'badge-ko';
        if ($tokensDisp > 5000000)  $bClass = 'badge-ok';
        elseif ($tokensDisp > 0)    $bClass = 'badge-low';
      ?>
      <span class="cp-solde-badge <?php print $bClass ?>"><?php print number_format($tokensDisp, 0, ',', ' ') ?></span>
    </div>
    <?php if ($tokensDisp < 100000): ?>
    <div style="font-size:11px;color:#856404;background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:6px 10px">
      <i class="bi bi-exclamation-triangle-fill"></i> Solde faible — rechargez pour continuer
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Abonnement mensuel -->
  <div class="cp-card purple">
    <div class="cp-card-title"><i class="bi bi-stars"></i> Abonnement mensuel</div>
    <div class="cp-card-desc">
      Tokens renouvelés automatiquement chaque mois. Sans engagement, résiliable à tout moment.
    </div>
    <div class="cp-plans">
      <?php foreach ($subPlans as $sp):
        $actif = !empty($sp['actif']);
      ?>
      <div class="cp-plan">
        <div class="cp-plan-name"><?php print htmlspecialchars($sp['label']) ?></div>
        <div class="cp-plan-price"><?php print number_format(floatval($sp['prix']), 2, ',', ' ') ?>&nbsp;€<span style="font-size:10px;font-weight:400">/mois</span></div>
        <div class="cp-plan-tok"><?php print number_format(intval($sp['tokens']), 0, ',', ' ') ?> tokens</div>
        <?php if ($actif): ?>
        <button class="cp-btn cp-btn-purple" style="margin-top:8px;width:100%;font-size:11px"
          onclick="souscrire('<?php print htmlspecialchars($sp['plan']) ?>')">
          Souscrire
        </button>
        <?php else: ?>
        <div style="font-size:10px;color:#ce93d8;margin-top:8px;font-style:italic">Bientôt disponible</div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <form method="POST" action="crediter-pers-sub-checkout.php" id="form-sub">
      <input type="hidden" name="plan"       id="sub-plan"    value="">
      <input type="hidden" name="contact"    id="sub-contact" value="<?php print htmlspecialchars($emailCompte ?? '') ?>">
      <input type="hidden" name="idkeypers"  value="<?php print htmlspecialchars($idkeypers) ?>">
      <input type="hidden" name="productid"  value="<?php print htmlspecialchars($productid) ?>">
      <input type="hidden" name="url"        value="<?php print htmlspecialchars($url) ?>">
    </form>
  </div>

  <!-- Recharge one-shot -->
  <div class="cp-card">
    <div class="cp-card-title"><i class="bi bi-coin"></i> Recharge à la demande</div>
    <div class="cp-card-desc">
      Achetez des tokens selon vos besoins, sans abonnement. Valables sans limite de durée.
    </div>
    <a href="<?php print $urlRecharge ?>" class="cp-btn cp-btn-blue">
      Acheter des tokens
    </a>
  </div>

  <div class="cp-note"><i class="bi bi-lock-fill"></i> Paiements sécurisés par Stripe.</div>

</div>

<script>
function souscrire(plan) {
    document.getElementById('sub-plan').value = plan;
    document.getElementById('form-sub').submit();
}
function cancelSubPers(periodEndFmt) {
    var msg = periodEndFmt
        ? 'Vos tokens restent disponibles jusqu\'au ' + periodEndFmt + '. Après cette date, votre solde sera remis à zéro. Confirmer la résiliation ?'
        : 'Votre solde de tokens sera remis à zéro à la fin de la période en cours. Confirmer la résiliation ?';
    alertify.confirm('Résiliation abonnement', msg,
        function() {
            document.getElementById('cancel-period-fmt').value = periodEndFmt;
            document.getElementById('form-cancel-sub').submit();
        },
        function() {}
    );
}
<?php if ($stripeMsg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php print $stripeMsgType === 'success' ? 'success' : 'error'; ?>('<?php print addslashes($stripeMsg); ?>');
    if (window.history && window.history.replaceState) {
        window.history.replaceState({}, '', '<?php print './crediter-pers.php?idkeypers=' . urlencode($idkeypers) . '&productid=' . urlencode($productid) . '&url=' . urlencode($url); ?>');
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
