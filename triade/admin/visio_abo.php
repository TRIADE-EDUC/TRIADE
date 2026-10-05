<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");

include_once("../librairie_php/db_visio.php");

$msg = $msgtype = '';
if (isset($_GET['stripe'])) {
    switch ($_GET['stripe']) {
        case 'success':   $msg = 'Paiement Stripe accepté. Votre abonnement est en cours d\'activation (quelques secondes).'; $msgtype = 'success'; break;
        case 'cancel':    $msg = 'Paiement annulé. Aucun prélèvement n\'a été effectué.';  $msgtype = 'error';   break;
        case 'error':     $msg = 'Erreur Stripe : ' . htmlspecialchars($_GET['msg'] ?? 'impossible de créer la session de paiement.'); $msgtype = 'error'; break;
        case 'resilié':   $msg = 'Résiliation enregistrée. Votre accès reste actif jusqu\'à la fin de la période en cours.'; $msgtype = 'success'; break;
        case 'resil-err': $msg = 'Erreur résiliation : ' . htmlspecialchars($_GET['msg'] ?? 'pas de réponse du serveur central.'); $msgtype = 'error'; break;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler'])) {
    $ch = curl_init(VISIO_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'action' => 'stripe_cancel',
            'key'    => VISIO_API_KEY,
            'ecole'  => _visioCodeEcole(),
        ]),
    ]);
    $json = curl_exec($ch); curl_close($ch);
    $resp = $json ? json_decode($json, true) : null;
    if (!empty($resp['ok'])) {
        header('Location: visio_abo.php?stripe=r%C3%A9sili%C3%A9'); exit;
    }
    $errMsg = urlencode($resp['erreur'] ?? 'erreur inconnue');
    header('Location: visio_abo.php?stripe=resil-err&msg=' . $errMsg); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sauvegarder'])) {
    // "Enregistrer" ne sauvegarde QUE le contact de facturation.
    // Le statut, le plan et les dates ne sont modifiables que via le paiement Stripe.
    $aboActuel = getAbonnementVisio();
    $apiErr    = '';
    $ok = sauvegarderAbonnementDetail(
        $aboActuel ? $aboActuel['plan']                : 'starter',
        $aboActuel ? $aboActuel['statut']              : 'inactif',
        $aboActuel ? $aboActuel['date_debut']          : '',
        $aboActuel ? $aboActuel['date_fin']            : '',
        $aboActuel ? $aboActuel['prix_mois']           : 0,
        $aboActuel ? $aboActuel['nb_max_participants'] : 6,
        $aboActuel ? $aboActuel['nb_max_salles']       : 2,
        $_POST['contact'] ?? '',
        '',
        $apiErr
    );
    $msg     = $ok ? 'Coordonnées enregistrées.' : 'Erreur API : ' . ($apiErr ?: 'pas de réponse du serveur central.');
    $msgtype = $ok ? 'success' : 'error';
}

$abo = getAbonnementVisio();
$emailFacturation = $abo ? trim($abo['contact_facturation']) : '';
$emailValide      = filter_var($emailFacturation, FILTER_VALIDATE_EMAIL) !== false;
$plans = [
    'starter'  => ['label' => 'Starter',  'participants' => 6,  'salles' => 2,  'prix' => 15, 'icon' => '&#11088;'],
    'premium'  => ['label' => 'Premium',  'participants' => 15, 'salles' => 5,  'prix' => 35, 'icon' => '&#128640;'],
    'illimite' => ['label' => 'Illimité', 'participants' => 50, 'salles' => 20, 'prix' => 60, 'icon' => '&#8734;'],
];
$statutAff  = $abo ? $abo['statut'] : 'inactif';
$dateFinAff = $abo && $abo['date_fin'] ? date('d/m/Y', strtotime($abo['date_fin'])) : '—';
$planAff    = $abo ? ($plans[$abo['plan']]['label'] ?? $abo['plan']) : '—';
$badgeClass  = 'badge-inactif';
$statutLabel = strtoupper($statutAff);
if ($abo && $abo['statut'] === 'actif') {
    $badgeClass = ($abo['date_fin'] && $abo['date_fin'] < date('Y-m-d')) ? 'badge-expire' : 'badge-actif';
} elseif ($abo && $abo['statut'] === 'actif-resi') {
    $badgeClass  = 'badge-resil';
    $statutLabel = 'ACTIF — résil. le ' . $dateFinAff;
}
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
<title>Triade — Abonnement Visioconférence</title>
<style>
.abo-wrap { padding: 10px 16px; }
.abo-status {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    background: #f0f2fa; border: 1px solid #c5caee; border-radius: 8px;
    padding: 14px 16px; margin-bottom: 18px; font-size: 12px;
}
.status-badge { padding: 4px 14px; border-radius: 20px; font-weight: bold; font-size: 12px; }
.badge-actif   { background: #d4edda; color: #155724; }
.badge-inactif { background: #f8d7da; color: #721c24; }
.badge-expire  { background: #fff3cd; color: #856404; }
.badge-resil   { background: #fff3cd; color: #856404; }
.plan-cards { display: flex; gap: 14px; margin-bottom: 16px; flex-wrap: wrap; }
.plan-card {
    flex: 1; min-width: 130px; background: #fff;
    border: 1px solid #c5caee; border-radius: 10px;
    padding: 20px 16px; text-align: center;
    text-decoration: none !important; color: inherit; cursor: pointer;
}
.plan-card:hover, .plan-card:focus, .plan-card:active { text-decoration: none !important; color: inherit; outline: none; }
.plan-card *, .plan-card *:hover { text-decoration: none !important; }
.plan-card.selected { border-color: #080A66; background: #f0f2fa; }
.plan-card .tc-icon  { font-size: 28px; margin-bottom: 10px; }
.plan-card .tc-titre { font-weight: bold; color: #080A66; font-size: 13px; margin-bottom: 6px; }
.plan-card .tc-desc  { font-size: 11px; color: #666; line-height: 1.4; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.fg label { display: block; font-size: 11px; color: #555; margin-bottom: 3px; font-weight: bold; }
.fg input, .fg select, .fg textarea {
    width: 100%; padding: 6px 8px; border: 1px solid #c5caee;
    border-radius: 5px; font-size: 12px; box-sizing: border-box;
}
.fg textarea { height: 60px; resize: vertical; }
.btn-save {
    background: #080A66; color: #fff; border: none; padding: 9px 24px;
    border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer; margin-top: 12px;
}
.btn-save:hover { background: #0d12a3; }
.btn-stripe {
    background: #635bff; color: #fff; border: none; padding: 9px 22px;
    border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer; margin-top: 12px;
    display: inline-flex; align-items: center; gap: 7px;
}
.btn-stripe:hover:not(:disabled) { background: #4f46e5; }
.btn-stripe:disabled { background: #b0adf5; cursor: not-allowed; opacity: 0.7; }
.stripe-sep { display: inline-block; margin: 0 10px; color: #bbb; font-size: 12px; }
.section-box { background: #fff; border: 1px solid #dde; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px; }
.section-box h3 { color: #080A66; font-size: 13px; margin: 0 0 12px; }
.btn-resil {
    background: #fff; color: #c0392b; border: 1px solid #c0392b; padding: 7px 16px;
    border-radius: 6px; font-size: 12px; cursor: pointer; margin-top: 10px;
}
.btn-resil:hover { background: #fdf0ee; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Abonnement Visioconférence</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div class="abo-wrap">
    <div class="abo-status">
        <div>
            <div style="color:#666;margin-bottom:3px;">Statut</div>
            <span class="status-badge <?php echo $badgeClass; ?>"><?php echo $statutLabel; ?></span>
        </div>
        <div>
            <div style="color:#666;margin-bottom:3px;">Plan</div>
            <strong style="color:#080A66;"><?php echo htmlspecialchars($planAff); ?></strong>
        </div>
        <div>
            <div style="color:#666;margin-bottom:3px;">Expire le</div>
            <strong><?php echo $dateFinAff; ?></strong>
        </div>
        <?php if ($abo && $abo['statut'] === 'actif'): ?>
        <div style="margin-left:auto;">
            <a href="../visio/rooms.php" target="_blank"
               style="background:#080A66;color:#fff;padding:6px 14px;border-radius:5px;text-decoration:none;font-size:12px;">
                Ouvrir la visio →
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div style="font-size:12px;font-weight:bold;color:#080A66;margin-bottom:8px;">Choisir un plan :</div>
    <div class="plan-cards">
        <?php foreach ($plans as $key => $p): ?>
        <div class="plan-card <?php echo ($abo && $abo['plan'] === $key) ? 'selected' : ''; ?>"
             onclick="selectionnerPlan('<?php echo $key; ?>', <?php echo $p['participants']; ?>, <?php echo $p['salles']; ?>, <?php echo $p['prix']; ?>, event)">
            <div class="tc-icon"><?php echo $p['icon']; ?></div>
            <div class="tc-titre"><?php echo $p['label']; ?> — <?php echo $p['prix']; ?>€/mois</div>
            <div class="tc-desc"><?php echo $p['participants']; ?> participants<br><?php echo $p['salles']; ?> salles</div>
        </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="visio_abo.php">
        <div class="section-box">
            <h3>Coordonnées de facturation</h3>
            <div class="form-grid">
                <div class="fg">
                    <label>Contact facturation</label>
                    <input type="text" name="contact"
                           value="<?php echo htmlspecialchars($abo ? $abo['contact_facturation'] : ''); ?>"
                           placeholder="email ou nom">
                </div>
            </div>
        </div>
        <button type="submit" name="sauvegarder" class="btn-save">Enregistrer les coordonnées</button>
    </form>

    <!-- Formulaire résiliation -->
    <form method="POST" action="visio_abo.php" id="form-annuler">
        <input type="hidden" name="annuler" value="1">
    </form>

    <!-- Formulaire Stripe séparé (POST vers checkout.php) -->
    <form method="POST" action="../visio/stripe/checkout.php" id="form-stripe">
        <input type="hidden" name="plan"    id="stripe-plan"    value="<?php echo htmlspecialchars($abo ? $abo['plan'] : 'starter'); ?>">
        <input type="hidden" name="contact" id="stripe-contact" value="<?php echo htmlspecialchars($abo ? $abo['contact_facturation'] : ''); ?>">
    </form>
    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #e0e3f0;">
        <div style="font-size:11px;color:#888;margin-bottom:8px;">Paiement sécurisé via Stripe — abonnement mensuel, résiliable à tout moment.</div>
        <?php if (!$emailValide): ?>
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:8px 12px;font-size:11px;color:#856404;margin-bottom:8px;">
            ⚠️ Enregistrez un email de facturation valide pour activer le paiement.
        </div>
        <?php endif; ?>
        <button class="btn-stripe" id="btn-stripe-pay" onclick="lancerStripe()"
                <?php echo !$emailValide ? 'disabled' : ''; ?>
                title="<?php echo !$emailValide ? 'Enregistrez d\'abord un email de facturation valide' : 'Payer avec Stripe'; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13.97 9.056c0-.78.64-1.08 1.7-1.08 1.52 0 3.44.46 4.96 1.28V5.16C19.1 4.56 17.6 4 15.67 4 11.8 4 9.2 6.04 9.2 9.3c0 5.12 7.04 4.3 7.04 6.5 0 .92-.8 1.22-1.92 1.22-1.66 0-3.78-.68-5.46-1.6v4.06c1.86.8 3.74 1.14 5.46 1.14 4 0 6.74-1.98 6.74-5.3 0-5.52-7.09-4.54-7.09-6.22z"/></svg>
            Payer avec Stripe
        </button>
        <span class="stripe-sep">—</span>
        <span style="font-size:11px;color:#555;">Plan sélectionné : <strong id="stripe-plan-label"><?php
            $pk = $abo ? $abo['plan'] : 'starter';
            echo htmlspecialchars(isset($plans[$pk]) ? $plans[$pk]['label'] . ' · ' . $plans[$pk]['prix'] . '€/mois' : $pk);
        ?></strong></span>
        <?php if ($abo && $abo['statut'] === 'actif'): ?>
        <div style="margin-top:14px;padding-top:10px;border-top:1px dashed #e0e3f0;">
            <button class="btn-resil" onclick="confirmerResiliation()">&#10005; Résilier l'abonnement</button>
            <div style="font-size:10px;color:#999;margin-top:4px;">L'accès restera actif jusqu'à la fin de la période en cours (<?php echo $dateFinAff; ?>).</div>
        </div>
        <?php elseif ($abo && $abo['statut'] === 'actif-resi'): ?>
        <div style="margin-top:14px;padding-top:10px;border-top:1px dashed #e0e3f0;background:#fffbea;border-radius:6px;padding:10px 12px;">
            <span style="color:#856404;font-size:12px;">&#9888; Résiliation programmée — accès jusqu'au <?php echo $dateFinAff; ?>.</span>
        </div>
        <?php endif; ?>
    </div>
</div>

</td></tr>
</table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>
var planLabels = <?php echo json_encode(array_map(function($p){ return $p['label'] . ' — ' . $p['prix'] . '€/mois'; }, $plans)); ?>;
var planStripeLabels = <?php echo json_encode(array_map(function($p){ return $p['label'] . ' · ' . $p['prix'] . '€/mois'; }, $plans)); ?>;
function selectionnerPlan(plan, maxP, maxS, prix, e) {
    document.querySelectorAll('.plan-card').forEach(function(c) { c.classList.remove('selected'); });
    e.currentTarget.classList.add('selected');
    // Mettre à jour le formulaire Stripe
    document.getElementById('stripe-plan').value = plan;
    document.getElementById('stripe-plan-label').textContent = planStripeLabels[plan] || plan;
}
function confirmerResiliation() {
    alertify.confirm(
        'Résilier l\'abonnement',
        'Votre abonnement ne sera pas renouvelé. Vous conservez l\'accès à la visioconférence jusqu\'au <?php echo $dateFinAff; ?>.',
        function() { document.getElementById('form-annuler').submit(); },
        function() {}
    ).set('labels', { ok: 'Résilier définitivement', cancel: 'Annuler' });
}
function lancerStripe() {
    var btn = document.getElementById('btn-stripe-pay');
    if (btn && btn.disabled) return;
    var contact = document.querySelector('input[name="contact"]');
    if (contact) document.getElementById('stripe-contact').value = contact.value;
    document.getElementById('form-stripe').submit();
}

// Si l'admin modifie le contact sans sauvegarder → désactiver le bouton Stripe
document.addEventListener('DOMContentLoaded', function() {
    var contactInput = document.querySelector('input[name="contact"]');
    var btnStripe    = document.getElementById('btn-stripe-pay');
    if (!contactInput || !btnStripe) return;
    var emailSauvegarde = <?php echo $emailValide ? 'true' : 'false'; ?>;
    var valeurSauvegarde = <?php echo json_encode($emailFacturation); ?>;

    contactInput.addEventListener('input', function() {
        var val = this.value.trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        // Actif seulement si la valeur correspond exactement à ce qui est en base
        if (emailSauvegarde && val === valeurSauvegarde) {
            btnStripe.disabled = false;
            btnStripe.title = 'Payer avec Stripe';
        } else {
            btnStripe.disabled = true;
            btnStripe.title = val !== valeurSauvegarde
                ? 'Enregistrez d\'abord vos coordonnées'
                : 'Enregistrez un email de facturation valide';
        }
    });
});
<?php if ($msg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php echo $msgtype === 'success' ? 'success' : 'error'; ?>('<?php echo addslashes($msg); ?>');
});
<?php endif; ?>
<?php if (isset($_GET['stripe']) && $_GET['stripe'] === 'success' && $statutAff !== 'actif'): ?>
// Abonnement pas encore activé : recharger dans 4 s pour laisser le webhook traiter
window.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() { window.location.reload(); }, 4000);
});
<?php endif; ?>
</script>
</body>
</HTML>
