<?php
/*
 * Suppression des fichiers d'une salle visio.
 * Appelé par :
 *  - le serveur WS (webhook POST JSON { room, secret }) quand la salle se vide
 *  - raccrocher() en JS (fetch GET ?room=X depuis la session active)
 */
require_once __DIR__ . '/config.php';

// ── Authentification : soit session PHP active, soit secret WS ────────
$authorized = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true);
    $secret = getenv('TRIADE_WS_SECRET') ?: '';
    if ($secret && isset($body['secret']) && hash_equals($secret, $body['secret'])) {
        $authorized = true;
        $room = preg_replace('/[^a-zA-Z0-9]/', '', $body['room'] ?? '');
    }
} else {
    session_start();
    if (!empty($_SESSION['membre']) || !empty($_SESSION['admin1'])) {
        $authorized = true;
        $room = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['room'] ?? '');
    }
}

if (!$authorized || empty($room)) { http_response_code(403); exit; }

// ── Suppression du dossier de la salle ───────────────────────────────
$dir = VISIO_UPLOADS_BASE . $room . DIRECTORY_SEPARATOR;
$dir = realpath($dir);
$base = realpath(VISIO_UPLOADS_BASE);

if ($dir && $base && strpos($dir, $base . DIRECTORY_SEPARATOR) === 0 && is_dir($dir)) {
    array_map('unlink', glob($dir . '*'));
    @rmdir($dir);
}

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
