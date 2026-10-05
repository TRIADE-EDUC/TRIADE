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

if (empty($_SESSION['id_pers']) && empty($_SESSION['membre'])) {
    header('Location: ./index.php');
    exit;
}

$idkeypers    = trim($_POST['idkeypers']      ?? '');
$productid    = trim($_POST['productid']      ?? '');
$url          = trim($_POST['url']            ?? '');
$periodEndFmt = trim($_POST['period_end_fmt'] ?? '');

$retour = './crediter-pers.php'
        . '?idkeypers=' . urlencode($idkeypers)
        . '&productid=' . urlencode($productid)
        . '&url='       . urlencode($url);

if (!$idkeypers) {
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode('idkeypers manquant'));
    exit;
}

$apiKey = 'ia-2026-xJ8kPmR5';

$post = http_build_query(array(
    'action'    => 'cancel_sub_pers',
    'key'       => $apiKey,
    'idkeypers' => $idkeypers,
));

$ch = curl_init('https://triade-educ.org/ia/api.php');
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $post,
));
$resp  = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

if ($errno || !$resp) {
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode('Erreur réseau — veuillez réessayer'));
    exit;
}

$data = json_decode($resp, true);

if (!empty($data['ok'])) {
    // Formater la date de fin depuis le timestamp retourné
    $until = '';
    if (!empty($data['period_end'])) {
        $until = date('d/m/Y', intval($data['period_end']));
    } elseif ($periodEndFmt) {
        $until = $periodEndFmt;
    }
    header('Location: ' . $retour . '&stripe=cancelled&until=' . urlencode($until));
} else {
    $msg = isset($data['error']) ? $data['error'] : 'Impossible de résilier l\'abonnement';
    header('Location: ' . $retour . '&stripe=error&msg=' . urlencode($msg));
}
exit;
