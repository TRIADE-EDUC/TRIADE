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

$allowedMembres = ['menuprof', 'menuadmin', 'menuscolaire'];
if (!in_array($_SESSION["membre"], $allowedMembres)) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
    die();
}

$cnx        = cnx();
$prefixe    = PREFIXE;
$idprof     = intval($_SESSION["id_pers"]);
$nomUser    = $_SESSION["nom"]    ?? '';
$prenomUser = $_SESSION["prenom"] ?? '';

$idclasse   = trim($_POST['idclasse']   ?? '');
$idmatiere  = trim($_POST['idmatiere']  ?? '');
$nomclasse  = trim($_POST['nomclasse']  ?? '');
$nommatiere = trim($_POST['nommatiere'] ?? '');
$creneau    = trim($_POST['creneau']    ?? '');
$aucunMode  = trim($_POST['aucun']      ?? '') === 'oui';
$elevesJson = stripslashes(trim($_POST['eleves'] ?? '[]'));

if (!$idclasse) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre idclasse requis"]);
    exit;
}

$idclasseSafe  = addslashes($idclasse);
$idmatiereSafe = addslashes($idmatiere);

if ($_SESSION["membre"] === 'menuprof') {
    $autorise = false;
    if ($idmatiere && $idmatiere !== '0') {
        $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe' AND code_matiere='$idmatiereSafe'"));
        $autorise = intval($chk[0][0]) > 0;
    }
    if (!$autorise) {
        $chk2 = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe'"));
        $autorise = intval($chk2[0][0]) > 0;
    }
    if (!$autorise) {
        http_response_code(403);
        echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
        exit;
    }
}

$date        = dateDMY();   // DD/MM/YYYY
$date_saisie = dateDMY2();  // YYYY-MM-DD

if ($aucunMode) {
    aucun_retard($nomclasse, $nommatiere, $nomUser, $prenomUser, $date);
    history_cmd($nomUser, "RTD/ABS", "enr. via app mobile");
    Pgclose();
    echo json_encode(["ok" => true, "nbabs" => 0, "nbrtd" => 0, "aucun" => true]);
    exit;
}

// Analyser le créneau : format "libelle#HH:MM:SS#HH:MM:SS"
$creneauParts = explode('#', $creneau);
$horaireLibelle = trim($creneauParts[0] ?? '');
$heure          = trim($creneauParts[1] ?? '');
$horaireFin     = trim($creneauParts[2] ?? '');

if (!$heure || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $heure)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Créneau requis ou format invalide"]);
    exit;
}

$creneauxFmt = "$horaireLibelle#" . timeForm($heure) . "#" . timeForm($horaireFin);

// Élèves valides pour cette classe (whitelist côté serveur)
$validIds = [];
$elevData = chargeMat(execSql("SELECT elev_id FROM {$prefixe}eleves WHERE classe='$idclasseSafe' AND (compte_inactif IS NULL OR compte_inactif != '1')"));
for ($i = 0; $i < count($elevData); $i++) {
    $validIds[] = intval($elevData[$i][0]);
}

$eleves  = json_decode($elevesJson, true);
if (!is_array($eleves)) { $eleves = []; }
$nbrtd   = 0;
$nbabs   = 0;
$dateFmt = preg_match('/\//', $date) ? dateFormBase($date) : $date;

$notifyAbs = [];
$notifyRtd = [];

foreach ($eleves as $el) {
    $elevId = intval($el['id']   ?? 0);
    $statut = trim($el['statut'] ?? 'rien');
    $duree  = max(0, intval($el['duree'] ?? 0));

    if (!$elevId || $statut === 'rien') continue;
    if (!in_array($elevId, $validIds)) continue; // élève n'appartient pas à cette classe

    $timeNow = dateHIS();

    if ($statut === 'retard') {
        $sqlRtd = "INSERT INTO {$prefixe}retards"
            . "(elev_id,heure_ret,date_ret,date_saisie,origin_saisie,duree_ret,motif,idmatiere,justifier,heure_saisie,idprof,creneaux)"
            . " VALUES ('$elevId','$heure','$dateFmt','$date_saisie','$nomUser','$duree','inconnu','$idmatiere','0','$timeNow','$idprof','$creneauxFmt')";
        $resRtd = $cnx->query($sqlRtd);
        if (DB::isError($resRtd)) {
            Pgclose();
            echo json_encode(["ok" => false, "message" => "Erreur lors de l'enregistrement du retard"]);
            exit;
        }
        history_cmd($nomUser, "RTD", "enr. via app mobile");
        $nbrtd++;
        $notifyRtd[] = $elevId;

    } elseif ($statut === 'absent') {
        $sqlAbs = "INSERT INTO {$prefixe}absences"
            . "(elev_id,date_ab,date_saisie,origin_saisie,duree_ab,date_fin,motif,duree_heure,id_matiere,time,justifier,heure_saisie,heuredabsence,idprof,creneaux,idrattrapage)"
            . " VALUES ('$elevId','$dateFmt','$date_saisie','$nomUser','0','$dateFmt','inconnu','0','$idmatiere','$timeNow','0','$timeNow','$heure','$idprof','$creneauxFmt','')";
        $resAbs = $cnx->query($sqlAbs);
        if (DB::isError($resAbs)) {
            Pgclose();
            echo json_encode(["ok" => false, "message" => "Erreur lors de l'enregistrement de l'absence"]);
            exit;
        }
        history_cmd($nomUser, "ABS", "enr. via app mobile");
        $nbabs++;
        $notifyAbs[] = $elevId;
    }
}

enrAbsrtdHisto($nomclasse, $nommatiere, $nomUser, $prenomUser, $date, $nbabs, $nbrtd);

foreach ($notifyAbs as $eid) {
    notifyEleve($prefixe, $eid, 'Absence', 'Une absence a été enregistrée le ' . $date);
}
foreach ($notifyRtd as $eid) {
    notifyEleve($prefixe, $eid, 'Retard', 'Un retard a été enregistré le ' . $date);
}

Pgclose();
echo json_encode(["ok" => true, "nbabs" => $nbabs, "nbrtd" => $nbrtd]);
?>
