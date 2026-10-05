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

if (!isset($_SESSION['unique_id'])) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Compte MSN non initialisé"]);
    die();
}

$cnx        = cnx();
$prefixe    = PREFIXE;
$myId       = intval($_SESSION['unique_id']);
$incomingId = intval($_GET['incoming_id'] ?? 0);
$since      = intval($_GET['since'] ?? 0);

if (!$incomingId) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "incoming_id requis"]);
    die();
}

$now = time();
execSql("UPDATE {$prefixe}users SET status='Active now', update_sync='$now' WHERE unique_id='$myId'");

function msnPhotoUri($img) {
    if (empty($img) || $img === 'default.png') return null;
    $path = "../tchat/php/images/$img";
    if (!file_exists($path)) return null;
    $ext  = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
}

$contactRow  = chargeMat(execSql("SELECT fname, lname, status, img FROM {$prefixe}users WHERE unique_id='$incomingId'"));
$sinceClause = $since ? "AND m.msg_id > '$since'" : '';
$sql = "SELECT m.msg_id, m.msg, m.outgoing_msg_id
        FROM {$prefixe}messages m
        WHERE ((m.outgoing_msg_id='$myId' AND m.incoming_msg_id='$incomingId')
            OR (m.outgoing_msg_id='$incomingId' AND m.incoming_msg_id='$myId'))
        $sinceClause
        ORDER BY m.msg_id ASC";

$rows = chargeMat(execSql($sql));
$messages = [];
foreach ((array)$rows as $row) {
    $messages[] = [
        "msg_id"  => intval($row[0]),
        "msg"     => $row[1],
        "from_me" => intval($row[2]) === $myId,
    ];
}

// Debug élargi : messages impliquant myId (peu importe le partenaire)
$rawMyId = chargeMat(execSql(
    "SELECT msg_id, outgoing_msg_id, incoming_msg_id FROM {$prefixe}messages
     WHERE outgoing_msg_id='$myId' OR incoming_msg_id='$myId'
     ORDER BY msg_id DESC LIMIT 5"
));
$debugMyId = [];
foreach ((array)$rawMyId as $r) {
    if (empty($r)) continue;
    $debugMyId[] = ['id'=>$r[0],'out'=>$r[1],'in'=>$r[2]];
}

// Debug élargi : messages impliquant contactId (peu importe le partenaire)
$rawContactId = chargeMat(execSql(
    "SELECT msg_id, outgoing_msg_id, incoming_msg_id FROM {$prefixe}messages
     WHERE outgoing_msg_id='$incomingId' OR incoming_msg_id='$incomingId'
     ORDER BY msg_id DESC LIMIT 5"
));
$debugContactId = [];
foreach ((array)$rawContactId as $r) {
    if (empty($r)) continue;
    $debugContactId[] = ['id'=>$r[0],'out'=>$r[1],'in'=>$r[2]];
}

// Vérifier aussi ce qu'est le contact dans tria_users
$contactCheck = chargeMat(execSql(
    "SELECT unique_id, fname, lname, email FROM {$prefixe}users WHERE unique_id='$incomingId'"
));
$myCheck = chargeMat(execSql(
    "SELECT unique_id, fname, lname, email FROM {$prefixe}users WHERE unique_id='$myId'"
));

Pgclose();
echo json_encode([
    "messages" => $messages,
    "contact"  => [
        "fname"     => $contactRow[0][0] ?? '',
        "lname"     => $contactRow[0][1] ?? '',
        "online"    => ($contactRow[0][2] ?? '') === 'Active now',
        "photo_uri" => msnPhotoUri($contactRow[0][3] ?? ''),
    ],
    "_d" => [
        "my"          => $myId,
        "inc"         => $incomingId,
        "found"       => count($messages),
        "myUser"      => $myCheck[0] ?? null,
        "incUser"     => $contactCheck[0] ?? null,
        "myMsgs"      => $debugMyId,
        "incMsgs"     => $debugContactId,
    ],
], JSON_UNESCAPED_UNICODE);
?>
