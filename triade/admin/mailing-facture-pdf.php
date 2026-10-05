<?php
/*
 * Proxy PDF facture TRIADE-MAILING
 * Le navigateur ne connaît que ?f=nom.pdf — MAILINGKEY et clé API restent serveur
 */
session_start();
include_once("./librairie_php/lib_licence.php");

if (!file_exists("../common/config-mailing.php")) {
    header("Location: mailing.php");
    exit;
}
include_once("../common/config-mailing.php");

$fichier = isset($_GET['f']) ? basename(trim($_GET['f'])) : '';
if (!preg_match('/^[a-zA-Z0-9_\-]+\.pdf$/i', $fichier)) {
    header("Location: mailing.php?onglet=factures");
    exit;
}

define('MAILING_API_KEY', 'mailing-2026-xT4vNqP8');

$ch = curl_init('https://www.triade-educ.org/mailing/pdf.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'key' => MAILING_API_KEY,
    'idm' => MAILINGKEY,
    'f'   => $fichier,
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$content  = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $content) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="facture.pdf"');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: private');
    echo $content;
    exit;
}

header("Location: mailing.php?onglet=factures&err=pdf");
exit;
