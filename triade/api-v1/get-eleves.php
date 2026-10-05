<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

if (empty($_SESSION['api_access']) || $_SESSION['api_access'] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, aucune session API active"]);
    exit;
}

include_once('../common/config.inc.php');
include_once('../common/config2.inc.php');
include_once('../librairie_php/db_triade.php');

$prefixe = PREFIXE;
$cnx     = cnx();

$sql  = "SELECT * FROM {$prefixe}eleves";
$curs = execSql($sql);

$data = [];
$champs_sensibles = ['passwd', 'passwd_eleve', 'passwd_parent_2', 'mdp_moodle'];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    foreach ($champs_sensibles as $champ) {
        unset($ligne[$champ]);
    }
    $data[] = $ligne;
}

if (empty($data)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucun élève trouvé"]);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération liste élèves (".count($data)." résultats)");

echo json_encode([
    "status" => "success",
    "data"   => $data
], JSON_PRETTY_PRINT);

?>
