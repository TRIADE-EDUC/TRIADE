<?php
/*
 * OIDC UserInfo Endpoint — Triade
 * GET|POST /sso/userinfo.php
 * Authorization: Bearer ACCESS_TOKEN
 *
 * Retourne les claims de l'utilisateur correspondant à l'access_token.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
error_reporting(0);

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');
include_once('./config.php');

$prefixe = PREFIXE;
$cnx     = cnx();

// ─── Extraction du Bearer token ───────────────────────────────────────────────

$accessToken = '';

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $m)) {
    $accessToken = $m[1];
}

// Fallback : POST body (certains clients)
if (empty($accessToken)) {
    $accessToken = trim($_POST['access_token'] ?? '');
}

if (empty($accessToken)) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer realm="triade", error="invalid_token"');
    echo json_encode(['error' => 'invalid_token', 'error_description' => 'Token manquant.']);
    exit;
}

// ─── Validation de l'access_token ────────────────────────────────────────────

$c_at = addslashes($accessToken);
$res  = execSql("SELECT id_pers, membre, nom, prenom, scope, expires_at, revoked
                 FROM {$prefixe}oidc_tokens
                 WHERE access_token = '$c_at' LIMIT 1");
$row  = chargeMat($res);

if (empty($row)) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer realm="triade", error="invalid_token"');
    echo json_encode(['error' => 'invalid_token', 'error_description' => 'Token inconnu.']);
    exit;
}

$t = $row[0];
$token = [
    'id_pers'    => (int)$t[0],
    'membre'     => $t[1],
    'nom'        => $t[2],
    'prenom'     => $t[3],
    'scope'      => $t[4],
    'expires_at' => $t[5],
    'revoked'    => (int)$t[6],
];

if ($token['revoked']) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer realm="triade", error="invalid_token"');
    echo json_encode(['error' => 'invalid_token', 'error_description' => 'Token révoqué.']);
    exit;
}

if (strtotime($token['expires_at']) < time()) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer realm="triade", error="invalid_token"');
    echo json_encode(['error' => 'invalid_token', 'error_description' => 'Token expiré.']);
    exit;
}

// ─── Construction des claims selon les scopes accordés ───────────────────────

$scopes = explode(' ', $token['scope']);

$sub = $token['membre'] . '_' . $token['id_pers'];

$claims = ['sub' => $sub];

if (in_array('profile', $scopes)) {
    $claims['name']        = trim($token['nom'] . ' ' . $token['prenom']);
    $claims['given_name']  = $token['prenom'];
    $claims['family_name'] = $token['nom'];
    $claims['triade_role'] = $token['membre'];
    $claims['triade_id']   = $token['id_pers'];
}

echo json_encode($claims, JSON_UNESCAPED_UNICODE);
