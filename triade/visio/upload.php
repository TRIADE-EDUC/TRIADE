<?php
session_start();
if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) { http_response_code(403); exit; }
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

// Nettoyage passif : supprimer les dossiers de salle plus vieux que VISIO_UPLOADS_TTL
if (is_dir(VISIO_UPLOADS_BASE)) {
    $limit = time() - VISIO_UPLOADS_TTL;
    foreach (glob(VISIO_UPLOADS_BASE . '*', GLOB_ONLYDIR) as $roomDir) {
        if (filemtime($roomDir) < $limit) {
            array_map('unlink', glob($roomDir . DIRECTORY_SEPARATOR . '*'));
            @rmdir($roomDir);
        }
    }
}

$maxSize     = 10 * 1024 * 1024;
$allowedExt  = ['pdf','jpg','jpeg','png','gif','docx','pptx','xlsx','doc','ppt','xls'];

if (!isset($_FILES['doc']) || $_FILES['doc']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Aucun fichier ou erreur d\'upload']); exit;
}

$file = $_FILES['doc'];
if ($file['size'] > $maxSize) {
    echo json_encode(['error' => 'Fichier trop grand (max 10 Mo)']); exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt)) {
    echo json_encode(['error' => 'Type de fichier non autorisé']); exit;
}

$room = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['room'] ?? '');
if (!$room) { echo json_encode(['error' => 'Salle manquante']); exit; }

$dir = VISIO_UPLOADS_BASE . $room . DIRECTORY_SEPARATOR;
if (!is_dir($dir)) mkdir($dir, 0750, true);

$nom_original = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', basename($file['name']));
$nom_unique   = time() . '_' . $nom_original;
$dest         = $dir . $nom_unique;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['error' => 'Impossible de sauvegarder le fichier']); exit;
}

$base     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$dir_path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
$url      = $base . $dir_path . '/doc.php?room=' . urlencode($room) . '&file=' . urlencode($nom_unique);

echo json_encode(['ok' => true, 'url' => $url, 'nom' => $nom_original]);
