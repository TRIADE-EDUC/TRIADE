<?php
session_start();
/**
 *  get-notes-v2.php — API mobile notes (version 2)
 *
 *  GET ?annee=YYYY&mois=MM   → notes du mois, groupées par matière
 *  GET ?annee_scolaire=XXXX  → toutes les notes de l'année scolaire XXXX (ex: 2024-2025)
 *                              Si absent, l'année scolaire courante de la classe est utilisée.
 *
 *  Réponse :
 *  {
 *    "nombre": 5,
 *    "mode": "mois" | "annee_scolaire",
 *    "periode": "Mai 2025" | "2024-2025",
 *    "matieres": [
 *      {
 *        "code": "MATHS",
 *        "nom": "Mathématiques",
 *        "moyenne": "14.50",
 *        "notes": [
 *          { "date", "sujet", "coefficient", "note", "bareme",
 *            "moyenne", "notemin", "notemax" }
 *        ]
 *      }
 *    ]
 *  }
 */

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/recupnoteperiode.php");
include_once("../librairie_php/timezone.php");

$prefixe = PREFIXE;

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, vous n'êtes pas connecté"]);
    die();
}

$cnx      = cnx();
$idpers   = $_SESSION["id_pers"];
$idclasse = $_SESSION["idClasse"];

// ── Détermination du mode et filtres SQL ────────────────────────────────────

$moisNoms = ['', 'Janvier','Février','Mars','Avril','Mai','Juin',
             'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

// L'année scolaire est toujours nécessaire pour lister les matières de la classe
$anneeScolaire = anneeScolaireViaIdClasse($idclasse);

if (isset($_GET['annee']) && isset($_GET['mois'])) {
    // ── Mode mois ────────────────────────────────────────────────────────────
    $annee = intval($_GET['annee']);
    $mois  = intval($_GET['mois']);

    if ($annee < 2000 || $annee > 2100 || $mois < 1 || $mois > 12) {
        http_response_code(400);
        echo json_encode(["code" => "400", "message" => "Paramètres invalides"]);
        exit;
    }

    $moisPad   = str_pad($mois, 2, '0', STR_PAD_LEFT);
    $whereDate = "AND DATE_FORMAT(date,'%m') = '$moisPad' AND DATE_FORMAT(date,'%Y') = '$annee'";
    $mode      = "mois";
    $periode   = $moisNoms[$mois] . ' ' . $annee;

} else {
    // ── Mode année scolaire ──────────────────────────────────────────────────
    if (isset($_GET['annee_scolaire']) && preg_match('/^\d{4}-\d{4}$/', $_GET['annee_scolaire'])) {
        $anneeScolaire = $_GET['annee_scolaire'];
    }

    // "2024-2025" → debut = 2024-09-01, fin = 2025-08-31
    $parts      = explode('-', $anneeScolaire);
    $anneeDebut = intval($parts[0]);
    $anneeFin   = intval($parts[1]);
    $dateDebut  = $anneeDebut . '-09-01';
    $dateFin    = $anneeFin   . '-08-31';

    $whereDate  = "AND date >= '$dateDebut' AND date <= '$dateFin'";
    $mode       = "annee_scolaire";
    $periode    = $anneeScolaire;
}

// ── Requête notes ────────────────────────────────────────────────────────────

$sql = <<<SQL
SELECT
    m.code_mat,
    n.coef,
    n.sujet,
    DATE_FORMAT(n.date, '%d-%m-%Y'),
    TRUNCATE(n.note, 2),
    n.typenote,
    n.notationsur,
    n.notevisiblele,
    n.noteexam,
    n.prof_id,
    n.id_groupe,
    n.id_classe
FROM
    {$prefixe}notes n
    INNER JOIN {$prefixe}matieres m ON m.code_mat = n.code_mat
WHERE
    n.elev_id = '$idpers'
    $whereDate
ORDER BY
    m.code_mat, n.date DESC
SQL;

$curs = execSql($sql);
$data = chargeMat($curs);

// ── Construction réponse groupée par matière ─────────────────────────────────

$matiereMap = []; // code_mat → [ nom, notes[] ]

for ($i = 0; $i < count($data); $i++) {
    $codemat     = $data[$i][0];
    $coef        = $data[$i][1];
    $sujet       = $data[$i][2];
    $date        = $data[$i][3];
    $note        = $data[$i][4];
    $typenote    = $data[$i][5];
    $notationsur = $data[$i][6];
    // $notevisiblele = $data[$i][7]; // non exposé
    $noteexam    = $data[$i][8];
    $idprof      = $data[$i][9];
    $idgroupe    = $data[$i][10];
    $idclassenote = $data[$i][11];

    // Nom de la matière
    if (!isset($matiereMap[$codemat])) {
        $matiereMap[$codemat] = [
            'nom'   => chercheMatiereNom($codemat),
            'notes' => [],
        ];
    }

    // Moyenne de classe pour ce devoir
    $moyResult = moyenneDevoir($codemat, $date, $idprof, $sujet, $coef, $noteexam, $idgroupe, $idclassenote);
    $moyClasse = is_array($moyResult) ? $moyResult['moy'] : '';
    $notemin   = is_array($moyResult) ? $moyResult['min'] : '';
    $notemax   = is_array($moyResult) ? $moyResult['max'] : '';

    // Normalisation des notes spéciales
    $note = preg_replace('/-1/', 'abs',  $note);
    $note = preg_replace('/-2/', 'disp', $note);
    $note = preg_replace('/-3/', ' ',    $note);
    $note = preg_replace('/-4/', 'DNN',  $note);
    $note = preg_replace('/-5/', 'DNR',  $note);
    $note = preg_replace('/-6/', 'VAL',  $note);
    $note = preg_replace('/.00$/', '',   $note);

    $matiereMap[$codemat]['notes'][] = [
        "date"        => $date,
        "sujet"       => $sujet,
        "coefficient" => $coef,
        "note"        => $note,
        "bareme"      => $notationsur,
        "moyenne"     => $moyClasse,
        "notemin"     => $notemin,
        "notemax"     => $notemax,
    ];
}

// ── Ajout des matières de la classe sans notes ───────────────────────────────

$ordreMatList = ordre_matiere($idclasse, $anneeScolaire);
for ($i = 0; $i < count($ordreMatList); $i++) {
    $codemat = $ordreMatList[$i][0];
    $ordre   = intval($ordreMatList[$i][2]);
    if (!isset($matiereMap[$codemat])) {
        $matiereMap[$codemat] = [
            'nom'   => chercheMatiereNom($codemat),
            'notes' => [],
            'ordre' => $ordre,
        ];
    } else {
        $matiereMap[$codemat]['ordre'] = $ordre;
    }
}

// ── Calcul moyenne par matière ────────────────────────────────────────────────

function calcMoyenneMatiere(array $notes): ?string {
    $sum = 0;
    $coefSum = 0;
    foreach ($notes as $n) {
        $val = floatval($n['note']);
        if (!is_numeric($n['note']) || $val < 0) continue;
        $coef = floatval($n['coefficient']) ?: 1;
        $bar  = floatval($n['bareme']) ?: 20;
        $sum     += ($val / $bar) * 20 * $coef;
        $coefSum += $coef;
    }
    if ($coefSum == 0) return null;
    return number_format($sum / $coefSum, 2, '.', '');
}

// ── Assemblage réponse ────────────────────────────────────────────────────────

$matieres = [];
$totalNotes = 0;

foreach ($matiereMap as $code => $mat) {
    $totalNotes += count($mat['notes']);
    $matieres[] = [
        "code"    => $code,
        "nom"     => $mat['nom'],
        "moyenne" => calcMoyenneMatiere($mat['notes']),
        "notes"   => $mat['notes'],
    ];
}

// Trier par ordre d'affichage de la classe (puis par nom si pas dans la liste de la classe)
usort($matieres, function($a, $b) {
    $oa = isset($a['ordre']) ? $a['ordre'] : 9999;
    $ob = isset($b['ordre']) ? $b['ordre'] : 9999;
    if ($oa !== $ob) return $oa - $ob;
    return strcmp($a['nom'], $b['nom']);
});

// Retirer le champ interne 'ordre' de la réponse
foreach ($matieres as &$mat) unset($mat['ordre']);

$response = [
    "nombre"  => $totalNotes,
    "mode"    => $mode,
    "periode" => $periode,
    "matieres" => $matieres,
];

Pgclose();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
