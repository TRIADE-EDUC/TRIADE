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

if ($_SESSION["membre"] !== 'menueleve') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Réservé aux élèves"]);
    Pgclose();
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idEleve = intval($_SESSION["id_pers"]);

if (!$idEleve) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Session invalide"]);
    Pgclose();
    exit;
}

$lat    = floatval($_POST['latitude']  ?? 0);
$lon    = floatval($_POST['longitude'] ?? 0);
$precis = isset($_POST['precision']) && $_POST['precision'] !== '' ? floatval($_POST['precision']) : null;

if (!$lat && !$lon) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Coordonnées manquantes"]);
    Pgclose();
    exit;
}

$dateGps  = date('Y-m-d');
$heureGps = date('H:i:s');
$precisSql = ($precis !== null) ? "'$precis'" : "NULL";

execSql("INSERT INTO {$prefixe}gps_eleve (id_eleve, date_gps, heure_gps, latitude, longitude, precision_m)
    VALUES ('$idEleve', '$dateGps', '$heureGps', '$lat', '$lon', $precisSql)");

Pgclose();
echo json_encode(["ok" => true]);
?>
