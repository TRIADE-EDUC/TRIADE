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

$cnx     = cnx();
$prefixe = PREFIXE;
$idPers  = intval($_SESSION["id_pers"]);
$membre  = addslashes($_SESSION["membre"]);

// Vérification optionnelle des params GET contre la session (sécurité : le client ne peut pas usurper un autre compte)
if (isset($_GET['idpers']) || isset($_GET['membre'])) {
    $idPersGet  = intval($_GET['idpers'] ?? 0);
    $membreGet  = addslashes(trim($_GET['membre'] ?? ''));
    if ($idPersGet !== $idPers || $membreGet !== $membre) {
        Pgclose();
        http_response_code(403);
        echo json_encode(["code" => "403", "message" => "Accès interdit"]);
        die();
    }
}
$nom     = addslashes(trim($_SESSION["nom"]));
$prenom  = addslashes(trim($_SESSION["prenom"]));
$email   = $idPers . '_' . $membre . '@msn.triade';

// Compte déjà initialisé dans cette session : vérifier que l'ID existe réellement
if (isset($_SESSION['unique_id']) && intval($_SESSION['unique_id']) > 0) {
    $uniqueId = intval($_SESSION['unique_id']);
    $checkExists = chargeMat(execSql("SELECT unique_id FROM {$prefixe}users WHERE unique_id='$uniqueId'"));
    if (countTriade($checkExists) >= 1) {
        $now = time();
        execSql("UPDATE {$prefixe}users SET status='Active now', update_sync='$now', id_pers='$idPers', membre='$membre' WHERE unique_id='$uniqueId'");
        Pgclose();
        echo json_encode(["ok" => true, "unique_id" => $uniqueId, "fname" => trim($_SESSION["prenom"]), "lname" => trim($_SESSION["nom"])]);
        die();
    }
    unset($_SESSION['unique_id']);
}

// 1. Recherche par id_pers + membre (identifiant sûr et unique)
$byIdpers = chargeMat(execSql(
    "SELECT unique_id FROM {$prefixe}users WHERE id_pers='$idPers' AND membre='$membre' LIMIT 1"
));
if (countTriade($byIdpers) >= 1) {
    $uniqueId = intval($byIdpers[0][0]);
    $_SESSION['unique_id'] = $uniqueId;
    $now = time();
    execSql("UPDATE {$prefixe}users SET email='$email', status='Active now', update_sync='$now' WHERE unique_id='$uniqueId'");
    Pgclose();
    echo json_encode(["ok" => true, "unique_id" => $uniqueId, "fname" => trim($_SESSION["prenom"]), "lname" => trim($_SESSION["nom"])]);
    die();
}

// 2. Backward compat : compte existant avec email @msn.triade
if (verifCompteIntraMsn($email)) {
    $uniqueId = intval($_SESSION['unique_id']);
    $now = time();
    execSql("UPDATE {$prefixe}users SET id_pers='$idPers', membre='$membre', status='Active now', update_sync='$now' WHERE unique_id='$uniqueId'");
    Pgclose();
    echo json_encode(["ok" => true, "unique_id" => $uniqueId, "fname" => trim($_SESSION["prenom"]), "lname" => trim($_SESSION["nom"])]);
    die();
}

// 3. Création automatique (plus jamais de 404 pour un utilisateur authentifié)
$uid = abs(crc32($email)) % 9000000 + 1000000;
$chk = chargeMat(execSql("SELECT unique_id FROM {$prefixe}users WHERE unique_id='$uid'"));
if (countTriade($chk)) {
    $uid = rand(10000000, 99999999);
}
$now = time();
execSql("INSERT INTO {$prefixe}users
    (unique_id, fname, lname, email, password, img, status, update_sync, id_pers, membre)
    VALUES ('$uid', '$prenom', '$nom', '$email', '', 'default.png', 'Active now', '$now', '$idPers', '$membre')");
$_SESSION['unique_id'] = $uid;

Pgclose();
echo json_encode(["ok" => true, "unique_id" => $uid, "fname" => trim($_SESSION["prenom"]), "lname" => trim($_SESSION["nom"])]);
?>
