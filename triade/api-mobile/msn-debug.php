<?php
// Fichier de diagnostic désactivé en production.
http_response_code(403);
echo json_encode(["error" => "Accès interdit"]);
exit;
/*
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Non authentifié"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idPers  = intval($_SESSION["id_pers"] ?? 0);
$membre  = $_SESSION["membre"] ?? '';
$prenom  = trim($_SESSION["prenom"] ?? '');
$nom     = trim($_SESSION["nom"] ?? '');
$uid     = intval($_SESSION["unique_id"] ?? 0);

$expectedEmail = $idPers . '_' . $membre . '@msn.triade';
$crc32uid = abs(crc32($expectedEmail)) % 9000000 + 1000000;

// Compte en DB
$dbAccount = null;
if ($uid) {
    $rows = chargeMat(execSql("SELECT unique_id, fname, lname, email, status FROM {$prefixe}users WHERE unique_id='$uid'"));
    if ($rows && !empty($rows[0])) {
        $dbAccount = ['unique_id'=>$rows[0][0],'fname'=>$rows[0][1],'lname'=>$rows[0][2],'email'=>$rows[0][3],'status'=>$rows[0][4]];
    }
}

// Compte par email attendu
$byEmail = null;
if ($expectedEmail) {
    $em = addslashes($expectedEmail);
    $rows2 = chargeMat(execSql("SELECT unique_id, fname, lname, email FROM {$prefixe}users WHERE email='$em'"));
    if ($rows2 && !empty($rows2[0])) {
        $byEmail = ['unique_id'=>$rows2[0][0],'fname'=>$rows2[0][1],'lname'=>$rows2[0][2],'email'=>$rows2[0][3]];
    }
}

// Derniers messages
$msgs = [];
if ($uid) {
    $mrows = chargeMat(execSql("SELECT msg_id, outgoing_msg_id, incoming_msg_id, SUBSTRING(msg,1,50) FROM {$prefixe}messages WHERE outgoing_msg_id='$uid' OR incoming_msg_id='$uid' ORDER BY msg_id DESC LIMIT 10"));
    foreach ((array)$mrows as $r) {
        if (empty($r)) continue;
        $msgs[] = ['msg_id'=>$r[0],'from'=>$r[1],'to'=>$r[2],'msg'=>$r[3]];
    }
}

Pgclose();
echo json_encode([
    "session" => [
        "id_pers"   => $idPers,
        "membre"    => $membre,
        "prenom"    => $prenom,
        "nom"       => $nom,
        "unique_id" => $uid,
    ],
    "expected_email"  => $expectedEmail,
    "crc32_uid"       => $crc32uid,
    "uid_matches_crc" => ($uid === $crc32uid),
    "db_by_session_uid" => $dbAccount,
    "db_by_expected_email" => $byEmail,
    "last_messages"   => $msgs,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
*/
?>
