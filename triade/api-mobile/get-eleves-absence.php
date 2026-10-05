<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
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

$cnx     = cnx();
$prefixe = PREFIXE;
$idprof  = intval($_SESSION["id_pers"]);

$idclasse  = isset($_GET['idclasse'])  ? trim($_GET['idclasse'])  : '';
$idmatiere = isset($_GET['idmatiere']) ? trim($_GET['idmatiere']) : '';
$idgroupe  = isset($_GET['idgroupe'])  ? trim($_GET['idgroupe'])  : '0';

if (!$idclasse) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre idclasse requis"]);
    exit;
}

$idclasseSafe  = addslashes($idclasse);
$idmatiereSafe = addslashes($idmatiere);

if ($_SESSION["membre"] === 'menuprof') {
    // Vérification : le prof enseigne cette matière dans cette classe
    $autorise = false;
    if ($idmatiere) {
        $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe' AND code_matiere='$idmatiereSafe'"));
        $autorise = intval($chk[0][0]) > 0;
    }
    // Fallback : le prof enseigne au moins une matière dans cette classe
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

// Libellés
$libclasse = trim(chargeMat(execSql("SELECT libelle FROM {$prefixe}classes WHERE code_class='$idclasseSafe'"))[0][0] ?? $idclasse);
$libmatRes = chargeMat(execSql("SELECT CONCAT(trim(libelle),' ',trim(IFNULL(sous_matiere,''))) FROM {$prefixe}matieres WHERE code_mat='$idmatiereSafe'"));
$libmatiere = preg_replace('/(\s+0)+\s*$/', '', trim($libmatRes[0][0] ?? $idmatiere));

// ── Récupérer la liste des élèves ─────────────────────────────────────────────
$idg = ($idgroupe && $idgroupe !== '0') ? addslashes($idgroupe) : '';
if ($idg) {
    $inData = chargeMat(execSql("SELECT liste_elev FROM {$prefixe}groupes WHERE group_id='$idg'"));
    $inList = trim($inData[0][0] ?? '', ',');
    if (!$inList) {
        Pgclose();
        echo json_encode(["eleves" => [], "creneaux" => [], "seanceEdt" => null,
            "date" => dateDMY(), "nomclasse" => $libclasse, "nommatiere" => $libmatiere]);
        exit;
    }
    $sqlEleves = "SELECT elev_id, CONCAT(upper(trim(nom)), ' ', trim(prenom)), compte_inactif
                  FROM {$prefixe}eleves WHERE elev_id IN ($inList) ORDER BY 2";
} else {
    $sqlEleves = "SELECT elev_id, CONCAT(upper(trim(nom)), ' ', trim(prenom)), compte_inactif
                  FROM {$prefixe}eleves WHERE classe='$idclasseSafe' ORDER BY 2";
}

$elevesData = chargeMat(execSql($sqlEleves));

$eleves = [];
for ($i = 0; $i < count($elevesData); $i++) {
    if ($elevesData[$i][2] == '1') continue; // compte inactif
    $elevId = $elevesData[$i][0];

    // Vérifier abs/retard existants aujourd'hui
    $dejaAbs    = count(dejaabs($elevId)) > 0 || count(verifsiabs($elevId)) > 0;
    $dejaRetard = count(verifsiretard($elevId)) > 0;

    $eleves[] = [
        "id"         => "$elevId",
        "nom"        => $elevesData[$i][1],
        "dejaAbs"    => $dejaAbs,
        "dejaRetard" => $dejaRetard,
    ];
}

// ── Créneaux configurés ───────────────────────────────────────────────────────
$creneauxData = affCreneaux();
$creneaux = [];
for ($i = 0; $i < count($creneauxData); $i++) {
    $lib = trim($creneauxData[$i][0]);
    $dep = $creneauxData[$i][1];
    $fin = $creneauxData[$i][2];
    $creneaux[] = [
        "value" => "$lib#$dep#$fin",
        "label" => "$lib : " . timeForm($dep) . " - " . timeForm($fin),
    ];
}

// ── Séance EDT actuelle (disponible pour les enseignants uniquement) ─────────
$seanceEdt = null;
if ($_SESSION["membre"] === 'menuprof') {
    $dateNow  = dateDMY2();
    $heureNow = dateHIS();
    $dataseance = recupInfoSeance2($dateNow, $heureNow, $idprof, $idmatiere, $idclasse, $idg ?: '0');
    if (count($dataseance) > 0) {
        $hDeb        = $dataseance[0][2];
        $dureeSeance = $dataseance[0][3];
        $sommeSec    = conv_en_seconde($hDeb) + conv_en_seconde($dureeSeance);
        $hFinRaw     = calcul_hours($sommeSec);
        $seanceEdt   = [
            "value" => "EDT#$hDeb#$hFinRaw",
            "label" => "EDT : " . timeForm($hDeb) . " - " . timeForm($hFinRaw),
        ];
    }
}

Pgclose();
echo json_encode([
    "eleves"     => $eleves,
    "creneaux"   => $creneaux,
    "seanceEdt"  => $seanceEdt,
    "date"       => dateDMY(),
    "nomclasse"  => $libclasse,
    "nommatiere" => $libmatiere,
], JSON_UNESCAPED_UNICODE);
?>
