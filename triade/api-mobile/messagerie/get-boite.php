<?php session_start();

include_once("../../common/config.inc.php");
include_once("../../common/config2.inc.php");
include_once("../../librairie_php/db_triade.php");
include_once("../../librairie_php/timezone.php");

$prefixe = PREFIXE;
$cnx = cnx();

header('Content-Type: application/json; charset=utf-8');

// Capture les erreurs fatales et les retourne en JSON pour le debug
register_shutdown_function(function() {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) http_response_code(500);
        echo json_encode(["code" => "500", "fatal" => $e['message'], "line" => $e['line']]);
    }
});

/* get-boite.php — GET avec session PHP
 * URL : .../get-boite.php?boite=[std-reception|std-envoyer|std-corbeille]&curseur=N&lot=N
 */

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
$type_personne = $type_personne_map[$membre] ?? "ELE";

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

if (!isset($_GET["boite"]) || !isset($_GET["curseur"]) || !isset($_GET["lot"])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "La demande n'a pas été formulée correctement"]);
    Pgclose();
    die();
}

$boite  = $_GET["boite"];
$offset = max(0, intval($_GET["curseur"]));
$limit  = min(50, max(1, intval($_GET["lot"])));

function formaterLigne($row, $is_envoye = false, $hasPJ = false) {
    if ($is_envoye) {
        $id_pers = $row[2]; // destinataire
        $type_p  = $row[9]; // type_personne_dest
    } else {
        $id_pers = $row[1]; // emetteur
        $type_p  = $row[7]; // type_personne
    }
    $nom = trim(
        recherche_personne_nom($id_pers, $type_p) . " " .
        recherche_personne_prenom($id_pers, $type_p)
    );
    if ($is_envoye) {
        $nom = "À : " . $nom;
    }
    $date_obj = DateTime::createFromFormat('Y-m-d', $row[4]);
    // réception : $row[6] = lu (message lu par le destinataire)
    // envoi     : $row[10] = lu_par_utilisateur (mis à 1 par valide_message_lu_envoyer quand le destinataire lit)
    $lu = $is_envoye
        ? ($row[10] == 1 || $row[10] === 'true')
        : ($row[6]  == 1 || $row[6]  === 'true');

    return [
        "id"            => intval($row[0]),
        "emetteur_id"   => intval($id_pers),
        "type_emetteur" => $type_p,
        "date"          => $date_obj ? $date_obj->format('d/m/Y') : $row[4],
        "horodatage"    => substr($row[5], 0, 5),
        "expediteur"    => $nom,
        "objet"         => stripslashes($row[8]),
        "estlu"         => $lu,
        "hasPJ"         => (bool)$hasPJ,
    ];
}

function buildPJSet($data, $table) {
    global $prefixe;
    if (empty($data)) return [];
    $ids = implode(',', array_map(function($r){ return intval($r[0]); }, $data));
    // Vérifie qu'il existe au moins une pièce jointe réelle (etat=1) dans tria_piecejointe
    $res = execSql(
        "SELECT DISTINCT m.id_message FROM {$prefixe}{$table} m " .
        "INNER JOIN {$prefixe}piecejointe pj ON pj.idpiecejointe = m.idpiecejointe AND pj.etat = '1' " .
        "WHERE m.id_message IN ($ids) AND m.idpiecejointe IS NOT NULL AND m.idpiecejointe != '' AND m.idpiecejointe != '0'"
    );
    $rows = chargeMat($res);
    $set = [];
    foreach ($rows as $r) { $set[intval($r[0])] = true; }
    return $set;
}

$contenu = ["init" => "init"];

if ($boite === "std-reception") {
    $data  = affichage_messagerie_limit($type_personne, $destinataire, $offset, $limit, "");
    $pjSet = buildPJSet($data, 'messageries');
    foreach ($data as $i => $row) {
        $contenu["$i"] = formaterLigne($row, false, isset($pjSet[intval($row[0])]));
    }
    echo json_encode(["nombre" => count($data), "contenu" => $contenu]);

} elseif ($boite === "std-corbeille") {
    $data  = affichage_messagerie_limit($type_personne, $destinataire, $offset, $limit, "", "date", "1");
    $pjSet = buildPJSet($data, 'messageries');
    foreach ($data as $i => $row) {
        $contenu["$i"] = formaterLigne($row, false, isset($pjSet[intval($row[0])]));
    }
    echo json_encode(["nombre" => count($data), "contenu" => $contenu]);

} elseif ($boite === "std-envoyer") {
    $sql = "SELECT id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, lu_par_utilisateur, idpiecejointe FROM {$prefixe}messagerie_envoyer WHERE type_personne='$type_personne' AND emetteur='$destinataire' AND (repertoire IS NULL OR repertoire = '0') ORDER BY date DESC, heure DESC LIMIT $offset, $limit";
    $res  = execSql($sql);
    $data = ($res !== null && !DB::isError($res)) ? chargeMat($res) : [];
    $pjSet = buildPJSet($data, 'messagerie_envoyer');
    foreach ($data as $i => $row) {
        $contenu["$i"] = formaterLigne($row, true, isset($pjSet[intval($row[0])]));
    }
    echo json_encode(["nombre" => count($data), "contenu" => $contenu]);

} elseif (strpos($boite, "usr-") !== false) {
    http_response_code(501);
    echo json_encode(["code" => "501", "message" => "Fonctionnalité pas encore implémentée"]);

} else {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Boite inconnue: " . htmlspecialchars($boite)]);
}

Pgclose();
?>
