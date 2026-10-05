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
include_once('../librairie_php/recupnoteperiode.php');

/* Forme URL de la requête : GET /api-v1/get-notes.php?idpers=[id]&annee=AAAA&mois=MM */

$prefixe = PREFIXE;
$cnx     = cnx();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée"]);
    exit;
}

if (!isset($_GET['idpers'], $_GET['annee'], $_GET['mois'])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres manquants : idpers, annee, mois"]);
    exit;
}

$idpers = intval($_GET['idpers']);
$annee  = intval($_GET['annee']);
$mois   = intval($_GET['mois']);

if ($annee < 2000 || $annee > 2100 || $mois < 1 || $mois > 12) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres annee ou mois invalides"]);
    exit;
}

if ($mois < 10 && strlen($mois) < 2) $mois = "0" . $mois;

$sql = "SELECT
            m.code_mat                   AS idmatiere,
            n.coef,
            n.sujet,
            DATE_FORMAT(n.date,'%d-%m-%Y') AS date,
            TRUNCATE(n.note, 2)          AS note,
            n.typenote,
            n.notationsur,
            n.noteexam,
            n.prof_id                    AS idprof,
            n.id_groupe                  AS idgroupe,
            n.id_classe                  AS idclasse
        FROM {$prefixe}notes n, {$prefixe}matieres m
        WHERE n.elev_id = '$idpers'
          AND DATE_FORMAT(n.date,'%m') = '$mois'
          AND DATE_FORMAT(n.date,'%Y') = '$annee'
          AND m.code_mat = n.code_mat";

$curs = execSql($sql);

$contenu = [];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    $note = $ligne['note'];
    $note = preg_replace('/-1/', 'abs',  $note);
    $note = preg_replace('/-2/', 'disp', $note);
    $note = preg_replace('/-3/', ' ',    $note);
    $note = preg_replace('/-4/', 'DNN',  $note);
    $note = preg_replace('/-5/', 'DNR',  $note);
    $note = preg_replace('/-6/', 'VAL',  $note);
    $note = preg_replace('/.00$/', '',   $note);

    $nommatiere = chercheMatiereNom($ligne['idmatiere']);
    $moyenne    = moyenneDevoir($ligne['idmatiere'], $ligne['date'], $ligne['idprof'], $ligne['sujet'], $ligne['coef'], $ligne['noteexam'], $ligne['idgroupe'], $ligne['idclasse']);

    $contenu[] = [
        "date"        => $ligne['date'],
        "matiere"     => $nommatiere,
        "sujet"       => $ligne['sujet'],
        "coefficient" => $ligne['coef'],
        "note"        => $note,
        "bareme"      => $ligne['notationsur'],
        "moyenne"     => $moyenne['moy'],
        "notemin"     => $moyenne['min'],
        "notemax"     => $moyenne['max']
    ];
}

if (empty($contenu)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune note trouvée"]);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération notes élève ID $idpers ($annee/$mois) - ".count($contenu)." notes");

echo json_encode([
    "status"  => "success",
    "nombre"  => count($contenu),
    "contenu" => $contenu
], JSON_PRETTY_PRINT);
