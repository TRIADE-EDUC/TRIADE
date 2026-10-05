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

$idsms  = defined('SMSKEY') ? SMSKEY : '';
$retour = './admin/sms.php';

if (!$idsms) {
    header('Location: ' . $retour . '?stripe=error&msg=' . urlencode('Compte SMS non configuré'));
    exit;
}

$post = http_build_query(array(
    'action' => 'cancel_sub',
    'key'    => 'sms-2026-xR7nPqK2',
    'idsms'  => $idsms,
));

$ch = curl_init('https://triade-educ.org/sms/api.php');
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $post,
    CURLOPT_SSL_VERIFYPEER => false,
));
$resp  = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

if ($errno || !$resp) {
    header('Location: ' . $retour . '?stripe=error&msg=' . urlencode('Erreur réseau — veuillez réessayer'));
    exit;
}

$data = json_decode($resp, true);

if (!empty($data['ok'])) {
    $until = '';
    if (!empty($data['period_end'])) {
        $until = date('d/m/Y', $data['period_end']);
    }
    header('Location: ' . $retour . '?stripe=sub_cancelled&until=' . urlencode($until));
} else {
    $msg = isset($data['error']) ? $data['error'] : 'Erreur lors de la résiliation';
    header('Location: ' . $retour . '?stripe=error&msg=' . urlencode($msg));
}
exit;
