<?php
header('Content-Type: application/json; charset=utf-8');

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');
include_once('./config.php');
include_once('./lib/jwt.php');

$prefixe = PREFIXE;
$cnx     = cnx();

/* POST /sso/validate.php   body: token=eyJ...
   GET  /sso/validate.php?token=eyJ...  (rétrocompatibilité)
   Appelé par l'app externe pour valider un token JWT reçu après redirection SSO.
   Token à usage unique — invalidé après première validation réussie.
   Aucune clé API requise — la sécurité repose sur la signature JWT (SSO_SECRET).
*/

$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(["valid" => false, "error" => "Méthode non autorisée"]);
    exit;
}

if ($method === 'POST') {
    $token = trim($_POST['token'] ?? '');
    if (empty($token)) {
        $body  = json_decode(file_get_contents('php://input'), true);
        $token = trim($body['token'] ?? '');
    }
} else {
    $token = trim($_GET['token'] ?? '');
}

if (empty($token)) {
    http_response_code(400);
    echo json_encode(["valid" => false, "error" => "Token manquant"]);
    exit;
}

// Vérification signature + expiration
$payload = JWT::decode($token, SSO_SECRET);
if (!$payload) {
    http_response_code(401);
    echo json_encode(["valid" => false, "error" => "Token invalide ou expiré"]);
    exit;
}

// Vérification révocation + usage unique
$jti = addslashes($payload['jti'] ?? '');
$res = execSql("SELECT revoked, used FROM {$prefixe}sso_tokens WHERE jti = '$jti' LIMIT 1");
$row = chargeMat($res);

if (empty($row)) {
    http_response_code(401);
    echo json_encode(["valid" => false, "error" => "Token inconnu"]);
    exit;
}

if ($row[0][0] == 1) {
    http_response_code(401);
    echo json_encode(["valid" => false, "error" => "Token révoqué"]);
    exit;
}

if ($row[0][1] == 1) {
    http_response_code(401);
    echo json_encode(["valid" => false, "error" => "Token déjà utilisé"]);
    exit;
}

// Marquer le token comme consommé (usage unique)
execSql("UPDATE {$prefixe}sso_tokens SET used = 1 WHERE jti = '$jti'");

$nom = ($payload['nom'] ?? '') . ' ' . ($payload['prenom'] ?? '');
history_cmd(trim($nom), "SSO-VALIDATE", "Validation token pour service : " . ($payload['service'] ?? ''));

echo json_encode([
    "valid"    => true,
    "id_pers"  => $payload['id_pers'],
    "nom"      => $payload['nom'],
    "prenom"   => $payload['prenom'],
    "type"     => $payload['type'],
    "sub"      => $payload['sub'],
    "exp"      => $payload['exp'],
    "service"  => $payload['service'],
]);
