<?php
/*
 * JWKS — clé publique RSA pour vérifier les id_token RS256
 * Servi à : {OIDC_ISSUER}/.well-known/jwks.json
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: max-age=86400');

include_once('../../common/config.inc.php');
include_once('../config.php');
include_once('../lib/jwt_rsa.php');

$pubKey = @file_get_contents(OIDC_KEY_DIR . '/public.pem');
if (!$pubKey) {
    http_response_code(503);
    echo json_encode(['error' => 'Clé publique non disponible']);
    exit;
}

echo json_encode(JwtRsa::jwks($pubKey, OIDC_KEY_ID), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
