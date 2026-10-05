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
    echo json_encode(["code" => "403", "message" => "Accès réservé à l'administration"]);
    die();
}

$q = trim($_GET['q'] ?? '');
if (mb_strlen($q) < 2) {
    echo json_encode(["eleves" => []]);
    exit;
}

$cnx     = cnx();
$prefixe = PREFIXE;
$qSafe   = addslashes(strtolower($q));

$sql = "SELECT e.elev_id, trim(e.nom), trim(e.prenom), trim(COALESCE(c.libelle, e.classe))
        FROM {$prefixe}eleves e
        LEFT JOIN {$prefixe}classes c ON c.code_class = e.classe
        WHERE (lower(trim(e.nom)) LIKE '%{$qSafe}%' OR lower(trim(e.prenom)) LIKE '%{$qSafe}%')
          AND (e.compte_inactif IS NULL OR e.compte_inactif != '1')
        ORDER BY e.nom, e.prenom
        LIMIT 50";

$data   = chargeMat(execSql($sql));
$eleves = [];

for ($i = 0; $i < count($data); $i++) {
    $eleves[] = [
        "id"        => $data[$i][0],
        "nom"       => strtoupper(trim($data[$i][1])),
        "prenom"    => ucfirst(strtolower(trim($data[$i][2]))),
        "libclasse" => trim($data[$i][3]),
    ];
}

Pgclose();
echo json_encode(["eleves" => $eleves], JSON_UNESCAPED_UNICODE);
?>
