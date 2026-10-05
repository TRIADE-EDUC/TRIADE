<?php
/*
 * OIDC Discovery Document
 * Servi à : {OIDC_ISSUER}/.well-known/openid-configuration
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: max-age=86400');

include_once('../../common/config.inc.php');
include_once('../config.php');

$issuer = OIDC_ISSUER;

echo json_encode([
    'issuer'                                => $issuer,
    'authorization_endpoint'               => "$issuer/authorize.php",
    'token_endpoint'                        => "$issuer/token.php",
    'userinfo_endpoint'                     => "$issuer/userinfo.php",
    'jwks_uri'                              => "$issuer/.well-known/jwks.json",
    'end_session_endpoint'                  => "$issuer/logout.php",
    'response_types_supported'             => ['code'],
    'subject_types_supported'              => ['public'],
    'id_token_signing_alg_values_supported'=> ['RS256'],
    'scopes_supported'                     => explode(' ', OIDC_SUPPORTED_SCOPES),
    'token_endpoint_auth_methods_supported'=> ['client_secret_post', 'client_secret_basic', 'none'],
    'claims_supported'                     => [
        'sub', 'iss', 'aud', 'exp', 'iat', 'auth_time', 'nonce',
        'name', 'given_name', 'family_name',
        'triade_role', 'triade_id',
    ],
    'code_challenge_methods_supported'     => ['S256', 'plain'],
    'grant_types_supported'                => ['authorization_code'],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
