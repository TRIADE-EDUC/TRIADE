<?php
/*
 * Enregistre les modifications du compte TRIADE-MAILING via API centrale
 * MAILINGKEY reste côté serveur
 */
session_start();
include_once("./librairie_php/lib_licence.php");

if (!file_exists("../common/config-mailing.php") || !isset($_POST['create'])) {
    header("Location: mailing.php?onglet=modif");
    exit;
}
include_once("../common/config-mailing.php");

define('MAILING_API_KEY', 'mailing-2026-xT4vNqP8');

$ch = curl_init('https://www.triade-educ.org/mailing/api.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'key'           => MAILING_API_KEY,
    'action'        => 'save_info',
    'idmailing'     => MAILINGKEY,
    'etablissement' => isset($_POST['etablissement']) ? $_POST['etablissement'] : '',
    'adresse'       => isset($_POST['adresse'])       ? $_POST['adresse']       : '',
    'ville'         => isset($_POST['ville'])         ? $_POST['ville']         : '',
    'ccp'           => isset($_POST['ccp'])           ? $_POST['ccp']           : '',
    'nom'           => isset($_POST['nom'])           ? $_POST['nom']           : '',
    'prenom'        => isset($_POST['prenom'])        ? $_POST['prenom']        : '',
    'email'         => isset($_POST['email'])         ? $_POST['email']         : '',
    'tel'           => isset($_POST['tel'])           ? $_POST['tel']           : '',
    'emailreply'    => isset($_POST['emailreply'])    ? $_POST['emailreply']    : '',
    'refclient'     => isset($_POST['refclient'])     ? $_POST['refclient']     : '',
)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$resp = curl_exec($ch);
curl_close($ch);

$data = $resp ? json_decode($resp, true) : array();
$ok   = !empty($data['ok']);

header("Location: mailing.php?onglet=modif" . ($ok ? "&saved=1" : "&err=1"));
exit;
