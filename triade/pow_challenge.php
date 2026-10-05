<?php
/*
 * Proof-of-Work challenge endpoint for index.html
 * Le secret est défini dans common/config.inc.php (constante POW_SECRET).
 */
if (file_exists('./common/config.inc.php')) include_once('./common/config.inc.php');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache');
header('Pragma: no-cache');

$nonce      = bin2hex(random_bytes(16));
$expires    = time() + 120; // 2 minutes
$difficulty = 16;           // ~65 000 itérations en moyenne (~0.3 s dans le browser)
$challenge  = $nonce . ':' . $expires . ':' . $difficulty;
$_secret    = defined('POW_SECRET') ? POW_SECRET : 'triade_pow_Kx9mP2vQ8rL5wN3j';
$signature  = hash_hmac('sha256', $challenge, $_secret);

echo json_encode([
    'challenge'  => $challenge,
    'signature'  => $signature,
    'difficulty' => $difficulty,
]);
