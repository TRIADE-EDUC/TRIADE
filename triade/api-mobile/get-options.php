<?php
session_start();
include_once("../common/config.inc.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

$before = get_defined_constants(true)['user'] ?? [];
include_once("../common/config2.inc.php");
include_once("../common/config-module.php");
$after = get_defined_constants(true)['user'] ?? [];

$diff = array_diff_key($after, $before);
$sensitiveWords = ['PASS', 'PWD', 'MDP', 'SECRET', 'TOKEN', 'SMTP', 'DSN', 'PREFIXE', 'METEOID', 'IAKEY'];
$options = array_filter($diff, function($val, $key) use ($sensitiveWords) {
    foreach ($sensitiveWords as $w) {
        if (stripos($key, $w) !== false) return false;
    }
    return true;
}, ARRAY_FILTER_USE_BOTH);
echo json_encode(["options" => $options]);
?>
