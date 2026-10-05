<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;

$token = trim($_POST['token'] ?? '');
if (empty($token)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Token manquant"]);
    Pgclose();
    exit;
}

$typeMap = [
    'menueleve'    => 'ELE',
    'menuparent'   => 'PAR',
    'menuprof'     => 'ENS',
    'menuadmin'    => 'ADM',
    'menuscolaire' => 'MVS',
];
$userType = $typeMap[$_SESSION["membre"] ?? ''] ?? '';
$userId   = intval($_SESSION["id_pers"] ?? 0);

if (!$userType || !$userId) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Session invalide"]);
    Pgclose();
    exit;
}

$tokenSafe = addslashes($token);
$sql = "INSERT INTO {$prefixe}push_tokens (user_type, user_id, token)"
     . " VALUES ('$userType', '$userId', '$tokenSafe')"
     . " ON DUPLICATE KEY UPDATE token='$tokenSafe', updated_at=NOW()";
$cnx->query($sql);

Pgclose();
echo json_encode(["ok" => true]);
?>
