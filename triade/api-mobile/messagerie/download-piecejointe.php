<?php
session_start();

include_once("../../common/config.inc.php");
include_once("../../common/config2.inc.php");
include_once("../../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, vous n'êtes pas connecté"]);
    die();
}

if (!isset($_GET["md5"]) || trim($_GET["md5"]) === '') {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre md5 manquant"]);
    exit;
}

// Sanitize : uniquement des caractères hexadécimaux
$md5 = preg_replace('/[^a-f0-9A-F]/', '', trim($_GET["md5"]));
if (strlen($md5) < 8) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "md5 invalide"]);
    exit;
}

$prefixe = PREFIXE;
$cnx     = cnx();

$res  = execSql("SELECT nom, md5 FROM {$prefixe}piecejointe WHERE md5='$md5' AND etat='1' LIMIT 1");
$data = chargeMat($res);

if (empty($data) || empty($data[0][0])) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Pièce jointe introuvable"]);
    Pgclose();
    exit;
}

$nom      = $data[0][0];
$filepath = realpath(__DIR__ . '/../../data/fichiersj/' . $md5);

// Vérification de sécurité : le chemin doit rester dans data/fichiersj/
$base_dir = realpath(__DIR__ . '/../../data/fichiersj');
if (!$filepath || strpos($filepath, $base_dir) !== 0 || !is_file($filepath)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Fichier manquant sur le serveur"]);
    Pgclose();
    exit;
}

$ext = strtolower(pathinfo($nom, PATHINFO_EXTENSION));
$mime_map = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'odt'  => 'application/vnd.oasis.opendocument.text',
    'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    'odg'  => 'application/vnd.oasis.opendocument.graphics',
    'txt'  => 'text/plain',
    'zip'  => 'application/zip',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
];
$mime = $mime_map[$ext] ?? 'application/octet-stream';

Pgclose();

echo json_encode([
    "nom"  => $nom,
    "type" => $mime,
    "data" => base64_encode(file_get_contents($filepath)),
], JSON_UNESCAPED_UNICODE);
?>
