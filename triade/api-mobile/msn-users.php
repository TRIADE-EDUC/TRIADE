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
    echo json_encode(["users" => []]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$myId    = intval($_SESSION['unique_id']);
$q       = addcslashes(trim($_GET['q'] ?? ''), '%_\\');

$now = time();
execSql("UPDATE {$prefixe}users SET status='Active now', update_sync='$now' WHERE unique_id='$myId'");

// Charge l'avatar MSN (tchat/php/images/) en base64 ; retourne null si absent/default
function msnPhotoUri($img) {
    if (empty($img) || $img === 'default.png') return null;
    $path = "../tchat/php/images/$img";
    if (!file_exists($path)) return null;
    $ext  = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
}

$search = $q ? "AND (u.fname LIKE '%$q%' OR u.lname LIKE '%$q%')" : '';
$sql    = "SELECT u.unique_id, u.fname, u.lname, u.status, u.img
           FROM {$prefixe}users u
           WHERE u.unique_id != '$myId'
             $search
           ORDER BY u.status ASC, u.fname, u.lname";

$rows  = chargeMat(execSql($sql));
$users = [];

foreach ((array)$rows as $row) {
    $uid    = intval($row[0]);
    $msgRow = chargeMat(execSql(
        "SELECT msg, outgoing_msg_id, msg_id FROM {$prefixe}messages
         WHERE (incoming_msg_id='$myId' AND outgoing_msg_id='$uid')
            OR (incoming_msg_id='$uid'  AND outgoing_msg_id='$myId')
         ORDER BY msg_id DESC LIMIT 1"
    ));
    $users[] = [
        "unique_id"    => $uid,
        "fname"        => $row[1],
        "lname"        => $row[2],
        "online"       => ($row[3] === 'Active now'),
        "last_msg"     => $msgRow[0][0] ?? '',
        "last_msg_id"  => isset($msgRow[0][2]) ? intval($msgRow[0][2]) : 0,
        "last_from_me" => isset($msgRow[0][1]) && intval($msgRow[0][1]) === $myId,
        "photo_uri"    => msnPhotoUri($row[4] ?? ''),
    ];
}

Pgclose();
echo json_encode(["users" => $users], JSON_UNESCAPED_UNICODE);
?>
