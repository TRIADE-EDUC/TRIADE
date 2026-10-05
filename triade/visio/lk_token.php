<?php
session_start();
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");

if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) {
    http_response_code(403);
    echo json_encode(array('error' => 'non_autorise'));
    exit;
}

$cnx = cnx();
include_once("lk_config.php");

header('Content-Type: application/json');

$room     = preg_replace('/[^a-zA-Z0-9]/',    '', isset($_POST['room'])     ? $_POST['room']     : '');
$identity = preg_replace('/[^a-zA-Z0-9_\-]/', '', isset($_POST['identity']) ? $_POST['identity'] : '');
$nom      = substr(strip_tags(isset($_POST['nom']) ? $_POST['nom'] : ''), 0, 64);

if (!$room || !$identity) {
    echo json_encode(array('error' => 'parametres_manquants')); Pgclose(); exit;
}
if (!lkEnabled()) {
    echo json_encode(array('error' => 'livekit_desactive')); Pgclose(); exit;
}

$r = _lkPost(array(
    'action'   => 'token',
    'room'     => $room,
    'identity' => $identity,
    'nom'      => $nom
));

Pgclose();

if (empty($r['token'])) {
    echo json_encode(array('error' => isset($r['error']) ? $r['error'] : 'token_error'));
    exit;
}

echo json_encode(array('token' => $r['token'], 'lk_url' => $r['lk_url']));
