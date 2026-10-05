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
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../common/config2.inc.php");

$cnx     = cnx();
$prefixe = PREFIXE;

$ssoConfigFile = __DIR__ . '/../sso/config.php';
$ssoConfigOk   = file_exists($ssoConfigFile);

// CSRF
if (empty($_SESSION['sso_admin_csrf'])) {
    $_SESSION['sso_admin_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['sso_admin_csrf'];

$msgOk    = '';
$msgError = '';

// ─── Lecture complète de config.php (SSO JWT + OIDC) ─────────────────────────
// Les deux sections sont lues et réécrites ensemble pour ne pas s'effacer.

function lireSsoConfig(string $f): array {
    $raw = file_get_contents($f);

    // SSO JWT
    preg_match("/define\('SSO_SECRET',\s*'(.*)'\)/U", $raw, $m);
    $secret = isset($m[1]) ? stripslashes($m[1]) : 'CHANGER_MOI_CLE_SECRETE_MIN32CARS!!';
    preg_match("/define\('SSO_TOKEN_LIFETIME',\s*(\d+)\)/", $raw, $m);
    $lifetime = isset($m[1]) ? (int)$m[1] : 3600;
    preg_match("/define\('SSO_ISSUER',\s*'(.*)'\)/U", $raw, $m);
    $issuer = isset($m[1]) ? stripslashes($m[1]) : 'triade-sso';
    preg_match("/define\('SSO_FORCE_HTTPS',\s*(true|false)\)/", $raw, $m);
    $https = isset($m[1]) && $m[1] === 'true';
    preg_match('/\$SSO_ALLOWED_SERVICES\s*=\s*\[(.*?)\];/s', $raw, $sm);
    $services = [];
    if (isset($sm[1])) {
        preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $sm[1], $sm2);
        $services = $sm2[1] ?? [];
    }

    // OIDC — préservé tel quel lors de la sauvegarde SSO JWT
    preg_match("/define\('OIDC_ISSUER',\s*'(.*)'\)/U", $raw, $m);
    $oidcIssuer = isset($m[1]) ? stripslashes($m[1]) : 'https://triade.monecole.fr/sso';
    preg_match("/define\('OIDC_CODE_LIFETIME',\s*(\d+)\)/", $raw, $m);
    $oidcCodeLifetime = isset($m[1]) ? (int)$m[1] : 600;
    preg_match("/define\('OIDC_TOKEN_LIFETIME',\s*(\d+)\)/", $raw, $m);
    $oidcTokenLifetime = isset($m[1]) ? (int)$m[1] : 3600;
    preg_match("/define\('OIDC_ID_TOKEN_LIFETIME',\s*(\d+)\)/", $raw, $m);
    $oidcIdTokenLifetime = isset($m[1]) ? (int)$m[1] : 3600;
    preg_match("/define\('OIDC_KEY_ID',\s*'(.*)'\)/U", $raw, $m);
    $oidcKid = isset($m[1]) ? stripslashes($m[1]) : 'REMPLACER_PAR_KID_GENERE';

    return [
        'sso'  => compact('secret', 'lifetime', 'issuer', 'https', 'services'),
        'oidc' => [
            'issuer'            => $oidcIssuer,
            'code_lifetime'     => $oidcCodeLifetime,
            'token_lifetime'    => $oidcTokenLifetime,
            'id_token_lifetime' => $oidcIdTokenLifetime,
            'kid'               => $oidcKid,
        ],
    ];
}

function genererConfig(array $sso, array $oidc): string {
    $secretEsc     = str_replace("'", "\\'", $sso['secret']);
    $issuerEsc     = str_replace("'", "\\'", $sso['issuer']);
    $oidcIssuerEsc = str_replace("'", "\\'", $oidc['issuer']);
    $oidcKidEsc    = str_replace("'", "\\'", $oidc['kid']);
    $https         = $sso['https'] ? 'true' : 'false';
    $jwtLifetime   = (int)$sso['lifetime'];
    $oidcCode      = (int)$oidc['code_lifetime'];
    $oidcToken     = (int)$oidc['token_lifetime'];
    $oidcIdToken   = (int)$oidc['id_token_lifetime'];
    $servicesPhp   = '';
    foreach ($sso['services'] as $svc) {
        $svc = str_replace("'", "\\'", $svc);
        $servicesPhp .= "    '$svc',\n";
    }
    return <<<PHP
<?php
/*
 * Configuration SSO/OIDC — Triade
 * (généré via admin/config_sso.php)
 */

// ─── SSO JWT historique (login.php / validate.php) ───────────────────────────

define('SSO_SECRET',         '$secretEsc');
define('SSO_TOKEN_LIFETIME', $jwtLifetime);
define('SSO_ISSUER',         '$issuerEsc');
define('SSO_FORCE_HTTPS',    $https);

\$SSO_ALLOWED_SERVICES = [
$servicesPhp];

// ─── OIDC (authorize.php / token.php / userinfo.php) ─────────────────────────

define('OIDC_ISSUER',        '$oidcIssuerEsc');
define('OIDC_CODE_LIFETIME',  $oidcCode);
define('OIDC_TOKEN_LIFETIME', $oidcToken);
define('OIDC_ID_TOKEN_LIFETIME', $oidcIdToken);
define('OIDC_KEY_DIR',       __DIR__ . '/keys');
define('OIDC_KEY_ID',        '$oidcKidEsc');
define('OIDC_SUPPORTED_SCOPES', 'openid profile email');
PHP;
}

function ecrireConfig(string $fichier, array $sso, array $oidc): bool {
    $ok = file_put_contents($fichier, genererConfig($sso, $oidc)) !== false;
    if ($ok && function_exists('opcache_invalidate')) opcache_invalidate($fichier, true);
    return $ok;
}

// ─── Traitement POST ─────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ssoConfigOk) {

    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $msgError = 'Requête invalide (CSRF).';
    } else {
        $current     = lireSsoConfig($ssoConfigFile);
        $newSecret   = trim($_POST['sso_secret']   ?? '');
        $newHttps    = isset($_POST['sso_https']);
        $newLifetime = max(300, (int)($_POST['sso_lifetime'] ?? 3600));
        $newIssuer   = trim($_POST['sso_issuer']   ?? 'triade-sso');
        $rawServices = trim($_POST['sso_services'] ?? '');

        if (strlen($newSecret) < 32) {
            $msgError = 'La clé secrète doit faire au moins 32 caractères.';
        } else {
            $services = array_values(array_filter(array_map('trim', explode("\n", $rawServices))));
            $newSso   = ['secret' => $newSecret, 'lifetime' => $newLifetime,
                         'issuer' => $newIssuer,  'https'    => $newHttps, 'services' => $services];

            if (ecrireConfig($ssoConfigFile, $newSso, $current['oidc'])) {
                $_SESSION['sso_admin_csrf'] = bin2hex(random_bytes(16));
                $csrf = $_SESSION['sso_admin_csrf'];
                history_cmd($_SESSION['nom'] ?? 'admin', 'SSO-CONFIG', 'Mise à jour SSO JWT');
                $msgOk = 'Configuration SSO JWT enregistrée.';
            } else {
                $msgError = 'Impossible d\'écrire sso/config.php — vérifier les permissions.';
            }
        }
    }
}

// ─── Valeurs courantes ────────────────────────────────────────────────────────

$cfg  = $ssoConfigOk ? lireSsoConfig($ssoConfigFile) : null;
$sso  = $cfg['sso']  ?? [];
$oidc = $cfg['oidc'] ?? [];

$secretDefault = ($sso['secret'] ?? '') === 'CHANGER_MOI_CLE_SECRETE_MIN32CARS!!';
$secretOk      = !$secretDefault && strlen($sso['secret'] ?? '') >= 32;

// Table de révocation
$tblSsoExists = false; $tblSsoTotal = 0; $tblSsoRevoked = 0;
$resCheck = @execSql("SHOW TABLES LIKE '{$prefixe}sso_tokens'");
if ($resCheck && !empty(@chargeMat($resCheck))) {
    $tblSsoExists = true;
    $r = @chargeMat(@execSql("SELECT COUNT(*) FROM {$prefixe}sso_tokens"));
    $tblSsoTotal  = isset($r[0][0]) ? (int)$r[0][0] : 0;
    $r = @chargeMat(@execSql("SELECT COUNT(*) FROM {$prefixe}sso_tokens WHERE revoked = 1"));
    $tblSsoRevoked= isset($r[0][0]) ? (int)$r[0][0] : 0;
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script>
function genererCle(len) {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var arr   = new Uint8Array(len);
    window.crypto.getRandomValues(arr);
    var result = '';
    for (var i = 0; i < len; i++) { result += chars[arr[i] % chars.length]; }
    return result;
}
</script>
<title>Triade — Configuration SSO JWT</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<!-- ═══════════════════════════════════════════════════════
     Section 1 : Configuration SSO JWT
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration SSO JWT</font></b></td></tr>
<tr id="cadreCentral0"><td>

<?php if (!$ssoConfigOk): ?>
    <div style="margin:16px 8px;padding:10px 14px;background:#fff0f0;border:1px solid #fcc;border-radius:6px;color:#c62828;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;">
        Fichier <code>sso/config.php</code> introuvable.
    </div>
<?php else: ?>

<?php if ($msgOk): ?>
    <div style="margin:12px 8px;padding:10px 14px;background:#d4edda;border:1px solid #b8dac4;border-radius:6px;color:#155724;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;">
        ✓ <?php echo htmlspecialchars($msgOk); ?>
    </div>
<?php endif; ?>
<?php if ($msgError): ?>
    <div style="margin:12px 8px;padding:10px 14px;background:#fff0f0;border:1px solid #fcc;border-radius:6px;color:#c62828;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;">
        <?php echo htmlspecialchars($msgError); ?>
    </div>
<?php endif; ?>
<?php if (!$secretOk): ?>
    <div style="margin:12px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;color:#856404;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;">
        ⚠ Clé secrète par défaut non modifiée — à changer avant toute mise en production.
    </div>
<?php endif; ?>

<form method="post" action="config_sso.php">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

<div class="na-card" style="margin-top:12px;">

    <div class="na-row">
        <span class="na-lbl" style="min-width:200px;">Clé secrète <span style="font-weight:400;font-size:10px;">(min. 32 car.)</span> :</span>
        <span style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
            <input type="text" name="sso_secret" id="sso_secret"
                   value="<?php echo htmlspecialchars($sso['secret'] ?? ''); ?>"
                   size="40" maxlength="128" class="cc-select">
            <button type="button" class="btn-retour"
                    onclick="document.getElementById('sso_secret').value=genererCle(48)">Générer</button>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:200px;">Forcer HTTPS <span style="font-weight:400;font-size:10px;">(recommandé)</span> :</span>
        <label>
            <input type="checkbox" name="sso_https" value="1" <?php echo !empty($sso['https']) ? 'checked' : ''; ?>>
            Activer la redirection HTTPS
        </label>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:200px;">Durée du token <span style="font-weight:400;font-size:10px;">(secondes)</span> :</span>
        <span style="display:flex;gap:8px;align-items:center;">
            <input type="number" name="sso_lifetime" value="<?php echo $sso['lifetime'] ?? 3600; ?>"
                   min="300" max="86400" class="cc-select" style="width:90px;">
            <span style="font-size:11px;color:#555;"><?php echo round(($sso['lifetime'] ?? 3600) / 60); ?> min actuellement</span>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:200px;">Émetteur (SSO_ISSUER) <span style="font-weight:400;font-size:10px;">(champ iss du JWT)</span> :</span>
        <input type="text" name="sso_issuer" value="<?php echo htmlspecialchars($sso['issuer'] ?? 'triade-sso'); ?>"
               size="30" maxlength="64" class="cc-select">
    </div>

    <div class="na-row" style="align-items:flex-start;">
        <span class="na-lbl" style="min-width:200px;padding-top:4px;">Services autorisés <span style="font-weight:400;font-size:10px;">(un par ligne, <code>*</code> = tous)</span> :</span>
        <textarea name="sso_services" rows="5" class="cc-select" style="width:260px;resize:vertical;"><?php
            echo htmlspecialchars(implode("\n", array_map('stripslashes', $sso['services'] ?? [])));
        ?></textarea>
    </div>

</div>
<br>
<div class="na-foot">
    <button type="submit" class="btn-enr">Enregistrer</button>
</div>
<br>
</form>

<!-- Table de révocation -->
<?php if ($tblSsoExists): ?>
<div class="na-card" style="margin-top:8px;">
    <div style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;margin-bottom:8px;">Table de révocation</div>
    <div class="na-row">
        <span class="na-lbl">Tokens enregistrés :</span>
        <span style="font-size:13px;font-weight:700;color:#080A66;"><?php echo $tblSsoTotal; ?></span>
    </div>
    <div class="na-row">
        <span class="na-lbl">Dont révoqués :</span>
        <span style="font-size:13px;font-weight:700;color:<?php echo $tblSsoRevoked > 0 ? '#c62828' : '#2e7d32'; ?>;"><?php echo $tblSsoRevoked; ?></span>
    </div>
</div>
<?php else: ?>
<div style="margin:8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;">
    Table <code>tria_sso_tokens</code> introuvable — exécuter le SQL indiqué dans <code>sso/config.php</code>.
</div>
<?php endif; ?>

<?php endif; ?>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Section 2 : Documentation SSO JWT
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Documentation SSO JWT</font></b></td></tr>
<tr id="cadreCentral0"><td>

<div class="dest-list" style="margin:8px 5px;">
    <div class="dest-row">
        <span class="dest-row-label">Documentation SSO JWT — flux, endpoints, sécurité, intégration</span>
        <a href="sso_doc.php" class="btn-dest">Consulter</a>
    </div>
</div>

</td></tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
