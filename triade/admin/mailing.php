<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   copyright : (C) 2000 E. TAESCH - F. ORY
 ***************************************************************************/
include_once("./librairie_php/lib_licence.php");

// Données chargées côté serveur — MAILINGKEY jamais exposé au navigateur
$onglet    = isset($_GET['onglet']) ? $_GET['onglet'] : 'dashboard';
$allowed   = array('dashboard', 'factures', 'modif', 'log', 'achat');
if (!in_array($onglet, $allowed)) { $onglet = 'dashboard'; }

$info      = array();
$factures  = array();
$log       = array();
$tarifs    = array();
$apiErr    = false;

// Messages retour Stripe
$stripeMsg     = '';
$stripeMsgType = '';
if (isset($_GET['stripe'])) {
    switch ($_GET['stripe']) {
        case 'success':
            $stripeMsg = 'Paiement confirmé. Vos crédits email seront disponibles sous quelques instants.';
            $stripeMsgType = 'success'; break;
        case 'cancel':
            $stripeMsg = 'Paiement annulé. Aucun prélèvement effectué.';
            $stripeMsgType = 'error'; break;
        case 'error':
            $stripeMsg = 'Erreur : ' . htmlspecialchars(isset($_GET['msg']) ? $_GET['msg'] : 'impossible de créer la session.');
            $stripeMsgType = 'error'; break;
    }
}

function mailingApi($action, $extra = array()) {
    $payload = array_merge(array(
        'key'       => 'mailing-2026-xT4vNqP8',
        'action'    => $action,
        'idmailing' => MAILINGKEY,
    ), $extra);
    $ch = curl_init('https://www.triade-educ.org/mailing/api.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r ? json_decode($r, true) : array();
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<style>
.ml-tabs{display:flex;gap:2px;background:#e8eaf6;border-radius:6px;padding:3px;margin-bottom:12px}
.ml-tab{flex:1;text-align:center;padding:7px 4px;font-size:11px;border-radius:4px;text-decoration:none;color:#555;font-family:Electrolize,'Trebuchet MS',Arial;display:flex;align-items:center;justify-content:center;gap:4px}
.ml-tab.active{background:#080A66;color:#fff}
.ml-tab:hover:not(.active){background:rgba(8,10,102,.1);color:#080A66}
.ml-stat{flex:1;border-radius:8px;padding:14px 16px;text-align:center}
.ml-stat-val{font-size:32px;font-weight:bold;font-family:Electrolize,'Trebuchet MS',Arial;line-height:1}
.ml-stat-lbl{font-size:11px;margin-top:4px;font-family:Electrolize,'Trebuchet MS',Arial}
.ml-field{display:flex;flex-direction:column;gap:4px;margin-bottom:10px}
.ml-field label{font-size:11px;color:#555;font-family:Electrolize,'Trebuchet MS',Arial}
.ml-field label b{color:#c62828}
.ml-field input{border:1px solid #c5cae9;border-radius:4px;padding:7px 9px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;width:100%;box-sizing:border-box}
.ml-field input:focus{outline:none;border-color:#080A66;box-shadow:0 0 0 2px rgba(8,10,102,.08)}
.ml-2col{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.ml-offer-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.ml-offer{border:2px solid #c5cae9;border-radius:8px;padding:14px 16px;cursor:pointer;background:#fff;transition:border-color .15s,background .15s;text-align:center;font-family:Electrolize,'Trebuchet MS',Arial}
.ml-offer:hover{border-color:#080A66;background:#f5f7ff}
.ml-offer.selected{border-color:#080A66;background:#f0f4ff}
.ml-offer-nb{font-size:22px;font-weight:bold;color:#080A66;line-height:1.1}
.ml-offer-prix{font-size:13px;font-weight:bold;color:#333;margin:6px 0 2px}
.ml-offer-unit{font-size:10px;color:#888}
.ml-offer-badge{display:inline-block;background:#080A66;color:#fff;border-radius:20px;padding:2px 10px;font-size:10px;margin-top:6px}
.btn-stripe{background:#635bff;color:#fff;border:none;padding:9px 22px;border-radius:6px;font-size:13px;font-weight:bold;cursor:pointer;margin-top:4px;display:inline-flex;align-items:center;gap:7px}
.btn-stripe:hover:not(:disabled){background:#4f46e5}
.btn-stripe:disabled{background:#b0adf5;cursor:not-allowed;opacity:.7}
</style>
<title>Triade — Gestion des emails externes</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des emails externes — TRIADE-MAILING</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php if (isset($_GET["init"])): ?>
<div style="margin:8px 0 10px;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:6px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#2e7d32">
    <i class="bi bi-check-circle-fill"></i> Compte réinitialisé. Vous pouvez effectuer une nouvelle inscription.
</div>
<?php endif; ?>

<?php if (LAN == "oui"): ?>

<?php if (!file_exists("../common/config-mailing.php")): ?>
<div style="margin:12px 8px;padding:18px 20px;background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#333">
    <div style="font-size:14px;font-weight:bold;color:#080A66;margin-bottom:10px"><i class="bi bi-envelope-at-fill"></i> TRIADE-MAILING</div>
    Vous n'avez aucun compte créé pour la gestion des emails TRIADE-MAILING.<br><br>
    <b>Garantissez vos envois d'emails sans être signalé comme spammeur !</b><br><br>
    <form method="post" action="mailing-inscription.php">
        <input type="submit" name="create" value="Inscription Gratuite" class="btn-enr">
    </form>
</div>

<?php else:
include_once("../common/config-mailing.php");

// Appel API selon l'onglet actif (un seul appel par chargement)
if ($onglet === 'dashboard' || $onglet === 'modif') {
    $resp = mailingApi('get_info');
    if (!empty($resp['ok'])) { $info = $resp; } else { $apiErr = true; }
} elseif ($onglet === 'factures') {
    $resp = mailingApi('get_factures');
    if (!empty($resp['ok'])) { $factures = $resp['factures']; } else { $apiErr = true; }
} elseif ($onglet === 'log') {
    $logAll  = (isset($_GET['all']) && $_GET['all'] == '1') ? '1' : '0';
    $resp = mailingApi('get_log', array('all' => $logAll));
    if (!empty($resp['ok'])) {
        $log      = $resp['log'];
        $logTotal = isset($resp['total']) ? intval($resp['total']) : count($log);
        $logLimit = isset($resp['limit']) ? intval($resp['limit']) : 30;
    } else { $apiErr = true; }
} elseif ($onglet === 'achat') {
    $resp = mailingApi('get_tarifs');
    if (!empty($resp['ok'])) { $tarifs = $resp; } else { $apiErr = true; }
    $resp2 = mailingApi('get_info');
    if (!empty($resp2['ok'])) { $info = $resp2; }
}
?>

<!-- Formulaire Stripe (caché) — MAILINGKEY et clé API restent côté serveur PHP -->
<form method="POST" action="mailing-checkout.php" id="form-mailing-stripe">
    <input type="hidden" name="offre"   id="fm-offre"   value="">
    <input type="hidden" name="contact" id="fm-contact" value="<?php echo htmlspecialchars(isset($info['email']) ? $info['email'] : ''); ?>">
</form>

<?php if ($stripeMsg): ?>
<script>document.addEventListener('DOMContentLoaded',function(){alertify.<?php echo $stripeMsgType === 'success' ? 'success' : 'error'; ?>('<?php echo addslashes($stripeMsg); ?>');})</script>
<?php endif; ?>

<!-- ── Onglets ── -->
<div class="ml-tabs">
    <a href="mailing.php" class="ml-tab <?php echo ($onglet==='dashboard')?'active':''; ?>">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>
    <a href="mailing.php?onglet=factures" class="ml-tab <?php echo ($onglet==='factures')?'active':''; ?>">
        <i class="bi bi-receipt"></i> Factures
    </a>
    <a href="mailing.php?onglet=modif" class="ml-tab <?php echo ($onglet==='modif')?'active':''; ?>">
        <i class="bi bi-pencil-square"></i> Modifier infos
    </a>
    <a href="mailing.php?onglet=log" class="ml-tab <?php echo ($onglet==='log')?'active':''; ?>">
        <i class="bi bi-clock-history"></i> Historique
    </a>
</div>

<?php if ($apiErr): ?>
<div style="padding:12px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#856404">
    <i class="bi bi-exclamation-triangle-fill"></i> <b>Impossible de contacter le serveur central.</b> Veuillez actualiser la page.
</div>

<?php elseif ($onglet === 'dashboard'): ?>
<!-- ════════════════════ TABLEAU DE BORD ════════════════════ -->

<div style="display:flex;gap:10px;margin-bottom:10px">
    <a href="mailing.php?onglet=achat" class="btn-enr" style="text-decoration:none;display:inline-flex;align-items:center;gap:5px;font-size:11px">
        <i class="bi bi-envelope-plus-fill"></i> Créditer des emails
    </a>
    <span style="flex:1"></span>
    <a href="mailing-init.php" class="btn-retour" style="text-decoration:none;display:inline-flex;align-items:center;gap:5px;font-size:11px">
        <i class="bi bi-trash3"></i> Réinitialiser
    </a>
</div>

<div style="display:flex;gap:10px;margin-bottom:10px">
    <div class="ml-stat" style="background:#f0f4ff;border:1px solid #c5cae9">
        <div class="ml-stat-val" style="color:#080A66"><?php echo intval($info['nbemailcredit']); ?></div>
        <div class="ml-stat-lbl" style="color:#555"><i class="bi bi-envelope-check-fill" style="color:#080A66"></i> emails disponibles</div>
    </div>
    <div class="ml-stat" style="background:#f5f7ff;border:1px solid #c5cae9">
        <div class="ml-stat-val" style="color:#546e7a"><?php echo intval($info['nbemailenr']); ?></div>
        <div class="ml-stat-lbl" style="color:#777"><i class="bi bi-send-fill" style="color:#546e7a"></i> emails envoyés</div>
    </div>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px">
<tr class="cc-th">
    <td style="padding:6px 10px;font-size:11px"><i class="bi bi-building"></i> Établissement</td>
    <td style="padding:6px 10px;font-size:11px"><i class="bi bi-person-badge"></i> Responsable</td>
</tr>
<tr class="cc-tr-data">
    <td style="padding:8px 10px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;vertical-align:top">
        <b><?php echo htmlspecialchars($info['etablissement']); ?></b><br>
        <?php if ($info['adresse']): ?><?php echo htmlspecialchars($info['adresse']); ?><br><?php endif; ?>
        <?php echo htmlspecialchars($info['ccp'] . ' ' . $info['ville']); ?>
        <?php if ($info['pays']): ?>(<?php echo htmlspecialchars($info['pays']); ?>)<?php endif; ?>
    </td>
    <td style="padding:8px 10px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;vertical-align:top">
        <b><?php echo htmlspecialchars($info['nom'] . ' ' . $info['prenom']); ?></b><br>
        <i class="bi bi-envelope" style="color:#546e7a"></i> <?php echo htmlspecialchars($info['email']); ?>
    </td>
</tr>
</table>

<?php if (!empty($info['log'])): ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="cc-th">
    <td colspan="4" style="padding:6px 10px;font-size:11px"><i class="bi bi-clock-history"></i> Derniers emails envoyés</td>
</tr>
<tr style="background:#e8eaf6">
    <td style="padding:5px 10px;font-size:10px;font-weight:bold;color:#080A66;font-family:Electrolize,'Trebuchet MS',Arial">Date</td>
    <td style="padding:5px 10px;font-size:10px;font-weight:bold;color:#080A66;font-family:Electrolize,'Trebuchet MS',Arial">Destinataire</td>
    <td style="padding:5px 10px;font-size:10px;font-weight:bold;color:#080A66;font-family:Electrolize,'Trebuchet MS',Arial">Message</td>
    <td style="padding:5px 10px;font-size:10px;font-weight:bold;color:#080A66;font-family:Electrolize,'Trebuchet MS',Arial;text-align:center">Statut</td>
</tr>
<?php foreach ($info['log'] as $row): ?>
<tr class="cc-tr-data">
    <td style="padding:6px 10px;font-size:11px;white-space:nowrap;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['date']); ?></td>
    <td style="padding:6px 10px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['dest']); ?></td>
    <td style="padding:6px 10px;font-size:11px;color:#555;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['message']); ?></td>
    <td style="padding:6px 10px;text-align:center">
        <?php if ($row['status'] == 80): ?>
        <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:2px 9px;font-size:10px;font-family:Electrolize,'Trebuchet MS',Arial"><i class="bi bi-check-circle-fill"></i> envoyé</span>
        <?php else: ?>
        <span style="background:#fdecea;color:#c62828;border-radius:20px;padding:2px 9px;font-size:10px;font-family:Electrolize,'Trebuchet MS',Arial"><i class="bi bi-x-circle-fill"></i> erreur</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<div style="padding:12px;font-size:12px;color:#888;text-align:center;font-family:Electrolize,'Trebuchet MS',Arial"><i class="bi bi-inbox"></i> Aucun email envoyé.</div>
<?php endif; ?>

<?php elseif ($onglet === 'factures'): ?>
<!-- ════════════════════ FACTURES ════════════════════ -->
<?php if (isset($_GET['err']) && $_GET['err'] === 'pdf'): ?>
<div style="margin-bottom:10px;padding:10px 14px;background:#fdecea;border:1px solid #ef9a9a;border-radius:6px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#c62828">
    <i class="bi bi-x-circle-fill"></i> Impossible de télécharger cette facture. Contactez le support.
</div>
<?php endif; ?>

<?php if (empty($factures)): ?>
<div style="padding:20px;text-align:center;font-size:12px;color:#888;font-family:Electrolize,'Trebuchet MS',Arial">
    <i class="bi bi-receipt" style="font-size:24px;display:block;margin-bottom:8px"></i>
    Aucune facture disponible.
</div>
<?php else: ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="cc-th">
    <td style="padding:6px 10px;font-size:11px">Référence</td>
    <td style="padding:6px 10px;font-size:11px">Montant TTC</td>
    <td style="padding:6px 10px;font-size:11px">Date</td>
    <td style="padding:6px 10px;font-size:11px;text-align:center">PDF</td>
</tr>
<?php foreach ($factures as $f): ?>
<tr class="cc-tr-data">
    <td style="padding:7px 10px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($f['ref']); ?></td>
    <td style="padding:7px 10px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;font-weight:bold;color:#080A66"><?php echo htmlspecialchars($f['montant']); ?></td>
    <td style="padding:7px 10px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($f['date']); ?></td>
    <td style="padding:7px 10px;text-align:center">
        <?php if (!empty($f['fichier']) && strpos($f['fichier'], 'http') === 0): ?>
        <a href="<?php echo htmlspecialchars($f['fichier']); ?>" target="_blank" rel="noopener" class="btn-secondary" style="text-decoration:none;font-size:11px;display:inline-flex;align-items:center;gap:4px">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#c62828"></i> Voir facture
        </a>
        <?php elseif (!empty($f['fichier'])): ?>
        <a href="mailing-facture-pdf.php?f=<?php echo urlencode($f['fichier']); ?>" class="btn-secondary" style="text-decoration:none;font-size:11px;display:inline-flex;align-items:center;gap:4px">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#c62828"></i> Télécharger
        </a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<?php elseif ($onglet === 'modif'): ?>
<!-- ════════════════════ MODIFIER INFOS ════════════════════ -->
<?php if (isset($_GET['saved'])): ?>
<script>document.addEventListener('DOMContentLoaded',function(){alertify.success('Informations enregistrées.');});</script>
<?php elseif (isset($_GET['err'])): ?>
<script>document.addEventListener('DOMContentLoaded',function(){alertify.error('Erreur lors de l\'enregistrement. Veuillez réessayer.');});</script>
<?php endif; ?>

<form method="post" action="mailing-save-info.php" name="formulaire">

    <div style="font-size:11px;font-weight:bold;letter-spacing:.6px;color:#080A66;text-transform:uppercase;padding-bottom:6px;margin-bottom:12px;border-bottom:2px solid #e8eaf6;font-family:Electrolize,'Trebuchet MS',Arial">
        <i class="bi bi-building"></i> Établissement
    </div>
    <div class="ml-field">
        <label>Nom de l'établissement <b>*</b></label>
        <input type="text" name="etablissement" maxlength="60" value="<?php echo htmlspecialchars($info['etablissement']); ?>" required>
    </div>
    <div class="ml-field">
        <label>Adresse</label>
        <input type="text" name="adresse" value="<?php echo htmlspecialchars($info['adresse']); ?>">
    </div>
    <div class="ml-2col">
        <div class="ml-field">
            <label>Code postal</label>
            <input type="text" name="ccp" maxlength="20" value="<?php echo htmlspecialchars($info['ccp']); ?>">
        </div>
        <div class="ml-field">
            <label>Ville</label>
            <input type="text" name="ville" maxlength="30" value="<?php echo htmlspecialchars($info['ville']); ?>">
        </div>
    </div>

    <div style="font-size:11px;font-weight:bold;letter-spacing:.6px;color:#080A66;text-transform:uppercase;padding-bottom:6px;margin:14px 0 12px;border-bottom:2px solid #e8eaf6;font-family:Electrolize,'Trebuchet MS',Arial">
        <i class="bi bi-person-badge"></i> Responsable
    </div>
    <div class="ml-2col">
        <div class="ml-field">
            <label>Nom <b>*</b></label>
            <input type="text" name="nom" maxlength="50" value="<?php echo htmlspecialchars($info['nom']); ?>" required>
        </div>
        <div class="ml-field">
            <label>Prénom <b>*</b></label>
            <input type="text" name="prenom" maxlength="50" value="<?php echo htmlspecialchars($info['prenom']); ?>" required>
        </div>
        <div class="ml-field">
            <label>Téléphone portable</label>
            <input type="text" name="tel" maxlength="15" value="<?php echo htmlspecialchars($info['tel']); ?>">
        </div>
        <div class="ml-field">
            <label>Email responsable <b>*</b></label>
            <input type="email" name="email" maxlength="90" value="<?php echo htmlspecialchars($info['email']); ?>" required>
        </div>
    </div>
    <div class="ml-field">
        <label>Email de réponse (reply-to)</label>
        <input type="email" name="emailreply" maxlength="90" value="<?php echo htmlspecialchars($info['emailreply']); ?>">
    </div>
    <div class="ml-field">
        <label>Product ID <span style="color:#888;font-weight:normal">(identifiant unique de cet établissement)</span></label>
        <input type="text" name="refclient" maxlength="64" value="<?php echo htmlspecialchars($info['refclient']); ?>" placeholder="ex : 18226e26fd7f4b79c63c6b0d4a62e89a" style="font-family:monospace;font-size:11px">
    </div>

    <div style="margin-top:14px;display:flex;gap:10px;align-items:center">
        <input type="submit" name="create" value="1" id="hidden-submit" style="display:none">
        <button type="button" class="btn-enr" id="btn-save" onclick="
            var btn=document.getElementById('btn-save');
            btn.disabled=true;btn.style.opacity='.7';
            btn.innerHTML='<i class=\'bi bi-hourglass-split\'></i> Enregistrement…';
            document.getElementById('hidden-submit').click();">
            <i class="bi bi-check-circle-fill"></i> Enregistrer les modifications
        </button>
    </div>

</form>

<?php elseif ($onglet === 'log'): ?>
<!-- ════════════════════ HISTORIQUE ════════════════════ -->
<?php if (empty($log)): ?>
<div style="padding:20px;text-align:center;font-size:12px;color:#888;font-family:Electrolize,'Trebuchet MS',Arial">
    <i class="bi bi-inbox" style="font-size:24px;display:block;margin-bottom:8px"></i>
    Aucun email dans l'historique.
</div>
<?php else: ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="cc-th">
    <td style="padding:6px 10px;font-size:11px">Date</td>
    <td style="padding:6px 10px;font-size:11px">Destinataire</td>
    <td style="padding:6px 10px;font-size:11px">Expéditeur</td>
    <td style="padding:6px 10px;font-size:11px">Message</td>
    <td style="padding:6px 10px;font-size:11px;text-align:center">Statut</td>
</tr>
<?php foreach ($log as $row): ?>
<tr class="cc-tr-data">
    <td style="padding:6px 10px;font-size:11px;white-space:nowrap;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['date']); ?></td>
    <td style="padding:6px 10px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['dest']); ?></td>
    <td style="padding:6px 10px;font-size:11px;color:#888;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['emetteur']); ?></td>
    <td style="padding:6px 10px;font-size:11px;color:#555;font-family:Electrolize,'Trebuchet MS',Arial"><?php echo htmlspecialchars($row['message']); ?></td>
    <td style="padding:6px 10px;text-align:center">
        <?php if ($row['status'] == 80): ?>
        <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:2px 9px;font-size:10px;font-family:Electrolize,'Trebuchet MS',Arial"><i class="bi bi-check-circle-fill"></i> envoyé</span>
        <?php else: ?>
        <span style="background:#fdecea;color:#c62828;border-radius:20px;padding:2px 9px;font-size:10px;font-family:Electrolize,'Trebuchet MS',Arial"><i class="bi bi-x-circle-fill"></i> erreur</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php if ($logTotal > $logLimit): ?>
<div style="text-align:center;margin-top:10px">
    <a href="mailing.php?onglet=log&all=1" class="btn-retour" style="text-decoration:none;display:inline-flex;align-items:center;gap:5px;font-size:11px">
        <i class="bi bi-chevron-down"></i> Voir tout (<?php echo $logTotal; ?> emails)
    </a>
</div>
<?php endif; ?>
<?php endif; ?>

<?php elseif ($onglet === 'achat'): ?>
<!-- ════════════════════ CRÉDITER DES EMAILS ════════════════════ -->
<?php if (!empty($tarifs['vacances'])): ?>
<div style="margin-bottom:14px;padding:12px 16px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#856404">
    <i class="bi bi-calendar-x-fill"></i> <b>Service temporairement suspendu.</b>
    Validation possible à partir du <b><?php echo htmlspecialchars($tarifs['vacances_fin']); ?></b>.
</div>
<?php else: ?>
<?php
$frais = isset($tarifs['frais']) ? intval($tarifs['frais']) : 9;
$pk    = isset($tarifs['pk'])    ? $tarifs['pk']            : '';
$offres = isset($tarifs['offres']) ? $tarifs['offres'] : array();
// Encoder les données offres en JSON pour le JS (clé API et MAILINGKEY jamais inclus)
$offresJs = array();
foreach ($offres as $o) {
    $offresJs[] = array(
        'code' => $o['code'],
        'prix' => $o['prix'],
        'nb'   => $o['nb'],
        'btn'  => $o['btn'],
        'total'=> $o['prix'] + $frais,
    );
}
?>
<script>
var ML_OFFRES = <?php echo json_encode($offresJs); ?>;
var ML_FRAIS  = <?php echo $frais; ?>;

function mlSelectOffre(code) {
    var cards = document.querySelectorAll('.ml-offer');
    for (var i = 0; i < cards.length; i++) { cards[i].classList.remove('selected'); }
    document.getElementById('ml-card-' + code).classList.add('selected');
    var btn = document.getElementById('ml-btn-valider');
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';
    btn.setAttribute('data-offre', code);
}

function mlValider() {
    var code = document.getElementById('ml-btn-valider').getAttribute('data-offre');
    if (!code) return;
    var off = null;
    for (var i = 0; i < ML_OFFRES.length; i++) {
        if (ML_OFFRES[i].code === code) { off = ML_OFFRES[i]; break; }
    }
    if (!off) return;

    // Étape 2 : récapitulatif
    document.getElementById('ml-step1').style.display = 'none';
    document.getElementById('ml-s2-nb').textContent    = off.nb.toLocaleString('fr-FR');
    document.getElementById('ml-s2-prix').textContent  = off.prix + ' € HT + ' + ML_FRAIS + ' € (frais)';
    document.getElementById('ml-s2-total').textContent = off.total + ' €';
    document.getElementById('ml-s2-label').textContent = off.nb.toLocaleString('fr-FR') + ' emails — ' + off.total + ' €';

    // Mettre à jour le formulaire caché
    document.getElementById('fm-offre').value = code;
    document.getElementById('ml-step2').style.display = 'block';
}

function mlPayerStripe() {
    document.getElementById('form-mailing-stripe').submit();
}

function mlRetour() {
    document.getElementById('ml-step2').style.display = 'none';
    document.getElementById('ml-step1').style.display = 'block';
}
</script>

<!-- Étape 1 : sélection de l'offre -->
<div id="ml-step1">
    <div style="font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#555;margin-bottom:12px;line-height:1.6">
        Sélectionnez la quantité d'emails à créditer sur votre compte.
        Vos crédits sont valables <b>sans date limite</b>.
        <br>Les frais de mise en service (<b><?php echo $frais; ?> €</b>) s'appliquent à chaque achat.
    </div>

    <div class="ml-offer-grid">
    <?php foreach ($offres as $o): ?>
    <?php
        $prixUnit = round($o['prix'] / $o['nb'] * 1000, 2); // prix pour 1000 emails
        $total    = $o['prix'] + $frais;
        $popular  = ($o['code'] === 'OFFB');
    ?>
    <div class="ml-offer" id="ml-card-<?php echo $o['code']; ?>" onclick="mlSelectOffre('<?php echo $o['code']; ?>')">
        <?php if ($popular): ?>
        <div class="ml-offer-badge"><i class="bi bi-star-fill"></i> Populaire</div><br>
        <?php endif; ?>
        <div class="ml-offer-nb"><?php echo number_format($o['nb'], 0, ',', ' '); ?> emails</div>
        <div class="ml-offer-prix"><?php echo $o['prix']; ?> € HT</div>
        <div class="ml-offer-unit"><?php echo $prixUnit; ?> € / 1 000 emails</div>
        <div style="font-size:11px;color:#546e7a;margin-top:8px;font-family:Electrolize,'Trebuchet MS',Arial">
            Total : <b><?php echo $total; ?> €</b> <span style="color:#aaa">(+ <?php echo $frais; ?> € frais)</span>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:4px">
        <button id="ml-btn-valider" class="btn-enr" onclick="mlValider()" disabled
            style="display:inline-flex;align-items:center;gap:7px;font-size:12px;opacity:.5;cursor:not-allowed">
            <i class="bi bi-arrow-right-circle-fill"></i> Procéder au paiement
        </button>
    </div>
</div>

<!-- Étape 2 : récapitulatif + Stripe -->
<div id="ml-step2" style="display:none">
    <div style="background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:14px 18px;margin-bottom:14px;font-family:Electrolize,'Trebuchet MS',Arial">
        <div style="font-size:12px;font-weight:bold;color:#080A66;margin-bottom:10px"><i class="bi bi-cart-check-fill"></i> Récapitulatif de votre commande</div>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="font-size:12px;color:#555;padding:3px 0">Emails à créditer</td>
            <td style="font-size:12px;font-weight:bold;color:#080A66;text-align:right" id="ml-s2-nb">—</td>
        </tr>
        <tr>
            <td style="font-size:12px;color:#555;padding:3px 0">Détail prix</td>
            <td style="font-size:12px;color:#555;text-align:right" id="ml-s2-prix">—</td>
        </tr>
        <tr>
            <td colspan="2"><hr style="border:none;border-top:1px solid #c5cae9;margin:8px 0"></td>
        </tr>
        <tr>
            <td style="font-size:13px;font-weight:bold;color:#333;padding:3px 0">Total TTC</td>
            <td style="font-size:16px;font-weight:bold;color:#080A66;text-align:right" id="ml-s2-total">—</td>
        </tr>
        </table>
    </div>

    <div style="margin-top:4px;padding-top:12px;border-top:1px solid #e0e3f0;">
        <div style="font-size:11px;color:#888;margin-bottom:8px;font-family:Electrolize,'Trebuchet MS',Arial">Paiement sécurisé via Stripe — achat unique, aucun abonnement.</div>
        <button class="btn-stripe" id="ml-btn-stripe-pay" onclick="mlPayerStripe()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13.97 9.056c0-.78.64-1.08 1.7-1.08 1.52 0 3.44.46 4.96 1.28V5.16C19.1 4.56 17.6 4 15.67 4 11.8 4 9.2 6.04 9.2 9.3c0 5.12 7.04 4.3 7.04 6.5 0 .92-.8 1.22-1.92 1.22-1.66 0-3.78-.68-5.46-1.6v4.06c1.86.8 3.74 1.14 5.46 1.14 4 0 6.74-1.98 6.74-5.3 0-5.52-7.09-4.54-7.09-6.22z"/></svg>
            Payer avec Stripe
        </button>
        <span style="font-size:11px;color:#555;margin-left:10px;font-family:Electrolize,'Trebuchet MS',Arial">
            Offre : <strong id="ml-s2-label">—</strong>
        </span>
    </div>

    <div style="margin-top:10px">
        <button type="button" class="btn-retour" onclick="mlRetour()" style="display:inline-flex;align-items:center;gap:5px;font-size:11px;cursor:pointer">
            <i class="bi bi-arrow-left"></i> Modifier mon choix
        </button>
    </div>

    <div style="margin-top:12px;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:6px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;color:#2e7d32">
        <i class="bi bi-info-circle-fill"></i>
        Les emails sont crédités sur votre compte après confirmation du paiement.
        La facture sera disponible dans l'onglet <b>Factures</b>.
    </div>
</div>
<?php endif; // fin vacation ?>

<?php endif; // fin des onglets ?>

<?php endif; // fin else config-mailing.php ?>

<?php else: ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#856404;text-align:center">
    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo ERREUR1; ?><br><br><i><?php echo ERREUR2; ?></i>
</div>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
