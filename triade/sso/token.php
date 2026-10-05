<?php
/*
 * OIDC Token Endpoint — Triade
 * POST /sso/token.php
 * Content-Type: application/x-www-form-urlencoded
 *
 * Paramètres :
 *   grant_type    = authorization_code
 *   code          = AUTH_CODE
 *   client_id     = CLIENT_ID
 *   client_secret = CLIENT_SECRET   (clients confidentiels)
 *   redirect_uri  = REDIRECT_URI    (doit correspondre à authorize)
 *   code_verifier = VERIFIER        (clients publics avec PKCE)
 *
 * Retourne :
 *   { access_token, token_type, expires_in, id_token }
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Pragma: no-cache');

error_reporting(0);

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');
include_once('./config.php');
include_once('./lib/jwt_rsa.php');

$prefixe = PREFIXE;
$cnx     = cnx();

// ─── Helpers ─────────────────────────────────────────────────────────────────

function tokenError(string $error, string $desc, int $http = 400): void {
    http_response_code($http);
    echo json_encode(['error' => $error, 'error_description' => $desc]);
    exit;
}

// sub stable : "{membre}_{id_pers}"
function oidcSub(string $membre, int $idPers): string {
    return $membre . '_' . $idPers;
}

// ─── POST uniquement ─────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tokenError('invalid_request', 'Méthode non autorisée — POST requis.', 405);
}

$grantType   = trim($_POST['grant_type']    ?? '');
$code        = trim($_POST['code']          ?? '');
$clientId    = trim($_POST['client_id']     ?? '');
$clientSecret= trim($_POST['client_secret'] ?? '');
$redirectUri = trim($_POST['redirect_uri']  ?? '');
$codeVerifier= trim($_POST['code_verifier'] ?? '');

// Support HTTP Basic Auth pour client_secret
if (empty($clientId) && !empty($_SERVER['PHP_AUTH_USER'])) {
    $clientId     = $_SERVER['PHP_AUTH_USER'];
    $clientSecret = $_SERVER['PHP_AUTH_PW'] ?? '';
}

// ─── Validation de base ───────────────────────────────────────────────────────

if ($grantType !== 'authorization_code') {
    tokenError('unsupported_grant_type', 'Seul authorization_code est supporté.');
}
if (empty($code))        tokenError('invalid_request', 'Paramètre code manquant.');
if (empty($clientId))    tokenError('invalid_request', 'Paramètre client_id manquant.');
if (empty($redirectUri)) tokenError('invalid_request', 'Paramètre redirect_uri manquant.');

// ─── Chargement du client ─────────────────────────────────────────────────────

$cid = addslashes($clientId);
$res = execSql("SELECT client_id, client_secret, redirect_uris, pkce_required
                FROM {$prefixe}oidc_clients
                WHERE client_id = '$cid' AND active = 1 LIMIT 1");
$row = chargeMat($res);

if (empty($row)) {
    tokenError('invalid_client', 'Client inconnu ou inactif.', 401);
}

$client = [
    'client_id'     => $row[0][0],
    'client_secret' => $row[0][1],
    'redirect_uris' => json_decode($row[0][2], true) ?: [],
    'pkce_required' => (bool)$row[0][3],
];

// ─── Authentification du client ───────────────────────────────────────────────

$isPkceClient = !empty($codeVerifier);

if (!$isPkceClient) {
    // Client confidentiel → vérifier secret
    if (empty($clientSecret)) {
        tokenError('invalid_client', 'client_secret requis.', 401);
    }
    if (!hash_equals($client['client_secret'], $clientSecret)) {
        tokenError('invalid_client', 'client_secret invalide.', 401);
    }
}

// ─── Chargement et validation du code d'autorisation ─────────────────────────

$c = addslashes($code);
$res = execSql("SELECT id, client_id, id_pers, membre, nom, prenom, redirect_uri, scope,
                       nonce, code_challenge, code_challenge_method, expires_at, used
                FROM {$prefixe}oidc_auth_codes
                WHERE code = '$c' LIMIT 1");
$row = chargeMat($res);

if (empty($row)) {
    tokenError('invalid_grant', 'Code d\'autorisation inconnu.');
}

$ac = $row[0];
$authCode = [
    'id'                    => (int)$ac[0],
    'client_id'             => $ac[1],
    'id_pers'               => (int)$ac[2],
    'membre'                => $ac[3],
    'nom'                   => $ac[4],
    'prenom'                => $ac[5],
    'redirect_uri'          => $ac[6],
    'scope'                 => $ac[7],
    'nonce'                 => $ac[8],
    'code_challenge'        => $ac[9],
    'code_challenge_method' => $ac[10],
    'expires_at'            => $ac[11],
    'used'                  => (int)$ac[12],
];

if ($authCode['used']) {
    tokenError('invalid_grant', 'Code déjà utilisé.');
}

if (strtotime($authCode['expires_at']) < time()) {
    tokenError('invalid_grant', 'Code expiré.');
}

if ($authCode['client_id'] !== $clientId) {
    tokenError('invalid_grant', 'Code émis pour un autre client.');
}

if ($authCode['redirect_uri'] !== $redirectUri) {
    tokenError('invalid_grant', 'redirect_uri ne correspond pas.');
}

// ─── Vérification PKCE ───────────────────────────────────────────────────────

if (!empty($authCode['code_challenge'])) {
    if (empty($codeVerifier)) {
        tokenError('invalid_grant', 'code_verifier requis (PKCE).');
    }
    $method = $authCode['code_challenge_method'] ?: 'plain';
    if ($method === 'S256') {
        $computed = JwtRsa::b64u(hash('sha256', $codeVerifier, true));
    } else {
        $computed = $codeVerifier;
    }
    if (!hash_equals($authCode['code_challenge'], $computed)) {
        tokenError('invalid_grant', 'code_verifier invalide.');
    }
} elseif ($client['pkce_required']) {
    tokenError('invalid_grant', 'PKCE obligatoire pour ce client.');
}

// ─── Marquer le code comme utilisé ───────────────────────────────────────────

execSql("UPDATE {$prefixe}oidc_auth_codes SET used = 1 WHERE id = {$authCode['id']}");

// ─── Génération de l'access_token (opaque) ────────────────────────────────────

$accessToken = bin2hex(random_bytes(32));
$now         = time();
$expiresAt   = date('Y-m-d H:i:s', $now + OIDC_TOKEN_LIFETIME);
$createdAt   = date('Y-m-d H:i:s', $now);

$c_at     = addslashes($accessToken);
$c_cid    = addslashes($clientId);
$c_idPers = $authCode['id_pers'];
$c_membre = addslashes($authCode['membre']);
$c_nom    = addslashes($authCode['nom']);
$c_prenom = addslashes($authCode['prenom']);
$c_scope  = addslashes($authCode['scope']);

execSql("INSERT INTO {$prefixe}oidc_tokens
         (access_token, client_id, id_pers, membre, nom, prenom, scope, expires_at, revoked, created_at)
         VALUES
         ('$c_at','$c_cid',$c_idPers,'$c_membre','$c_nom','$c_prenom','$c_scope','$expiresAt',0,'$createdAt')");

// ─── Génération de l'id_token (RS256) ────────────────────────────────────────

$privateKey = @file_get_contents(OIDC_KEY_DIR . '/private.pem');
if (!$privateKey) {
    http_response_code(500);
    echo json_encode(['error' => 'server_error', 'error_description' => 'Clé RSA non disponible']);
    exit;
}

$sub = oidcSub($authCode['membre'], $authCode['id_pers']);

$idTokenPayload = [
    'iss'         => OIDC_ISSUER,
    'sub'         => $sub,
    'aud'         => $clientId,
    'iat'         => $now,
    'exp'         => $now + OIDC_ID_TOKEN_LIFETIME,
    'auth_time'   => $now,
    // Profil
    'name'        => trim($authCode['nom'] . ' ' . $authCode['prenom']),
    'given_name'  => $authCode['prenom'],
    'family_name' => $authCode['nom'],
    // Claims Triade spécifiques
    'triade_role' => $authCode['membre'],
    'triade_id'   => $authCode['id_pers'],
];

// Inclure nonce si présent (obligatoire si fourni dans authorize)
if (!empty($authCode['nonce'])) {
    $idTokenPayload['nonce'] = $authCode['nonce'];
}

$idToken = JwtRsa::encode($idTokenPayload, $privateKey, OIDC_KEY_ID);

// ─── Log et réponse ───────────────────────────────────────────────────────────

$label = $authCode['nom'] . ' ' . $authCode['prenom'];
history_cmd($label, 'OIDC-TOKEN', "access_token + id_token émis pour client $clientId");

echo json_encode([
    'access_token' => $accessToken,
    'token_type'   => 'Bearer',
    'expires_in'   => OIDC_TOKEN_LIFETIME,
    'id_token'     => $idToken,
    'scope'        => $authCode['scope'],
]);
