<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$membre  = $_SESSION["membre"] ?? "";

$typeMap = [
    "menuadmin"     => "ADM",
    "menututeur"    => "TUT",
    "menupersonnel" => "PER",
    "menuprof"      => "ENS",
    "menuscolaire"  => "MVS",
    "menuparent"    => "PAR",
    "menueleve"     => "ELE",
];
$typePersonne = $typeMap[$membre] ?? "ELE";

if ($membre === "menuparent" || $membre === "menueleve") {
    $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]);
} else {
    $civMap = [
        "menuadmin"     => "ADM",
        "menututeur"    => "TUT",
        "menupersonnel" => "PER",
        "menuprof"      => "ENS",
        "menuscolaire"  => "MVS",
    ];
    $civ          = $civMap[$membre] ?? "ADM";
    $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], $civ);
}

$res   = chargeMat(execSql(
    "SELECT COUNT(*) FROM {$prefixe}messageries"
    . " WHERE brouillon='0' AND type_personne_dest='$typePersonne' AND destinataire='$destinataire'"
    . " AND lu='0' AND (repertoire IS NULL OR repertoire='0')"
));
$count = intval($res[0][0] ?? 0);

Pgclose();
echo json_encode(["count" => $count]);
?>
