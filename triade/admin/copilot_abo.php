<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");

// Charger IAKEY
$iakey = '';
$iaActif = false;
$iaApiDispo = false;
if (file_exists('../common/config-ia.php')) {
    include_once('../common/config-ia.php');
    if (defined('IAKEY')) { $iakey = IAKEY; $iaActif = true; }
}
if (file_exists('../librairie_php/db_ia.php')) {
    include_once('../librairie_php/db_ia.php');
    $iaApiDispo = true;
}

// Messages Stripe
$msg = $msgtype = '';
if (isset($_GET['stripe'])) {
    switch ($_GET['stripe']) {
        case 'success': $msg = 'Paiement accepté. Vos tokens sont en cours de crédit (quelques secondes).'; $msgtype = 'success'; break;
        case 'cancel':  $msg = 'Paiement annulé. Aucun prélèvement effectué.'; $msgtype = 'error'; break;
        case 'error':   $msg = 'Erreur : ' . htmlspecialchars($_GET['msg'] ?? 'impossible de créer la session.'); $msgtype = 'error'; break;
    }
}

// Solde et infos compte
$solde      = ($iaActif && $iaApiDispo) ? getIABalance($iakey) : null;
$tokensDisp = $solde   ? intval($solde['nbtokencredit']) : 0;
$tokensEnrg = $solde   ? intval($solde['nbtokenenrg'])   : 0;
$iaInfo     = ($iaActif && $iaApiDispo) ? getIAInfo($iakey) : null;
$emailCompte = $iaInfo ? ($iaInfo['email'] ?? '') : '';

$plansMeta = array(
    'OFFA' => array('label' => 'Pack A', 'icon' => '&#127775;', 'desc' => '100 millions de tokens'),
    'OFFB' => array('label' => 'Pack B', 'icon' => '&#10024;',  'desc' => '75 millions de tokens'),
    'OFFC' => array('label' => 'Pack C', 'icon' => '&#9889;',   'desc' => '50 millions de tokens'),
    'OFFD' => array('label' => 'Pack D', 'icon' => '&#128640;', 'desc' => '25 millions de tokens'),
    'OFFE' => array('label' => 'Pack E', 'icon' => '&#127807;', 'desc' => '10 millions de tokens'),
);

$frais = 8;
$plans = array();
if ($iaApiDispo) {
    $tarifs = getIATarifs();
    if ($tarifs && !empty($tarifs['plans'])) {
        $frais = isset($tarifs['frais']) ? (int)$tarifs['frais'] : 8;
        foreach ($tarifs['plans'] as $code => $t) {
            $meta = isset($plansMeta[$code]) ? $plansMeta[$code] : array('label' => $code, 'icon' => '&#128230;', 'desc' => $t['tokens'] . ' tokens');
            $plans[$code] = array_merge($meta, array('tokens' => (int)$t['tokens'], 'prix' => (float)$t['prix']));
        }
        // Tri : du plus grand pack au plus petit
        arsort($plans);
    }
}
if (empty($plans)) {
    $plans = array(
        'OFFE' => array('label' => 'Pack E', 'tokens' => 10000000,  'prix' =>  53.99, 'icon' => '&#127807;', 'desc' => '10 millions de tokens'),
        'OFFD' => array('label' => 'Pack D', 'tokens' => 25000000,  'prix' => 118.79, 'icon' => '&#128640;', 'desc' => '25 millions de tokens'),
        'OFFC' => array('label' => 'Pack C', 'tokens' => 50000000,  'prix' => 194.39, 'icon' => '&#9889;',   'desc' => '50 millions de tokens'),
        'OFFB' => array('label' => 'Pack B', 'tokens' => 75000000,  'prix' => 269.99, 'icon' => '&#10024;',  'desc' => '75 millions de tokens'),
        'OFFA' => array('label' => 'Pack A', 'tokens' => 100000000, 'prix' => 345.59, 'icon' => '&#127775;', 'desc' => '100 millions de tokens'),
    );
}

$contact = $emailCompte;
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<title>Triade — Crédits TRIADE-COPILOT</title>
<style>
.abo-wrap { padding: 10px 16px; }
.abo-status {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    background: #f0f2fa; border: 1px solid #c5caee; border-radius: 8px;
    padding: 14px 16px; margin-bottom: 18px; font-size: 12px;
}
.status-badge { padding: 4px 14px; border-radius: 20px; font-weight: bold; font-size: 12px; }
.badge-ok  { background: #d4edda; color: #155724; }
.badge-low { background: #fff3cd; color: #856404; }
.badge-ko  { background: #f8d7da; color: #721c24; }
.plan-cards { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.plan-card {
    flex: 1; min-width: 110px; background: #fff;
    border: 1px solid #c5caee; border-radius: 10px;
    padding: 18px 12px; text-align: center; cursor: pointer;
    text-decoration: none !important; color: inherit;
    transition: border-color .15s, background .15s;
}
.plan-card:hover { text-decoration: none !important; color: inherit; outline: none; }
.plan-card.selected { border-color: #080A66; background: #f0f2fa; }
.plan-card .pc-icon  { font-size: 26px; margin-bottom: 8px; }
.plan-card .pc-titre { font-weight: bold; color: #080A66; font-size: 12px; margin-bottom: 4px; }
.plan-card .pc-prix  { font-size: 18px; font-weight: bold; color: #080A66; margin-bottom: 4px; }
.plan-card .pc-desc  { font-size: 10px; color: #666; line-height: 1.4; }
.fg label { display: block; font-size: 11px; color: #555; margin-bottom: 3px; font-weight: bold; }
.fg input { width: 100%; padding: 6px 8px; border: 1px solid #c5caee; border-radius: 5px; font-size: 12px; box-sizing: border-box; }
.btn-stripe {
    background: #635bff; color: #fff; border: none; padding: 9px 22px;
    border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer; margin-top: 12px;
    display: inline-flex; align-items: center; gap: 7px;
}
.btn-stripe:hover:not(:disabled) { background: #4f46e5; }
.btn-stripe:disabled { background: #b0adf5; cursor: not-allowed; opacity: 0.7; }
.section-box { background: #fff; border: 1px solid #dde; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px; }
.section-box h3 { color: #080A66; font-size: 13px; margin: 0 0 12px; }
.info-note { font-size: 11px; color: #888; margin-bottom: 6px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Crédits TRIADE-COPILOT</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php if (!$iaActif): ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#856404;">
    &#9888; Compte COPILOT non configuré. <a href="ia-inscription.php" style="color:#c0392b;font-weight:700;">Inscription gratuite</a>
</div>
<?php else: ?>

<div class="abo-wrap">

    <!-- Solde actuel -->
    <div class="abo-status">
        <div>
            <div style="color:#666;margin-bottom:3px;">Tokens disponibles</div>
            <?php
            $bClass = 'badge-ko';
            if ($tokensDisp > 5000000)       $bClass = 'badge-ok';
            elseif ($tokensDisp > 0)          $bClass = 'badge-low';
            ?>
            <span class="status-badge <?php echo $bClass; ?>"><?php echo number_format($tokensDisp, 0, ',', ' '); ?></span>
        </div>
        <div>
            <div style="color:#666;margin-bottom:3px;">Tokens utilisés</div>
            <strong><?php echo number_format($tokensEnrg, 0, ',', ' '); ?></strong>
        </div>
        <?php if ($tokensDisp < 1000000): ?>
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:6px 10px;font-size:11px;color:#856404;">
            &#9888; Solde faible — rechargez pour continuer à utiliser COPILOT
        </div>
        <?php endif; ?>
    </div>

    <!-- Plans -->
    <div style="font-size:12px;font-weight:bold;color:#080A66;margin-bottom:8px;">Choisir un pack de tokens :</div>
    <div class="plan-cards">
        <?php $first = true; foreach ($plans as $key => $p):
            $total = $p['prix'] + $frais;
        ?>
        <div class="plan-card <?php echo $first ? 'selected' : ''; ?>"
             onclick="selectionnerPlan('<?php echo $key; ?>', <?php echo $p['tokens']; ?>, <?php echo $total; ?>, event)">
            <div class="pc-icon"><?php echo $p['icon']; ?></div>
            <div class="pc-titre"><?php echo $p['label']; ?></div>
            <div class="pc-prix"><?php echo number_format($total, 2, ',', ' '); ?>&nbsp;€</div>
            <div class="pc-desc"><?php echo number_format($p['prix'], 2, ',', ' '); ?>&nbsp;€ + <?php echo $frais; ?>&nbsp;€ frais</div>
        </div>
        <?php $first = false; endforeach; ?>
    </div>

    <!-- Contact + paiement -->
    <div class="section-box">
        <h3>Email du compte</h3>
        <div class="fg">
            <label>Adresse email du compte (reçu Stripe)</label>
            <input type="email" id="contact-email" placeholder="contact@etablissement.fr" value="<?php echo htmlspecialchars($contact); ?>">
        </div>
    </div>

    <!-- Formulaire Stripe -->
    <form method="POST" action="../ia/stripe/checkout.php" id="form-stripe">
        <input type="hidden" name="plan"    id="stripe-plan"    value="OFFE">
        <input type="hidden" name="contact" id="stripe-contact" value="">
    </form>

    <div style="padding-top:12px;border-top:1px solid #e0e3f0;">
        <p class="info-note">Paiement sécurisé via Stripe — achat unique, aucun abonnement.</p>
        <button class="btn-stripe" id="btn-stripe-pay" onclick="lancerStripe()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M13.97 9.056c0-.78.64-1.08 1.7-1.08 1.52 0 3.44.46 4.96 1.28V5.16C19.1 4.56 17.6 4 15.67 4 11.8 4 9.2 6.04 9.2 9.3c0 5.12 7.04 4.3 7.04 6.5 0 .92-.8 1.22-1.92 1.22-1.66 0-3.78-.68-5.46-1.6v4.06c1.86.8 3.74 1.14 5.46 1.14 4 0 6.74-1.98 6.74-5.3 0-5.52-7.09-4.54-7.09-6.22z"/>
            </svg>
            Payer avec Stripe
        </button>
        <span style="font-size:11px;color:#555;margin-left:10px;">
            Pack sélectionné : <strong id="stripe-plan-label">Pack E — <?php echo number_format(53.99 + $frais, 2, ',', '.'); ?>€</strong>
        </span>
    </div>

</div>

<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>
var planLabels = {
<?php foreach ($plans as $key => $p):
    $total = $p['prix'] + $frais;
?>
    '<?php echo $key; ?>': '<?php echo $p['label']; ?> — <?php echo number_format($total, 2, ',', '.'); ?>€',
<?php endforeach; ?>
};

function selectionnerPlan(plan, tokens, prix, e) {
    document.querySelectorAll('.plan-card').forEach(function(c) { c.classList.remove('selected'); });
    e.currentTarget.classList.add('selected');
    document.getElementById('stripe-plan').value = plan;
    document.getElementById('stripe-plan-label').textContent = planLabels[plan] || plan;
}

function lancerStripe() {
    var email = document.getElementById('contact-email').value.trim();
    var re    = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(email)) {
        alertify.error('Entrez un email de facturation valide.');
        return;
    }
    document.getElementById('stripe-contact').value = email;
    document.getElementById('form-stripe').submit();
}

<?php if ($msg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php echo $msgtype === 'success' ? 'success' : 'error'; ?>('<?php echo addslashes($msg); ?>');
});
<?php endif; ?>
<?php if (isset($_GET['stripe']) && $_GET['stripe'] === 'success'): ?>
// Recharger vers l'URL propre après 5s pour afficher le solde mis à jour
window.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() { window.location.href = window.location.pathname; }, 5000);
});
<?php endif; ?>
</script>
</body>
</HTML>
