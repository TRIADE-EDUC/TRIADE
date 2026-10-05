<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

if ($_SESSION["membre"] !== 'menuprof') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Réservé aux enseignants"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idprof  = intval($_SESSION["id_pers"]);

$idclasse    = trim($_POST['idclasse']    ?? '');
$idmatiere   = trim($_POST['idmatiere']   ?? '');
$sujet       = trim($_POST['sujet']       ?? '');
$dateDevoir  = trim($_POST['date_devoir'] ?? date('Y-m-d'));
$texte       = trim($_POST['texte']       ?? '');
$bareme      = max(1, intval($_POST['bareme']    ?? 20));
$coef        = max(0.01, floatval(str_replace(',', '.', $_POST['coef'] ?? '1')));
$tempsestime = trim($_POST['tempsestime'] ?? '');
$notesJson   = stripslashes(trim($_POST['notes'] ?? '[]'));

if (!$idclasse || !$idmatiere || !$sujet) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres idclasse, idmatiere, sujet requis"]);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateDevoir)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Format date_devoir invalide (YYYY-MM-DD)"]);
    exit;
}

$idclasseSafe  = addslashes($idclasse);
$idmatiereSafe = addslashes($idmatiere);
$sujetSafe     = addslashes($sujet);
$texteSafe     = addslashes($texte);
$tempsSafe     = addslashes($tempsestime);

// Vérification affectation prof/classe/matière
$chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe' AND code_matiere='$idmatiereSafe'"));
if (intval($chk[0][0]) == 0) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
    exit;
}

// Whitelist élèves actifs de la classe (sécurité côté serveur)
$validIds = [];
$elevData = chargeMat(execSql("SELECT elev_id FROM {$prefixe}eleves WHERE classe='$idclasseSafe' AND (compte_inactif IS NULL OR compte_inactif != '1')"));
foreach ($elevData as $row) {
    $validIds[] = intval($row[0]);
}

$notes = json_decode($notesJson, true);
if (!is_array($notes)) $notes = [];

$dateNow = dateDMY2(); // YYYY-MM-DD
$timeNow = dateHIS();  // HH:MM:SS

function convertNote($val) {
    $v = strtolower(trim((string)$val));
    if ($v === 'abs')                                    return '-1';
    if ($v === 'disp')                                   return '-2';
    if ($v === 'néant' || $v === 'neant' || $v === '')  return '-3';
    if ($v === 'dnn')                                    return '-4';
    if ($v === 'dnr')                                    return '-5';
    if ($v === 'val')                                    return '-6';
    if (is_numeric($v))                                  return strval(floatval($v));
    return '-3';
}

$nbNotes = 0;

foreach ($notes as $n) {
    $elevId  = intval($n['id']   ?? 0);
    $noteVal = trim((string)($n['note'] ?? ''));

    if (!$elevId || $noteVal === '') continue;
    if (!in_array($elevId, $validIds)) continue;

    $noteDb   = convertNote($noteVal);
    $coefDb   = floatval($coef);
    $baremeDb = intval($bareme);

    $sql = "INSERT INTO {$prefixe}notes(elev_id,prof_id,code_mat,coef,date,sujet,note,id_classe,id_groupe,typenote,noteexam,notationsur,notevisiblele)"
         . " VALUES('$elevId','$idprof','$idmatiereSafe','$coefDb','$dateDevoir','$sujetSafe','$noteDb','$idclasseSafe','0','','','$baremeDb','neant')";

    $res = $cnx->query($sql);
    if (DB::isError($res)) {
        Pgclose();
        echo json_encode(["ok" => false, "err" => $res->getMessage()]);
        exit;
    }
    $nbNotes++;
}

// Créer l'entrée dans le cahier de textes si un texte de devoir est fourni
$devoirCree = false;
if ($texte) {
    create_devoirscolaire(
        $idclasseSafe, $idmatiereSafe, $dateNow, $dateDevoir,
        $texteSafe, '0', '0', $idprof, $tempsSafe
    );
    $devoirCree = true;
}

Pgclose();
echo json_encode(["ok" => true, "nb_notes" => $nbNotes, "devoir" => $devoirCree]);
?>
