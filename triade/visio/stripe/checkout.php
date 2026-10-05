<?php
/*
 * Checkout Stripe — côté école.
 * Délègue entièrement au serveur central triade-educ.org.
 * Aucune clé Stripe stockée ici.
 */
session_start();
if (empty($_SESSION['admin1']) && (empty($_SESSION['membre']) || $_SESSION['membre'] !== 'menuadmin')) {
    http_response_code(403); exit('Accès refusé.');
}

include_once(__DIR__ . '/../../common/config.inc.php');
include_once(__DIR__ . '/../../librairie_php/db_visio.php');

$planMap = [
    'starter'  => ['participants' => 6,  'salles' => 2,  'prix' => 15],
    'premium'  => ['participants' => 15, 'salles' => 5,  'prix' => 35],
    'illimite' => ['participants' => 50, 'salles' => 20, 'prix' => 60],
];
$plan = isset($_POST['plan'], $planMap[$_POST['plan']]) ? $_POST['plan'] : 'starter';

$proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl  = $proto . '://' . $_SERVER['HTTP_HOST'];
$adminUrl = $baseUrl . rtrim(dirname(dirname(dirname($_SERVER['PHP_SELF']))), '/') . '/admin/visio_abo.php';

// Demander au serveur central de créer la session Stripe
$params = [
    'action'      => 'stripe_checkout',
    'key'         => VISIO_API_KEY,
    'ecole'       => _visioCodeEcole(),
    'plan'        => $plan,
    'contact'     => trim($_POST['contact'] ?? ''),
    'success_url' => $adminUrl . '?stripe=success',
    'cancel_url'  => $adminUrl . '?stripe=cancel',
];

$ch = curl_init(VISIO_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($params),
]);
$json = curl_exec($ch);
curl_close($ch);

$resp = $json ? json_decode($json, true) : null;

if (!empty($resp['stripe_url'])) {
    header('Location: ' . $resp['stripe_url']);
    exit;
}

$errMsg = urlencode($resp['erreur'] ?? 'Le serveur central n\'a pas pu créer la session de paiement.');
header('Location: ' . $adminUrl . '?stripe=error&msg=' . $errMsg);
exit;
