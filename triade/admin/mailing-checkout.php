<?php
/*
 * Checkout Stripe — TRIADE-MAILING, côté école.
 * Délègue entièrement au serveur central. Aucune clé Stripe ici.
 */
session_start();
if (empty($_SESSION['admin1']) && (empty($_SESSION['membre']) || $_SESSION['membre'] !== 'menuadmin')) {
    http_response_code(403); exit('Accès refusé.');
}

if (!file_exists('../common/config-mailing.php')) {
    header('Location: mailing.php?onglet=achat&stripe=error&msg=' . urlencode('Compte MAILING non configuré'));
    exit;
}
include_once('../common/config-mailing.php');

define('MAILING_API_KEY', 'mailing-2026-xT4vNqP8');

$offresOk = array('OFFA', 'OFFB', 'OFFC', 'OFFD');
$offre    = isset($_POST['offre'])   ? strtoupper(trim($_POST['offre']))  : '';
$contact  = isset($_POST['contact']) ? trim($_POST['contact'])            : '';

if (!in_array($offre, $offresOk)) {
    header('Location: mailing.php?onglet=achat&stripe=error&msg=' . urlencode('Offre invalide'));
    exit;
}

$proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl  = $proto . '://' . $_SERVER['HTTP_HOST'];
$adminUrl = $baseUrl . rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/mailing.php';

// On envoie l'URL de retour école au central — le central construit le success_url
// avec {CHECKOUT_SESSION_ID} directement (évite le double-encodage)
$params = array(
    'key'       => MAILING_API_KEY,
    'action'    => 'stripe_checkout',
    'idmailing' => MAILINGKEY,
    'offre'     => $offre,
    'contact'   => $contact,
    'back_url'  => $adminUrl,   // URL de base de l'école, sans query string
);

$ch = curl_init('https://www.triade-educ.org/mailing/api.php');
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($params),
    CURLOPT_SSL_VERIFYPEER => false,
));
$resp  = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

if ($errno || !$resp) {
    header('Location: mailing.php?onglet=achat&stripe=error&msg=' . urlencode('Erreur réseau — veuillez réessayer'));
    exit;
}

$data = json_decode($resp, true);

if (!empty($data['ok']) && !empty($data['stripe_url'])) {
    header('Location: ' . $data['stripe_url']);
} else {
    $msg = isset($data['error']) ? $data['error'] : 'Impossible de créer la session de paiement';
    header('Location: mailing.php?onglet=achat&stripe=error&msg=' . urlencode($msg));
}
exit;
