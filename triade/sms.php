<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<title>Triade — Gestion des SMS</title>
<style>
.sm-wrap{display:flex;flex-direction:column;gap:12px;padding:10px 6px}
.sm-card{background:#fff;border:1px solid #dde0f0;border-radius:10px;padding:16px 18px;border-top:4px solid #080A66}
.sm-card.green{border-top-color:#2e7d32}
.sm-card.grey{border-top-color:#607d8b}
.sm-card.amber{border-top-color:#e65100}
.sm-card-title{font-size:13px;font-weight:800;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;display:flex;align-items:center;gap:8px;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #eef0f8}
.sm-card.green .sm-card-title{color:#2e7d32}
.sm-card.grey  .sm-card-title{color:#37474f}
.sm-card.amber .sm-card-title{color:#e65100}
.sm-solde{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.sm-badge{padding:4px 14px;border-radius:20px;font-weight:700;font-size:13px}
.badge-ok{background:#d4edda;color:#155724}
.badge-low{background:#fff3cd;color:#856404}
.badge-ko{background:#f8d7da;color:#721c24}
.badge-info{background:#e3f2fd;color:#1565c0;font-size:11px;font-weight:600}
.badge-sub{background:#f3e5f5;color:#6a1b9a;font-size:11px;font-weight:600}
.sm-offres{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px}
.sm-offre-bloc{border:2px solid #dde0f0;border-radius:8px;padding:10px 12px}
.sm-offre-titre{font-size:12px;font-weight:800;color:#080A66;margin-bottom:4px;font-family:Electrolize,Trebuchet MS,Arial}
.sm-offre-detail{font-size:11px;color:#555;line-height:1.7;margin-bottom:8px}
.sm-packs-row{display:flex;gap:6px;flex-wrap:wrap}
.sm-pack{flex:1;min-width:80px;border:2px solid #c5caee;border-radius:6px;padding:8px 10px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s}
.sm-pack:hover,.sm-pack.selected{border-color:#080A66;background:#f0f2fa}
.sm-pack.disabled{opacity:.5;cursor:default}
.sm-pack-nb{font-size:14px;font-weight:800;color:#080A66}
.sm-pack-prix{font-size:11px;color:#666}
.sm-pack-dispo{font-size:10px;color:#ce93d8;font-style:italic}
.sm-sub-row{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.sm-sub-pill{padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700}
.sub-actif{background:#e8f5e9;color:#2e7d32}
.sub-cancel{background:#fff3cd;color:#856404}
.sub-inactif{background:#fce4ec;color:#880e4f}
.sm-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px 14px;font-size:12px;color:#333;margin-bottom:10px}
.sm-info-label{color:#888;font-size:11px}
.sm-info-val{font-weight:600}
.sm-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.sm-form-grid .full{grid-column:1/-1}
.sm-form-grid input{width:100%;border:1px solid #c5caee;border-radius:6px;padding:6px 8px;font-size:12px;box-sizing:border-box}
.sm-log-table{width:100%;border-collapse:collapse;font-size:11px}
.sm-log-table th{background:#f0f2fa;color:#080A66;font-weight:700;padding:5px 8px;text-align:left;border-bottom:2px solid #c5caee}
.sm-log-table td{padding:5px 8px;border-bottom:1px solid #f0f0f0;vertical-align:top}
.sm-log-table tr:hover td{background:#f8f9ff}
.sm-ok{color:#2e7d32;font-weight:700}
.sm-err{color:#c62828;font-weight:700}
.sm-fact-table{width:100%;border-collapse:collapse;font-size:11px}
.sm-fact-table th{background:#f0f2fa;color:#080A66;font-weight:700;padding:5px 8px;text-align:left}
.sm-fact-table td{padding:5px 8px;border-bottom:1px solid #f0f0f0}
.btn-sm{display:inline-flex;align-items:center;gap:5px;border:none;border-radius:7px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;font-family:Electrolize,Trebuchet MS,Arial;text-decoration:none}
.btn-sm-blue{background:#080A66;color:#fff}
.btn-sm-blue:hover{background:#0b0f8a;color:#fff}
.btn-sm-ghost{background:#f0f2fa;color:#080A66;border:1px solid #c5caee}
.btn-sm-ghost:hover{background:#e4e8f7}
.btn-sm-green{background:#2e7d32;color:#fff}
.btn-sm-green:hover{background:#1b5e20;color:#fff}
.btn-sm-red{background:#c62828;color:#fff}
.btn-sm-red:hover{background:#b71c1c;color:#fff}
.btn-sm-purple{background:#6a1b9a;color:#fff}
.btn-sm-purple:hover{background:#4a148c;color:#fff}
.btn-stripe{background:#635bff;color:#fff;border:none;padding:9px 22px;border-radius:6px;font-size:13px;font-weight:bold;cursor:pointer;display:inline-flex;align-items:center;gap:7px}
.btn-stripe:hover:not(:disabled){background:#4f46e5}
.btn-stripe:disabled{background:#b0adf5;cursor:not-allowed;opacity:.7}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS215 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
$productid = htmlspecialchars(isset($_GET['productid']) ? $_GET['productid'] : (defined('PRODUCTID') ? PRODUCTID : ''));
$url       = htmlspecialchars(isset($_GET['url'])       ? $_GET['url']       : $_SERVER['SERVER_NAME']);

// Messages Stripe
$stripeMsg = ''; $stripeMsgType = '';
if (isset($_GET['stripe'])) {
    switch ($_GET['stripe']) {
        case 'success':
            $stripeMsg = 'Paiement confirmé. Vos SMS seront crédités sous quelques instants.';
            $stripeMsgType = 'success'; break;
        case 'cancel':
            $stripeMsg = 'Paiement annulé. Aucun prélèvement effectué.';
            $stripeMsgType = 'error'; break;
        case 'error':
            $stripeMsg = 'Erreur : ' . htmlspecialchars(isset($_GET['msg']) ? $_GET['msg'] : 'impossible de créer la session.');
            $stripeMsgType = 'error'; break;
        case 'sub_ok':
            $stripeMsg = 'Abonnement activé ! Vos 200 SMS mensuels seront crédités sous quelques instants.';
            $stripeMsgType = 'success'; break;
        case 'sub_cancel':
            $stripeMsg = 'Souscription annulée. Aucun prélèvement effectué.';
            $stripeMsgType = 'error'; break;
        case 'sub_cancelled':
            $until = htmlspecialchars(isset($_GET['until']) ? $_GET['until'] : '');
            $stripeMsg = 'Résiliation programmée.' . ($until ? ' Vos SMS restent disponibles jusqu\'au ' . $until . '.' : '') . ' Le solde sera remis à zéro à l\'échéance.';
            $stripeMsgType = 'success'; break;
    }
}
$infoMsg = ''; $infoMsgType = '';
if (isset($_GET['info'])) {
    switch ($_GET['info']) {
        case 'success': $infoMsg = 'Informations enregistrées.'; $infoMsgType = 'success'; break;
        case 'error':   $infoMsg = 'Erreur : ' . htmlspecialchars(isset($_GET['msg']) ? $_GET['msg'] : 'impossible de sauvegarder.'); $infoMsgType = 'error'; break;
    }
}

if (isset($_GET["init"])):
?>
<div style="margin:10px 8px;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:6px;font-size:12px;color:#2e7d32;">
    Votre compte a été réinitialisé. Vous pouvez effectuer une nouvelle inscription.
</div>
<?php endif; ?>

<?php if (LAN == "oui"): ?>

<?php if (!file_exists("./common/config-sms.php")): ?>
<div class="sm-wrap">
  <div class="sm-card">
    <div class="sm-card-title"><i class="bi bi-chat-dots"></i> Compte SMS</div>
    <div style="font-size:12px;color:#555;margin-bottom:12px">
      Vous n'avez aucun compte SMS. Créez-en un gratuitement pour envoyer des SMS depuis TRIADE.
    </div>
    <form method="post" action="./admin/sms-inscription.php">
      <button type="submit" name="create" class="btn-sm btn-sm-blue">
        <i class="bi bi-plus-circle"></i> Inscription gratuite
      </button>
    </form>
  </div>
</div>

<?php else:
    include_once("./common/config-sms.php");
    $idsms  = defined('SMSKEY') ? SMSKEY : '';
    $apiKey = 'sms-2026-xR7nPqK2';

    function smsApiGet($action, $idsms, $apiKey, $extra = '') {
        $url = 'https://triade-educ.org/sms/api.php?key=' . urlencode($apiKey)
             . '&idsms=' . urlencode($idsms) . '&action=' . urlencode($action) . $extra;
        $ch  = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $r = curl_exec($ch); curl_close($ch);
        return $r ? json_decode($r, true) : array();
    }

    $emailCompte = '';
    if (!empty($_SESSION['id_pers'])) {
        include_once("./librairie_php/db_triade.php");
        global $prefixe;
        $cnx = cnx();
        $idp = intval($_SESSION['id_pers']);
        $res = execSql("SELECT email FROM {$prefixe}personnel WHERE pers_id=$idp");
        if ($res && !DB::isError($res)) {
            $mat = chargeMat($res);
            if (!empty($mat[0][0])) $emailCompte = $mat[0][0];
        }
        Pgclose();
    }

    $smsInfo = null;
    $d = smsApiGet('get_info', $idsms, $apiKey);
    if (!empty($d['ok'])) $smsInfo = $d;

    $smsPacks = array(); $tarif_top = '0.13'; $tarif_direct = '0.109';
    $subPlan  = array('actif' => false, 'prix' => 20, 'nb' => 200);
    if (!isset($_SESSION['sms_packs_ts']) || (time() - $_SESSION['sms_packs_ts']) > 300) {
        $ch = curl_init('https://support.triade-educ.org/support/sms-tarifs-api.php?action=get-plans');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $pr = curl_exec($ch); curl_close($ch);
        if ($pr) {
            $pd = json_decode($pr, true);
            if (!empty($pd['ok'])) { $_SESSION['sms_packs_cache'] = $pd; $_SESSION['sms_packs_ts'] = time(); }
        }
    }
    if (!empty($_SESSION['sms_packs_cache'])) {
        $smsPacks     = isset($_SESSION['sms_packs_cache']['packs'])        ? $_SESSION['sms_packs_cache']['packs']        : array();
        $tarif_top    = isset($_SESSION['sms_packs_cache']['tarif_top'])    ? $_SESSION['sms_packs_cache']['tarif_top']    : '0.13';
        $tarif_direct = isset($_SESSION['sms_packs_cache']['tarif_direct']) ? $_SESSION['sms_packs_cache']['tarif_direct'] : '0.109';
        $subPlan      = isset($_SESSION['sms_packs_cache']['sub'])          ? $_SESSION['sms_packs_cache']['sub']          : $subPlan;
    }
    if (empty($smsPacks)) {
        $smsPacks = array(
            array('pack'=>'TOP300',    'offre'=>'OFF0','qualite'=>'SMS-TOP',    'nb'=>300,  'prix'=>48,  'actif'=>false),
            array('pack'=>'TOP500',    'offre'=>'OFF0','qualite'=>'SMS-TOP',    'nb'=>500,  'prix'=>74,  'actif'=>false),
            array('pack'=>'DIRECT1000','offre'=>'OFF1','qualite'=>'SMS-DIRECT', 'nb'=>1000, 'prix'=>118, 'actif'=>false),
            array('pack'=>'DIRECT2000','offre'=>'OFF1','qualite'=>'SMS-DIRECT', 'nb'=>2000, 'prix'=>227, 'actif'=>false),
        );
    }
    $packsByOffre = array('OFF0' => array(), 'OFF1' => array());
    foreach ($smsPacks as $sp) $packsByOffre[$sp['offre']][] = $sp;

    $subInfo = array('found' => false);
    if (!empty($subPlan['actif'])) {
        $ds = smsApiGet('get_sub', $idsms, $apiKey);
        if (!empty($ds['ok'])) $subInfo = $ds;
    }

    $allLogs  = !empty($_GET['all_logs']);
    $logLimit = $allLogs ? 0 : 10;
    $smsLogs  = array();
    $ld = smsApiGet('get_logs', $idsms, $apiKey, '&limit=' . $logLimit);
    if (!empty($ld['ok'])) $smsLogs = $ld['logs'];

    $smsFact = array();
    $fd = smsApiGet('get_factures', $idsms, $apiKey);
    if (!empty($fd['ok'])) $smsFact = $fd['factures'];

    $nbSms    = $smsInfo ? $smsInfo['nbsmscredit'] : null;
    $nbEnvoye = $smsInfo ? $smsInfo['nbsmsenrg']   : 0;
    $aboLabel = $smsInfo ? $smsInfo['abonnement']  : '';
    $factProxyBase = 'https://triade-educ.org/sms/facture.php?key=' . urlencode($apiKey) . '&idsms=' . urlencode($idsms) . '&fichier=';
?>
<div class="sm-wrap">

  <!-- ── Solde SMS ──────────────────────────────────────────────────── -->
  <?php if ($nbSms !== null): ?>
  <div class="sm-card">
    <div class="sm-card-title"><i class="bi bi-chat-dots-fill"></i> Solde SMS</div>
    <div class="sm-solde">
      <div style="font-size:12px;color:#555">SMS disponibles</div>
      <?php
        $bClass = 'badge-ko';
        if ($nbSms > 500)   $bClass = 'badge-ok';
        elseif ($nbSms > 0) $bClass = 'badge-low';
      ?>
      <span class="sm-badge <?php print $bClass ?>"><?php print number_format($nbSms, 0, ',', ' ') ?></span>
      <?php if ($aboLabel && $nbSms > 0): ?>
      <span class="sm-badge badge-info"><?php print htmlspecialchars($aboLabel) ?></span>
      <?php endif; ?>
      <?php if (!empty($subInfo['found'])): ?>
      <span class="sm-badge badge-sub"><i class="bi bi-arrow-repeat"></i> Abonnement actif</span>
      <?php endif; ?>
      <span style="font-size:11px;color:#888"><?php print number_format($nbEnvoye, 0, ',', ' ') ?> envoyé(s)</span>
    </div>
    <?php if ($nbSms < 50): ?>
    <div style="font-size:11px;color:#856404;background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:6px 10px;margin-top:8px">
      <i class="bi bi-exclamation-triangle-fill"></i> Solde faible — rechargez pour continuer à envoyer des SMS
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- ── Abonnement mensuel 200 SMS / 20€ ─────────────────────────── -->
  <?php if (!empty($subPlan['actif'])): ?>
  <div class="sm-card amber">
    <div class="sm-card-title"><i class="bi bi-arrow-repeat"></i> Abonnement mensuel — <?php print intval($subPlan['nb']) ?> SMS / <?php print intval($subPlan['prix']) ?> €/mois</div>
    <?php if (!empty($subInfo['found'])): ?>
      <?php
        $subStatus = isset($subInfo['status']) ? $subInfo['status'] : 'active';
        $cancelEnd  = !empty($subInfo['cancel_at_period_end']);
        $periodeEnd = !empty($subInfo['current_period_end']) ? date('d/m/Y', intval($subInfo['current_period_end'])) : '';
      ?>
      <div class="sm-sub-row">
        <?php if ($cancelEnd): ?>
          <span class="sm-sub-pill sub-cancel"><i class="bi bi-clock"></i> Résiliation le <?php print $periodeEnd ?></span>
          <span style="font-size:11px;color:#555">Vos SMS restent disponibles jusqu'à cette date.</span>
        <?php elseif ($subStatus === 'active'): ?>
          <span class="sm-sub-pill sub-actif"><i class="bi bi-check-circle-fill"></i> Actif</span>
          <span style="font-size:11px;color:#555">Renouvellement le <?php print $periodeEnd ?></span>
          <span style="flex:1"></span>
          <form method="POST" action="./sms-stripe-cancel-sub.php" id="form-cancel-sub" style="display:inline">
            <button type="button" class="btn-sm btn-sm-red" style="font-size:11px;padding:5px 12px" onclick="cancelSub('<?php print $periodeEnd ?>')">
              <i class="bi bi-x-circle"></i> Résilier
            </button>
          </form>
        <?php else: ?>
          <span class="sm-sub-pill sub-inactif"><?php print htmlspecialchars($subStatus) ?></span>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div style="font-size:12px;color:#555;margin-bottom:10px">
        <strong><?php print intval($subPlan['nb']) ?> SMS</strong> renouvelés automatiquement chaque mois.<br>
        <span style="font-size:11px;color:#888">0,10 €/SMS effectif — sans frais de service — résiliable à tout moment.</span><br>
        <span style="font-size:11px;color:#888"><i class="bi bi-calendar-check"></i> Les SMS non utilisés restent disponibles pendant 1 an.</span>
      </div>
      <form method="POST" action="./sms-stripe-checkout-sub.php">
        <input type="hidden" name="contact" value="<?php print htmlspecialchars($emailCompte ? $emailCompte : (isset($smsInfo['email']) ? $smsInfo['email'] : '')) ?>">
        <button type="submit" class="btn-sm btn-sm-purple">
          <i class="bi bi-credit-card"></i> Souscrire — <?php print intval($subPlan['prix']) ?> €/mois
        </button>
      </form>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- ── Acheter des SMS ───────────────────────────────────────────── -->
  <div class="sm-card">
    <div class="sm-card-title"><i class="bi bi-cart3"></i> Acheter des SMS</div>
    <div class="sm-offres">

      <!-- SMS-TOP -->
      <div class="sm-offre-bloc">
        <div class="sm-offre-titre">SMS-TOP</div>
        <div class="sm-offre-detail">
          <?php print $tarif_top ?> €/SMS HT &bull; Frais 9 € HT<br>
          Min. 300 SMS &bull; Remise garantie
        </div>
        <div class="sm-packs-row">
          <?php foreach ($packsByOffre['OFF0'] as $sp): ?>
          <div class="sm-pack<?php print !$sp['actif'] ? ' disabled' : '' ?>"
            <?php if ($sp['actif']): ?>onclick="selPack('<?php print htmlspecialchars($sp['pack']) ?>', this)"<?php endif; ?>>
            <div class="sm-pack-nb"><?php print number_format($sp['nb'], 0, ',', ' ') ?> SMS</div>
            <div class="sm-pack-prix"><?php print $sp['prix'] ?> €</div>
            <?php if (!$sp['actif']): ?><div class="sm-pack-dispo">Bientôt</div><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- SMS-DIRECT -->
      <div class="sm-offre-bloc">
        <div class="sm-offre-titre">SMS-DIRECT</div>
        <div class="sm-offre-detail">
          <?php print $tarif_direct ?> €/SMS HT &bull; Frais 9 € HT<br>
          Min. 1 000 SMS &bull; Remise garantie
        </div>
        <div class="sm-packs-row">
          <?php foreach ($packsByOffre['OFF1'] as $sp): ?>
          <div class="sm-pack<?php print !$sp['actif'] ? ' disabled' : '' ?>"
            <?php if ($sp['actif']): ?>onclick="selPack('<?php print htmlspecialchars($sp['pack']) ?>', this)"<?php endif; ?>>
            <div class="sm-pack-nb"><?php print number_format($sp['nb'], 0, ',', ' ') ?> SMS</div>
            <div class="sm-pack-prix"><?php print $sp['prix'] ?> €</div>
            <?php if (!$sp['actif']): ?><div class="sm-pack-dispo">Bientôt</div><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <form method="POST" action="./sms-stripe-checkout.php" id="form-sms">
      <input type="hidden" name="pack"      id="sms-pack"    value="">
      <input type="hidden" name="contact"   value="<?php print htmlspecialchars($emailCompte ? $emailCompte : (isset($smsInfo['email']) ? $smsInfo['email'] : '')) ?>">
      <input type="hidden" name="productid" value="<?php print htmlspecialchars($productid) ?>">
      <input type="hidden" name="url"       value="<?php print htmlspecialchars($url) ?>">
    </form>
    <div style="margin-top:12px;padding-top:10px;border-top:1px solid #e0e3f0">
        <div style="font-size:11px;color:#888;margin-bottom:8px;font-family:Electrolize,'Trebuchet MS',Arial"><i class="bi bi-lock-fill"></i> Paiement sécurisé via Stripe — achat unique, SMS valables 1 an. Prix HT + frais 9 €.</div>
        <button class="btn-stripe" id="btn-pay" style="display:none" onclick="payerSms()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13.97 9.056c0-.78.64-1.08 1.7-1.08 1.52 0 3.44.46 4.96 1.28V5.16C19.1 4.56 17.6 4 15.67 4 11.8 4 9.2 6.04 9.2 9.3c0 5.12 7.04 4.3 7.04 6.5 0 .92-.8 1.22-1.92 1.22-1.66 0-3.78-.68-5.46-1.6v4.06c1.86.8 3.74 1.14 5.46 1.14 4 0 6.74-1.98 6.74-5.3 0-5.52-7.09-4.54-7.09-6.22z"/></svg>
            Payer avec Stripe
        </button>
    </div>
  </div>

  <!-- ── Informations compte ───────────────────────────────────────── -->
  <?php if ($smsInfo): ?>
  <div class="sm-card green">
    <div class="sm-card-title"><i class="bi bi-building"></i> Informations compte</div>

    <div id="sm-info-view">
      <div class="sm-info-grid">
        <div>
          <div class="sm-info-label">Établissement</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['etablissement']) ?></div>
        </div>
        <div>
          <div class="sm-info-label">Pays</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['pays']) ?></div>
        </div>
        <div>
          <div class="sm-info-label">Adresse</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['adresse']) ?></div>
        </div>
        <div>
          <div class="sm-info-label">Ville</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['ccp']) ?> <?php print htmlspecialchars($smsInfo['ville']) ?></div>
        </div>
        <div>
          <div class="sm-info-label">Responsable</div>
          <div class="sm-info-val"><?php print htmlspecialchars(strtoupper($smsInfo['nom'])) ?> <?php print htmlspecialchars(ucwords(strtolower($smsInfo['prenom']))) ?></div>
        </div>
        <div>
          <div class="sm-info-label">Tél portable</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['tel'] ? $smsInfo['tel'] : '—') ?></div>
        </div>
        <div>
          <div class="sm-info-label">Email responsable</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['email']) ?></div>
        </div>
        <div>
          <div class="sm-info-label">Email réponses SMS</div>
          <div class="sm-info-val"><?php print htmlspecialchars($smsInfo['emailreply'] ? $smsInfo['emailreply'] : '—') ?></div>
        </div>
      </div>
      <button class="btn-sm btn-sm-ghost" onclick="smToggleEdit(true)">
        <i class="bi bi-pencil"></i> Modifier
      </button>
    </div>

    <div id="sm-info-form" style="display:none">
      <form method="POST" action="./sms-info-save.php">
        <input type="hidden" name="back"      value="./sms.php">
        <input type="hidden" name="productid" value="<?php print htmlspecialchars($productid) ?>">
        <input type="hidden" name="url"       value="<?php print htmlspecialchars($url) ?>">
        <div class="sm-form-grid">
          <div class="full">
            <div class="sm-info-label">Établissement</div>
            <input type="text" name="etablissement" value="<?php print htmlspecialchars($smsInfo['etablissement']) ?>" maxlength="60">
          </div>
          <div class="full">
            <div class="sm-info-label">Adresse</div>
            <input type="text" name="adresse" value="<?php print htmlspecialchars($smsInfo['adresse']) ?>" maxlength="100">
          </div>
          <div>
            <div class="sm-info-label">Code postal</div>
            <input type="text" name="ccp" value="<?php print htmlspecialchars($smsInfo['ccp']) ?>" maxlength="20">
          </div>
          <div>
            <div class="sm-info-label">Ville</div>
            <input type="text" name="ville" value="<?php print htmlspecialchars($smsInfo['ville']) ?>" maxlength="50">
          </div>
          <div>
            <div class="sm-info-label">Nom responsable</div>
            <input type="text" name="nom" value="<?php print htmlspecialchars($smsInfo['nom']) ?>" maxlength="50">
          </div>
          <div>
            <div class="sm-info-label">Prénom responsable</div>
            <input type="text" name="prenom" value="<?php print htmlspecialchars($smsInfo['prenom']) ?>" maxlength="50">
          </div>
          <div>
            <div class="sm-info-label">Tél portable (envoi test)</div>
            <input type="text" name="tel" value="<?php print htmlspecialchars($smsInfo['tel']) ?>" maxlength="15">
          </div>
          <div>
            <div class="sm-info-label">Email responsable</div>
            <input type="email" name="email" value="<?php print htmlspecialchars($smsInfo['email']) ?>" maxlength="90">
          </div>
          <div class="full">
            <div class="sm-info-label">Email réponses SMS <span style="color:#aaa;font-weight:400">(fonctionne avec SMS-TOP)</span></div>
            <input type="email" name="emailreply" value="<?php print htmlspecialchars($smsInfo['emailreply']) ?>" maxlength="90">
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:10px">
          <button type="submit" class="btn-sm btn-sm-green"><i class="bi bi-check-lg"></i> Enregistrer</button>
          <button type="button" class="btn-sm btn-sm-ghost" onclick="smToggleEdit(false)"><i class="bi bi-x"></i> Annuler</button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Historique SMS ────────────────────────────────────────────── -->
  <?php if (!empty($smsInfo)): ?>
  <div class="sm-card grey">
    <div class="sm-card-title">
      <i class="bi bi-clock-history"></i>
      <?php print $allLogs ? 'Historique complet SMS' : 'Derniers SMS envoyés'; ?>
      <span style="flex:1"></span>
      <?php if (!$allLogs): ?>
      <a href="sms.php?all_logs=1<?php print $productid ? '&productid='.urlencode($productid) : ''; ?>"
         class="btn-sm btn-sm-ghost" style="font-size:10px;padding:3px 10px">
        <i class="bi bi-list-ul"></i> Voir tous
      </a>
      <?php else: ?>
      <a href="sms.php<?php print $productid ? '?productid='.urlencode($productid) : ''; ?>"
         class="btn-sm btn-sm-ghost" style="font-size:10px;padding:3px 10px">
        <i class="bi bi-arrow-left"></i> Réduire
      </a>
      <?php endif; ?>
    </div>
    <?php if (!empty($smsLogs)): ?>
    <div style="overflow-x:auto">
    <table class="sm-log-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Destinataire</th>
          <?php if ($allLogs): ?><th>Émetteur</th><?php endif; ?>
          <th>Message</th>
          <th style="text-align:center">Statut</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($smsLogs as $log): ?>
        <tr>
          <td style="white-space:nowrap"><?php
            $d = $log['date'];
            if ($d && strpos($d,'-') !== false) {
                $p = explode('-',$d); print (count($p)===3) ? $p[2].'/'.$p[1].'/'.$p[0] : htmlspecialchars($d);
            } else { print htmlspecialchars($d); }
          ?></td>
          <td>+<?php print htmlspecialchars($log['tel']) ?></td>
          <?php if ($allLogs): ?>
          <td style="font-size:10px;color:#888"><?php print htmlspecialchars($log['emetteur']) ?></td>
          <?php endif; ?>
          <td style="max-width:<?php print $allLogs ? '220px' : '160px'; ?>;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
              title="<?php print htmlspecialchars($log['message']) ?>">
            <?php print htmlspecialchars(mb_strimwidth($log['message'], 0, $allLogs ? 60 : 40, '…')) ?>
          </td>
          <td style="text-align:center">
            <?php if ($log['ok']): ?>
            <span class="sm-ok"><i class="bi bi-check-circle-fill"></i></span>
            <?php else: ?>
            <span class="sm-err"><i class="bi bi-x-circle-fill"></i></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <div style="font-size:11px;color:#aaa;margin-top:6px"><?php print count($smsLogs); ?> SMS affiché(s)</div>
    <?php else: ?>
    <div style="font-size:12px;color:#888;padding:6px 0">Aucun SMS envoyé pour le moment.</div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- ── Factures ───────────────────────────────────────────────────── -->
  <?php if (!empty($smsFact)): ?>
  <div class="sm-card grey">
    <div class="sm-card-title"><i class="bi bi-receipt"></i> Factures</div>
    <div style="overflow-x:auto">
    <table class="sm-fact-table">
      <thead>
        <tr><th>Référence</th><th>Montant TTC</th><th>Date</th><th></th></tr>
      </thead>
      <tbody>
      <?php foreach ($smsFact as $f): ?>
        <tr>
          <td><?php print htmlspecialchars($f['ref']) ?></td>
          <td><?php print htmlspecialchars($f['montant']) ?> €</td>
          <td><?php
            $d = $f['date'];
            if ($d && strpos($d,'-') !== false) {
                $p = explode('-',$d); print (count($p)===3) ? $p[2].'/'.$p[1].'/'.$p[0] : htmlspecialchars($d);
            } else { print htmlspecialchars($d); }
          ?></td>
          <td>
            <?php if (!empty($f['fichier'])): ?>
            <a href="<?php print $factProxyBase . urlencode($f['fichier']) ?>" target="_blank" class="btn-sm btn-sm-ghost" style="padding:3px 10px;font-size:11px">
              <i class="bi bi-download"></i>
            </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
  <?php elseif (!empty($smsInfo)): ?>
  <div class="sm-card grey">
    <div class="sm-card-title"><i class="bi bi-receipt"></i> Factures</div>
    <div style="font-size:12px;color:#888;padding:6px 0">Aucune facture disponible.</div>
  </div>
  <?php endif; ?>

</div>

<script>
var _selPack = '';
function selPack(pack, el) {
    document.querySelectorAll('.sm-pack').forEach(function(c){ c.classList.remove('selected'); });
    el.classList.add('selected');
    _selPack = pack;
    document.getElementById('btn-pay').style.display = 'inline-flex';
}
function payerSms() {
    if (!_selPack) { alertify.error('Veuillez sélectionner un pack.'); return; }
    document.getElementById('sms-pack').value = _selPack;
    document.getElementById('form-sms').submit();
}
function smToggleEdit(show) {
    document.getElementById('sm-info-view').style.display = show ? 'none' : 'block';
    document.getElementById('sm-info-form').style.display = show ? 'block' : 'none';
}
function cancelSub(periodeEnd) {
    alertify.confirm('Résilier l\'abonnement SMS',
        'Vos SMS restent disponibles jusqu\'au ' + periodeEnd + '. Après cette date, le solde mensuel ne sera plus renouvelé. Confirmer la résiliation ?',
        function() { document.getElementById('form-cancel-sub').submit(); },
        function() {}
    );
}
<?php if ($stripeMsg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php print $stripeMsgType === 'success' ? 'success' : 'error'; ?>('<?php print addslashes($stripeMsg); ?>');
    window.history && window.history.replaceState({}, '', 'sms.php<?php print $productid ? '?productid=' . urlencode($productid) : ''; ?>');
});
<?php endif; ?>
<?php if ($infoMsg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php print $infoMsgType === 'success' ? 'success' : 'error'; ?>('<?php print addslashes($infoMsg); ?>');
    window.history && window.history.replaceState({}, '', 'sms.php<?php print $productid ? '?productid=' . urlencode($productid) : ''; ?>');
});
<?php endif; ?>
</script>

<?php endif; ?>

<?php else: ?>
<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#856404;text-align:center;">
    &#9888; <?php echo ERREUR1; ?><br><br><i><?php echo ERREUR2; ?></i>
</div>
<?php endif; ?>

</td></tr></table>
<br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
