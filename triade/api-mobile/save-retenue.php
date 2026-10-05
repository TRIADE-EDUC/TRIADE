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

$cnx     = cnx();
$prefixe = PREFIXE;
$idUser  = intval($_SESSION["id_pers"]);

// Résoudre le nom depuis la base
$attribuerPar = '';
$persData = chargeMat(execSql("SELECT CONCAT(trim(prenom),' ',trim(nom)) FROM {$prefixe}personnel WHERE pers_id='$idUser'"));
if (!empty($persData) && trim($persData[0][0]) !== '') {
    $attribuerPar = trim($persData[0][0]);
} else {
    $attribuerPar = trim(($_SESSION["prenom"] ?? '') . ' ' . ($_SESSION["nom"] ?? ''));
    if ($attribuerPar === '') $attribuerPar = "$idUser";
}

$elevId       = intval($_POST['elev_id']        ?? 0);
$idCategory   = trim($_POST['id_category']      ?? '');
$motif        = trim($_POST['motif']            ?? '');
$description  = trim($_POST['description_fait'] ?? '');
$devoir       = trim($_POST['devoir_a_faire']   ?? '');
$dateRetenue  = trim($_POST['date_retenue']     ?? ''); // DD/MM/YYYY
$heureRetenue = trim($_POST['heure_retenue']    ?? ''); // HH:MM
$dureeRetenu  = trim($_POST['duree_retenu']     ?? ''); // HH:MM

if (!$elevId || !$idCategory || !$motif || !$dateRetenue || !$heureRetenue || !$dureeRetenu) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres requis manquants"]);
    exit;
}

// Vérifier que l'élève existe
$chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}eleves WHERE elev_id='$elevId' AND (compte_inactif IS NULL OR compte_inactif != '1')"));
if (intval($chk[0][0]) == 0) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Étudiant introuvable"]);
    exit;
}

$dateRetenueDb  = dateFormBase($dateRetenue);    // YYYY-MM-DD
$heureRetenueDb = $heureRetenue . ':00';          // HH:MM:SS
$dureeRetenueDb = $dureeRetenu  . ':00';          // HH:MM:SS
$dateSaisie     = dateDMY2();                     // YYYY-MM-DD

$idCatSafe        = addslashes($idCategory);
$motifSafe        = addslashes($motif);
$descSafe         = addslashes($description);
$devoirSafe       = addslashes($devoir);
$attribuerParSafe = addslashes($attribuerPar);

$sql = "INSERT INTO {$prefixe}discipline_retenue"
     . "(id_elev, date_de_la_retenue, heure_de_la_retenue, date_de_saisie, origi_saisie,"
     . " id_category, retenue_effectuer, motif, attribuer_par, signature_parent,"
     . " duree_retenu, devoir_a_faire, description_fait, courrier_env, repport_du)"
     . " VALUES ('$elevId', '$dateRetenueDb', '$heureRetenueDb', '$dateSaisie', 'App mobile',"
     . " '$idCatSafe', 'false', '$motifSafe', '$attribuerParSafe', 'false',"
     . " '$dureeRetenueDb', '$devoirSafe', '$descSafe', '', '')";

$res = $cnx->query($sql);
if (DB::isError($res)) {
    Pgclose();
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Erreur lors de l'enregistrement"]);
    exit;
}

notifyEleve($prefixe, $elevId, 'Retenue', 'Une retenue est prévue le ' . $dateRetenue);

Pgclose();
echo json_encode(["ok" => true]);
?>
