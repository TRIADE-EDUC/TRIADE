<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

if (isset($_SESSION["connecte"]) && $_SESSION["connecte"] == true) {
    $cnx = cnx();
    header('Content-Type: application/json; charset=utf-8');
    $rows = getHistoriqueConnexionsMobile($_SESSION['id_pers'], $_SESSION['membre'], $_SESSION['idparent']);
    $result = [];
    foreach ($rows as $r) {
        $result[] = ['ip' => $r[0], 'source' => $r[1], 'dernier_acces' => $r[2], 'pays' => ipToCountryCode($r[0])];
    }
    Pgclose();
    echo json_encode(['code' => '200', 'connexions' => $result]);
} else {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => '403', 'message' => 'Accès interdit']);
}
?>
