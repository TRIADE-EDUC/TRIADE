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

if ($_SESSION["membre"] !== 'menuprof') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès réservé aux enseignants"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idprof  = intval($_SESSION["id_pers"]);

$idclasse  = isset($_GET['idclasse'])  ? trim($_GET['idclasse'])  : '';
$idmatiere = isset($_GET['idmatiere']) ? trim($_GET['idmatiere']) : '';
$idgroupe  = isset($_GET['idgroupe'])  ? trim($_GET['idgroupe'])  : '0';
$trimestre = isset($_GET['trimestre']) ? trim($_GET['trimestre']) : '';

if (!$idclasse || !$idmatiere) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres idclasse et idmatiere requis"]);
    exit;
}

$idclasseSafe  = addslashes($idclasse);
$idmatiereSafe = addslashes($idmatiere);

// Vérifier que le prof est affecté à cette classe/matière
$chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe' AND code_matiere='$idmatiereSafe'"));
if (intval($chk[0][0]) == 0) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé à cette classe/matière"]);
    exit;
}

$anneeScolaire = anneeScolaireViaIdClasse($idclasse);
if (!$anneeScolaire) {
    $y = intval(date('Y'));
    $m = intval(date('m'));
    $anneeScolaire = ($m >= 9) ? "$y-" . ($y + 1) : ($y - 1) . "-$y";
}

// ── Périodes disponibles pour cette classe ────────────────────────────────────
$periodesData = recherche_trimestre_classe_anneescolaire($anneeScolaire, $idclasse);
$periodes = [];
for ($i = 0; $i < count($periodesData); $i++) {
    $periodes[] = [
        "id"        => $periodesData[$i][2],   // ex: "trimestre1"
        "date_debut"=> $periodesData[$i][0],
        "date_fin"  => $periodesData[$i][1],
    ];
}

// Période courante si non précisée
if (!$trimestre) {
    $trimestre = recherche_trimestre_en_cours_via_classe($idclasse, $anneeScolaire);
    if (!$trimestre && count($periodes) > 0) $trimestre = $periodes[0]['id'];
}

// Plage de dates pour la période sélectionnée
$dateDebut = '';
$dateFin   = '';
if ($trimestre) {
    $intervalData = recherche_intervalle_trimestre_via_classe($trimestre, $idclasse, $anneeScolaire);
    if (count($intervalData) > 0) {
        $dateDebut = $intervalData[0][0];
        $dateFin   = $intervalData[0][1];
    }
}
// Fallback : toute l'année scolaire
if (!$dateDebut) {
    $parts     = explode('-', $anneeScolaire);
    $dateDebut = intval($parts[0]) . '-09-01';
    $dateFin   = intval($parts[1] ?? ($parts[0] + 1)) . '-08-31';
}

// ── Récupérer la liste des élèves ─────────────────────────────────────────────
$idg = ($idgroupe && $idgroupe !== '0') ? addslashes($idgroupe) : '';
if ($idg) {
    $inData = chargeMat(execSql("SELECT liste_elev FROM {$prefixe}groupes WHERE group_id='$idg'"));
    $inList = trim($inData[0][0] ?? '', ',');
    if ($inList && !preg_match('/^[\d,]+$/', $inList)) {
        Pgclose();
        http_response_code(400);
        echo json_encode(["code" => "400", "message" => "Format liste élèves invalide"]);
        exit;
    }
    if (!$inList) {
        Pgclose();
        echo json_encode(["annee_scolaire" => $anneeScolaire, "trimestre" => $trimestre, "periodes" => $periodes, "eleves" => []]);
        exit;
    }
    $sqlEleves = "SELECT elev_id, CONCAT(upper(trim(nom)), ' ', trim(prenom))
                  FROM {$prefixe}eleves WHERE elev_id IN ($inList) ORDER BY 2";
} else {
    $sqlEleves = "SELECT s.elev_id, s.np FROM (
        SELECT elev_id, CONCAT(upper(trim(nom)),' ',trim(prenom)) AS np, classe
        FROM {$prefixe}eleves, {$prefixe}classes
        WHERE classe='$idclasseSafe' AND code_class=classe AND annee_scolaire='$anneeScolaire'
        UNION ALL
        SELECT e.elev_id, CONCAT(upper(trim(e.nom)),' ',trim(e.prenom)), e.classe
        FROM {$prefixe}eleves e, {$prefixe}classes c, {$prefixe}eleves_histo h
        WHERE h.idclasse='$idclasseSafe' AND e.elev_id=h.ideleve
          AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'
    ) s ORDER BY 2";
}

$eleves = chargeMat(execSql($sqlEleves));

// ── Notes par élève ───────────────────────────────────────────────────────────
$result = [];
for ($i = 0; $i < count($eleves); $i++) {
    $elevId = $eleves[$i][0];

    $sqlNotes = "SELECT coef, sujet, DATE_FORMAT(date,'%d-%m-%Y'), TRUNCATE(note,2), notationsur, noteexam
                 FROM {$prefixe}notes
                 WHERE elev_id='$elevId'
                   AND code_mat='$idmatiereSafe'
                   AND prof_id='$idprof'
                   AND date >= '$dateDebut' AND date <= '$dateFin'
                 ORDER BY date";

    $notesData = chargeMat(execSql($sqlNotes));

    $notes = [];
    $total = 0; $coefTotal = 0;
    for ($j = 0; $j < count($notesData); $j++) {
        $note = $notesData[$j][3];
        if ($note == -1)      $noteAff = 'abs';
        elseif ($note == -2)  $noteAff = 'disp';
        elseif ($note == -3)  $noteAff = ' ';
        elseif ($note == -4)  $noteAff = 'DNN';
        elseif ($note == -5)  $noteAff = 'DNR';
        elseif ($note == -6)  $noteAff = 'VAL';
        elseif ($note == -7)  $noteAff = 'NVAL';
        else                  $noteAff = preg_replace('/\.00$/', '', strval($note));

        $notes[] = [
            "date"   => $notesData[$j][2],
            "sujet"  => $notesData[$j][1],
            "coef"   => $notesData[$j][0],
            "note"   => $noteAff,
            "bareme" => $notesData[$j][4],
            "exam"   => $notesData[$j][5] ?? '',
        ];

        if (is_numeric($note) && floatval($note) >= 0) {
            $coef = floatval($notesData[$j][0]) ?: 1;
            $bar  = floatval($notesData[$j][4]) ?: 20;
            $total     += (floatval($note) / $bar) * 20 * $coef;
            $coefTotal += $coef;
        }
    }

    $result[] = [
        "id"      => "$elevId",
        "nom"     => $eleves[$i][1],
        "notes"   => $notes,
        "moyenne" => ($coefTotal > 0) ? number_format($total / $coefTotal, 2, '.', '') : null,
    ];
}

Pgclose();
echo json_encode([
    "annee_scolaire" => $anneeScolaire,
    "trimestre"      => $trimestre,
    "periodes"       => $periodes,
    "idclasse"       => $idclasse,
    "idmatiere"      => $idmatiere,
    "eleves"         => $result,
], JSON_UNESCAPED_UNICODE);
?>
