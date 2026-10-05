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

/* Forme URL de la requête : GET /api-v1/get-matieres.php
   Paramètres optionnels :
     - id  : ID d'une matière précise
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
    $where .= " AND code_mat = " . intval($_GET['id']);
}

$sql  = "SELECT * FROM {$prefixe}matieres $where ORDER BY libelle ASC";
$curs = execSql($sql);

$data = [];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    $data[] = $ligne;
}

if (empty($data)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune matière trouvée"]);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération matières (".count($data)." résultats)");

echo json_encode([
    "status" => "success",
    "nombre" => count($data),
    "data"   => $data
], JSON_PRETTY_PRINT);
?>
