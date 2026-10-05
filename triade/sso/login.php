<?php
session_start();
error_reporting(0);

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');
include_once('./config.php');
include_once('./lib/jwt.php');

$prefixe = PREFIXE;
$cnx     = cnx();
global $gestionMDP;
$gestionMDP = GESTIONMDP;

// Forcer HTTPS en production
if (SSO_FORCE_HTTPS && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $url", true, 301);
    exit;
}

$service = trim($_GET['service'] ?? $_POST['service'] ?? '');
$error   = '';
$ip      = $_SERVER['REMOTE_ADDR'];

if (empty($service)) {
    http_response_code(400);
    die('<p style="color:red">Paramètre <code>service</code> manquant.</p>');
}

if (!serviceAutorise($service, $SSO_ALLOWED_SERVICES)) {
    http_response_code(403);
    history_cmd("SSO", "SSO-REFUS", "Service non autorisé : $service depuis IP $ip");
    die('<p style="color:red">Service non autorisé.</p>');
}

function serviceAutorise(string $service, array $liste): bool {
    if (in_array('*', $liste)) return true;
    foreach ($liste as $s) {
        if (strpos($service, $s) === 0) return true;
    }
    return false;
}

// Génère un JWT et redirige vers le service
function emettreToken(array $sessionData, string $service): void {
    global $cnx, $prefixe, $ip;

    $jti = bin2hex(random_bytes(16));
    $now = time();

    $payload = [
        'iss'     => SSO_ISSUER,
        'iat'     => $now,
        'exp'     => $now + SSO_TOKEN_LIFETIME,
        'jti'     => $jti,
        'sub'     => strtolower($sessionData['nom']) . '.' . strtolower($sessionData['prenom']),
        'id_pers' => (int)$sessionData['id_pers'],
        'nom'     => $sessionData['nom'],
        'prenom'  => $sessionData['prenom'],
        'type'    => $sessionData['membre'],
        'service' => $service,
    ];

    $createdAt = date('Y-m-d H:i:s', $now);
    $expiresAt = date('Y-m-d H:i:s', $now + SSO_TOKEN_LIFETIME);
    $idPers    = (int)$sessionData['id_pers'];
    $svcEsc    = addslashes($service);
    $jtiEsc    = addslashes($jti);
    execSql("INSERT INTO {$prefixe}sso_tokens (jti, id_pers, service, created_at, expires_at, revoked)
             VALUES ('$jtiEsc', $idPers, '$svcEsc', '$createdAt', '$expiresAt', 0)");

    history_cmd($sessionData['nom'] . ' ' . $sessionData['prenom'], 'SSO-LOGIN', "Token émis pour $service");

    ip_timeout_clear($ip);

    $token = JWT::encode($payload, SSO_SECRET);
    $sep   = strpos($service, '?') !== false ? '&' : '?';
    header("Location: {$service}{$sep}token=" . urlencode($token));
    exit;
}

// Session Triade déjà active → token immédiat
if (!empty($_SESSION['nom']) && !empty($_SESSION['membre'])) {
    emettreToken([
        'nom'     => $_SESSION['nom'],
        'prenom'  => $_SESSION['prenom'],
        'membre'  => $_SESSION['membre'],
        'id_pers' => $_SESSION['id_pers'] ?? 0,
    ], $service);
}

// Jeton CSRF
if (empty($_SESSION['sso_csrf'])) {
    $_SESSION['sso_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['sso_csrf'];

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérification CSRF
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die('<p style="color:red">Requête invalide (CSRF).</p>');
    }

    $membre = trim($_POST['membre']  ?? '');
    $nom    = trim($_POST['nom']     ?? '');
    $prenom = trim($_POST['prenom']  ?? '');
    $pwd    = trim($_POST['pwd']     ?? '');

    if ($membre && $nom && $prenom && $pwd) {

        // Vérification IP bloquée
        $_rl_remaining = ip_check($ip);
        if ($_rl_remaining > 0) {
            $minutes = ceil($_rl_remaining / 60);
            $error = "Trop de tentatives. Réessayez dans $minutes minute(s).";
        } else {

        // Vérification blacklist Triade
        $bl = verifblacklist(strtolower($nom), strtolower($prenom), strtolower($membre));
        if (countTriade($bl) > 0) {
            $error = 'Accès refusé.';
            history_cmd("SSO", "SSO-ECHEC", "Utilisateur blacklisté : $nom $prenom ($membre)");
        } else {
            $hashPostVar = [
                'membre' => $membre,
                'nom'    => strtolower($nom),
                'prenom' => strtolower($prenom),
                'pwd'    => $pwd,
            ];
            $code = acces($hashPostVar, '0');

            if ($code == 1) {
                $typeMap = [
                    'administrateur' => ['type' => 'ADM', 'menu' => 'menuadmin'],
                    'enseignant'     => ['type' => 'ENS', 'menu' => 'menuprof'],
                    'vie scolaire'   => ['type' => 'MVS', 'menu' => 'menuscolaire'],
                    'personnel'      => ['type' => 'PER', 'menu' => 'menupersonnel'],
                    'tuteurstage'    => ['type' => 'TUT', 'menu' => 'menututeur'],
                ];

                $nom    = ucwords($nom);
                $prenom = ucwords($prenom);

                if (isset($typeMap[$membre])) {
                    $id_pers       = chercheIdPersonne(strtolower($nom), strtolower($prenom), $typeMap[$membre]['type']);
                    $membreSession = $typeMap[$membre]['menu'];
                } else {
                    $id_pers       = chercheIdEleve(strtolower($nom), strtolower($prenom));
                    $membreSession = 'menu' . $membre;
                }

                emettreToken([
                    'nom'     => $nom,
                    'prenom'  => $prenom,
                    'membre'  => $membreSession,
                    'id_pers' => $id_pers,
                ], $service);

            } else {
                // Échec → délai exponentiel sur l'IP
                ip_timeout($ip);
                $error = 'Identifiants incorrects.';
                history_cmd("SSO", "SSO-ECHEC", "Tentative échouée : $nom $prenom ($membre) → $service depuis $ip");
            }
        }
        } // fin ip_check
    } else {
        $error = 'Tous les champs sont obligatoires.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Connexion SSO — Triade</title>
<link rel="stylesheet" href="../librairie_css/css.css">
<style>
  body { font-family: Arial, sans-serif; background: #f0f0f0; }
  .sso-box { width: 380px; margin: 80px auto; background: #fff; padding: 30px; border: 1px solid #ccc; border-radius: 4px; }
  .sso-box h2 { margin: 0 0 20px; font-size: 18px; color: #0B3A0C; text-align: center; }
  .sso-box label { display: block; margin: 10px 0 3px; font-size: 13px; }
  .sso-box input, .sso-box select { width: 100%; padding: 6px; box-sizing: border-box; }
  .sso-box .btn { width: 100%; margin-top: 18px; padding: 8px; background: #0B3A0C; color: #fff; border: none; cursor: pointer; border-radius: 3px; }
  .sso-box .btn:hover { background: #155a16; }
  .error { color: red; font-size: 13px; margin-bottom: 10px; text-align: center; }
  .service-info { font-size: 11px; color: #666; text-align: center; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="sso-box">
  <h2>Connexion Triade SSO</h2>
  <p class="service-info">Connexion demandée par :<br><strong><?php echo htmlspecialchars($service); ?></strong></p>
  <?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
  <form method="post" autocomplete="off">
    <input type="hidden" name="service"    value="<?php echo htmlspecialchars($service); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    <label>Profil</label>
    <select name="membre">
      <option value="administrateur">Administrateur</option>
      <option value="enseignant">Enseignant</option>
      <option value="vie scolaire">Vie scolaire</option>
      <option value="personnel">Personnel</option>
      <option value="eleve">Élève</option>
      <option value="parent">Parent</option>
      <option value="tuteurstage">Tuteur de stage</option>
    </select>
    <label>Nom</label>
    <input type="text" name="nom" autocomplete="family-name">
    <label>Prénom</label>
    <input type="text" name="prenom" autocomplete="given-name">
    <label>Mot de passe</label>
    <input type="password" name="pwd">
    <button type="submit" class="btn">Se connecter</button>
  </form>
</div>
</body>
</html>
