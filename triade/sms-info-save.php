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
$productid = trim($_POST['productid'] ?? '');
$url       = trim($_POST['url']       ?? $_SERVER['SERVER_NAME']);

$back   = trim(isset($_POST['back']) ? $_POST['back'] : './admin/sms.php');
// Sécurité : on n'accepte que des chemins relatifs commençant par ./
if (!preg_match('#^\./[a-zA-Z0-9/_\-]+\.php$#', $back)) {
    $back = './admin/sms.php';
}
$retour = $back
        . '?productid=' . urlencode($productid)
        . '&url='       . urlencode($url);

if (!$idsms) {
    header('Location: ' . $retour . '&info=error&msg=' . urlencode('Compte SMS non configuré'));
    exit;
}

$post = http_build_query(array(
    'action'        => 'save_info',
    'key'           => 'sms-2026-xR7nPqK2',
    'idsms'         => $idsms,
    'etablissement' => trim($_POST['etablissement'] ?? ''),
    'adresse'       => trim($_POST['adresse']       ?? ''),
    'ville'         => trim($_POST['ville']         ?? ''),
    'ccp'           => trim($_POST['ccp']           ?? ''),
    'nom'           => trim($_POST['nom']           ?? ''),
    'prenom'        => trim($_POST['prenom']        ?? ''),
    'email'         => trim($_POST['email']         ?? ''),
    'tel'           => trim($_POST['tel']           ?? ''),
    'emailreply'    => trim($_POST['emailreply']    ?? ''),
));

$ch = curl_init('https://triade-educ.org/sms/api.php');
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $post,
));
$resp  = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

if ($errno || !$resp) {
    header('Location: ' . $retour . '&info=error&msg=' . urlencode('Erreur réseau'));
    exit;
}

$data = json_decode($resp, true);
if (!empty($data['ok'])) {
    header('Location: ' . $retour . '&info=success');
} else {
    $msg = isset($data['error']) ? $data['error'] : 'Erreur inconnue';
    header('Location: ' . $retour . '&info=error&msg=' . urlencode($msg));
}
exit;
