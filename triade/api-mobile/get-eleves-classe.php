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

$allowedMembres = ['menuprof', 'menuadmin', 'menuscolaire'];
if (!in_array($_SESSION["membre"], $allowedMembres)) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idprof  = intval($_SESSION["id_pers"]);

$idclasse  = isset($_GET['idclasse'])  ? trim($_GET['idclasse'])  : '';
$idmatiere = isset($_GET['idmatiere']) ? trim($_GET['idmatiere']) : '';

if (!$idclasse) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre idclasse requis"]);
    exit;
}

$idclasseSafe  = addslashes($idclasse);
$idmatiereSafe = addslashes($idmatiere);

if ($_SESSION["membre"] === 'menuprof') {
    if (!$idmatiere) {
        http_response_code(400);
        echo json_encode(["code" => "400", "message" => "Paramètre idmatiere requis"]);
        exit;
    }
    $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe' AND code_matiere='$idmatiereSafe'"));
    if (intval($chk[0][0]) == 0) {
        http_response_code(403);
        echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
        exit;
    }
}

$sql = "SELECT elev_id, trim(nom), trim(prenom)
        FROM {$prefixe}eleves
        WHERE classe='$idclasseSafe'
          AND (compte_inactif IS NULL OR compte_inactif != '1')
        ORDER BY nom, prenom";
$data = chargeMat(execSql($sql));

$eleves = [];
for ($i = 0; $i < count($data); $i++) {
    $eleves[] = [
        "id"     => $data[$i][0],
        "nom"    => strtoupper(trim($data[$i][1])),
        "prenom" => ucfirst(strtolower(trim($data[$i][2]))),
    ];
}

Pgclose();
echo json_encode(["eleves" => $eleves], JSON_UNESCAPED_UNICODE);
?>
