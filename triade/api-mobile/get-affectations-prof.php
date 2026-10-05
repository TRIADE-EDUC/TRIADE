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

// Année scolaire : paramètre GET ou la plus récente dans les affectations
if (isset($_GET['annee_scolaire']) && preg_match('/^\d{4}-\d{4}$/', $_GET['annee_scolaire'])) {
    $anneeScolaire = $_GET['annee_scolaire'];
} else {
    $res = execSql("SELECT MAX(annee_scolaire) FROM {$prefixe}affectations WHERE code_prof='$idprof'");
    $d   = chargeMat($res);
    $anneeScolaire = $d[0][0] ?? '';
}

$sql = "SELECT
    a.code_classe,
    trim(c.libelle),
    a.code_matiere,
    CONCAT(trim(m.libelle), ' ', trim(m.sous_matiere), ' ', trim(IFNULL(a.langue,''))),
    a.code_groupe,
    trim(g.libelle)
FROM {$prefixe}affectations a
INNER JOIN {$prefixe}matieres m ON m.code_mat  = a.code_matiere
INNER JOIN {$prefixe}classes  c ON c.code_class = a.code_classe
INNER JOIN {$prefixe}groupes  g ON g.group_id   = a.code_groupe
WHERE a.code_prof = '$idprof'
AND (a.visubull = '1' OR a.visubullbtsblanc = '1')
AND a.annee_scolaire = '$anneeScolaire'
GROUP BY a.code_matiere, a.code_classe, a.code_groupe
ORDER BY c.libelle, m.libelle";

$res  = execSql($sql);
$data = chargeMat($res);

$affectations = [];
for ($i = 0; $i < count($data); $i++) {
    // Supprimer le suffixe " 0" ou " 0 0" généré par sous_matiere/langue vides
    $libmat = trim(preg_replace('/(\s+0)+\s*$/', '', $data[$i][3]));
    $affectations[] = [
        "idclasse"   => $data[$i][0],
        "libclasse"  => trim($data[$i][1]),
        "idmatiere"  => $data[$i][2],
        "libmatiere" => $libmat,
        "idgroupe"   => $data[$i][4],
        "libgroupe"  => trim($data[$i][5]),
    ];
}

Pgclose();
echo json_encode([
    "annee_scolaire" => $anneeScolaire,
    "affectations"   => $affectations,
], JSON_UNESCAPED_UNICODE);
?>
