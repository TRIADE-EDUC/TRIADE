<?php
session_start();
if (empty($_SESSION['membre']) && empty($_SESSION['admin1'])) { http_response_code(403); exit; }

require_once __DIR__ . '/config.php';

$room = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['room'] ?? '');
$file = preg_replace('/[^a-zA-Z0-9_\-.]/', '', basename($_GET['file'] ?? ''));

if (!$room || !$file) { http_response_code(400); exit; }

$baseDir = realpath(VISIO_UPLOADS_BASE);
$path    = VISIO_UPLOADS_BASE . $room . DIRECTORY_SEPARATOR . $file;
$real    = realpath($path);

if (!$baseDir || !$real || strpos($real, $baseDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($real)) {
    http_response_code(404); exit;
}

$ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'png'  => 'image/png',  'gif'  => 'image/gif',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'doc'  => 'application/msword',
    'ppt'  => 'application/vnd.ms-powerpoint',
    'xls'  => 'application/vnd.ms-excel',
][$ext] ?? 'application/octet-stream';

$inline = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif']);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($real));
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . rawurlencode($file) . '"');
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
readfile($real);
