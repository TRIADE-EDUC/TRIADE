<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("push_helper.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
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
$incomingId = intval($_POST['incoming_id'] ?? 0);
$messageRaw = trim($_POST['message'] ?? '');
$message    = addslashes($messageRaw);

if (!$incomingId || !$message) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "incoming_id et message requis"]);
    die();
}

// Vérifier que le destinataire existe et n'est pas soi-même
if ($incomingId === $myId) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Impossible d'envoyer un message à soi-même"]);
    die();
}
$targetChk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}users WHERE unique_id='$incomingId'"));
if (intval($targetChk[0][0]) == 0) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Destinataire introuvable"]);
    die();
}

$now = time();
execSql("UPDATE {$prefixe}users SET status='Active now', update_sync='$now' WHERE unique_id='$myId'");
execSql("INSERT INTO {$prefixe}messages (incoming_msg_id, outgoing_msg_id, msg)
         VALUES ('$incomingId', '$myId', '$message')");

$senderRows = chargeMat(execSql("SELECT fname, lname FROM {$prefixe}users WHERE unique_id='$myId'"));
$senderFname = ($senderRows && !empty($senderRows[0])) ? ($senderRows[0][0] ?? '') : '';
$senderLname = ($senderRows && !empty($senderRows[0])) ? ($senderRows[0][1] ?? '') : '';
$senderName  = trim("$senderFname $senderLname") ?: 'Nouveau message';
notifyMsnUser($prefixe, $incomingId, $senderName, $messageRaw, $myId, $senderFname, $senderLname);

Pgclose();
echo json_encode(["ok" => true]);
?>
