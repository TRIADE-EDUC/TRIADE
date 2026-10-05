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

$prefixe = PREFIXE;
$cnx     = cnx();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée, utilisez GET"]);
    exit;
}

if (!isset($_GET['idpers'])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre manquant : idpers"]);
    exit;
}

$idpers = intval($_GET['idpers']);
$annee  = isset($_GET['annee']) ? trim($_GET['annee']) : '';

if ($annee !== '') {
    if (preg_match('/^\d{4}$/', $annee)) {
        $annee = $annee . ' - ' . ($annee + 1);
    } elseif (preg_match('/^(\d{4})-(\d{4})$/', $annee, $m) === 1) {
        $annee = $m[1] . ' - ' . $m[2];
    }
}

$sanctions = affSanction_par_eleve($idpers, $annee);
$retenues = affRetenuTotal_par_eleve($idpers, $annee);

$listeSanctions = [];
if (is_array($sanctions)) {
    foreach ($sanctions as $ligne) {
        $date = dateForm($ligne[4]);
        $category = rechercheSanction($ligne[3]);
        $signataire = ($ligne[6] == '1' || $ligne[6] === 1) ? 'oui' : 'non';

        $listeSanctions[] = [
            "date" => "$date",
            "auteur" => "{$ligne[7]}",
            "categorie" => "$category",
            "type" => "$category",
            "motif" => "{$ligne[2]}",
            "devoir" => "{$ligne[8]}",
            "description_fait" => "{$ligne[9]}",
            "signature_parent" => "$signataire",
            "origine" => "{$ligne[5]}"
        ];
    }
}

$listeRetenues = [];
if (is_array($retenues)) {
    foreach ($retenues as $ligne) {
        $date = dateForm($ligne[1]);
        $heure = timeForm($ligne[2]);
        $duree = timeForm($ligne[10]);
        $category = rechercheSanction($ligne[5]);
        $signature_parent = ($ligne[9] == '1' || $ligne[9] === 1) ? 'oui' : 'non';
        $effectue = ($ligne[6] == '0' || $ligne[6] === 0) ? 'non' : 'oui';

        $listeRetenues[] = [
            "date" => "$date",
            "heure" => "$heure",
            "duree" => "$duree",
            "motif" => "{$ligne[7]}",
            "categorie" => "$category",
            "type" => "$category",
            "auteur" => "{$ligne[8]}",
            "devoir" => "{$ligne[11]}",
            "description_fait" => "{$ligne[12]}",
            "effectue" => "$effectue",
            "signature_parent" => "$signature_parent",
            "origine" => "{$ligne[4]}",
            "date_saisie" => dateForm($ligne[3]),
            "repport_du" => "{$ligne[14]}"
        ];
    }
}

$response = [
    "status" => "success",
    "idpers" => $idpers,
    "annee" => $annee,
    "nombre_sanctions" => count($listeSanctions),
    "nombre_retenues" => count($listeRetenues),
    "sanctions" => $listeSanctions,
    "retenues" => $listeRetenues
];

if (empty($listeSanctions) && empty($listeRetenues)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune sanction ou retenue trouvée pour cet élève"], JSON_PRETTY_PRINT);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération sanctions/retenues élève ID $idpers - ".count($listeSanctions)." sanctions, ".count($listeRetenues)." retenues");

echo json_encode($response, JSON_PRETTY_PRINT);
