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

/* Forme URL de la requête : GET /api-v1/get-prof.php
   Paramètres optionnels :
     - type  : filtre sur le type de personnel (ENS, ADM, PER, TUT, MVS...)
     - id    : ID d'un personnel précis
*/

$prefixe = PREFIXE;
$cnx     = cnx();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée"]);
    exit;
}

$where = "WHERE offline = 0";

if (!empty($_GET['id'])) {
    $where .= " AND pers_id = " . intval($_GET['id']);
}
if (!empty($_GET['type'])) {
    $where .= " AND type_pers = '" . addslashes($_GET['type']) . "'";
}

$sql  = "SELECT * FROM {$prefixe}personnel $where ORDER BY nom ASC, prenom ASC";
$curs = execSql($sql);

$champs_sensibles = ['mdp', 'keytriadepers', 'googleAuthen', 'identifiant'];

$data = [];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    foreach ($champs_sensibles as $champ) {
        unset($ligne[$champ]);
    }
    $data[] = $ligne;
}

if (empty($data)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucun personnel trouvé"]);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération personnel (".count($data)." résultats)");

echo json_encode([
    "status" => "success",
    "nombre" => count($data),
    "data"   => $data
], JSON_PRETTY_PRINT);
?>
