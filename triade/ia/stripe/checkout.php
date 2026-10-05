<?php
/*
 * Checkout Stripe — TRIADE-COPILOT côté école.
 * Délègue entièrement au serveur central triade-educ.org.
 * Aucune clé Stripe stockée ici.
 */
session_start();
if (empty($_SESSION['admin1']) && (empty($_SESSION['membre']) || $_SESSION['membre'] !== 'menuadmin')) {
    http_response_code(403); exit('Accès refusé.');
}

include_once(__DIR__ . '/../../common/config.inc.php');
include_once(__DIR__ . '/../../librairie_php/db_ia.php');

if (!defined('IA_API_URL')) {
    header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/admin/copilot_abo.php?stripe=error&msg=' . urlencode('Configuration IA manquante'));
    exit;
}

// Charger IAKEY
$iakey = '';
$cfgIA = __DIR__ . '/../../common/config-ia.php';
if (file_exists($cfgIA)) {
    include_once($cfgIA);
    if (defined('IAKEY')) $iakey = IAKEY;
}

if (!$iakey) {
    header('Location: ' . dirname(dirname($_SERVER['PHP_SELF'])) . '/admin/copilot_abo.php?stripe=error&msg=' . urlencode('Compte COPILOT non configuré'));
    exit;
}

$planMap = array(
    'OFFA' => 300, 'OFFB' => 225, 'OFFC' => 150, 'OFFD' => 75, 'OFFE' => 30,
);
$plan = (isset($_POST['plan']) && isset($planMap[$_POST['plan']])) ? $_POST['plan'] : 'OFFE';

$proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl  = $proto . '://' . $_SERVER['HTTP_HOST'];
$adminUrl = $baseUrl . rtrim(dirname(dirname(dirname($_SERVER['PHP_SELF']))), '/') . '/admin/copilot_abo.php';

$params = array(
    'action'      => 'stripe_checkout',
    'key'         => IA_API_KEY,
    'iakey'       => $iakey,
    'plan'        => $plan,
    'contact'     => trim($_POST['contact'] ?? ''),
    'success_url' => $adminUrl . '?stripe=success',
    'cancel_url'  => $adminUrl . '?stripe=cancel',
);

$ch = curl_init(IA_API_URL);
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($params),
));
$json = curl_exec($ch);
curl_close($ch);

$resp = $json ? json_decode($json, true) : null;

if (!empty($resp['stripe_url'])) {
    header('Location: ' . $resp['stripe_url']);
    exit;
}

$errMsg = urlencode(isset($resp['erreur']) ? $resp['erreur'] : 'Le serveur central n\'a pas pu créer la session de paiement.');
header('Location: ' . $adminUrl . '?stripe=error&msg=' . $errMsg);
exit;
