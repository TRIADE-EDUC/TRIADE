<?php
// Fichier de template désactivé en production — credentials de démo codés en dur.
http_response_code(403);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(["error" => "Accès interdit"]);
exit;
