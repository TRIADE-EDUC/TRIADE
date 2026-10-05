<?php
/*
 * Configuration SSO/OIDC — Triade
 * (généré via admin/config_sso.php)
 */

// ─── SSO JWT historique (login.php / validate.php) ───────────────────────────

define('SSO_SECRET',         'rWhWPZxESH3nSKAQHxlAoWRsnDUNhDy6kKiDH4S6wBE2FCdB');
define('SSO_TOKEN_LIFETIME', 3600);
define('SSO_ISSUER',         'triade-sso');
define('SSO_FORCE_HTTPS',    false);

$SSO_ALLOWED_SERVICES = [
    '*',
];

// ─── OIDC (authorize.php / token.php / userinfo.php) ─────────────────────────

define('OIDC_ISSUER',        'https://dev.triade-v4.net/triade/sso');
define('OIDC_CODE_LIFETIME',  600);
define('OIDC_TOKEN_LIFETIME', 3600);
define('OIDC_ID_TOKEN_LIFETIME', 3600);
define('OIDC_KEY_DIR',       __DIR__ . '/keys');
define('OIDC_KEY_ID',        'cea39517181ca1ce');
define('OIDC_SUPPORTED_SCOPES', 'openid profile email');