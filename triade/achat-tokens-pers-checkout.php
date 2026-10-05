<?php
/*
 * Relay Stripe — Achat tokens one-shot compte personnel TRIADE-COACH
 * Délègue au serveur central triade-educ.org/ia/api.php (sans clé Stripe ici).
 */
session_start();
error_reporting(0);

include_once('./common/config5.inc.php');
include_once('./common/config-ia-achat.php');
include_once('./librairie_php/db_triade.php');

if (empty($_SESSION['id_pers']) && empty($_SESSION['membre'])) {
    header('Location: ./index.php'); exit;
}

$idkeypers = trim($_POST['idkeypers'] ?? '');
$plan      = trim($_POST['plan']      ?? '');
$contact   = trim($_POST['contact']   ?? '');
$productid = trim($_POST['productid'] ?? (defined('PRODUCTID') ? PRODUCTID : ''));
$url       = trim($_POST['url']       ?? $_SERVER['SERVER_NAME']);

$allowedPlans = ['OFFE','OFFD','OFFC','OFFB','OFFA'];
if (!$idkeypers || !in_array($plan, $allowedPlans)) {
    header('Location: ./achat-tokens-pers.php?idkeypers=' . urlencode($idkeypers) . '&stripe=error&msg=' . urlencode('Paramètres invalides'));
    exit;
}

$proto   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $proto . '://' . $_SERVER['HTTP_HOST'];
$retour  = $baseUrl . rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/achat-tokens-pers.php'
         . '?idkeypers=' . urlencode($idkeypers)
         . '&productid=' . urlencode($productid)
         . '&url=' . urlencode($url);

$params = [
    'action'      => 'stripe_checkout_pers',
    'key'         => 'ia-2026-xJ8kPmR5',
    'idkeypers'   => $idkeypers,
    'plan'        => $plan,
    'contact'     => $contact,
    'success_url' => $retour . '&stripe=success',
    'cancel_url'  => $retour . '&stripe=cancel',
];

$ch = curl_init('https://triade-educ.org/ia/api.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($params),
]);
$json  = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

$resp = ($json && !$errno) ? json_decode($json, true) : null;

if (!empty($resp['stripe_url'])) {
    header('Location: ' . $resp['stripe_url']);
    exit;
}

$errMsg = isset($resp['erreur']) ? $resp['erreur'] : 'Le serveur central n\'a pas pu créer la session de paiement.';
header('Location: ' . $retour . '&stripe=error&msg=' . urlencode($errMsg));
exit;
