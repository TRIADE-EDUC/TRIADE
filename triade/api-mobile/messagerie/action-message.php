<?php session_start();

include_once("../../common/config.inc.php");
include_once("../../common/config2.inc.php");
include_once("../../librairie_php/db_triade.php");
include_once("../../librairie_php/timezone.php");

$prefixe = PREFIXE;
$cnx = cnx();

/* action-message.php — POST avec session PHP
 * Paramètres POST : action (marquer-lu|corbeille|restaurer|supprimer), id, source (reception|envoyes)
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, vous n'êtes pas connecté"]);
    Pgclose();
    die();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée"]);
    Pgclose();
    die();
}

if (!isset($_POST["action"]) || !isset($_POST["id"])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres manquants (action, id requis)"]);
    Pgclose();
    die();
}

$membre = $_SESSION["membre"] ?? "";

$type_personne_map = [
    "menuadmin"     => "ADM",
    "menututeur"    => "TUT",
    "menupersonnel" => "PER",
    "menuprof"      => "ENS",
    "menuscolaire"  => "MVS",
    "menuparent"    => "PAR",
    "menueleve"     => "ELE",
];
$type_personne = $type_personne_map[$membre] ?? "ELE";

if ($membre === "menuparent" || $membre === "menueleve") {
    $id_pers = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]);
} else {
    $civ_map = [
        "menuadmin"     => "ADM",
        "menututeur"    => "TUT",
        "menupersonnel" => "PER",
        "menuprof"      => "ENS",
        "menuscolaire"  => "MVS",
    ];
    $civ = $civ_map[$membre] ?? "ADM";
    $id_pers = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], $civ);
}

$action = trim($_POST["action"]);
$id     = intval($_POST["id"]);
$source = isset($_POST["source"]) ? trim($_POST["source"]) : "reception";

$actions_autorisees = ["marquer-lu", "corbeille", "restaurer", "supprimer"];
if (!in_array($action, $actions_autorisees)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Action invalide"]);
    Pgclose();
    die();
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Identifiant de message invalide"]);
    Pgclose();
    die();
}

// $qui="prof" → condition sur emetteur (boîte envoyés), $qui="" → condition sur destinataire
$qui = ($source === "envoyes") ? "prof" : "";

switch ($action) {
    case "marquer-lu":
        lecture_message($id);
        valide_message_lu($id);
        echo json_encode(["code" => "200", "message" => "Message marqué comme lu"]);
        break;

    case "corbeille":
        $nb = corbeille_message($id, $id_pers, $qui);
        if ($nb > 0) {
            echo json_encode(["code" => "200", "message" => "Message déplacé à la corbeille"]);
        } else {
            http_response_code(500);
            echo json_encode(["code" => "500", "message" => "Erreur lors du déplacement à la corbeille"]);
        }
        break;

    case "restaurer":
        $nb = restaurer_message($id, $id_pers, $qui);
        if ($nb > 0) {
            echo json_encode(["code" => "200", "message" => "Message restauré"]);
        } else {
            http_response_code(500);
            echo json_encode(["code" => "500", "message" => "Erreur lors de la restauration"]);
        }
        break;

    case "supprimer":
        if ($source === "envoyes") {
            suppression_message_envoyer($id, $id_pers, "");
        } else {
            suppression_message($id, $id_pers, $qui);
        }
        if ($cnx->affectedRows() > 0) {
            echo json_encode(["code" => "200", "message" => "Message supprimé définitivement"]);
        } else {
            http_response_code(500);
            echo json_encode(["code" => "500", "message" => "Erreur lors de la suppression"]);
        }
        break;
}

Pgclose();
?>
