<?php session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

$defaultPath = "../image/commun/photo_vide.jpg";
$defaultB64  = file_exists($defaultPath) ? base64_encode(file_get_contents($defaultPath)) : '';
$default     = json_encode(["data" => $defaultB64, "type" => "image/jpeg"]);

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    echo $default;
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;

$id   = intval($_GET['id']   ?? 0);
$type = trim(  $_GET['type'] ?? '');

$allowed = ['ELE', 'ENS', 'ADM', 'PER', 'MVS', 'TUT', 'PAR'];
if (!$id || !in_array($type, $allowed)) {
    Pgclose();
    echo $default;
    die();
}

if ($type === 'ELE') {
    $photoName = recherche_photo_eleve($id);
    $path      = "../data/image_eleve/$photoName";
} else {
    $photoName = recherche_photo_pers($id);
    $path      = "../data/image_pers/$photoName";
}

Pgclose();

if (!empty($photoName) && file_exists($path)) {
    $ext  = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
    echo json_encode(["data" => base64_encode(file_get_contents($path)), "type" => $mime]);
} else {
    echo $default;
}
?>
