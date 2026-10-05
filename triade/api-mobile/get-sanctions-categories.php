<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

if (!in_array($_SESSION["membre"], ['menuadmin', 'menuscolaire'])) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;

$data = chargeMat(execSql("SELECT id_sanc, libelle FROM {$prefixe}type_sanction ORDER BY libelle"));
$categories = [];
for ($i = 0; $i < count($data); $i++) {
    $categories[] = [
        "id"      => trim($data[$i][0]),
        "libelle" => trim($data[$i][1]),
    ];
}

Pgclose();
echo json_encode(["categories" => $categories], JSON_UNESCAPED_UNICODE);
?>
