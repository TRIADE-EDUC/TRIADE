<?php session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

$cnx      = cnx();
$profil   = $_SESSION["profil"]   ?? "0";
$idpers   = $_SESSION["id_pers"]  ?? 0;
$idclasse = $_SESSION["idClasse"] ?? 0;

// Seuls les élèves (1) et parents (2) ont des données personnelles de vie scolaire
if ($profil != "1" && $profil != "2") {
    Pgclose();
    echo json_encode(["total" => 0, "absences" => 0, "retards" => 0, "sanctions" => 0, "retenues" => 0]);
    exit;
}

$annee = anneeScolaireViaIdClasse($idclasse);

$absData  = affAbsence($idpers, $annee)             ?: [];
$rtdData  = affRetard($idpers, $annee)              ?: [];
$sancData = affSanction_par_eleve($idpers, $annee)  ?: [];
$retData  = affRetenuTotal_par_eleve($idpers, $annee) ?: [];

$nbAbs  = count($absData);
$nbRtd  = count($rtdData);
$nbSanc = count($sancData);
$nbRet  = count($retData);

Pgclose();
echo json_encode([
    "total"     => $nbAbs + $nbRtd + $nbSanc + $nbRet,
    "absences"  => $nbAbs,
    "retards"   => $nbRtd,
    "sanctions" => $nbSanc,
    "retenues"  => $nbRet,
]);
?>
