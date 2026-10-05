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

if ($_SESSION["membre"] !== 'menuparent') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Réservé aux parents"]);
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

// Nom/prénom de l'élève
$eleveData = chargeMat(execSql("SELECT nom, prenom FROM {$prefixe}eleves WHERE elev_id='$idEleve' LIMIT 1"));
$nom    = isset($eleveData[0][0]) ? stripslashes($eleveData[0][0]) : '';
$prenom = isset($eleveData[0][1]) ? stripslashes($eleveData[0][1]) : '';

// Derniers points GPS (48h max, 100 points max)
$points = [];
$gpsData = chargeMat(execSql(
    "SELECT date_gps, heure_gps, latitude, longitude, precision_m
     FROM {$prefixe}gps_eleve
     WHERE id_eleve='$idEleve'
     ORDER BY date_gps DESC, heure_gps DESC
     LIMIT 200"
));

if ($gpsData) {
    foreach ($gpsData as $row) {
        $dateObj = DateTime::createFromFormat('Y-m-d', $row[0]);
        $points[] = [
            "date"      => $dateObj ? $dateObj->format('d/m/Y') : $row[0],
            "heure"     => substr($row[1], 0, 5),
            "latitude"  => floatval($row[2]),
            "longitude" => floatval($row[3]),
            "precision" => $row[4] !== null ? floatval($row[4]) : null,
        ];
    }
}

Pgclose();
echo json_encode([
    "eleve"  => ["nom" => $nom, "prenom" => $prenom],
    "points" => $points,
]);
?>
