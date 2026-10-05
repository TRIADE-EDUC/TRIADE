<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

$prefixe = PREFIXE;
$cnx     = cnx();

header('Content-Type: application/json; charset=utf-8');

/* get-session.php — Authentification API par clef cliente */
/* Forme de la requête : POST /api-v1/get-session.php avec JSON body: {"key":"[clef]"} */

if (APIACCESS !== "oui") {
    http_response_code(503);
    echo json_encode(["code" => "503", "message" => "API désactivée sur ce serveur"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée, utilisez POST"]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['key']) || empty($input['key'])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre 'key' manquant dans le body JSON"]);
    exit;
}

$key = $input['key'];
$ip  = getClientIP();

$sql  = "SELECT id, nom, ip
         FROM {$prefixe}api_access
         WHERE clef = '" . addslashes($key) . "'
           AND active = 1
         LIMIT 1";
$res  = execSql($sql);
$data = chargeMat($res);

if (empty($data)) {
    http_response_code(403);
    history_cmd("IP:$ip", "API-AUTH-ECHEC", "Clef invalide ou désactivée depuis IP $ip");
    echo json_encode(["code" => "403", "message" => "Clef invalide ou accès désactivé"]);
    exit;
}

function ipAutorisee($ip, $allowed) {
    foreach (array_map('trim', explode(',', $allowed)) as $entree) {
        if ($entree === '*' || $entree === $ip) return true;
        if (strpos($entree, '/') !== false) {
            list($reseau, $masque) = explode('/', $entree, 2);
            $bits = (int)$masque;
            if ($bits >= 0 && $bits <= 32
                && (ip2long($ip) & (~0 << (32 - $bits))) === (ip2long($reseau) & (~0 << (32 - $bits)))) {
                return true;
            }
        }
    }
    return false;
}

if (!ipAutorisee($ip, $data[0][2])) {
    http_response_code(403);
    history_cmd($data[0][1], "API-AUTH-ECHEC", "IP non autorisée : $ip");
    echo json_encode(["code" => "403", "message" => "Adresse IP non autorisée ($ip)"]);
    exit;
}

// Authentification OK — ouverture de session
$_SESSION['api_access'] = true;
$_SESSION['api_client'] = $data[0][1];
$_SESSION['api_id']     = $data[0][0];

history_cmd($data[0][1], "API-AUTH", "Connexion API depuis IP $ip");

echo json_encode([
    "code"       => "200",
    "message"    => "Session ouverte",
    "session_id" => session_id(),
    "client"     => $data[0][1]
]);
