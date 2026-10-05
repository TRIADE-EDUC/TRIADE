<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   TRIADE-SIGN — Envoi de documents à signer (intégré)
 ***************************************************************************/
define('_SIGN_API',  'https://support.triade-educ.org/support/api-sign.php');
define('_SIGN_KEY',  'sign-2026-xT5nVqP9');

// ── Charger SIGNKEY depuis config-sign.php ────────────────────────────────
$signConfigFile = './common/config-sign.php';
if (file_exists($signConfigFile)) include_once($signConfigFile);
$idsign = defined('SIGNKEY') ? SIGNKEY : '';

// ── Servir le PDF temporaire pour PDF.js ─────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'pdf') {
    $tmpf = isset($_SESSION['sign_tmpfile']) ? $_SESSION['sign_tmpfile'] : '';
    if ($tmpf && file_exists($tmpf)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="document.pdf"');
        readfile($tmpf);
        exit;
    }
    http_response_code(404); exit;
}

// ── Proxy PDF signé (FPDI via API centrale) ──────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'dl') {
    $commun = isset($_GET['commun']) ? preg_replace('/[^a-f0-9]/', '', $_GET['commun']) : '';
    $token  = isset($_SESSION['sign_token']) ? $_SESSION['sign_token'] : '';
    if (!$commun || !$token || !$idsign) { http_response_code(403); echo 'Accès refusé'; exit; }

    $dlUrl = 'https://support.triade-educ.org/support/sign-dl.php?' . http_build_query(array(
        'key'    => _SIGN_KEY,
        'token'  => $token,
        'idsign' => $idsign,
        'commun' => $commun,
    ));

    $ch = curl_init($dlUrl);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HEADERFUNCTION => function($ch, $header) {
            $h = trim($header);
            $l = strtolower($h);
            if (strpos($l, 'content-type:') === 0 || strpos($l, 'content-disposition:') === 0) {
                header($h);
            }
            return strlen($header);
        }
    ));
    $pdfData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $pdfData) {
        echo $pdfData;
    } else {
        http_response_code(502);
        echo 'PDF non disponible (code ' . $httpCode . ').';
    }
    exit;
}

// ── Helper cURL ───────────────────────────────────────────────────────────
function signApi($action, $data, $withFile=false) {
    $data['action'] = $action;
    $data['key']    = _SIGN_KEY;
    $ch = curl_init(_SIGN_API);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $withFile ? $data : http_build_query($data),
    ));
    $r = curl_exec($ch);
    curl_close($ch);
    return $r ? json_decode($r, true) : null;
}

// ── Détermine l'étape ─────────────────────────────────────────────────────
$step = isset($_GET['step']) ? $_GET['step'] : '';
if (!$step) $step = isset($_SESSION['sign_token']) ? 'accueil' : 'login';

$flashOk = ''; $flashErr = '';

// ── Logout ────────────────────────────────────────────────────────────────
if ($step === 'logout') {
    $tmpf = isset($_SESSION['sign_tmpfile']) ? $_SESSION['sign_tmpfile'] : '';
    if ($tmpf && file_exists($tmpf)) unlink($tmpf);
    unset($_SESSION['sign_token'], $_SESSION['sign_email'],
          $_SESSION['sign_tmpfile'], $_SESSION['sign_signataires'],
          $_SESSION['sign_fichier_nom'], $_SESSION['sign_sent']);
    header('Location: triade-sign-envoi.php'); exit;
}

// ── POST : login ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_login'])) {
    $r = signApi('sign_login', array(
        'email'  => trim(isset($_POST['email']) ? $_POST['email'] : ''),
        'passe'  => trim(isset($_POST['passe']) ? $_POST['passe'] : ''),
        'idsign' => $idsign,
    ));
    if ($r && !empty($r['ok'])) {
        $_SESSION['sign_token'] = $r['token'];
        $_SESSION['sign_email'] = $r['email'];
        header('Location: triade-sign-envoi.php?step=accueil'); exit;
    }
    $flashErr = 'Email ou mot de passe incorrect.';
    $step = 'login';
}

// ── POST : upload PDF + signataires ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_upload'])) {
    $nb = max(1, min(4, intval(isset($_POST['nb']) ? $_POST['nb'] : 1)));
    $sigs = array();
    for ($i=1; $i<=$nb; $i++) {
        $sigs[] = array(
            'nom'   => trim(isset($_POST["nom_$i"])   ? $_POST["nom_$i"]   : ''),
            'email' => trim(isset($_POST["email_$i"]) ? $_POST["email_$i"] : ''),
            'tel'   => trim(isset($_POST["tel_$i"])   ? $_POST["tel_$i"]   : ''),
        );
    }
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === 0
        && strpos($_FILES['fichier']['type'], 'pdf') !== false) {
        $tmpf = sys_get_temp_dir() . '/triade_sign_' . session_id() . '.pdf';
        move_uploaded_file($_FILES['fichier']['tmp_name'], $tmpf);
        $_SESSION['sign_tmpfile']    = $tmpf;
        $_SESSION['sign_fichier_nom']= $_FILES['fichier']['name'];
        $_SESSION['sign_signataires']= $sigs;
        header('Location: triade-sign-envoi.php?step=placement'); exit;
    }
    $flashErr = 'Veuillez sélectionner un fichier PDF valide.';
    $step = 'nouveau';
}

// ── POST : envoi final à l'API ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_send'])) {
    $token  = isset($_SESSION['sign_token'])      ? $_SESSION['sign_token']      : '';
    $sigs   = isset($_SESSION['sign_signataires'])? $_SESSION['sign_signataires']: array();
    $tmpf   = isset($_SESSION['sign_tmpfile'])    ? $_SESSION['sign_tmpfile']    : '';
    $ficNom = isset($_SESSION['sign_fichier_nom'])? $_SESSION['sign_fichier_nom']: 'document.pdf';
    $nb     = count($sigs);

    if ($tmpf && file_exists($tmpf) && $nb > 0) {
        $data = array(
            'token'      => $token,
            'idsign'     => $idsign,
            'nb'         => $nb,
            'fichier_nom'=> $ficNom,
            'fichier'    => new CURLFile($tmpf, 'application/pdf', $ficNom),
        );
        for ($i=1; $i<=$nb; $i++) {
            $s = $sigs[$i-1];
            $data["nom_$i"]        = $s['nom'];
            $data["email_$i"]      = $s['email'];
            $data["tel_$i"]        = $s['tel'];
            $data["zoneX_$i"]      = isset($_POST["zoneX_$i"])      ? $_POST["zoneX_$i"]      : 0;
            $data["zoneY_$i"]      = isset($_POST["zoneY_$i"])      ? $_POST["zoneY_$i"]      : 0;
            $data["pageNumber_$i"] = isset($_POST["pageNumber_$i"]) ? intval($_POST["pageNumber_$i"]) : 1;
        }
        $r = signApi('sign_send', $data, true);
        if ($tmpf && file_exists($tmpf)) unlink($tmpf);
        unset($_SESSION['sign_tmpfile'], $_SESSION['sign_signataires'], $_SESSION['sign_fichier_nom']);
        if ($r && !empty($r['ok'])) {
            $_SESSION['sign_sent'] = intval($r['sent']);
            header('Location: triade-sign-envoi.php?step=result'); exit;
        }
        $flashErr = ($r && isset($r['error'])) ? $r['error'] : 'Erreur lors de l\'envoi.';
    } else {
        $flashErr = 'Session expirée ou fichier manquant.';
    }
    $step = 'placement';
}

// ── POST : suppression ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_supp'])) {
    signApi('sign_supp', array(
        'token'  => $_SESSION['sign_token'],
        'idsign' => $idsign,
        'id'     => intval(isset($_POST['sig_id']) ? $_POST['sig_id'] : 0),
    ));
    header('Location: triade-sign-envoi.php?step=accueil&msg=supp'); exit;
}

// ── POST : relance ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_relance'])) {
    signApi('sign_relance', array(
        'token'  => $_SESSION['sign_token'],
        'idsign' => $idsign,
        'id'     => intval(isset($_POST['sig_id']) ? $_POST['sig_id'] : 0),
    ));
    header('Location: triade-sign-envoi.php?step=accueil&msg=relance'); exit;
}

// ── Données pour l'affichage ──────────────────────────────────────────────
$solde   = 0;
$listing = array();
if ($step === 'accueil' && isset($_SESSION['sign_token'])) {
    $r = signApi('sign_solde', array('token'=>$_SESSION['sign_token'],'idsign'=>$idsign));
    $solde = ($r && !empty($r['ok'])) ? intval($r['solde']) : 0;
    $r = signApi('sign_listing', array('token'=>$_SESSION['sign_token'],'idsign'=>$idsign));
    $listing = ($r && !empty($r['ok'])) ? $r['listing'] : array();
}
$filtre  = (isset($_GET['filtre']) && in_array($_GET['filtre'], array('tous','attente','signe'))) ? $_GET['filtre'] : 'tous';
$page    = max(1, intval(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = 25;
$listingFiltered = array();
foreach ($listing as $_lr) {
    $isSigned = ($_lr['signe_le'] !== '0000-00-00');
    if ($filtre === 'attente' && $isSigned)  continue;
    if ($filtre === 'signe'   && !$isSigned) continue;
    $listingFiltered[] = $_lr;
}
$totalFiltered = count($listingFiltered);
$totalPages    = max(1, (int)ceil($totalFiltered / $perPage));
$page          = min($page, $totalPages);
$listingPage   = array_slice($listingFiltered, ($page-1)*$perPage, $perPage);
$signEmail = isset($_SESSION['sign_email']) ? $_SESSION['sign_email'] : '';
$signSent  = isset($_SESSION['sign_sent'])  ? intval($_SESSION['sign_sent']) : 0;
$gMsg      = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade — TRIADE-SIGN Envoi</title>
<style>
.se-step-nav{display:flex;gap:6px;margin-bottom:12px;flex-wrap:wrap}
.se-step{padding:4px 12px;border-radius:12px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;background:#e8eaf6;color:#555;border:1px solid #c5cae9}
.se-step.active{background:#080A66;color:#fff;border-color:#080A66}
.se-step.done{background:#e8f5e9;color:#2e7d32;border-color:#a5d6a7}
.se-sig-block{background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:12px 14px;margin-bottom:8px}
.se-sig-title{font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;font-family:Electrolize,'Trebuchet MS',Arial}
.se-field{margin-bottom:6px}
.se-field label{display:block;font-size:11px;color:#555;margin-bottom:2px}
.se-field input[type=text],.se-field input[type=email],.se-field input[type=tel]{
  width:100%;padding:5px 8px;border:1px solid #c8cfe8;border-radius:4px;
  font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;box-sizing:border-box}
.se-pdf-wrap{position:relative;overflow:auto;max-height:420px;border:1px solid #c5cae9;border-radius:6px;background:#888;cursor:crosshair}
.se-pdf-wrap canvas{display:block}
.se-marker{position:absolute;min-width:195px;height:66px;border:2px solid #e53935;background:rgba(229,57,53,.15);border-radius:4px;cursor:move;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:0 8px;box-sizing:border-box;user-select:none}
.se-marker-num{font-size:13px;font-weight:700;color:#e53935;font-family:Electrolize,'Trebuchet MS',Arial;line-height:1.2}
.se-marker-name{font-size:14px;font-weight:700;color:#c62828;font-family:Electrolize,'Trebuchet MS',Arial;white-space:nowrap;line-height:1.3;text-align:center}
.se-listing-table{width:100%;border-collapse:collapse;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial}
.se-listing-table th{background:#f0f2fa;color:#080A66;padding:7px 10px;text-align:left;font-weight:600;border-bottom:1px solid #dde0f0}
.se-listing-table td{padding:7px 10px;border-bottom:1px solid #f0f2fa;vertical-align:middle}
.se-listing-table tr:last-child td{border-bottom:none}
.se-listing-table tr:hover td{background:#f5f7ff}
.badge-attente{background:#fff3e0;color:#e65100;border-radius:10px;padding:2px 8px;font-size:10px;font-weight:700}
.badge-signe{background:#e8f5e9;color:#2e7d32;border-radius:10px;padding:2px 8px;font-size:10px;font-weight:700}
.ss-wrap{position:relative}
.ss-dropdown{position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #c5cae9;border-radius:4px;box-shadow:0 4px 12px rgba(0,0,0,.12);z-index:9999;max-height:200px;overflow-y:auto;display:none}
.ss-item{padding:6px 10px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;cursor:pointer;border-bottom:1px solid #f0f2fa;color:#222}
.ss-item:last-child{border-bottom:none}
.ss-item:hover,.ss-item.active{background:#e8eaf6;color:#080A66}
.ss-tag{font-size:10px;color:#888;margin-left:4px}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php if ($signEmail): ?>
<div style="display:flex;justify-content:flex-end;align-items:center;padding:4px 6px 2px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;color:#555;gap:8px">
    <i class="bi bi-person-circle" style="color:#080A66"></i>
    <?php echo htmlspecialchars($signEmail); ?>
</div>
<?php endif; ?>
<div style="max-width:700px;margin:0 auto;width:100%">

<?php
// Flash messages
if ($flashOk || $flashErr || $gMsg):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
<?php if ($flashOk): ?>
    alertify.success(<?php echo json_encode($flashOk); ?>);
<?php elseif ($flashErr): ?>
    alertify.error(<?php echo json_encode($flashErr); ?>);
<?php elseif ($gMsg === 'supp'): ?>
    alertify.success('Demande supprimée.');
<?php elseif ($gMsg === 'relance'): ?>
    alertify.success('Email de relance envoyé.');
<?php endif; ?>
});
</script>
<?php endif; ?>

<?php if (!$idsign): ?>
<!-- ── Pas de config SIGN ─────────────────────────────────────────────────── -->
<div class="card" style="margin:10px 6px">
<div class="card-body">
    <div style="text-align:center;padding:20px 0;font-size:12px;color:#666;font-family:Electrolize,'Trebuchet MS',Arial">
        <i class="bi bi-exclamation-triangle" style="font-size:28px;color:#f57c00;display:block;margin-bottom:10px"></i>
        TRIADE-SIGN n'est pas configuré sur cette installation.<br>
        <a href="admin/triade-sign.php" style="color:#080A66;font-weight:700;margin-top:8px;display:inline-block">
            <i class="bi bi-gear"></i> Configurer TRIADE-SIGN
        </a>
    </div>
</div>
</div>

<?php elseif ($step === 'login'): ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     ÉTAPE 1 — LOGIN
══════════════════════════════════════════════════════════════════════════ -->
<div class="card" style="margin:10px 6px">
<div class="card-header card-header-primary">
    <i class="bi bi-lock-fill"></i> Connexion à votre compte SIGN
</div>
<div class="card-body">
    <form method="post">
        <div class="se-field">
            <label><i class="bi bi-envelope" style="color:#080A66"></i> Email</label>
            <input type="text" name="email" placeholder="votre@email.com" required>
        </div>
        <div class="se-field" style="margin-top:6px">
            <label><i class="bi bi-key" style="color:#080A66"></i> Mot de passe</label>
            <input type="password" name="passe" placeholder="••••••••" required>
        </div>
        <div style="margin-top:12px;display:flex;align-items:center;gap:10px">
            <input type="hidden" name="do_login" value="1">
            <button type="submit" class="btn btn-primary" style="font-size:12px">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
            <a href="./admin/" target="_blank" style="font-size:11px;color:#666">
                <i class="bi bi-people"></i> Gérer les comptes
            </a>
        </div>
    </form>
</div>
</div>

<?php elseif ($step === 'accueil'): ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     ÉTAPE 2 — ACCUEIL (solde + listing)
══════════════════════════════════════════════════════════════════════════ -->
<div style="display:flex;flex-direction:column;gap:8px;padding:6px 4px">

  <!-- Solde -->
  <div class="card">
  <div class="card-body" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
    <div style="text-align:center;min-width:70px">
        <div style="font-size:28px;font-weight:700;color:<?php echo $solde>0?'#080A66':'#c62828'; ?>">
            <?php echo $solde; ?>
        </div>
        <div style="font-size:11px;color:#666">signature<?php echo $solde>1?'s':''; ?> disponible<?php echo $solde>1?'s':''; ?></div>
    </div>
    <div style="flex:1;min-width:120px">
        <?php if ($solde > 0): ?>
        <a href="triade-sign-envoi.php?step=nouveau" class="btn btn-primary" style="font-size:12px;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-plus-lg"></i> Nouveau document à signer
        </a>
        <?php else: ?>
        <div style="font-size:12px;color:#c62828">
            <i class="bi bi-exclamation-triangle"></i>
            Aucun crédit disponible.<br>
            <a href="admin/triade-sign.php" style="color:#080A66">Acheter des signatures</a>
        </div>
        <?php endif; ?>
    </div>
  </div>
  </div>

  <!-- Listing -->
  <div class="card">
  <div class="card-header card-header-primary" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <span><i class="bi bi-list-ul"></i> Demandes de signature</span>
    <?php if ($totalFiltered): ?>
    <span class="card-badge"><?php echo $totalFiltered; ?></span>
    <?php endif; ?>
    <div style="margin-left:auto;display:flex;gap:4px">
        <?php $filtres = array('tous'=>'Tous','attente'=>'En attente','signe'=>'Sign&eacute;s');
        foreach ($filtres as $fval => $flab):
            $isAct = ($filtre === $fval); ?>
        <a href="triade-sign-envoi.php?step=accueil&amp;filtre=<?php echo $fval; ?>&amp;page=1"
           style="font-size:10px;padding:2px 9px;border-radius:10px;text-decoration:none;border:1px solid;<?php echo $isAct ? 'background:#fff;color:#080A66;border-color:#fff;font-weight:700' : 'background:transparent;color:#bbc;border-color:#8899bb'; ?>">
            <?php echo $flab; ?>
        </a>
        <?php endforeach; ?>
    </div>
  </div>
  <div class="card-body" style="padding:0">
    <?php if (!count($listingPage)): ?>
    <div style="padding:16px;text-align:center;font-size:12px;color:#888;font-style:italic">
        <?php echo $filtre !== 'tous' ? 'Aucune demande pour ce filtre.' : 'Aucune demande pour l\'instant.'; ?>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto">
    <table class="se-listing-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Signataire</th>
            <th>Email</th>
            <th>Document</th>
            <th>Statut</th>
            <th style="width:110px"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($listingPage as $row): ?>
        <tr>
            <td style="white-space:nowrap"><?php echo htmlspecialchars(substr($row['date'],0,10)); ?></td>
            <td><?php echo htmlspecialchars($row['nom']); ?></td>
            <td style="font-size:11px;color:#555"><?php echo htmlspecialchars($row['email']); ?></td>
            <td style="font-size:11px;color:#777;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                title="<?php echo htmlspecialchars($row['fichier']); ?>">
                <?php echo htmlspecialchars(substr($row['fichier'],0,20)); ?>
            </td>
            <td>
                <?php if ($row['signe_le'] === '0000-00-00'): ?>
                <span class="badge-attente"><i class="bi bi-clock"></i> En attente</span>
                <?php else: ?>
                <span class="badge-signe"><i class="bi bi-check-circle"></i> Sign&eacute;</span>
                <?php endif; ?>
            </td>
            <td style="white-space:nowrap">
                <?php if ($row['signe_le'] === '0000-00-00'): ?>
                <form method="post" style="display:inline" onsubmit="return confirm('Relancer ce signataire ?')">
                    <input type="hidden" name="sig_id" value="<?php echo intval($row['id']); ?>">
                    <input type="hidden" name="do_relance" value="1">
                    <button type="submit" class="btn" style="font-size:10px;padding:2px 7px;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Supprimer cette demande ?')">
                    <input type="hidden" name="sig_id" value="<?php echo intval($row['id']); ?>">
                    <input type="hidden" name="do_supp" value="1">
                    <button type="submit" class="btn btn-danger" style="font-size:10px;padding:2px 7px">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                <?php else: ?>
                <a href="triade-sign-envoi.php?action=dl&commun=<?php echo urlencode($row['commun']); ?>"
                   target="_blank" class="btn" style="font-size:10px;padding:2px 7px;background:#f5f7ff;color:#080A66;border:1px solid #c5cae9">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php if ($totalPages > 1): ?>
    <div style="display:flex;align-items:center;justify-content:center;gap:4px;padding:8px 6px;flex-wrap:wrap;font-family:Electrolize,'Trebuchet MS',Arial;font-size:11px;border-top:1px solid #f0f2fa">
        <?php if ($page > 1): ?>
        <a href="triade-sign-envoi.php?step=accueil&amp;filtre=<?php echo urlencode($filtre); ?>&amp;page=<?php echo $page-1; ?>"
           class="btn" style="font-size:10px;padding:2px 8px">&lsaquo; Pr&eacute;c.</a>
        <?php endif; ?>
        <?php for ($p=1; $p<=$totalPages; $p++): ?>
        <a href="triade-sign-envoi.php?step=accueil&amp;filtre=<?php echo urlencode($filtre); ?>&amp;page=<?php echo $p; ?>"
           style="font-size:10px;padding:2px 8px;border-radius:4px;text-decoration:none;<?php echo $p===$page ? 'background:#080A66;color:#fff;font-weight:700' : 'background:#f0f2fa;color:#080A66'; ?>">
            <?php echo $p; ?>
        </a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="triade-sign-envoi.php?step=accueil&amp;filtre=<?php echo urlencode($filtre); ?>&amp;page=<?php echo $page+1; ?>"
           class="btn" style="font-size:10px;padding:2px 8px">Suiv. &rsaquo;</a>
        <?php endif; ?>
        <span style="color:#888;margin-left:6px"><?php echo $totalFiltered; ?> demande<?php echo $totalFiltered>1?'s':''; ?> &mdash; page <?php echo $page; ?>/<?php echo $totalPages; ?></span>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
  </div>

</div>

<?php elseif ($step === 'nouveau'): ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     ÉTAPE 3 — NOUVEAU (upload PDF + signataires)
══════════════════════════════════════════════════════════════════════════ -->
<div style="padding:6px 4px">

<div class="se-step-nav">
    <span class="se-step done">1. Connexion</span>
    <span class="se-step done">2. Accueil</span>
    <span class="se-step active">3. Document</span>
    <span class="se-step">4. Zones</span>
</div>

<form method="post" enctype="multipart/form-data" id="frmUpload">
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-file-earmark-pdf"></i> Document PDF à faire signer
</div>
<div class="card-body">
    <div class="se-field">
        <label><i class="bi bi-upload" style="color:#080A66"></i> Fichier PDF</label>
        <input type="file" name="fichier" accept=".pdf" required
               style="font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial">
    </div>
    <div class="se-field" style="margin-top:10px">
        <label><i class="bi bi-people" style="color:#080A66"></i> Nombre de signataires</label>
        <select name="nb" id="nbSelect" class="cc-select" style="width:120px"
                onchange="buildSigs(this.value)">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select>
    </div>
</div>
</div>

<div id="sigBlocks" style="margin-top:8px"></div>

<div style="display:flex;gap:8px;align-items:center;margin-top:10px;flex-wrap:wrap">
    <input type="hidden" name="do_upload" value="1">
    <button type="submit" class="btn btn-primary" style="font-size:12px">
        <i class="bi bi-arrow-right-circle"></i> Continuer — Positionner les zones
    </button>
    <script language=JavaScript>buttonMagicRetour("triade-sign-envoi.php?step=accueil","_self");</script>
</div>
</form>

<script>
function buildSigs(n) {
    var h = '';
    for (var i=1; i<=parseInt(n); i++) {
        h += '<div class="se-sig-block">';
        h += '<div class="se-sig-title"><i class="bi bi-person-fill"></i> Signataire ' + i + '</div>';
        h += '<div style="display:flex;gap:8px;flex-wrap:wrap">';
        h += '<div class="se-field ss-wrap" style="flex:1 1 150px"><label>Nom / Prénom</label>';
        h += '<input type="text" name="nom_'+i+'" id="nom_'+i+'" autocomplete="off" required>';
        h += '<div class="ss-dropdown" id="dd_nom_'+i+'"></div></div>';
        h += '<div class="se-field ss-wrap" style="flex:1 1 180px"><label>Email</label>';
        h += '<input type="email" name="email_'+i+'" id="email_'+i+'" autocomplete="off" required>';
        h += '<div class="ss-dropdown" id="dd_email_'+i+'"></div></div>';
        h += '<div class="se-field" style="flex:0 0 130px"><label>Téléphone</label><input type="tel" name="tel_'+i+'" id="tel_'+i+'"></div>';
        h += '</div></div>';
    }
    document.getElementById('sigBlocks').innerHTML = h;
    for (var j=1; j<=parseInt(n); j++) { attachSuggest(j); }
}

var _ssTimer = {};
function attachSuggest(i) {
    attachField(i, 'nom');
    attachField(i, 'email');
}

function attachField(i, field) {
    var inp = document.getElementById(field+'_'+i);
    var dd  = document.getElementById('dd_'+field+'_'+i);
    if (!inp || !dd) return;
    inp.addEventListener('input', function() {
        clearTimeout(_ssTimer[field+i]);
        var q = inp.value.trim();
        if (q.length < 2) { dd.style.display='none'; dd.innerHTML=''; return; }
        _ssTimer[field+i] = setTimeout(function() { fetchSuggest(i, field, q, inp, dd); }, 250);
    });
    inp.addEventListener('keydown', function(e) {
        var items = dd.querySelectorAll('.ss-item');
        var cur   = dd.querySelector('.ss-item.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            var next = cur ? cur.nextElementSibling : items[0];
            if (cur) cur.classList.remove('active');
            if (next) next.classList.add('active');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            var prev = cur ? cur.previousElementSibling : items[items.length-1];
            if (cur) cur.classList.remove('active');
            if (prev) prev.classList.add('active');
        } else if (e.key === 'Enter' && cur) {
            e.preventDefault();
            cur.click();
        } else if (e.key === 'Escape') {
            dd.style.display='none';
        }
    });
    document.addEventListener('click', function(e) {
        if (!inp.contains(e.target) && !dd.contains(e.target)) dd.style.display='none';
    });
}

function fetchSuggest(i, field, q, inp, dd) {
    var url = 'sign-suggest.php?field='+encodeURIComponent(field)+'&q='+encodeURIComponent(q);
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState !== 4 || xhr.status !== 200) return;
        var data;
        try { data = JSON.parse(xhr.responseText); } catch(e) { return; }
        if (!data.length) { dd.style.display='none'; dd.innerHTML=''; return; }
        var h = '';
        for (var k=0; k<data.length; k++) {
            var lbl  = data[k].label || '';
            var tag  = '';
            if (lbl.indexOf('(') !== -1) {
                var m = lbl.match(/\(([^)]+)\)$/);
                if (m) { tag=m[1]; lbl=lbl.replace(/\s*\([^)]+\)$/, ''); }
            }
            h += '<div class="ss-item" data-idx="'+k+'">'
               + escHtml(lbl)
               + (tag ? '<span class="ss-tag">('+escHtml(tag)+')</span>' : '')
               + '</div>';
        }
        dd.innerHTML = h;
        dd.style.display = 'block';
        dd.querySelectorAll('.ss-item').forEach(function(el, k) {
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                var d = data[k];
                applySuggest(i, d);
                dd.style.display='none';
            });
        });
    };
    xhr.send();
}

function applySuggest(i, d) {
    var nomEl   = document.getElementById('nom_'+i);
    var emailEl = document.getElementById('email_'+i);
    var telEl   = document.getElementById('tel_'+i);
    if (nomEl   && d.nom)   nomEl.value   = d.nom;
    if (emailEl && d.email) emailEl.value = d.email;
    if (telEl   && d.tel)   telEl.value   = d.tel;
    var ddN = document.getElementById('dd_nom_'+i);
    var ddE = document.getElementById('dd_email_'+i);
    if (ddN) ddN.style.display='none';
    if (ddE) ddE.style.display='none';
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

buildSigs(1);
</script>
</div>

<?php elseif ($step === 'placement'): ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     ÉTAPE 4 — PLACEMENT DES ZONES (PDF.js)
══════════════════════════════════════════════════════════════════════════ -->
<?php
$sigs    = isset($_SESSION['sign_signataires']) ? $_SESSION['sign_signataires'] : array();
$ficNom  = isset($_SESSION['sign_fichier_nom'])? $_SESSION['sign_fichier_nom'] : 'document.pdf';
$nbSigs  = count($sigs);
?>
<div style="padding:6px 4px">

<div class="se-step-nav">
    <span class="se-step done">1. Connexion</span>
    <span class="se-step done">2. Accueil</span>
    <span class="se-step done">3. Document</span>
    <span class="se-step active">4. Zones</span>
</div>

<div class="card" style="margin-bottom:8px">
<div class="card-body" style="font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial;color:#444;padding:10px 14px">
    <i class="bi bi-info-circle" style="color:#080A66;margin-right:5px"></i>
    Cliquez sur le document PDF pour positionner la zone de signature de chaque signataire.
    Sélectionnez le signataire dans la liste, puis cliquez à l'emplacement souhaité.
</div>
</div>

<div style="display:flex;flex-direction:column;gap:10px">

  <!-- Panneau signataires (en haut, en ligne) -->
  <div>
    <div style="font-size:11px;font-weight:700;color:#080A66;margin-bottom:6px;font-family:Electrolize,'Trebuchet MS',Arial">
        Signataires — cliquez sur un signataire puis positionnez sa zone dans le PDF
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
    <?php foreach ($sigs as $idx => $s): ?>
    <div id="sig-panel-<?php echo $idx+1; ?>"
         onclick="selectSig(<?php echo $idx+1; ?>)"
         style="flex:1 1 150px;max-width:220px;padding:8px 10px;border-radius:6px;cursor:pointer;border:2px solid #c5cae9;background:#fff;font-family:Electrolize,'Trebuchet MS',Arial;font-size:11px">
        <div style="font-weight:700;color:#080A66"><?php echo $idx+1; ?>. <?php echo htmlspecialchars($s['nom']); ?></div>
        <div style="color:#888;font-size:10px"><?php echo htmlspecialchars($s['email']); ?></div>
        <div id="sig-status-<?php echo $idx+1; ?>" style="margin-top:4px;font-size:10px;color:#999">
            <i class="bi bi-circle"></i> Non positionné
        </div>
    </div>
    <?php endforeach; ?>
    </div>
  </div>

  <!-- PDF Canvas (en bas, pleine largeur) -->
  <div>
    <div style="font-size:11px;color:#555;margin-bottom:4px;font-family:Electrolize,'Trebuchet MS',Arial">
        Page : <span id="pageNum">1</span> / <span id="pageCount">—</span>
        &nbsp;
        <button type="button" onclick="changePage(-1)" class="btn" style="font-size:10px;padding:2px 7px">‹</button>
        <button type="button" onclick="changePage(1)"  class="btn" style="font-size:10px;padding:2px 7px">›</button>
    </div>
    <div class="se-pdf-wrap" id="pdfWrap">
        <canvas id="pdfCanvas"></canvas>
    </div>
  </div>

</div>

<!-- Formulaire d'envoi caché -->
<form method="post" id="frmSend" style="margin-top:12px">
    <input type="hidden" name="do_send" value="1">
    <?php for ($i=1; $i<=$nbSigs; $i++): ?>
    <input type="hidden" name="zoneX_<?php echo $i; ?>"      id="zoneX_<?php echo $i; ?>"      value="0">
    <input type="hidden" name="zoneY_<?php echo $i; ?>"      id="zoneY_<?php echo $i; ?>"      value="0">
    <input type="hidden" name="pageNumber_<?php echo $i; ?>" id="pageNumber_<?php echo $i; ?>" value="1">
    <?php endfor; ?>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <button type="button" onclick="submitSend()" class="btn btn-primary" style="font-size:12px">
            <i class="bi bi-send-fill"></i> Envoyer les demandes de signature
        </button>
        <script language=JavaScript>buttonMagicRetour("triade-sign-envoi.php?step=nouveau","_self");</script>
    </div>
</form>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
var pdfjsLib = window['pdfjs-dist/build/pdf'];
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

var pdfDoc = null, pageNum = 1, curSig = 1;
var nbSigs = <?php echo $nbSigs; ?>;
var placed = {};
var sigNames = <?php echo json_encode(array_map(function($s){ return $s['nom']; }, $sigs)); ?>;
var dragging = null; // {idx, offX, offY}
var SCALE = 1.4;
var PX_PER_MM = SCALE * 72 / 25.4; // pixels canvas → mm FPDF

document.addEventListener('mousemove', function(e) {
    if (!dragging) return;
    var wrap  = document.getElementById('pdfWrap');
    var wRect = wrap.getBoundingClientRect();
    var nx = e.clientX - wRect.left + wrap.scrollLeft - dragging.offX;
    var ny = e.clientY - wRect.top  + wrap.scrollTop  - dragging.offY;
    placed[dragging.idx].x = nx;
    placed[dragging.idx].y = ny;
    document.getElementById('zoneX_' + dragging.idx).value = Math.round(nx / PX_PER_MM * 10) / 10;
    document.getElementById('zoneY_' + dragging.idx).value = Math.round(ny / PX_PER_MM * 10) / 10;
    redrawMarkers();
});

document.addEventListener('mouseup', function() { dragging = null; });

pdfjsLib.getDocument('triade-sign-envoi.php?action=pdf').promise.then(function(doc) {
    pdfDoc = doc;
    document.getElementById('pageCount').textContent = doc.numPages;
    renderPage(1);
});

function renderPage(n) {
    pdfDoc.getPage(n).then(function(page) {
        var vp     = page.getViewport({scale: SCALE});
        var canvas = document.getElementById('pdfCanvas');
        canvas.height  = vp.height;
        canvas.width   = vp.width;
        canvas.onclick = placeZone;
        canvas.style.cursor = 'crosshair';
        page.render({canvasContext: canvas.getContext('2d'), viewport: vp});
        pageNum = n;
        document.getElementById('pageNum').textContent = n;
        redrawMarkers();
    });
}

function changePage(d) {
    if (!pdfDoc) return;
    var n = pageNum + d;
    if (n < 1 || n > pdfDoc.numPages) return;
    renderPage(n);
}

function selectSig(i) {
    curSig = i;
    for (var j=1; j<=nbSigs; j++) {
        var p = document.getElementById('sig-panel-'+j);
        if (p) p.style.borderColor = (j===i) ? '#080A66' : '#c5cae9';
    }
}

function placeZone(e) {
    var wrap = document.getElementById('pdfWrap');
    var x = e.offsetX;
    var y = e.offsetY;
    placed[curSig] = {x: x, y: y, page: pageNum};
    document.getElementById('zoneX_'+curSig).value        = Math.round(x / PX_PER_MM * 10) / 10;
    document.getElementById('zoneY_'+curSig).value        = Math.round(y / PX_PER_MM * 10) / 10;
    document.getElementById('pageNumber_'+curSig).value   = pageNum;
    var st = document.getElementById('sig-status-'+curSig);
    if (st) st.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#2e7d32"></i> Page '+pageNum+' ('+Math.round(x)+','+Math.round(y)+')';
    redrawMarkers();
    if (curSig < nbSigs) selectSig(curSig + 1);
}

function redrawMarkers() {
    var wrap = document.getElementById('pdfWrap');
    wrap.querySelectorAll('.se-marker').forEach(function(m){ m.remove(); });
    for (var i=1; i<=nbSigs; i++) {
        if (placed[i] && placed[i].page === pageNum) {
            var m = document.createElement('div');
            m.className = 'se-marker';
            m.style.left = placed[i].x + 'px';
            m.style.top  = placed[i].y + 'px';
            m.innerHTML  = '<div class="se-marker-num">Signataire '+i+'</div>'
                         + '<div class="se-marker-name">'+(sigNames[i-1]||'')+'</div>';
            (function(idx, el) {
                el.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var wrap  = document.getElementById('pdfWrap');
                    var wRect = wrap.getBoundingClientRect();
                    var mx = e.clientX - wRect.left + wrap.scrollLeft;
                    var my = e.clientY - wRect.top  + wrap.scrollTop;
                    dragging = {
                        idx:  idx,
                        offX: mx - placed[idx].x,
                        offY: my - placed[idx].y
                    };
                });
            })(i, m);
            wrap.appendChild(m);
        }
    }
}

function submitSend() {
    var missing = [];
    for (var i=1; i<=nbSigs; i++) {
        if (!placed[i]) missing.push(i);
    }
    if (missing.length) {
        alertify.error('Signataire(s) non positionné(s) : ' + missing.join(', '));
        return;
    }
    document.getElementById('frmSend').submit();
}

selectSig(1);
</script>

<?php elseif ($step === 'result'): ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     RÉSULTAT
══════════════════════════════════════════════════════════════════════════ -->
<div style="padding:10px 4px">
<?php if ($signSent > 0): ?>
<div class="card">
<div class="card-body" style="text-align:center;padding:24px">
    <i class="bi bi-check-circle-fill" style="font-size:36px;color:#2e7d32;display:block;margin-bottom:12px"></i>
    <div style="font-size:15px;font-weight:700;color:#2e7d32;font-family:Electrolize,'Trebuchet MS',Arial">
        <?php echo $signSent; ?> email<?php echo $signSent>1?'s':''; ?> de signature envoyé<?php echo $signSent>1?'s':''; ?> !
    </div>
    <div style="font-size:12px;color:#666;margin-top:8px">
        Les signataires ont reçu un lien par email pour signer le document.
    </div>
    <div style="margin-top:16px">
        <a href="triade-sign-envoi.php?step=accueil" class="btn btn-primary" style="font-size:12px">
            <i class="bi bi-arrow-left"></i> Retour aux demandes
        </a>
    </div>
</div>
</div>
<?php else: ?>
<div class="card">
<div class="card-body" style="text-align:center;padding:24px">
    <i class="bi bi-x-circle-fill" style="font-size:36px;color:#c62828;display:block;margin-bottom:12px"></i>
    <div style="font-size:14px;font-weight:700;color:#c62828;font-family:Electrolize,'Trebuchet MS',Arial">
        Erreur lors de l'envoi.
    </div>
    <div style="margin-top:16px">
        <a href="triade-sign-envoi.php?step=accueil" class="btn btn-primary" style="font-size:12px">Retour</a>
    </div>
</div>
</div>
<?php endif; ?>
</div>

<?php endif; ?>

</div>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
