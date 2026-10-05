<?php
/*
 * OIDC Authorization Endpoint — Triade
 * GET  /sso/authorize.php?response_type=code&client_id=...&redirect_uri=...&scope=openid...
 *
 * Flux Authorization Code (+ PKCE pour clients publics comme TRIADE-PHONE)
 * Après authentification, redirige vers redirect_uri?code=CODE&state=STATE
 */
session_start();
error_reporting(0);

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');
include_once('./config.php');

$prefixe = PREFIXE;
$cnx     = cnx();
global $gestionMDP;
$gestionMDP = GESTIONMDP;

if (SSO_FORCE_HTTPS && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function oidcError(string $msg, int $code = 400): void {
    http_response_code($code);
    echo '<p style="color:red;font-family:sans-serif">' . htmlspecialchars($msg) . '</p>';
    exit;
}

// Redirige vers redirect_uri avec un paramètre error= (erreur OIDC standard)
function oidcRedirectError(string $redirectUri, string $error, string $desc, string $state): void {
    $p = ['error' => $error, 'error_description' => $desc];
    if ($state !== '') $p['state'] = $state;
    $sep = strpos($redirectUri, '?') !== false ? '&' : '?';
    header('Location: ' . $redirectUri . $sep . http_build_query($p));
    exit;
}

// Charge le client depuis la DB
function oidcLoadClient(string $clientId): ?array {
    global $cnx, $prefixe;
    $cid = addslashes($clientId);
    $res = execSql("SELECT client_id, client_secret, client_name, redirect_uris, scopes, pkce_required
                    FROM {$prefixe}oidc_clients
                    WHERE client_id = '$cid' AND active = 1 LIMIT 1");
    $row = chargeMat($res);
    if (empty($row)) return null;
    $r = $row[0];
    return [
        'client_id'     => $r[0],
        'client_secret' => $r[1],
        'client_name'   => $r[2],
        'redirect_uris' => json_decode($r[3], true) ?: [],
        'scopes'        => $r[4],
        'pkce_required' => (bool)$r[5],
    ];
}

// Vérifie que redirect_uri est dans la liste du client
function oidcCheckRedirectUri(array $client, string $uri): bool {
    return in_array($uri, $client['redirect_uris'], true);
}

// Génère et stocke le code d'autorisation
function oidcIssueCode(array $params): string {
    global $cnx, $prefixe;

    $code      = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + OIDC_CODE_LIFETIME);
    $createdAt = date('Y-m-d H:i:s');

    $c_code      = addslashes($code);
    $c_clientId  = addslashes($params['client_id']);
    $c_idPers    = (int)$params['id_pers'];
    $c_membre    = addslashes($params['membre']);
    $c_nom       = addslashes($params['nom']);
    $c_prenom    = addslashes($params['prenom']);
    $c_redir     = addslashes($params['redirect_uri']);
    $c_scope     = addslashes($params['scope']);
    $c_nonce     = addslashes($params['nonce'] ?? '');
    $c_challenge = addslashes($params['code_challenge'] ?? '');
    $c_method    = addslashes($params['code_challenge_method'] ?? '');

    execSql("INSERT INTO {$prefixe}oidc_auth_codes
             (code, client_id, id_pers, membre, nom, prenom, redirect_uri, scope, nonce,
              code_challenge, code_challenge_method, expires_at, used, created_at)
             VALUES
             ('$c_code','$c_clientId',$c_idPers,'$c_membre','$c_nom','$c_prenom',
              '$c_redir','$c_scope','$c_nonce','$c_challenge','$c_method',
              '$expiresAt',0,'$createdAt')");

    $label = $params['nom'] . ' ' . $params['prenom'];
    history_cmd($label, 'OIDC-CODE', "Code émis pour {$params['client_id']}");

    return $code;
}

// Redirige après succès
function oidcRedirectCode(string $redirectUri, string $code, string $state): void {
    $p = ['code' => $code];
    if ($state !== '') $p['state'] = $state;
    $sep = strpos($redirectUri, '?') !== false ? '&' : '?';
    header('Location: ' . $redirectUri . $sep . http_build_query($p));
    exit;
}

// ─── Lecture des paramètres OIDC ─────────────────────────────────────────────

// Lors du GET initial on lit les params de l'URL ; lors du POST on les récupère
// depuis la session où ils ont été sauvegardés avant d'afficher le formulaire.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $responseType         = trim($_GET['response_type']          ?? '');
    $clientId             = trim($_GET['client_id']              ?? '');
    $redirectUri          = trim($_GET['redirect_uri']           ?? '');
    $scope                = trim($_GET['scope']                  ?? '');
    $state                = trim($_GET['state']                  ?? '');
    $nonce                = trim($_GET['nonce']                  ?? '');
    $codeChallenge        = trim($_GET['code_challenge']         ?? '');
    $codeChallengeMethod  = trim($_GET['code_challenge_method']  ?? 'plain');
} else {
    // POST — restaurer depuis session
    $pending             = $_SESSION['oidc_pending'] ?? [];
    $responseType        = $pending['response_type']         ?? '';
    $clientId            = $pending['client_id']             ?? '';
    $redirectUri         = $pending['redirect_uri']          ?? '';
    $scope               = $pending['scope']                 ?? '';
    $state               = $pending['state']                 ?? '';
    $nonce               = $pending['nonce']                 ?? '';
    $codeChallenge       = $pending['code_challenge']        ?? '';
    $codeChallengeMethod = $pending['code_challenge_method'] ?? 'plain';
}

// ─── Validations préliminaires (avant d'avoir redirect_uri fiable) ────────────

if ($responseType !== 'code') {
    oidcError('response_type non supporté : seul "code" est accepté.');
}
if (empty($clientId)) {
    oidcError('Paramètre client_id manquant.');
}

$client = oidcLoadClient($clientId);
if (!$client) {
    oidcError('Client OIDC inconnu ou inactif.', 403);
}

if (empty($redirectUri) || !oidcCheckRedirectUri($client, $redirectUri)) {
    oidcError('redirect_uri invalide ou non enregistrée pour ce client.', 403);
}

// À partir d'ici, redirect_uri est fiable → les erreurs partent en redirection

if (strpos($scope, 'openid') === false) {
    oidcRedirectError($redirectUri, 'invalid_scope', 'Le scope "openid" est obligatoire.', $state);
}

if ($client['pkce_required'] && empty($codeChallenge)) {
    oidcRedirectError($redirectUri, 'invalid_request', 'PKCE obligatoire pour ce client.', $state);
}

if (!empty($codeChallenge) && !in_array($codeChallengeMethod, ['S256', 'plain'])) {
    oidcRedirectError($redirectUri, 'invalid_request', 'code_challenge_method invalide (S256 ou plain).', $state);
}

// ─── Session Triade déjà active → code immédiat ──────────────────────────────

if (!empty($_SESSION['nom']) && !empty($_SESSION['membre']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $code = oidcIssueCode([
        'client_id'            => $clientId,
        'id_pers'              => $_SESSION['id_pers'] ?? 0,
        'membre'               => $_SESSION['membre'],
        'nom'                  => $_SESSION['nom'],
        'prenom'               => $_SESSION['prenom'] ?? '',
        'redirect_uri'         => $redirectUri,
        'scope'                => $scope,
        'nonce'                => $nonce,
        'code_challenge'       => $codeChallenge,
        'code_challenge_method'=> $codeChallengeMethod,
    ]);
    oidcRedirectCode($redirectUri, $code, $state);
}

// ─── Traitement du formulaire de connexion (POST) ─────────────────────────────

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (!hash_equals($_SESSION['oidc_csrf'] ?? '', $_POST['csrf_token'] ?? '')) {
        oidcRedirectError($redirectUri, 'server_error', 'Requête invalide (CSRF).', $state);
    }

    $membre = trim($_POST['membre']  ?? '');
    $nom    = trim($_POST['nom']     ?? '');
    $prenom = trim($_POST['prenom']  ?? '');
    $pwd    = trim($_POST['pwd']     ?? '');

    if ($membre && $nom && $prenom && $pwd) {

        $ip = $_SERVER['REMOTE_ADDR'];

        // Vérification IP bloquée
        $_rl_remaining = ip_check($ip);
        if ($_rl_remaining > 0) {
            $minutes = ceil($_rl_remaining / 60);
            $error = "Trop de tentatives. Réessayez dans $minutes minute(s).";
        } else {

        $bl = verifblacklist(strtolower($nom), strtolower($prenom), strtolower($membre));
        if (countTriade($bl) > 0) {
            $error = 'Accès refusé.';
        } else {
            $code = acces([
                'membre' => $membre,
                'nom'    => strtolower($nom),
                'prenom' => strtolower($prenom),
                'pwd'    => $pwd,
            ], '0');

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
                    $idPers        = chercheIdPersonne(strtolower($nom), strtolower($prenom), $typeMap[$membre]['type']);
                    $membreSession = $typeMap[$membre]['menu'];
                } else {
                    $idPers        = chercheIdEleve(strtolower($nom), strtolower($prenom));
                    $membreSession = 'menu' . $membre;
                }

                ip_timeout_clear($ip);

                $authCode = oidcIssueCode([
                    'client_id'            => $clientId,
                    'id_pers'              => $idPers,
                    'membre'               => $membreSession,
                    'nom'                  => $nom,
                    'prenom'               => $prenom,
                    'redirect_uri'         => $redirectUri,
                    'scope'                => $scope,
                    'nonce'                => $nonce,
                    'code_challenge'       => $codeChallenge,
                    'code_challenge_method'=> $codeChallengeMethod,
                ]);

                unset($_SESSION['oidc_pending'], $_SESSION['oidc_csrf']);
                oidcRedirectCode($redirectUri, $authCode, $state);

            } else {
                ip_timeout($ip);
                $error = 'Identifiants incorrects.';
                history_cmd('OIDC', 'OIDC-ECHEC', "Tentative échouée pour client $clientId depuis {$_SERVER['REMOTE_ADDR']}");
            }
        }
        } // fin ip_check
    } else {
        $error = 'Tous les champs sont obligatoires.';
    }
}

// ─── Affichage du formulaire de connexion ─────────────────────────────────────

// Sauvegarder les params OIDC en session pour le POST
$_SESSION['oidc_pending'] = compact(
    'responseType', 'clientId', 'redirectUri', 'scope',
    'state', 'nonce', 'codeChallenge', 'codeChallengeMethod'
);
if (empty($_SESSION['oidc_csrf'])) {
    $_SESSION['oidc_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['oidc_csrf'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Connexion — <?php echo htmlspecialchars($client['client_name'] ?: $clientId); ?></title>
<link rel="stylesheet" href="../librairie_css/css.css">
<style>
  body { font-family: Arial, sans-serif; background: #f0f0f0; margin: 0; }
  .oidc-box { width: 380px; margin: 80px auto; background: #fff; padding: 32px;
              border: 1px solid #ccc; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .oidc-box h2 { margin: 0 0 6px; font-size: 17px; color: #0B3A0C; text-align: center; }
  .oidc-app   { font-size: 12px; color: #666; text-align: center; margin: 0 0 20px; }
  .oidc-box label { display: block; margin: 12px 0 3px; font-size: 13px; font-weight: bold; }
  .oidc-box input, .oidc-box select { width: 100%; padding: 7px; box-sizing: border-box;
                                      border: 1px solid #ccc; border-radius: 3px; font-size: 13px; }
  .oidc-box .btn { width: 100%; margin-top: 20px; padding: 9px; background: #0B3A0C;
                   color: #fff; border: none; cursor: pointer; border-radius: 3px;
                   font-size: 14px; }
  .oidc-box .btn:hover { background: #155a16; }
  .oidc-error { color: #c00; font-size: 13px; margin-bottom: 12px; text-align: center;
                background: #fff0f0; padding: 6px; border-radius: 3px; }
  .oidc-lock  { text-align: center; margin-bottom: 16px; color: #0B3A0C; font-size: 22px; }
</style>
</head>
<body>
<div class="oidc-box">
  <div class="oidc-lock">&#128274;</div>
  <h2>Authentification Triade</h2>
  <p class="oidc-app">Application : <strong><?php echo htmlspecialchars($client['client_name'] ?: $clientId); ?></strong></p>

  <?php if ($error): ?>
    <p class="oidc-error"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post" autocomplete="off">
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
