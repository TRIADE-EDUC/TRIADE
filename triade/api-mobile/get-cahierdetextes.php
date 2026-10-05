<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../librairie_php/langue-text-fr.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, vous n'êtes pas connecté"]);
    die();
}

$cnx     = cnx();
$isProf  = isset($_SESSION["membre"]) && $_SESSION["membre"] === 'menuprof';
$idprof  = $isProf ? intval($_SESSION["id_pers"]) : 0;
$mode    = $_GET["mode"] ?? "devoirs";

if ($isProf) {
    $idclasse  = isset($_GET['idclasse'])  ? trim($_GET['idclasse'])  : '';
    $idmatiere = isset($_GET['idmatiere']) ? trim($_GET['idmatiere']) : '';
    if (!$idclasse || !$idmatiere) {
        http_response_code(400);
        echo json_encode(["code" => "400", "message" => "Paramètres idclasse et idmatiere requis"]);
        exit;
    }
    $idclasseSafe  = addslashes($idclasse);
    $idmatiereSafe = addslashes($idmatiere);
    $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe' AND code_matiere='$idmatiereSafe'"));
    if (intval($chk[0][0]) == 0) {
        http_response_code(403);
        echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
        exit;
    }
} else {
    $idclasse      = $_SESSION["idClasse"];
    $idmatiere     = null;
    $idclasseSafe  = addslashes($idclasse);
    $idmatiereSafe = null;
}

/* ── MODE CALENDRIER ── indicateurs par jour pour un mois entier ─────── */
if ($mode === "calendrier") {
    $annee = (isset($_GET["annee"]) && ctype_digit($_GET["annee"])) ? (int)$_GET["annee"] : (int)date('Y');
    $mois  = (isset($_GET["mois"])  && ctype_digit($_GET["mois"]))  ? (int)$_GET["mois"]  : (int)date('n');
    $moisStr = sprintf('%02d', $mois);
    $debut   = "$annee-$moisStr-01";
    $fin     = date('Y-m-d', strtotime("$debut +1 month -1 day"));

    $matiereFilter = $isProf ? "AND matiere_id='$idmatiereSafe'" : "";
    $jours = [];

    $sqlC = "SELECT date_contenu,
              MAX(CASE WHEN contenu  IS NOT NULL AND contenu  <> '' THEN 1 ELSE 0 END) AS has_cours,
              MAX(CASE WHEN objectif IS NOT NULL AND objectif <> '' THEN 1 ELSE 0 END) AS has_objectif
             FROM {$prefixe}cahiertexte
             WHERE id_class_or_grp='$idclasseSafe'
               $matiereFilter
               AND date_contenu BETWEEN '$debut' AND '$fin'
             GROUP BY date_contenu";
    $dataC = chargeMat(execSql($sqlC));
    for ($i = 0; $i < countTriade($dataC); $i++) {
        $d = $dataC[$i][0];
        if (!isset($jours[$d])) $jours[$d] = [];
        $jours[$d]["cours"]    = (bool)(int)$dataC[$i][1];
        $jours[$d]["objectif"] = (bool)(int)$dataC[$i][2];
    }

    $sqlD = "SELECT date_devoir, COUNT(*) AS nb
             FROM {$prefixe}devoir_scolaire
             WHERE id_class_or_grp='$idclasseSafe'
               $matiereFilter
               AND date_devoir BETWEEN '$debut' AND '$fin'
               AND texte IS NOT NULL AND texte <> ''
             GROUP BY date_devoir";
    $dataD = chargeMat(execSql($sqlD));
    for ($i = 0; $i < countTriade($dataD); $i++) {
        $d = $dataD[$i][0];
        if (!isset($jours[$d])) $jours[$d] = [];
        $jours[$d]["devoirs"] = (int)$dataD[$i][1];
    }

    echo json_encode(["annee" => $annee, "mois" => $mois, "jours" => $jours]);
    Pgclose();
    exit;
}

/* ── MODE JOUR ── tous les contenus d'une journée ───────────────────── */
if ($mode === "jour") {
    $dateIso = (isset($_GET["date"]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["date"]))
               ? $_GET["date"] : date('Y-m-d');
    $dateFr  = dateForm($dateIso);

    $result = [];

    $types = [
        ["cours",    "affcontenuScolaireParent",  "date_contenu"],
        ["objectif", "affobjectifScolaireParent", "date_contenu"],
        ["devoir",   "affdevoirScolaireParent",   "date_devoir"],
    ];

    foreach ($types as [$type, $fn, $ref]) {
        $data = $fn($idclasse, $dateFr, $ref);
        for ($i = 0; $i < countTriade($data); $i++) {
            if ($isProf && $data[$i][1] != $idmatiere) continue;
            $contenu = trim($data[$i][5]);
            if ($contenu === "") continue;
            $matiere = trim(ucwords(chercheMatiereNom($data[$i][1])));
            $prof    = trim(recherche_personne($data[$i][9]));
            if ($prof === "Message Automatique") $prof = "";
            $entry = [
                "type"       => $type,
                "id"         => $data[$i][7],
                "date"       => $dateFr,
                "date_iso"   => $dateIso,
                "matiere"    => $matiere,
                "professeur" => $prof,
                "contenu"    => $contenu,
            ];
            if ($type === "devoir" && isset($data[$i][10]) && trim((string)$data[$i][10]) !== '' && $data[$i][10] !== '00:00:00') {
                $entry["duree"] = timeForm($data[$i][10]);
            }
            $result[] = $entry;
        }
    }

    echo json_encode(["date" => $dateFr, "date_iso" => $dateIso, "nombre" => count($result), "entries" => $result]);
    Pgclose();
    exit;
}

/* ── MODES DEVOIRS / COURS (liste sur plage de dates) ───────────────── */
if (!in_array($mode, ["devoirs", "cours"])) $mode = "devoirs";

$dateRef = (isset($_GET["date"]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["date"]))
           ? $_GET["date"] : date('Y-m-d');

if ($mode === "devoirs") {
    $dateDebut = $dateRef;
    $dateFin   = date('Y-m-d', strtotime($dateRef . ' +13 days'));
} else {
    $dateDebut = date('Y-m-d', strtotime($dateRef . ' -6 days'));
    $dateFin   = $dateRef;
}

$entries = [];
$current = $dateDebut;

while ($current <= $dateFin) {
    $dateFr = dateForm($current);
    $data   = ($mode === "devoirs")
              ? affdevoirScolaireParent($idclasse, $dateFr, "date_devoir")
              : affcontenuScolaireParent($idclasse, $dateFr, "date_contenu");

    for ($i = 0; $i < countTriade($data); $i++) {
        if ($isProf && $data[$i][1] != $idmatiere) continue;
        $contenu = trim($data[$i][5]);
        if ($contenu === "") continue;
        $matiere = trim(ucwords(chercheMatiereNom($data[$i][1])));
        $prof    = trim(recherche_personne($data[$i][9]));
        if ($prof === "Message Automatique") $prof = "";
        $entry = [
            "id"         => $data[$i][7],
            "date"       => $dateFr,
            "date_iso"   => $current,
            "matiere"    => $matiere,
            "professeur" => $prof,
            "contenu"    => $contenu,
        ];
        if ($mode === "devoirs" && isset($data[$i][10]) && trim((string)$data[$i][10]) !== '' && $data[$i][10] !== '00:00:00') {
            $entry["duree"] = timeForm($data[$i][10]);
        }
        $entries[] = $entry;
    }
    $current = date('Y-m-d', strtotime($current . ' +1 day'));
}

echo json_encode([
    "mode"       => $mode,
    "date_debut" => dateForm($dateDebut),
    "date_fin"   => dateForm($dateFin),
    "nombre"     => count($entries),
    "entries"    => $entries,
]);

Pgclose();
?>
