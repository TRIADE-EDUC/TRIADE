<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2023
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.org
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
include_once("./librairie_php/lib_licence.php");

define('_SIGN_API_BASE', 'https://support.triade-educ.org/support/api-sign.php');
define('_SIGN_API_KEY',  'sign-2026-xT5nVqP9');

// ── Handler POST (avant tout output HTML) ──────────────────────────────────
if (!empty($_POST['sign_action']) && LAN == 'oui' && file_exists('../common/config-sign.php')) {
    include_once('../common/config-sign.php');
    if (defined('SIGNKEY')) {
        $idsign_h = SIGNKEY;
        $sa = $_POST['sign_action'];
        $ap = _SIGN_API_BASE;
        $ak = _SIGN_API_KEY;

        if ($sa === 'save_info') {
            $p = array(
                'action'        => 'save_info', 'key' => $ak, 'idsign' => $idsign_h,
                'etablissement' => isset($_POST['etablissement']) ? $_POST['etablissement'] : '',
                'adresse'       => isset($_POST['adresse'])       ? $_POST['adresse']       : '',
                'ville'         => isset($_POST['ville'])         ? $_POST['ville']         : '',
                'ccp'           => isset($_POST['ccp'])           ? $_POST['ccp']           : '',
                'nom'           => isset($_POST['nom'])           ? $_POST['nom']           : '',
                'prenom'        => isset($_POST['prenom'])        ? $_POST['prenom']        : '',
                'email'         => isset($_POST['email'])         ? $_POST['email']         : '',
            );
            $ch = curl_init($ap);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8,
                CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($p),
            ));
            curl_exec($ch); curl_close($ch);
            header('Location: triade-sign.php?sign_msg=info_saved'); exit;
        }

        if ($sa === 'create_compte') {
            $p = array(
                'action' => 'create_compte', 'key' => $ak, 'idsign' => $idsign_h,
                'email'  => isset($_POST['compte_email']) ? $_POST['compte_email'] : '',
                'mdp'    => isset($_POST['compte_mdp'])   ? $_POST['compte_mdp']   : '',
            );
            $ch = curl_init($ap);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8,
                CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($p),
            ));
            $rsp = curl_exec($ch); curl_close($ch);
            $r   = $rsp ? json_decode($rsp, true) : null;
            $msg = ($r && !empty($r['ok'])) ? 'compte_created' : 'compte_error';
            header('Location: triade-sign.php?sign_msg=' . $msg); exit;
        }

        if ($sa === 'delete_compte') {
            $p = array(
                'action' => 'delete_compte', 'key' => $ak, 'idsign' => $idsign_h,
                'id'     => isset($_POST['compte_id']) ? intval($_POST['compte_id']) : 0,
            );
            $ch = curl_init($ap);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8,
                CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($p),
            ));
            curl_exec($ch); curl_close($ch);
            header('Location: triade-sign.php?sign_msg=compte_deleted'); exit;
        }

        if ($sa === 'init_purchase') {
            $offre_p = isset($_POST['offre_achat']) ? trim($_POST['offre_achat']) : '';
            $allowed = array('OFFA', 'OFFB', 'OFFC', 'OFFD');
            if ($offre_p && in_array($offre_p, $allowed)) {
                $proto      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $selfUrl    = $proto . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/triade-sign.php';
                $successUrl = $selfUrl . '?sign_msg=payment_success';
                $cancelUrl  = $selfUrl . '?sign_msg=payment_cancelled';
                $p = array(
                    'action'      => 'create_checkout', 'key' => $ak, 'idsign' => $idsign_h,
                    'offre'       => $offre_p,
                    'success_url' => $successUrl,
                    'cancel_url'  => $cancelUrl,
                );
                $ch = curl_init($ap);
                curl_setopt_array($ch, array(
                    CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
                    CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($p),
                ));
                $rsp  = curl_exec($ch); curl_close($ch);
                $resp = $rsp ? json_decode($rsp, true) : null;
                if ($resp && !empty($resp['stripe_url'])) {
                    header('Location: ' . $resp['stripe_url']); exit;
                }
                $detail = ($resp && !empty($resp['error'])) ? urlencode($resp['error']) : '';
                header('Location: triade-sign.php?sign_msg=checkout_error&detail=' . $detail); exit;
            }
        }

        if ($sa === 'reset_account') {
            $cfg = '../common/config-sign.php';
            if (file_exists($cfg)) { unlink($cfg); }
            header('Location: triade-sign.php?sign_msg=account_reset'); exit;
        }
    }
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
<link rel="stylesheet" href="../librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<title>Triade — TRIADE-SIGN</title>
<style>
.sign-offer-grid { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.sign-offer-card {
  flex:1 1 130px; border:2px solid #c5cae9; border-radius:8px;
  padding:10px 12px; cursor:pointer; background:#fff; transition:border-color .15s, background .15s;
  text-align:center; font-family:Electrolize,Trebuchet MS,Arial,sans-serif;
}
.sign-offer-card:hover { border-color:#080A66; background:#f5f7ff; }
.sign-offer-card.selected { border-color:#080A66; background:#eef0ff; }
.sign-offer-card input[type=radio] { display:none; }
.sign-offer-nb { font-size:18px; font-weight:700; color:#080A66; }
.sign-offer-prix { font-size:12px; color:#555; margin-top:4px; }
.sign-offer-prix b { color:#333; font-size:14px; }
.btn-stripe {
  background:#635bff; color:#fff; border:none; padding:9px 22px;
  border-radius:6px; font-size:13px; font-weight:bold; cursor:pointer; margin-top:10px;
  display:inline-flex; align-items:center; gap:7px;
}
.btn-stripe:hover:not(:disabled) { background:#4f46e5; }
.btn-stripe:disabled { background:#b0adf5; cursor:not-allowed; opacity:.7; }
.sign-form-grid { display:flex; flex-wrap:wrap; gap:8px; }
.sign-form-grid .sf-field { flex:1 1 180px; }
.sign-form-grid label { display:block; font-size:11px; color:#555; margin-bottom:3px; }
.sign-form-grid input[type=text], .sign-form-grid input[type=email],
.sign-form-grid input[type=password] {
  width:100%; padding:5px 8px; border:1px solid #c8cfe8; border-radius:4px;
  font-size:12px; font-family:Electrolize,Trebuchet MS,Arial,sans-serif;
  box-sizing:border-box;
}
.sign-accounts-table { width:100%; border-collapse:collapse; font-size:12px; }
.sign-accounts-table td { padding:5px 8px; border-bottom:1px solid #e8eaf0; }
.sign-accounts-table tr:last-child td { border-bottom:none; }
.sign-accounts-table .sa-email { color:#333; }
.sign-sep { border:none; border-top:1px solid #e8eaf0; margin:10px 0; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>TRIADE-SIGN</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php if (LAN == "oui"): ?>
<?php
include_once("../librairie_php/db_triade.php");
valideProductId();
include_once("../common/config2.inc.php");
// Flash global (account_reset survient avant que config-sign.php soit rechargé)
$_gMsg = isset($_GET['sign_msg']) ? $_GET['sign_msg'] : '';
if ($_gMsg === 'account_reset'):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  alertify.success('Configuration TRIADE-SIGN supprimée. Vous pouvez vous réinscrire.');
});
</script>
<?php endif; ?>

    <?php if (!file_exists("../common/config-sign.php")): ?>
    <?php
    if (file_exists("../common/productid.php")) include_once("../common/productid.php");
    $productId = defined('PRODUCTID') ? PRODUCTID : '';
    ?>
    <div style="margin:12px 8px 14px;padding:14px 18px;background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;">
        Vous n'avez aucun compte créé pour TRIADE-SIGN.<br><br>
        <form method="post" action="sign-inscription.php">
            <input type="hidden" name="productid" value="<?php echo htmlspecialchars($productId); ?>">
            <br><br><input type="submit" name="create" value="Inscription Gratuite" class="btn-enr">
        </form>
    </div>

    <?php else: ?>
    <?php
    include_once("../common/config-sign.php");
    $idsign = SIGNKEY;

    // ── Données de base (get_info) ──────────────────────────────────────────
    $ch = curl_init(_SIGN_API_BASE . '?action=get_info&key=' . _SIGN_API_KEY . '&idsign=' . urlencode($idsign));
    curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8));
    $resp  = curl_exec($ch);
    $errno = curl_errno($ch);
    curl_close($ch);

    $info   = ($resp && !$errno) ? json_decode($resp, true) : null;
    $apiErr = '';
    if (!$info || empty($info['ok'])) {
        $apiErr = ($info && isset($info['error'])) ? $info['error'] : 'Impossible de contacter le serveur TRIADE-SIGN.';
    }

    // ── Données complémentaires (si compte OK) ──────────────────────────────
    $signTarifs = null;
    $signFact   = null;
    $signComp   = null;
    $signVatel  = 0;
    if (!$apiErr) {
        $signVatel = (isset($info['vatel']) && $info['vatel'] == 1) ? 1 : 0;
        $ap = _SIGN_API_BASE;
        $ak = _SIGN_API_KEY;

        $ch = curl_init($ap . '?action=get_tarifs&key=' . $ak . '&vatel=' . $signVatel);
        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5));
        $rsp = curl_exec($ch); curl_close($ch);
        $signTarifs = $rsp ? json_decode($rsp, true) : null;

        $ch = curl_init($ap . '?action=get_factures&key=' . $ak . '&idsign=' . urlencode($idsign));
        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5));
        $rsp = curl_exec($ch); curl_close($ch);
        $signFact = $rsp ? json_decode($rsp, true) : null;

        $ch = curl_init($ap . '?action=get_comptes&key=' . $ak . '&idsign=' . urlencode($idsign));
        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5));
        $rsp = curl_exec($ch); curl_close($ch);
        $signComp = $rsp ? json_decode($rsp, true) : null;
    }

    // Message flash
    $signMsg = isset($_GET['sign_msg']) ? $_GET['sign_msg'] : '';
    ?>

    <?php if ($apiErr): ?>
    <div class="alert alert-warning" style="margin:10px 0;">
        <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($apiErr); ?>
    </div>
    <?php else: ?>

    <div style="display:flex;flex-direction:column;gap:8px;padding:4px 0;">

      <?php if ($signMsg): ?>
      <script>
      document.addEventListener('DOMContentLoaded', function() {
        <?php if ($signMsg === 'info_saved'): ?>
          alertify.success('Informations enregistrées.');
        <?php elseif ($signMsg === 'compte_created'): ?>
          alertify.success('Compte utilisateur créé.');
        <?php elseif ($signMsg === 'compte_error'): ?>
          alertify.error('Erreur : email déjà utilisé ou invalide.');
        <?php elseif ($signMsg === 'compte_deleted'): ?>
          alertify.success('Compte utilisateur supprimé.');
        <?php elseif ($signMsg === 'account_reset'): ?>
          alertify.success('Configuration TRIADE-SIGN supprimée.');
        <?php elseif ($signMsg === 'payment_success'): ?>
          alertify.success('Paiement confirmé ! Vos signatures seront créditées automatiquement dans quelques instants.');
        <?php elseif ($signMsg === 'payment_cancelled'): ?>
          alertify.error('Paiement annulé.');
        <?php elseif ($signMsg === 'inscription_success'): ?>
          alertify.success('Inscription TRIADE-SIGN effectuée. Bienvenue !');
        <?php elseif ($signMsg === 'checkout_error'): ?>
          alertify.error('<?php $d = isset($_GET['detail']) ? addslashes(urldecode($_GET['detail'])) : ''; echo $d ? $d : 'Impossible de créer la session de paiement. Veuillez réessayer.'; ?>');
        <?php endif; ?>
      });
      </script>
      <?php endif; ?>

      <!-- ── Crédit tokens ─────────────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-pen-fill" style="margin-right:6px;"></i>Signatures électroniques</span>
        </div>
        <div class="card-body" style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
          <div style="text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#080A66;"><?php echo intval($info['nbtokencredit']); ?></div>
            <div style="font-size:11px;color:#666;">disponible<?php echo $info['nbtokencredit'] > 1 ? 's' : ''; ?></div>
          </div>
          <div style="text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#444;"><?php echo intval($info['nbtokenenrg']); ?></div>
            <div style="font-size:11px;color:#666;">utilisée<?php echo $info['nbtokenenrg'] > 1 ? 's' : ''; ?></div>
          </div>
          <?php if ($info['nbtokenwait'] > 0): ?>
          <div style="text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#e67e22;"><?php echo intval($info['nbtokenwait']); ?></div>
            <div style="font-size:11px;color:#666;">en attente</div>
          </div>
          <?php endif; ?>
          <?php if ($info['refclient']): ?>
          <div style="margin-left:auto;font-size:11px;color:#999;">
            Réf. <b><?php echo htmlspecialchars($info['refclient']); ?></b>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Établissement ─────────────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-building" style="margin-right:6px;"></i>Établissement</span>
        </div>
        <div class="card-body" style="font-size:12px;line-height:1.7;">
          <b><?php echo htmlspecialchars($info['etablissement']); ?></b><br>
          <?php if ($info['adresse']): ?><?php echo htmlspecialchars($info['adresse']); ?><br><?php endif; ?>
          <?php echo htmlspecialchars($info['ccp']); ?> <?php echo htmlspecialchars($info['ville']); ?>
          <?php if ($info['pays']): ?> &mdash; <?php echo htmlspecialchars($info['pays']); ?><?php endif; ?>
        </div>
      </div>

      <!-- ── Responsable ───────────────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-person-fill" style="margin-right:6px;"></i>Responsable</span>
        </div>
        <div class="card-body" style="font-size:12px;line-height:1.7;">
          <b><?php echo htmlspecialchars(strtoupper($info['nom'])); ?> <?php echo htmlspecialchars(ucwords($info['prenom'])); ?></b><br>
          <i class="bi bi-envelope" style="color:#080A66;margin-right:4px;"></i><?php echo htmlspecialchars($info['email']); ?>
        </div>
      </div>

      <!-- ── Acheter des signatures ─────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-cart-fill" style="margin-right:6px;"></i>Acheter des signatures</span>
        </div>
        <div class="card-body">

          <?php if ($signVatel): ?>
          <!-- VATEL : contact commercial -->
          <div style="font-size:12px;color:#555;line-height:1.7;">
            <i class="bi bi-envelope" style="color:#080A66;margin-right:5px;"></i>
            Pour créditer votre compte, contactez notre service commercial :<br>
            <a href="mailto:support@triade-educ.org" style="color:#080A66;font-weight:700;">support@triade-educ.org</a>
          </div>

          <?php elseif ($signTarifs && !empty($signTarifs['offers'])): ?>
          <!-- Sélection de l'offre + paiement direct Stripe -->
          <form method="post">
            <input type="hidden" name="sign_action" value="init_purchase">
            <div style="font-size:11px;color:#666;margin-bottom:8px;">
              Sélectionnez une offre puis cliquez sur <b>Payer avec Stripe</b>.
              Les frais de service de <?php echo intval($signTarifs['frais']); ?> € sont inclus dans le total.
            </div>
            <div class="sign-offer-grid">
              <?php foreach ($signTarifs['offers'] as $offer): ?>
              <label class="sign-offer-card" for="soff_<?php echo $offer['offre']; ?>">
                <input type="radio" id="soff_<?php echo $offer['offre']; ?>"
                       name="offre_achat" value="<?php echo $offer['offre']; ?>" required>
                <div class="sign-offer-nb"><?php echo number_format($offer['nb'], 0, ',', ' '); ?></div>
                <div style="font-size:10px;color:#555;margin-top:2px;">signatures</div>
                <div class="sign-offer-prix"><b><?php echo $offer['prix']; ?> €</b></div>
                <div style="font-size:10px;color:#999;">+ <?php echo $offer['frais']; ?> € frais</div>
                <div style="font-size:12px;font-weight:700;color:#080A66;margin-top:4px;border-top:1px solid #e0e3f5;padding-top:4px;">
                  = <?php echo $offer['total']; ?> €
                </div>
              </label>
              <?php endforeach; ?>
            </div>
            <div style="margin-top:2px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
              <button type="submit" class="btn-stripe">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13.97 9.056c0-.78.64-1.08 1.7-1.08 1.52 0 3.44.46 4.96 1.28V5.16C19.1 4.56 17.6 4 15.67 4 11.8 4 9.2 6.04 9.2 9.3c0 5.12 7.04 4.3 7.04 6.5 0 .92-.8 1.22-1.92 1.22-1.66 0-3.78-.68-5.46-1.6v4.06c1.86.8 3.74 1.14 5.46 1.14 4 0 6.74-1.98 6.74-5.3 0-5.52-7.09-4.54-7.09-6.22z"/></svg>
                Payer avec Stripe
              </button>
              <div id="sign-recap" style="display:none;font-size:12px;color:#333;background:#eef0ff;border:1px solid #c5cae9;border-radius:6px;padding:5px 12px;line-height:1.6;">
                <span id="sign-recap-nb" style="font-weight:700;color:#080A66;"></span>
                <span style="color:#555;margin:0 4px;">—</span>
                <span id="sign-recap-prix"></span>
                <span style="color:#999;font-size:11px;"> + <?php echo intval($signTarifs['frais']); ?> € frais =</span>
                <span id="sign-recap-total" style="font-weight:700;color:#080A66;"></span>
              </div>
            </div>
          </form>
          <script>
          (function() {
            var recap   = document.getElementById('sign-recap');
            var recapNb = document.getElementById('sign-recap-nb');
            var recapPx = document.getElementById('sign-recap-prix');
            var recapTt = document.getElementById('sign-recap-total');
            var data = <?php
              $offersJs = array();
              foreach ($signTarifs['offers'] as $o) {
                  $offersJs[$o['offre']] = array('nb' => $o['nb'], 'prix' => $o['prix'], 'total' => $o['total']);
              }
              echo json_encode($offersJs);
            ?>;
            document.querySelectorAll('.sign-offer-card').forEach(function(card) {
              card.addEventListener('click', function() {
                document.querySelectorAll('.sign-offer-card').forEach(function(c) { c.classList.remove('selected'); });
                this.classList.add('selected');
                var radio = this.querySelector('input[type=radio]');
                if (!radio) return;
                var d = data[radio.value];
                if (!d) return;
                recapNb.textContent = d.nb.toLocaleString('fr-FR') + ' signatures';
                recapPx.textContent = d.prix + ' €';
                recapTt.textContent = d.total + ' €';
                recap.style.display = 'block';
              });
            });
          })();
          </script>

          <?php else: ?>
          <div style="font-size:12px;color:#999;font-style:italic;">Tarifs temporairement indisponibles.</div>
          <?php endif; ?>

        </div>
      </div>

      <!-- ── Gestion des comptes ───────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-people-fill" style="margin-right:6px;"></i>Gestion des comptes
            <?php if ($signComp && !empty($signComp['comptes'])): ?>
            <span class="card-badge"><?php echo count($signComp['comptes']); ?></span>
            <?php endif; ?>
          </span>
        </div>
        <div class="card-body">
          <?php if ($signComp && !empty($signComp['comptes'])): ?>
          <table class="sign-accounts-table">
            <?php foreach ($signComp['comptes'] as $cpt): ?>
            <tr>
              <td class="sa-email"><i class="bi bi-person" style="color:#080A66;margin-right:5px;"></i><?php echo htmlspecialchars($cpt['email']); ?></td>
              <td style="text-align:right;white-space:nowrap;">
                <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce compte ?');">
                  <input type="hidden" name="sign_action" value="delete_compte">
                  <input type="hidden" name="compte_id" value="<?php echo intval($cpt['id']); ?>">
                  <button type="submit" class="btn btn-danger" style="font-size:11px;padding:3px 9px;">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </table>
          <hr class="sign-sep">
          <?php else: ?>
          <div style="font-size:12px;color:#888;font-style:italic;margin-bottom:10px;">Aucun compte utilisateur pour l'instant.</div>
          <?php endif; ?>
          <div style="font-size:11px;font-weight:700;color:#080A66;margin-bottom:6px;">
            <i class="bi bi-plus-circle"></i> Créer un compte
          </div>
          <form method="post">
            <input type="hidden" name="sign_action" value="create_compte">
            <div class="sign-form-grid">
              <div class="sf-field">
                <label>Email</label>
                <input type="email" name="compte_email" placeholder="email@exemple.com" required>
              </div>
              <div class="sf-field">
                <label>Mot de passe</label>
                <input type="text" name="compte_mdp" placeholder="Mot de passe" required>
              </div>
            </div>
            <div style="margin-top:8px;">
              <input type="submit" name="create" value="Créer le compte" class="btn btn-primary">
            </div>
          </form>
        </div>
      </div>

      <!-- ── Vos factures ──────────────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-receipt" style="margin-right:6px;"></i>Vos factures
            <?php if ($signFact && !empty($signFact['factures'])): ?>
            <span class="card-badge"><?php echo count($signFact['factures']); ?></span>
            <?php endif; ?>
          </span>
        </div>
        <div class="card-body">
          <?php if (!$signFact || empty($signFact['factures'])): ?>
          <div style="font-size:12px;color:#888;font-style:italic;">Aucune facture disponible.</div>
          <?php else: ?>
          <table class="table" style="margin:0;font-size:12px;">
            <thead>
              <tr>
                <th class="cc-th">Date</th>
                <th class="cc-th">Référence</th>
                <th class="cc-th" style="text-align:right;">Montant</th>
                <th class="cc-th" style="width:60px;"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($signFact['factures'] as $fac): ?>
              <tr class="cc-tr-data">
                <td><?php echo htmlspecialchars($fac['date']); ?></td>
                <td style="font-family:monospace;color:#555;"><?php echo htmlspecialchars($fac['ref']); ?></td>
                <td style="text-align:right;font-weight:700;"><?php echo htmlspecialchars($fac['montant']); ?> €</td>
                <td style="text-align:center;">
                  <?php if ($fac['fichier']): ?>
                  <a href="https://support.triade-educ.org/support/sign-dl-facture.php?ref=<?php echo urlencode($fac['fichier']); ?>&idsign=<?php echo urlencode($idsign); ?>&key=sign-2026-xT5nVqP9"
                     target="_blank" class="btn" style="font-size:11px;padding:3px 8px;background:#f5f7ff;color:#080A66;border:1px solid #c5cae9;">
                    <i class="bi bi-file-pdf"></i> PDF
                  </a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Vos paramètres ────────────────────────────────────────────── -->
      <div class="card">
        <div class="card-header card-header-primary">
          <span><i class="bi bi-gear-fill" style="margin-right:6px;"></i>Vos paramètres</span>
        </div>
        <div class="card-body">
          <form method="post">
            <input type="hidden" name="sign_action" value="save_info">
            <div class="sign-form-grid">
              <div class="sf-field" style="flex:2 1 260px;">
                <label>Établissement</label>
                <input type="text" name="etablissement" value="<?php echo htmlspecialchars($info['etablissement']); ?>">
              </div>
              <div class="sf-field">
                <label>Code postal</label>
                <input type="text" name="ccp" value="<?php echo htmlspecialchars($info['ccp']); ?>">
              </div>
              <div class="sf-field">
                <label>Ville</label>
                <input type="text" name="ville" value="<?php echo htmlspecialchars($info['ville']); ?>">
              </div>
              <div class="sf-field" style="flex:2 1 260px;">
                <label>Adresse</label>
                <input type="text" name="adresse" value="<?php echo htmlspecialchars($info['adresse']); ?>">
              </div>
              <div class="sf-field">
                <label>Nom</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($info['nom']); ?>">
              </div>
              <div class="sf-field">
                <label>Prénom</label>
                <input type="text" name="prenom" value="<?php echo htmlspecialchars($info['prenom']); ?>">
              </div>
              <div class="sf-field" style="flex:2 1 260px;">
                <label>Email de contact</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($info['email']); ?>">
              </div>
            </div>
            <div style="margin-top:10px;">
              <input type="submit" name="save" value="Enregistrer" class="btn btn-primary">
            </div>
          </form>
          <hr class="sign-sep" style="margin-top:16px;">
          <div style="font-size:11px;font-weight:700;color:#c62828;margin-bottom:6px;">
            <i class="bi bi-exclamation-triangle"></i> Zone dangereuse
          </div>
          <div style="font-size:11px;color:#666;margin-bottom:8px;line-height:1.5;">
            La réinitialisation dissocie cette installation du compte TRIADE-SIGN.<br>
            <b>Attention :</b> vous perdrez l'accès à tous vos crédits de signature non utilisés.<br>
            Vos données restent sur le serveur central ; vous pourrez vous réinscrire.
          </div>
          <button type="button" class="btn btn-danger"
                  style="font-size:11px;padding:4px 12px;"
                  onclick="signConfirmReset()">
            <i class="bi bi-trash"></i> Réinitialiser le compte TRIADE-SIGN
          </button>
          <form id="sign-reset-form" method="post" style="display:none;">
            <input type="hidden" name="sign_action" value="reset_account">
          </form>
          <script>
          function signConfirmReset() {
            alertify.confirm(
              'Réinitialisation TRIADE-SIGN',
              'Cette action supprime la configuration TRIADE-SIGN de cette installation.<br><br>' +
              '<b>Vous perdrez l\'accès à tous vos crédits de signature non utilisés.</b><br>' +
              'Vos données restent sur le serveur central.<br><br>' +
              'Confirmer la réinitialisation ?',
              function() { document.getElementById('sign-reset-form').submit(); },
              function() {}
            );
          }
          </script>
        </div>
      </div>

    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (defined('AFFICHAGESIGN') && AFFICHAGESIGN != "oui"): ?>
    <div style="margin:0 8px 14px;padding:10px 14px;background:#fff3e0;border:1px solid #ffcc80;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;">
        &#9888; Vous devez activer l'autorisation d'utilisation de TRIADE-SIGN dans le module
        <a href="configuration.php" style="color:#c0392b;font-weight:700;">Config. Générale</a>.
    </div>
    <?php endif; ?>

<?php else: ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;text-align:center;">
    &#9888; <?php echo ERREUR1; ?><br><br><i><?php echo ERREUR2; ?></i>
</div>
<?php endif; ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
