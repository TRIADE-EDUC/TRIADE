<?php session_start();

include_once("../../common/config.inc.php");
include_once("../../common/config2.inc.php");
include_once("../../librairie_php/db_triade.php");
include_once("../../librairie_php/timezone.php");

$prefixe = PREFIXE;
$cnx = cnx();

/* send-message.php — POST avec session PHP
 * Paramètres POST : dest_id, type_dest, objet, message
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

if (!isset($_POST["dest_id"]) || !isset($_POST["type_dest"]) ||
    !isset($_POST["objet"])   || !isset($_POST["message"])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres manquants (dest_id, type_dest, objet, message requis)"]);
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
    $emetteur = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]);
} else {
    $civ_map = [
        "menuadmin"     => "ADM",
        "menututeur"    => "TUT",
        "menupersonnel" => "PER",
        "menuprof"      => "ENS",
        "menuscolaire"  => "MVS",
    ];
    $civ = $civ_map[$membre] ?? "ADM";
    $emetteur = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], $civ);
}

$dest_id   = intval($_POST["dest_id"]);
$type_dest = preg_replace('/[^A-Z]/', '', strtoupper(trim($_POST["type_dest"])));
$objet     = strip_tags(trim($_POST["objet"]));
$texte     = str_replace(["\r\n", "\r", "\\n"], "\n", trim($_POST["message"]));

$types_autorises = ["ELE", "PAR", "ENS", "ADM", "PER", "MVS", "TUT"];
if (!in_array($type_dest, $types_autorises)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Type de destinataire invalide"]);
    Pgclose();
    die();
}

if ($dest_id <= 0 || empty($objet) || empty($texte)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Destinataire, objet et message sont requis"]);
    Pgclose();
    die();
}

$date   = date("Y-m-d");
$heure  = date("H:i:s");
$number = md5(uniqid(rand()));

$texte_crypte = Crypte($texte, $number);

$nb = envoi_messagerie($emetteur, $dest_id, $objet, $texte_crypte, $date, $heure,
                        $type_personne, $type_dest, $number);

if ($nb > 0) {
    echo json_encode(["code" => "200", "message" => "Message envoyé avec succès"]);
} else {
    http_response_code(500);
    echo json_encode(["code" => "500", "message" => "Erreur lors de l'envoi du message"]);
}

Pgclose();
?>
