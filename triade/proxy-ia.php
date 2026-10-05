<?php
session_start();
if (empty($_SESSION['nom']) || empty($_SESSION['membre'])) {
    http_response_code(403);
    echo json_encode(['erreur' => 'Non authentifié.']);
    exit;
}

if (file_exists('./common/config-ia.php')) include_once('./common/config-ia.php');

$allowed = [
    'apimessagerie.php', 'apitext-to-speech-ia.php', 'apiContenuCours.php',
    'apiDevoir.php', 'apiVisaDir.php', 'apicombull.php', 'verifToken.php',
    'apisearch.php', 'agent-veille-setup.php', 'agent-list.php',
    'apiVeille.php', 'agent-create.php', 'agent-run.php', 'agent-delete.php',
    'agent-direction-setup.php', 'agent-direction.php',
    'agent-eduxpert-COT-vGemini.php', 'apiagentmessagerie.php',
];

$endpoint = $_GET['e'] ?? '';
if (!in_array($endpoint, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Endpoint invalide.']);
    exit;
}

$url      = 'https://ia.triade-educ.net/' . $endpoint;
$postData = http_build_query($_POST);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
$origin = 'https://' . ($_SERVER['HTTP_HOST'] ?? '');

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: ' . $origin,
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
