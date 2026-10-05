<?php session_start();

include_once("../../common/config.inc.php");
include_once("../../common/config2.inc.php");
include_once("../../librairie_php/db_triade.php");
include_once("../../librairie_php/recupnoteperiode.php");
include_once("../../librairie_php/timezone.php");

$prefixe = PREFIXE;
$cnx = cnx();

/* get-message.php — GET avec session PHP
 * URL : .../get-message.php?id=[id_message]&source=[reception|envoi]
 * Retourne le contenu complet d'un message avec contrôle d'accès.
 * Marque automatiquement le message comme lu à la consultation.
 */

header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function() {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) http_response_code(500);
        echo json_encode(["code" => "500", "fatal" => $e['message'], "file" => basename($e['file']), "line" => $e['line']]);
    }
});

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, vous n'êtes pas connecté"]);
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

if ($membre === "menuparent" || $membre === "menueleve") {
    $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]);
} else {
    $civ_map = [
        "menuadmin"     => "ADM",
        "menututeur"    => "TUT",
        "menupersonnel" => "PER",
        "menuprof"      => "ENS",
        "menuscolaire"  => "MVS",
    ];
    $civ = $civ_map[$membre] ?? "ADM";
    $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], $civ);
}

if (!isset($_GET["id"])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "La demande n'a pas été formulée correctement"]);
    Pgclose();
    die();
}

function lireMessage($raw, $key) {
    if (empty($raw)) return '';
    $decrypted = Decrypte($raw, $key);
    $decrypted = str_replace(['\\r\\n', '\\r', '\\n'], "\n", $decrypted);
    $decrypted = stripslashes($decrypted);
    $decrypted = str_replace(['\\r\\n', '\\r', '\\n'], "\n", $decrypted);
    $decrypted = stripslashes($decrypted);
    $decrypted = str_replace('rnrn', "\n", $decrypted);
    return !empty(trim($decrypted)) ? $decrypted : stripslashes(stripslashes($raw));
}

$id     = intval($_GET["id"]);
$source = isset($_GET["source"]) ? trim($_GET["source"]) : "";

function listePiecesJointes($idpj) {
    if (empty($idpj) || $idpj === '0') return [];
    global $prefixe;
    $idpj_safe = addslashes($idpj);
    $res  = execSql("SELECT nom, md5 FROM {$prefixe}piecejointe WHERE idpiecejointe='$idpj_safe' AND etat='1'");
    $data = chargeMat($res);
    $out  = [];
    for ($i = 0; $i < count($data); $i++) {
        if (!empty($data[$i][0]) && !empty($data[$i][1])) {
            $out[] = ["nom" => $data[$i][0], "md5" => $data[$i][1]];
        }
    }
    return $out;
}

// source=envoi → chercher uniquement dans messagerie_envoyer (évite collision d'IDs)
if ($source === 'envoi') {
    $env_data = affichage_messagerie_envoyer_message($id);

    if (!empty($env_data) && isset($env_data[0]) && $env_data[0][1] == $destinataire) {
        $id_pers   = $env_data[0][2];
        $type_pers = $env_data[0][9];
        $date_obj  = DateTime::createFromFormat('Y-m-d', $env_data[0][4]);
        $nom_dest  = trim(recherche_personne_nom($id_pers, $type_pers) . " " . recherche_personne_prenom($id_pers, $type_pers));

        $raw_msg = $env_data[0][3];
        $raw_key = $env_data[0][10];
        $decoded = lireMessage($raw_msg, $raw_key);

        // Récupérer idpiecejointe et lu_par_utilisateur directement
        $extra   = chargeMat(execSql("SELECT idpiecejointe, lu_par_utilisateur FROM {$prefixe}messagerie_envoyer WHERE id_message='$id' LIMIT 1"));
        $idpj    = $extra[0][0] ?? '';
        $est_lu  = ($extra[0][1] == 1 || $extra[0][1] === 'true');

        $message = [
            "expediteur"    => "À : " . $nom_dest,
            "emetteur_id"   => strval($id_pers),
            "type_emetteur" => $type_pers,
            "date"          => $date_obj ? $date_obj->format('d/m/y') : $env_data[0][4],
            "horodatage"    => substr($env_data[0][5], 0, 5),
            "objet"         => stripslashes($env_data[0][8]),
            "message"       => $decoded,
            "estlu"         => $est_lu,
            "piecejointes"  => listePiecesJointes($idpj),
        ];
    } else {
        http_response_code(403);
        $message = ["code" => "403", "message" => "Message introuvable ou accès non autorisé"];
    }
} else {
    // source=reception ou non précisé → chercher dans messageries (messages reçus)
    $msg_data = affichage_messagerie_message($id);

    if (!empty($msg_data) && isset($msg_data[0]) && $msg_data[0][2] == $destinataire) {
        lecture_message($id);
        valide_message_lu($id);

        $id_pers   = $msg_data[0][1];
        $type_pers = $msg_data[0][7];
        $date_obj  = DateTime::createFromFormat('Y-m-d', $msg_data[0][4]);

        $pj_row = chargeMat(execSql("SELECT idpiecejointe FROM {$prefixe}messageries WHERE id_message='$id' LIMIT 1"));
        $idpj   = $pj_row[0][0] ?? '';

        $message = [
            "expediteur"    => trim(recherche_personne_nom($id_pers, $type_pers) . " " . recherche_personne_prenom($id_pers, $type_pers)),
            "emetteur_id"   => strval($id_pers),
            "type_emetteur" => $type_pers,
            "date"          => $date_obj ? $date_obj->format('d/m/y') : $msg_data[0][4],
            "horodatage"    => substr($msg_data[0][5], 0, 5),
            "objet"         => stripslashes($msg_data[0][8]),
            "message"       => lireMessage($msg_data[0][3], $msg_data[0][10]),
            "piecejointes"  => listePiecesJointes($idpj),
        ];
    } else {
        http_response_code(403);
        $message = ["code" => "403", "message" => "Message introuvable ou accès non autorisé"];
    }
}

echo json_encode($message);
Pgclose();
?>
