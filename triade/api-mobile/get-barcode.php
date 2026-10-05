<?php
session_start();

ini_set('display_errors', 0);
error_reporting(0);
define('IN_CB', true);

include_once(__DIR__ . "/../common/config.inc.php");
include_once(__DIR__ . "/../common/config2.inc.php");
include_once(__DIR__ . "/../librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

$text = strtoupper(trim($_GET["text"] ?? ''));
if ($text === '') {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre text manquant"]);
    exit;
}

$cbDir = __DIR__ . '/../codebar';

require($cbDir . '/class/index.php');
require($cbDir . '/class/FColor.php');
require($cbDir . '/class/BarCode.php');
require($cbDir . '/class/FDrawing.php');
include($cbDir . '/class/code39.barcode.php');

// Police GD intégrée (int 3) : évite toute dépendance à FreeType / fichier TTF
$color_bar   = new FColor(8, 10, 102);
$color_white = new FColor(255, 255, 255);

$barcode = new code39('40', $color_bar, $color_white, 1, $text, 3);
$drawing = new FDrawing('', $color_white);
$drawing->setBarcode($barcode);
$drawing->draw();

ob_start();
$drawing->finish(IMG_FORMAT_PNG);
$png = ob_get_clean();

Pgclose();

if (empty($png)) {
    http_response_code(500);
    echo json_encode(["code" => "500", "message" => "Erreur génération code-barres"]);
    exit;
}

echo json_encode(["image" => base64_encode($png)]);
?>
