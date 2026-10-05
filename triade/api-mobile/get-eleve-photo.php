<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

header('Content-Type: text/plain');

$default = base64_encode(file_get_contents("../image/commun/photo_vide.jpg"));

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SESSION["membre"] !== 'menuprof') {
    echo $default;
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idprof  = intval($_SESSION["id_pers"]);
$elevId  = intval($_GET['elev_id'] ?? 0);

if (!$elevId) {
    Pgclose();
    echo $default;
    die();
}

// Vérifier affectation : le prof doit être assigné à la classe de l'élève
$classeData = chargeMat(execSql("SELECT classe FROM {$prefixe}eleves WHERE elev_id='$elevId'"));
if (!empty($classeData) && !empty($classeData[0][0])) {
    $classeEleve = addslashes($classeData[0][0]);
    $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$classeEleve'"));
    if (intval($chk[0][0]) == 0) {
        Pgclose();
        echo $default;
        die();
    }
} else {
    Pgclose();
    echo $default;
    die();
}

$photoLocal = recherche_photo_eleve($elevId);
Pgclose();

$_img_base = realpath('../data/image_eleve');
$_img_path = ($photoLocal && $_img_base) ? realpath('../data/image_eleve/' . $photoLocal) : false;
if ($_img_path && strpos($_img_path, $_img_base) === 0 && file_exists($_img_path)) {
    echo base64_encode(file_get_contents($_img_path));
} else {
    echo $default;
}
?>
