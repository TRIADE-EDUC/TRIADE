<?php session_start();

include_once("../../common/config.inc.php");
include_once("../../common/config2.inc.php");
include_once("../../librairie_php/db_triade.php");

$prefixe = PREFIXE;
$cnx = cnx();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    Pgclose(); die();
}

$membre = $_SESSION["membre"] ?? "";

// Préfixe de constante selon le type de membre
$prefixe_const_map = [
    "menueleve"     => "ELEVE",
    "menuparent"    => "PARENT",
    "menuprof"      => "PROF",
    "menupersonnel" => "PERSONNEL",
    "menututeur"    => "TUTEUR",
];
$pfx = $prefixe_const_map[$membre] ?? "";

// Catégories de destinataires à vérifier (constante d'autorisation → type BDD → libellé)
$categories = [
    ["suffix" => "ENVOIDIREC",    "type" => "ADM", "label" => "Direction"],
    ["suffix" => "ENVOISCOLAIRE", "type" => "MVS", "label" => "Vie Scolaire"],
    ["suffix" => "ENVOIPROF",     "type" => "ENS", "label" => "Enseignants"],
    ["suffix" => "ENVOIPERSONNEL","type" => "PER", "label" => "Personnel"],
    ["suffix" => "ENVOITUTEUR",   "type" => "TUT", "label" => "Tuteurs"],
];

function getPersonnesByType($type, $prefixe) {
    global $cnx;
    $sql = "SELECT pers_id, nom, prenom FROM {$prefixe}personnel WHERE type_pers='$type' AND offline='0' ORDER BY nom";
    $res = execSql($sql);
    if ($res === null || DB::isError($res)) return [];
    $data = chargeMat($res);
    $list = [];
    foreach ($data as $row) {
        $list[] = [
            "id"   => intval($row[0]),
            "nom"  => strtoupper(trim($row[1])) . " " . ucfirst(strtolower(trim($row[2]))),
            "type" => $type,
        ];
    }
    return $list;
}

$groupes = [];

if ($membre === "menuadmin" || $membre === "menuscolaire") {
    // Admins et vie scolaire : accès complet à tous les types
    $all_types = [
        ["type" => "ADM", "label" => "Direction"],
        ["type" => "MVS", "label" => "Vie Scolaire"],
        ["type" => "ENS", "label" => "Enseignants"],
        ["type" => "PER", "label" => "Personnel"],
    ];
    foreach ($all_types as $t) {
        $personnes = getPersonnesByType($t["type"], $prefixe);
        if (!empty($personnes)) {
            $groupes[] = ["label" => $t["label"], "type_dest" => $t["type"], "personnes" => $personnes];
        }
    }
} else {
    // Autres membres : selon les constantes d'autorisation
    foreach ($categories as $cat) {
        $const_name = $pfx . $cat["suffix"];
        if (!defined($const_name) || constant($const_name) !== "oui") continue;
        $personnes = getPersonnesByType($cat["type"], $prefixe);
        if (!empty($personnes)) {
            $groupes[] = ["label" => $cat["label"], "type_dest" => $cat["type"], "personnes" => $personnes];
        }
    }
}

echo json_encode(["groupes" => $groupes], JSON_UNESCAPED_UNICODE);
Pgclose();
?>
