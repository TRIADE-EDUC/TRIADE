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
include_once("./librairie_php/db_triade_admin.php");
include_once("../librairie_php/timezone.php");
include_once("../common/config2.inc.php");

$cnx     = cnx();
$prefixe = PREFIXE;

$ssoConfigFile = __DIR__ . '/../sso/config.php';
$ssoKeysDir    = __DIR__ . '/../sso/keys';
$ssoConfigOk   = file_exists($ssoConfigFile);

// CSRF
if (empty($_SESSION['oidc_admin_csrf'])) {
    $_SESSION['oidc_admin_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['oidc_admin_csrf'];

$msgs = ['rsa' => '', 'tables' => '', 'config' => '', 'client' => ''];
$errs = ['rsa' => '', 'tables' => '', 'config' => '', 'client' => ''];

// ─── Helpers config.php ──────────────────────────────────────────────────────

function oidcLireConfig(string $f): array {
    if (!file_exists($f)) return ['sso' => [], 'oidc' => []];
    $raw = file_get_contents($f);

    // SSO JWT (préservé)
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

    // OIDC
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

function oidcGenererConfig(array $sso, array $oidc): string {
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
 * (généré via admin/config_oidc.php)
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

function oidcEcrireConfig(string $fichier, array $sso, array $oidc): bool {
    $ok = file_put_contents($fichier, oidcGenererConfig($sso, $oidc)) !== false;
    if ($ok && function_exists('opcache_invalidate')) opcache_invalidate($fichier, true);
    return $ok;
}

function tableExists(string $prefixe, string $suffix): bool {
    $res = @execSql("SHOW TABLES LIKE '{$prefixe}{$suffix}'");
    return $res && !empty(@chargeMat($res));
}

// ─── Traitement POST ─────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $errs['config'] = 'Requête invalide (CSRF).';
    } else {
        $action  = $_POST['action'] ?? '';
        $current = oidcLireConfig($ssoConfigFile);

        // ── Étape 1 : Génération clés RSA ─────────────────────────────────────
        if ($action === 'generer_rsa') {
            $forcer = isset($_POST['rsa_force']);
            if (!is_dir($ssoKeysDir)) @mkdir($ssoKeysDir, 0750, true);

            if (file_exists($ssoKeysDir . '/private.pem') && !$forcer) {
                $errs['rsa'] = 'Des clés existent déjà. Cochez "Régénérer" pour les remplacer.';
            } else {
                $res = openssl_pkey_new([
                    'digest_alg'       => 'sha256',
                    'private_key_bits' => 2048,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA,
                ]);
                if (!$res) {
                    $errs['rsa'] = 'Erreur OpenSSL : ' . openssl_error_string();
                } else {
                    openssl_pkey_export($res, $privateKey);
                    $details   = openssl_pkey_get_details($res);
                    $publicKey = $details['key'];
                    $kid       = substr(hash('sha256', $publicKey), 0, 16);

                    $ok  = file_put_contents($ssoKeysDir . '/private.pem', $privateKey) !== false;
                    $ok &= file_put_contents($ssoKeysDir . '/public.pem',  $publicKey)  !== false;
                    $ok &= file_put_contents($ssoKeysDir . '/kid.txt',      $kid)        !== false;
                    @chmod($ssoKeysDir . '/private.pem', 0640);

                    if ($ok && $ssoConfigOk) {
                        $newOidc = $current['oidc'];
                        $newOidc['kid'] = $kid;
                        if (oidcEcrireConfig($ssoConfigFile, $current['sso'], $newOidc)) {
                            $_SESSION['oidc_admin_csrf'] = bin2hex(random_bytes(16));
                            $csrf = $_SESSION['oidc_admin_csrf'];
                            history_cmd($_SESSION['nom'] ?? 'admin', 'OIDC-RSA', "Clés RSA générées, kid=$kid");
                            $msgs['rsa'] = "Clés RSA 2048 bits générées. kid enregistré dans config.php&nbsp;: <strong>$kid</strong>";
                            $current = oidcLireConfig($ssoConfigFile);
                        } else {
                            $errs['rsa'] = 'Clés générées mais impossible de mettre à jour config.php.';
                        }
                    } elseif (!$ok) {
                        $errs['rsa'] = "Impossible d'écrire dans $ssoKeysDir — vérifier les permissions.";
                    }
                }
            }
        }

        // ── Étape 2 : Création tables DB ──────────────────────────────────────
        elseif ($action === 'creer_tables') {
            if (tableExists($prefixe, 'oidc_clients') && tableExists($prefixe, 'oidc_auth_codes') && tableExists($prefixe, 'oidc_tokens')) {
                $errs['tables'] = 'Les 3 tables OIDC sont déjà présentes — aucune action effectuée.';
            } else {
            $sqlTables = [
                "CREATE TABLE IF NOT EXISTS `{$prefixe}oidc_clients` (
                    `id`            int(11)       NOT NULL AUTO_INCREMENT,
                    `client_id`     varchar(64)   NOT NULL,
                    `client_secret` varchar(128)  NOT NULL DEFAULT '',
                    `client_name`   varchar(100)  NOT NULL DEFAULT '',
                    `redirect_uris` text          NOT NULL,
                    `scopes`        varchar(255)  NOT NULL DEFAULT 'openid profile',
                    `grant_types`   varchar(100)  NOT NULL DEFAULT 'authorization_code',
                    `pkce_required` tinyint(1)    NOT NULL DEFAULT 0,
                    `active`        tinyint(1)    NOT NULL DEFAULT 1,
                    `created_at`    datetime      NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `client_id` (`client_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

                "CREATE TABLE IF NOT EXISTS `{$prefixe}oidc_auth_codes` (
                    `id`                    int(11)      NOT NULL AUTO_INCREMENT,
                    `code`                  varchar(128) NOT NULL,
                    `client_id`             varchar(64)  NOT NULL,
                    `id_pers`               int(11)      NOT NULL DEFAULT 0,
                    `membre`                varchar(50)  NOT NULL DEFAULT '',
                    `nom`                   varchar(100) NOT NULL DEFAULT '',
                    `prenom`                varchar(100) NOT NULL DEFAULT '',
                    `redirect_uri`          varchar(500) NOT NULL DEFAULT '',
                    `scope`                 varchar(255) NOT NULL DEFAULT 'openid',
                    `nonce`                 varchar(255) NOT NULL DEFAULT '',
                    `code_challenge`        varchar(255) NOT NULL DEFAULT '',
                    `code_challenge_method` varchar(10)  NOT NULL DEFAULT '',
                    `expires_at`            datetime     NOT NULL,
                    `used`                  tinyint(1)   NOT NULL DEFAULT 0,
                    `created_at`            datetime     NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `code` (`code`),
                    KEY `client_id` (`client_id`),
                    KEY `expires_at` (`expires_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

                "CREATE TABLE IF NOT EXISTS `{$prefixe}oidc_tokens` (
                    `id`           int(11)      NOT NULL AUTO_INCREMENT,
                    `access_token` varchar(128) NOT NULL,
                    `client_id`    varchar(64)  NOT NULL,
                    `id_pers`      int(11)      NOT NULL DEFAULT 0,
                    `membre`       varchar(50)  NOT NULL DEFAULT '',
                    `nom`          varchar(100) NOT NULL DEFAULT '',
                    `prenom`       varchar(100) NOT NULL DEFAULT '',
                    `scope`        varchar(255) NOT NULL DEFAULT 'openid',
                    `expires_at`   datetime     NOT NULL,
                    `revoked`      tinyint(1)   NOT NULL DEFAULT 0,
                    `created_at`   datetime     NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `access_token` (`access_token`),
                    KEY `client_id` (`client_id`),
                    KEY `expires_at` (`expires_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
            ];

            $errSql = '';
            foreach ($sqlTables as $sql) {
                $res = @execSql($sql);
                if ($res === false) { $errSql = 'Erreur SQL : ' . (error_get_last()['message'] ?? '?'); break; }
            }
            if ($errSql) {
                $errs['tables'] = $errSql;
            } else {
                $_SESSION['oidc_admin_csrf'] = bin2hex(random_bytes(16));
                $csrf = $_SESSION['oidc_admin_csrf'];
                history_cmd($_SESSION['nom'] ?? 'admin', 'OIDC-TABLES', 'Tables OIDC créées');
                $msgs['tables'] = 'Tables OIDC créées avec succès.';
            }
            } // end else (tables absentes)
        }

        // ── Étape 3 : Configuration OIDC ──────────────────────────────────────
        elseif ($action === 'config_oidc') {
            $newIssuer   = rtrim(trim($_POST['oidc_issuer']            ?? ''), '/');
            $newCode     = max(60,  (int)($_POST['oidc_code_lifetime']     ?? 600));
            $newToken    = max(300, (int)($_POST['oidc_token_lifetime']    ?? 3600));
            $newIdToken  = max(300, (int)($_POST['oidc_id_token_lifetime'] ?? 3600));

            if (empty($newIssuer) || !preg_match('#^https?://#', $newIssuer)) {
                $errs['config'] = 'OIDC Issuer invalide — URL complète requise (https://...).';
            } elseif (!$ssoConfigOk) {
                $errs['config'] = 'Fichier sso/config.php introuvable.';
            } else {
                $newOidc = [
                    'issuer'            => $newIssuer,
                    'code_lifetime'     => $newCode,
                    'token_lifetime'    => $newToken,
                    'id_token_lifetime' => $newIdToken,
                    'kid'               => $current['oidc']['kid'],
                ];
                if (oidcEcrireConfig($ssoConfigFile, $current['sso'], $newOidc)) {
                    $_SESSION['oidc_admin_csrf'] = bin2hex(random_bytes(16));
                    $csrf = $_SESSION['oidc_admin_csrf'];
                    history_cmd($_SESSION['nom'] ?? 'admin', 'OIDC-CONFIG', "OIDC_ISSUER=$newIssuer");
                    $msgs['config'] = 'Configuration OIDC enregistrée dans sso/config.php.';
                    $current = oidcLireConfig($ssoConfigFile);
                } else {
                    $errs['config'] = 'Impossible d\'écrire sso/config.php — vérifier les permissions.';
                }
            }
        }

        // ── Étape 4 : Ajouter un client ───────────────────────────────────────
        elseif ($action === 'ajouter_client') {
            $cId      = trim($_POST['client_id']     ?? '');
            $cName    = trim($_POST['client_name']   ?? '');
            $cSecret  = trim($_POST['client_secret'] ?? '');
            $cUris    = trim($_POST['redirect_uris'] ?? '');
            $cScopes  = trim($_POST['scopes']        ?? 'openid profile');
            $cPkce    = isset($_POST['pkce_required']) ? 1 : 0;

            if (empty($cId) || !preg_match('/^[a-zA-Z0-9_\-]{2,64}$/', $cId)) {
                $errs['client'] = 'client_id invalide (2-64 caractères alphanumériques, - ou _).';
            } elseif (empty($cUris)) {
                $errs['client'] = 'Au moins une redirect_uri est requise.';
            } elseif (!tableExists($prefixe, 'oidc_clients')) {
                $errs['client'] = 'Table tria_oidc_clients absente — créer les tables d\'abord (étape 2).';
            } else {
                $uriList = array_values(array_filter(array_map('trim', explode("\n", $cUris))));
                $uriJson = addslashes(json_encode($uriList));
                $cIdEsc  = addslashes($cId);
                $cNamEsc = addslashes($cName);
                $cSecEsc = addslashes($cSecret);
                $cScoEsc = addslashes($cScopes);
                $now     = date('Y-m-d H:i:s');

                // Vérifier doublon
                $r = @chargeMat(@execSql("SELECT id FROM {$prefixe}oidc_clients WHERE client_id='$cIdEsc' LIMIT 1"));
                if (!empty($r)) {
                    $errs['client'] = "Un client avec l'id \"$cId\" existe déjà.";
                } else {
                    $ok = @execSql("INSERT INTO {$prefixe}oidc_clients
                        (client_id, client_secret, client_name, redirect_uris, scopes, grant_types, pkce_required, active, created_at)
                        VALUES ('$cIdEsc','$cSecEsc','$cNamEsc','$uriJson','$cScoEsc','authorization_code',$cPkce,1,'$now')");
                    if ($ok !== false) {
                        $_SESSION['oidc_admin_csrf'] = bin2hex(random_bytes(16));
                        $csrf = $_SESSION['oidc_admin_csrf'];
                        history_cmd($_SESSION['nom'] ?? 'admin', 'OIDC-CLIENT', "Client ajouté : $cId");
                        $msgs['client'] = "Client \"$cId\" enregistré avec succès.";
                    } else {
                        $errs['client'] = 'Erreur SQL lors de l\'insertion.';
                    }
                }
            }
        }

        // ── Toggle client actif/inactif ────────────────────────────────────────
        elseif ($action === 'toggle_client') {
            $cId  = addslashes(trim($_POST['toggle_client_id'] ?? ''));
            $etat = (int)($_POST['toggle_etat'] ?? 0) ? 0 : 1;
            @execSql("UPDATE {$prefixe}oidc_clients SET active=$etat WHERE client_id='$cId'");
            $msgs['client'] = 'Statut du client mis à jour.';
        }

        // ── Supprimer client ───────────────────────────────────────────────────
        elseif ($action === 'supprimer_client') {
            $cId = addslashes(trim($_POST['del_client_id'] ?? ''));
            @execSql("DELETE FROM {$prefixe}oidc_clients WHERE client_id='$cId'");
            history_cmd($_SESSION['nom'] ?? 'admin', 'OIDC-CLIENT', "Client supprimé : $cId");
            $msgs['client'] = "Client \"$cId\" supprimé.";
        }
    }
}

// ─── État courant ─────────────────────────────────────────────────────────────

$cfg  = oidcLireConfig($ssoConfigFile);
$oidc = $cfg['oidc'] ?? [];

$rsaPrivateOk = file_exists($ssoKeysDir . '/private.pem');
$rsaPublicOk  = file_exists($ssoKeysDir . '/public.pem');
$rsaOk        = $rsaPrivateOk && $rsaPublicOk && !in_array($oidc['kid'] ?? '', ['REMPLACER_PAR_KID_GENERE', '']);

$tblClients   = tableExists($prefixe, 'oidc_clients');
$tblAuthCodes = tableExists($prefixe, 'oidc_auth_codes');
$tblTokens    = tableExists($prefixe, 'oidc_tokens');
$tblOk        = $tblClients && $tblAuthCodes && $tblTokens;

$oidcIssuerOk = !empty($oidc['issuer']) && preg_match('#^https?://#', $oidc['issuer'] ?? '');

// Liste des clients
$clients = [];
if ($tblClients) {
    $res = @execSql("SELECT client_id, client_name, redirect_uris, scopes, pkce_required, active, created_at
                     FROM {$prefixe}oidc_clients ORDER BY client_name, client_id");
    $rows = @chargeMat($res);
    foreach ($rows ?: [] as $r) {
        $uris = json_decode($r[2], true) ?: [];
        $clients[] = [
            'client_id'     => $r[0],
            'client_name'   => $r[1],
            'redirect_uris' => $uris,
            'scopes'        => $r[3],
            'pkce_required' => (bool)$r[4],
            'active'        => (bool)$r[5],
            'created_at'    => $r[6],
        ];
    }
}

// Bilan global : les 4 étapes sont-elles complètes ?
$step1ok = $rsaOk;
$step2ok = $tblOk;
$step3ok = $oidcIssuerOk;
$step4ok = count($clients) > 0;
$allOk   = $step1ok && $step2ok && $step3ok && $step4ok;
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
function genSecretOidc(len) {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var arr   = new Uint8Array(len);
    window.crypto.getRandomValues(arr);
    var result = '';
    for (var i = 0; i < len; i++) { result += chars[arr[i] % chars.length]; }
    return result;
}
</script>
<title>Triade — Configuration SSO OIDC</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<?php
// Helper d'affichage de badge statut
function badgeOk(bool $ok, string $ok_label = 'OK', string $ko_label = 'À faire'): string {
    $color = $ok ? '#2e7d32' : '#c62828';
    $label = $ok ? "✓ $ok_label" : "✗ $ko_label";
    return "<span style='font-size:11px;font-weight:700;color:$color;'>$label</span>";
}
function msgDiv(string $msg, string $type = 'ok'): string {
    if (!$msg) return '';
    [$bg, $bd, $co] = $type === 'ok'
        ? ['#d4edda', '#b8dac4', '#155724']
        : ['#fff0f0', '#fcc',    '#c62828'];
    $icon = $type === 'ok' ? '✓ ' : '';
    return "<div style='margin:10px 8px;padding:9px 13px;background:$bg;border:1px solid $bd;border-radius:6px;color:$co;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;'>$icon$msg</div>";
}
?>

<!-- ═══════════════════════════════════════════════════════
     Bilan global
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration SSO OIDC — Bilan</font></b></td></tr>
<tr id="cadreCentral0"><td>

<div class="na-card" style="margin:12px 8px;">
    <div class="na-row">
        <span class="na-lbl">Étape 1 — Clés RSA</span>
        <?php echo badgeOk($step1ok, 'Clés générées', 'Clés manquantes'); ?>
    </div>
    <div class="na-row">
        <span class="na-lbl">Étape 2 — Tables DB</span>
        <?php echo badgeOk($step2ok, '3 tables présentes', 'Tables manquantes'); ?>
    </div>
    <div class="na-row">
        <span class="na-lbl">Étape 3 — OIDC Issuer</span>
        <?php echo badgeOk($step3ok, htmlspecialchars($oidc['issuer'] ?? ''), 'Non configuré'); ?>
    </div>
    <div class="na-row">
        <span class="na-lbl">Étape 4 — Clients</span>
        <?php echo badgeOk($step4ok, count($clients) . ' client(s)', 'Aucun client'); ?>
    </div>
    <?php if ($allOk): ?>
    <div style="margin-top:10px;padding:8px 12px;background:#d4edda;border:1px solid #b8dac4;border-radius:4px;font-size:12px;color:#155724;">
        ✓ Module OIDC pleinement opérationnel.
        <a href="<?php echo htmlspecialchars($oidc['issuer'] ?? ''); ?>/.well-known/openid-configuration"
           target="_blank" style="color:#0a4080;margin-left:10px;font-size:11px;">Tester le discovery ↗</a>
    </div>
    <?php endif; ?>
</div>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Étape 1 : Clés RSA
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Étape 1 — Génération des clés RSA</font> <?php echo badgeOk($step1ok); ?></b></td></tr>
<tr id="cadreCentral0"><td>

<?php echo msgDiv($msgs['rsa']); echo msgDiv($errs['rsa'], 'err'); ?>

<div class="na-card" style="margin:12px 8px;">
    <div class="na-row">
        <span class="na-lbl">Clé privée (private.pem) :</span>
        <?php echo badgeOk($rsaPrivateOk, 'présente', 'absente'); ?>
    </div>
    <div class="na-row">
        <span class="na-lbl">Clé publique (public.pem) :</span>
        <?php echo badgeOk($rsaPublicOk, 'présente', 'absente'); ?>
    </div>
    <div class="na-row">
        <span class="na-lbl">Key ID (kid) :</span>
        <span style="font-family:monospace;font-size:12px;color:<?php echo $rsaOk ? '#2e7d32' : '#c62828'; ?>;">
            <?php echo $rsaOk ? htmlspecialchars($oidc['kid'] ?? '') : 'non configuré'; ?>
        </span>
    </div>
    <?php if ($rsaPrivateOk): ?>
    <div style="margin-top:6px;font-size:11px;color:#856404;">
        ⚠ Régénérer les clés invalidera tous les id_token OIDC en cours.
    </div>
    <?php endif; ?>
</div>

<form method="post" action="config_oidc.php"
      onsubmit="return <?php echo $rsaPrivateOk ? "confirm('Régénérer les clés RSA ? Tous les tokens OIDC actifs seront invalidés.')" : 'true'; ?>">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
<input type="hidden" name="action"     value="generer_rsa">
<?php if ($rsaPrivateOk): ?>
<div class="na-card" style="margin:0 8px 8px;">
    <div class="na-row">
        <span class="na-lbl">Remplacer les clés existantes :</span>
        <label>
            <input type="checkbox" name="rsa_force" value="1">
            Oui, régénérer (anciens tokens invalidés)
        </label>
    </div>
</div>
<?php endif; ?>
<div class="na-foot">
    <button type="submit" class="btn-enr"><?php echo $rsaPrivateOk ? 'Régénérer les clés RSA' : 'Générer les clés RSA'; ?></button>
</div>
<br>
</form>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Étape 2 : Tables DB
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Étape 2 — Création des tables DB</font> <?php echo badgeOk($tblOk); ?></b></td></tr>
<tr id="cadreCentral0"><td>

<?php echo msgDiv($msgs['tables']); echo msgDiv($errs['tables'], 'err'); ?>

<div class="na-card" style="margin:12px 8px;">
    <?php
    $tablesList = [
        'oidc_clients'    => [$tblClients,   'Registre des applications clientes'],
        'oidc_auth_codes' => [$tblAuthCodes, 'Codes d\'autorisation courts-vécus'],
        'oidc_tokens'     => [$tblTokens,    'Access tokens actifs'],
    ];
    foreach ($tablesList as $suffix => [$exists, $label]): ?>
    <div class="na-row">
        <span class="na-lbl"><code><?php echo $prefixe . $suffix; ?></code> — <?php echo $label; ?></span>
        <?php echo badgeOk($exists, 'présente', 'absente'); ?>
    </div>
    <?php endforeach; ?>
</div>

<?php if (!$tblOk): ?>
<form method="post" action="config_oidc.php">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
<input type="hidden" name="action"     value="creer_tables">
<div class="na-foot">
    <button type="submit" class="btn-enr">Créer les tables manquantes</button>
</div>
<br>
</form>
<?php else: ?>
<div style="margin:8px;font-size:11px;color:#2e7d32;">✓ Toutes les tables sont présentes.</div>
<br>
<?php endif; ?>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Étape 3 : Configuration OIDC (config.php)
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Étape 3 — Configuration OIDC (sso/config.php)</font> <?php echo badgeOk($step3ok); ?></b></td></tr>
<tr id="cadreCentral0"><td>

<?php echo msgDiv($msgs['config']); echo msgDiv($errs['config'], 'err'); ?>

<?php if (!$ssoConfigOk): ?>
<div style="margin:10px 8px;padding:9px;background:#fff0f0;border:1px solid #fcc;border-radius:6px;font-size:12px;color:#c62828;">
    Fichier sso/config.php introuvable.
</div>
<?php else: ?>

<form method="post" action="config_oidc.php">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
<input type="hidden" name="action"     value="config_oidc">

<div class="na-card" style="margin:12px 8px;">

    <div class="na-row">
        <span class="na-lbl" style="min-width:220px;">OIDC Issuer <span style="font-weight:400;font-size:10px;">(URL du serveur, sans slash final)</span> :</span>
        <input type="url" name="oidc_issuer"
               value="<?php echo htmlspecialchars($oidc['issuer'] ?? 'https://triade.monecole.fr/sso'); ?>"
               size="42" maxlength="200" class="cc-select" placeholder="https://triade.monecole.fr/sso">
    </div>
    <div style="margin:0 0 8px 0;font-size:11px;color:#666;padding-left:225px;">
        Discovery : <code><?php echo htmlspecialchars(($oidc['issuer'] ?? '...')); ?>/.well-known/openid-configuration</code>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:220px;">Durée code autorisation <span style="font-weight:400;font-size:10px;">(sec.)</span> :</span>
        <span style="display:flex;gap:8px;align-items:center;">
            <input type="number" name="oidc_code_lifetime" value="<?php echo $oidc['code_lifetime'] ?? 600; ?>"
                   min="60" max="3600" class="cc-select" style="width:90px;">
            <span style="font-size:11px;color:#555;"><?php echo round(($oidc['code_lifetime'] ?? 600) / 60); ?> min</span>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:220px;">Durée access_token <span style="font-weight:400;font-size:10px;">(sec.)</span> :</span>
        <span style="display:flex;gap:8px;align-items:center;">
            <input type="number" name="oidc_token_lifetime" value="<?php echo $oidc['token_lifetime'] ?? 3600; ?>"
                   min="300" max="86400" class="cc-select" style="width:90px;">
            <span style="font-size:11px;color:#555;"><?php echo round(($oidc['token_lifetime'] ?? 3600) / 60); ?> min</span>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:220px;">Durée id_token <span style="font-weight:400;font-size:10px;">(sec.)</span> :</span>
        <span style="display:flex;gap:8px;align-items:center;">
            <input type="number" name="oidc_id_token_lifetime" value="<?php echo $oidc['id_token_lifetime'] ?? 3600; ?>"
                   min="300" max="86400" class="cc-select" style="width:90px;">
            <span style="font-size:11px;color:#555;"><?php echo round(($oidc['id_token_lifetime'] ?? 3600) / 60); ?> min</span>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:220px;">Key ID actuel (kid) :</span>
        <span style="font-family:monospace;font-size:12px;color:<?php echo $rsaOk ? '#2e7d32' : '#c62828'; ?>;">
            <?php echo $rsaOk ? htmlspecialchars($oidc['kid'] ?? '') : 'générer les clés d\'abord (étape 1)'; ?>
        </span>
    </div>

</div>
<div class="na-foot">
    <button type="submit" class="btn-enr">Enregistrer la configuration OIDC</button>
</div>
<br>
</form>
<?php endif; ?>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Étape 4 : Clients OIDC
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Étape 4 — Clients OIDC (tria_oidc_clients)</font> <?php echo badgeOk($step4ok, count($clients) . ' client(s)', 'Aucun client'); ?></b></td></tr>
<tr id="cadreCentral0"><td>

<?php echo msgDiv($msgs['client']); echo msgDiv($errs['client'], 'err'); ?>

<?php if (!$tblClients): ?>
<div style="margin:10px 8px;padding:9px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#856404;">
    Table tria_oidc_clients absente — effectuer l'étape 2 d'abord.
</div>
<?php else: ?>

<!-- Liste des clients existants -->
<?php if ($clients): ?>
<div class="na-card" style="margin:12px 8px 8px;">
    <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;">Clients enregistrés</div>
    <table width="100%" cellpadding="4" cellspacing="0" style="font-size:12px;border-collapse:collapse;">
    <tr class="cc-th">
        <td><b>client_id</b></td>
        <td><b>Nom</b></td>
        <td><b>PKCE</b></td>
        <td><b>Redirect URIs</b></td>
        <td><b>Statut</b></td>
        <td><b>Actions</b></td>
    </tr>
    <?php foreach ($clients as $i => $c): ?>
    <tr class="<?php echo $i % 2 === 0 ? 'cc-tr-data' : ''; ?>">
        <td><code><?php echo htmlspecialchars($c['client_id']); ?></code></td>
        <td><?php echo htmlspecialchars($c['client_name']); ?></td>
        <td style="text-align:center;"><?php echo $c['pkce_required'] ? '✓' : '—'; ?></td>
        <td style="font-size:11px;max-width:180px;word-break:break-all;">
            <?php echo implode('<br>', array_map('htmlspecialchars', $c['redirect_uris'])); ?>
        </td>
        <td><?php echo badgeOk($c['active'], 'actif', 'inactif'); ?></td>
        <td>
            <form method="post" action="config_oidc.php" style="display:inline;">
                <input type="hidden" name="csrf_token"       value="<?php echo $csrf; ?>">
                <input type="hidden" name="action"           value="toggle_client">
                <input type="hidden" name="toggle_client_id" value="<?php echo htmlspecialchars($c['client_id']); ?>">
                <input type="hidden" name="toggle_etat"      value="<?php echo $c['active'] ? 1 : 0; ?>">
                <button type="submit" class="btn-retour" style="font-size:11px;padding:2px 8px;">
                    <?php echo $c['active'] ? 'Désactiver' : 'Activer'; ?>
                </button>
            </form>
            <form method="post" action="config_oidc.php" style="display:inline;"
                  onsubmit="return confirm('Supprimer le client <?php echo htmlspecialchars($c['client_id'], ENT_QUOTES); ?> ?')">
                <input type="hidden" name="csrf_token"    value="<?php echo $csrf; ?>">
                <input type="hidden" name="action"        value="supprimer_client">
                <input type="hidden" name="del_client_id" value="<?php echo htmlspecialchars($c['client_id']); ?>">
                <button type="submit" class="btn-retour" style="font-size:11px;padding:2px 8px;background:#c62828;color:#fff;">Supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<!-- Formulaire ajout client -->
<div class="na-card" style="margin:8px;">
    <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:10px;">Ajouter un client</div>

    <form method="post" action="config_oidc.php">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="action"     value="ajouter_client">

    <div class="na-row">
        <span class="na-lbl" style="min-width:180px;">client_id <span style="font-weight:400;font-size:10px;">(unique, 2-64 car.)</span> :</span>
        <input type="text" name="client_id" size="30" maxlength="64" class="cc-select"
               placeholder="moodle, pronote…" pattern="[a-zA-Z0-9_\-]{2,64}" required>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:180px;">Nom affiché :</span>
        <input type="text" name="client_name" size="30" maxlength="100" class="cc-select">
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:180px;">client_secret <span style="font-weight:400;font-size:10px;">(vide = client public PKCE)</span> :</span>
        <span style="display:flex;gap:6px;align-items:center;">
            <input type="text" name="client_secret" id="oidc_cs" size="36" maxlength="128" class="cc-select">
            <button type="button" class="btn-retour" onclick="document.getElementById('oidc_cs').value=genSecretOidc(48)">Générer</button>
        </span>
    </div>

    <div class="na-row" style="align-items:flex-start;">
        <span class="na-lbl" style="min-width:180px;padding-top:4px;">redirect_uris <span style="font-weight:400;font-size:10px;">(une par ligne)</span> :</span>
        <textarea name="redirect_uris" rows="4" class="cc-select" style="width:280px;resize:vertical;"
                  placeholder="https://moodle.ecole.fr/auth/oauth2/callback.php"></textarea>
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:180px;">Scopes autorisés :</span>
        <input type="text" name="scopes" value="openid profile" size="30" maxlength="100" class="cc-select">
    </div>

    <div class="na-row">
        <span class="na-lbl" style="min-width:180px;">PKCE obligatoire :</span>
        <label>
            <input type="checkbox" name="pkce_required" value="1">
            Oui <span style="font-size:11px;color:#666;">(apps mobiles — pas de client_secret)</span>
        </label>
    </div>

    <div class="na-foot" style="margin-top:10px;">
        <button type="submit" class="btn-enr">Enregistrer le client</button>
    </div>
    </form>
</div>

<?php endif; ?>

</td></tr></table>

<!-- ═══════════════════════════════════════════════════════
     Documentation SSO OIDC
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Documentation SSO OIDC</font></b></td></tr>
<tr id="cadreCentral0"><td>

<div class="dest-list" style="margin:8px 5px;">
    <div class="dest-row">
        <span class="dest-row-label">Documentation SSO OIDC — flux, endpoints, sécurité, intégration</span>
        <a href="oidc_doc.php" class="btn-dest">Consulter</a>
    </div>
</div>

</td></tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
