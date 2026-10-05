<?php
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");

header('Content-Type: application/json; charset=utf-8');

$id_category = isset($_GET['id_category']) ? intval($_GET['id_category']) : 0;

if ($id_category <= 0) {
    echo json_encode([]);
    die();
}

$cnx = cnx();
$sql = "SELECT libelle FROM " . PREFIXE . "type_sanction WHERE id_category='$id_category' ORDER BY libelle";
$res = execSql($sql);
$data = chargeMat($res);

$sanctions = [];
for ($i = 0; $i < count($data); $i++) {
    $sanctions[] = trim($data[$i][0]);
}

Pgclose();
echo json_encode($sanctions, JSON_UNESCAPED_UNICODE);
?>
