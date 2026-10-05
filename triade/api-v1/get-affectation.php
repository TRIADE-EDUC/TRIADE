<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['api_access']) || $_SESSION['api_access'] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, aucune session API active"]);
    exit;
}

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');

/* Forme URL de la requête : GET /api-v1/get-affectation.php
   Paramètres optionnels :
     - idprof         : filtre par ID professeur (code_prof)
     - idclasse       : filtre par ID classe (code_classe)
     - idmatiere      : filtre par ID matière (code_matiere)
     - annee_scolaire : filtre par année scolaire (ex: 2025-2026)
     - trim           : filtre par trimestre (ex: T1, T2, T3, tous)
*/

$prefixe = PREFIXE;
$cnx     = cnx();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée"]);
    exit;
}

$where = "WHERE 1=1";

if (!empty($_GET['idprof'])) {
    $where .= " AND a.code_prof = " . intval($_GET['idprof']);
}
if (!empty($_GET['idclasse'])) {
    $where .= " AND a.code_classe = " . intval($_GET['idclasse']);
}
if (!empty($_GET['idmatiere'])) {
    $where .= " AND a.code_matiere = " . intval($_GET['idmatiere']);
}
if (!empty($_GET['annee_scolaire'])) {
    $where .= " AND a.annee_scolaire = '" . addslashes($_GET['annee_scolaire']) . "'";
}
if (!empty($_GET['trim'])) {
    $where .= " AND a.trim = '" . addslashes($_GET['trim']) . "'";
}

$sql = "SELECT
            a.code_prof,
            a.code_classe,
            a.code_matiere,
            a.annee_scolaire,
            a.trim,
            a.coef,
            a.nb_heure,
            a.ordre_affichage,
            a.code_groupe,
            a.langue,
            a.visubull,
            per.nom          AS prof_nom,
            per.prenom       AS prof_prenom,
            per.type_pers    AS prof_type,
            cl.libelle       AS classe_libelle,
            cl.niveau        AS classe_niveau,
            mat.libelle      AS matiere_libelle
        FROM {$prefixe}affectations a
        LEFT JOIN {$prefixe}personnel per ON per.pers_id = a.code_prof
        LEFT JOIN {$prefixe}classes   cl  ON cl.code_class = a.code_classe
        LEFT JOIN {$prefixe}matieres  mat ON mat.code_mat = a.code_matiere
        $where
        ORDER BY a.annee_scolaire DESC, cl.libelle ASC, mat.libelle ASC, per.nom ASC";

$curs = execSql($sql);

$data = [];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    $data[] = $ligne;
}

if (empty($data)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune affectation trouvée"]);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération affectations (".count($data)." résultats)");

echo json_encode([
    "status" => "success",
    "nombre" => count($data),
    "data"   => $data
], JSON_PRETTY_PRINT);
?>
