<?php
session_start();
include_once("./librairie_php/lib_licence.php");

if (!file_exists("../common/config-mailing.php")) {
    header("Location: mailing.php");
    exit;
}
include_once("../common/config-mailing.php");

$allowed = array('accueil', 'achat', 'facture', 'modif', 'log');
$page    = (isset($_GET['page']) && in_array($_GET['page'], $allowed)) ? $_GET['page'] : 'accueil';

define('MAILING_API_KEY', 'mailing-2026-xT4vNqP8');

$ch = curl_init('https://www.triade-educ.org/mailing/api.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'key'       => MAILING_API_KEY,
    'action'    => 'create_token',
    'idmailing' => MAILINGKEY,
    'target'    => $page,
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$resp = curl_exec($ch);
curl_close($ch);

$data  = $resp ? json_decode($resp, true) : array();
$token = isset($data['token']) ? $data['token'] : '';

if ($token !== '') {
    header('Location: https://support.triade-educ.org/support/mailing-gate.php?t=' . urlencode($token));
    exit;
}

header("Location: mailing.php");
exit;
