<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/

if (empty($_SESSION['id_pers']) && empty($_SESSION['membre']) && empty($_SESSION['admin1'])) {
    header('Location: ./index.php');
    exit;
}

include_once('./common/config-sms.php');

$idsms     = defined('SMSKEY') ? SMSKEY : '';
$pack      = trim($_POST['pack']      ?? '');
$contact   = trim($_POST['contact']   ?? '');
$productid = trim($_POST['productid'] ?? '');
$url       = trim($_POST['url']       ?? $_SERVER['SERVER_NAME']);

$retour = './admin/sms.php'
        . '?productid=' . urlencode($productid)
        . '&url='       . urlencode($url);

if (!$idsms) {
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode('Compte SMS non configuré'));
    exit;
}
if (!$pack) {
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode('Pack non sélectionné'));
    exit;
}

$apiKey     = 'sms-2026-xR7nPqK2';
$successUrl = 'https://' . $_SERVER['SERVER_NAME'] . '/triade/admin/sms.php'
            . '?productid=' . urlencode($productid) . '&url=' . urlencode($url);
$cancelUrl  = $successUrl;

$post = http_build_query(array(
    'action'      => 'checkout',
    'key'         => $apiKey,
    'idsms'       => $idsms,
    'pack'        => $pack,
    'contact'     => $contact,
    'success_url' => $successUrl,
    'cancel_url'  => $cancelUrl,
));

$ch = curl_init('https://triade-educ.org/sms/api.php');
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_TIMEOUT         => 15,
    CURLOPT_POST            => true,
    CURLOPT_POSTFIELDS      => $post,
    CURLOPT_SSL_VERIFYPEER  => false,
));
$resp  = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

if ($errno || !$resp) {
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode('Erreur réseau — veuillez réessayer'));
    exit;
}

$data = json_decode($resp, true);

if (!empty($data['ok']) && !empty($data['stripe_url'])) {
    header('Location: ' . $data['stripe_url']);
} else {
    $msg = isset($data['error']) ? $data['error'] : 'Impossible de créer la session de paiement';
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode($msg));
}
exit;
