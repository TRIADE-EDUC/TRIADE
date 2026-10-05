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

/* Forme URL de la requête : GET /api-v1/get-UE.php
   Paramètres optionnels :
     - id             : code_ue d'une UE précise
     - idclasse       : filtre par ID classe
     - annee_scolaire : filtre par année scolaire (ex: 2025-2026)
     - semestre       : filtre par numéro de semestre (ex: 1, 2)
*/

$prefixe = PREFIXE;
$cnx     = cnx();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée"]);
    exit;
}

$where = "WHERE 1=1";

if (!empty($_GET['id'])) {
    $where .= " AND u.code_ue = " . intval($_GET['id']);
}
if (!empty($_GET['idclasse'])) {
    $where .= " AND u.code_classe = " . intval($_GET['idclasse']);
}
if (!empty($_GET['annee_scolaire'])) {
    $where .= " AND u.annee_scolaire = '" . addslashes($_GET['annee_scolaire']) . "'";
}
if (!empty($_GET['semestre'])) {
    $where .= " AND u.semestre = " . intval($_GET['semestre']);
}

$sql = "SELECT
            u.code_ue,
            u.num_ue,
            u.nom_ue,
            u.nom_ue_en,
            u.matricule_ue,
            u.semestre,
            u.coef_ue,
            u.ects_ue,
            u.annee_scolaire,
            u.code_classe,
            cl.libelle       AS classe_libelle,
            cl.niveau        AS classe_niveau,
            u.idpers_profp,
            per.nom          AS profp_nom,
            per.prenom       AS profp_prenom
        FROM {$prefixe}ue u
        LEFT JOIN {$prefixe}classes   cl  ON cl.code_class = u.code_classe
        LEFT JOIN {$prefixe}personnel per ON per.pers_id   = u.idpers_profp
        $where
        ORDER BY u.annee_scolaire DESC, u.semestre ASC, u.num_ue ASC";

$curs = execSql($sql);

$ues = [];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    $ligne['matieres'] = [];
    $ues[$ligne['code_ue']] = $ligne;
}

if (empty($ues)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune unité d'enseignement trouvée"]);
    exit;
}

$ids = implode(',', array_keys($ues));
$sqlDetail = "SELECT
                d.code_ue_detail,
                d.code_ue,
                d.code_matiere,
                d.code_enseignant,
                d.code_idgroupe,
                mat.libelle  AS matiere_libelle,
                per.nom      AS enseignant_nom,
                per.prenom   AS enseignant_prenom
              FROM {$prefixe}ue_detail d
              LEFT JOIN {$prefixe}matieres  mat ON mat.code_mat  = d.code_matiere
              LEFT JOIN {$prefixe}personnel per ON per.pers_id   = d.code_enseignant
              WHERE d.code_ue IN ($ids)
              ORDER BY mat.libelle ASC";

$cursDetail = execSql($sqlDetail);
while ($detail = $cursDetail->fetchRow(DB_FETCHMODE_ASSOC)) {
    $ues[$detail['code_ue']]['matieres'][] = $detail;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération UE (".count($ues)." résultats)");

echo json_encode([
    "status" => "success",
    "nombre" => count($ues),
    "data"   => array_values($ues)
], JSON_PRETTY_PRINT);
?>
