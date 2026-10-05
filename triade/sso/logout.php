<?php
session_start();

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');
include_once('./config.php');
include_once('./lib/jwt.php');

$prefixe = PREFIXE;
$cnx     = cnx();

/* GET /sso/logout.php?token=eyJ...&redirect=https://appexterne.fr/logout
   Révoque le token et détruit la session Triade.
   Redirige vers l'URL fournie ou affiche un message de déconnexion.
*/

$token    = trim($_GET['token']    ?? '');
$redirect = trim($_GET['redirect'] ?? '');

// Révocation du token si fourni
if (!empty($token)) {
    $payload = JWT::decode($token, SSO_SECRET);
    if ($payload && !empty($payload['jti'])) {
        $jti  = addslashes($payload['jti']);
        $nom  = $payload['nom']    ?? 'inconnu';
        $pren = $payload['prenom'] ?? '';
        execSql("UPDATE {$prefixe}sso_tokens SET revoked = 1 WHERE jti = '$jti'");
        history_cmd("$nom $pren", "SSO-LOGOUT", "Token révoqué pour " . ($payload['service'] ?? ''));
    }
}

// Destruction de la session Triade
$_SESSION = [];
session_unset();
session_destroy();

if (!empty($redirect) && filter_var($redirect, FILTER_VALIDATE_URL)) {
    header("Location: $redirect");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Déconnexion SSO — Triade</title>
<link rel="stylesheet" href="../librairie_css/css.css">
<style>
  body { font-family: Arial, sans-serif; background: #f0f0f0; }
  .msg { width: 360px; margin: 100px auto; background: #fff; padding: 30px; text-align: center; border: 1px solid #ccc; border-radius: 4px; }
  .msg h2 { color: #0B3A0C; }
</style>
</head>
<body>
<div class="msg">
  <h2>Déconnexion effectuée</h2>
  <p>Vous avez été déconnecté de Triade SSO.</p>
</div>
</body>
</html>
