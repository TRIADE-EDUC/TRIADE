<?php
session_start();

$sso_login    = '/triade/sso/login.php';
$sso_validate = 'http://dev.triade-v4.net/triade/sso/validate.php';
$callback     = 'http://dev.triade-v4.net/triade/sso/test_client.php';

// Etape 2 : reception du token apres redirection SSO
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $ch = curl_init($sso_validate);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['token' => $token]));
    $response = curl_exec($ch);
    curl_close($ch);

    echo '<pre>' . htmlspecialchars($response) . '</pre>';
    exit;
}

// Etape 1 : redirection vers le login SSO
header('Location: ' . $sso_login . '?service=' . urlencode($callback));
exit;
