<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("push_helper.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

if (!in_array($_SESSION["membre"], ['menuadmin', 'menuscolaire'])) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
    die();
}

$cnx        = cnx();
$prefixe    = PREFIXE;
$idUser     = intval($_SESSION["id_pers"]);

// Récupérer le nom depuis la base (attribuer_par doit être un nom lisible)
$attribuerPar = '';
$persData = chargeMat(execSql("SELECT CONCAT(trim(prenom),' ',trim(nom)) FROM {$prefixe}personnel WHERE pers_id='$idUser'"));
if (!empty($persData) && trim($persData[0][0]) !== '') {
    $attribuerPar = trim($persData[0][0]);
} else {
    $attribuerPar = trim(($_SESSION["prenom"] ?? '') . ' ' . ($_SESSION["nom"] ?? ''));
    if ($attribuerPar === '') $attribuerPar = "$idUser";
}

$elevId      = intval($_POST['elev_id']        ?? 0);
$idCategory  = trim($_POST['id_category']      ?? '');
$motif       = trim($_POST['motif']            ?? '');
$description = trim($_POST['description_fait'] ?? '');
$devoir      = trim($_POST['devoir_a_faire']   ?? '');

if (!$elevId) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre elev_id requis"]);
    exit;
}
if (!$idCategory) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Catégorie requise"]);
    exit;
}
if (!$motif) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Motif requis"]);
    exit;
}

// Vérifier que l'élève existe
$chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}eleves WHERE elev_id='$elevId' AND (compte_inactif IS NULL OR compte_inactif != '1')"));
if (intval($chk[0][0]) == 0) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Étudiant introuvable"]);
    exit;
}

$idCatSafe   = addslashes($idCategory);
$motifSafe   = addslashes($motif);
$descSafe    = addslashes($description);
$devoirSafe  = addslashes($devoir);
$dateSaisie  = dateDMY2(); // YYYY-MM-DD

$attribuerParSafe = addslashes($attribuerPar);
$sql = "INSERT INTO {$prefixe}discipline_sanction"
     . "(id_eleve, motif, id_category, date_saisie, attribuer_par, origin_saisie, enr_en_retenue, signature_parent, devoir_a_faire, description_fait)"
     . " VALUES ('$elevId', '$motifSafe', '$idCatSafe', '$dateSaisie', '$attribuerParSafe', 'App mobile', '0', '0', '$devoirSafe', '$descSafe')";

$res = $cnx->query($sql);
if (DB::isError($res)) {
    Pgclose();
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Erreur lors de l'enregistrement"]);
    exit;
}

notifyEleve($prefixe, $elevId, 'Sanction disciplinaire', 'Une sanction a été enregistrée');

Pgclose();
echo json_encode(["ok" => true]);
?>
