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

$absences = affAbsence($idpers, $annee);
$retards  = affRetard($idpers, $annee);

$listeAbsences = [];
if (is_array($absences)) {
    foreach ($absences as $ligne) {
        $date = dateForm($ligne[1]);
        $saisie = dateForm($ligne[2]);
        $duree = $ligne[4];
        if ($duree == "-1") {
            $duree = $ligne[7];
        }
        $duree = preg_replace('/\./', 'h', $duree);
        $matiere = chercheMatiereNom($ligne[8]);
        $justifier = ($ligne[10] == '1' || $ligne[10] === 1) ? 'oui' : 'non';

        $listeAbsences[] = [
            "date" => "$date",
            "saisie" => "$saisie",
            "duree" => "$duree",
            "motif" => "{$ligne[6]}",
            "matiere" => "$matiere",
            "justifie" => "$justifier",
            "date_fin" => dateForm($ligne[5]),
            "heure_saisie" => timeForm($ligne[11]),
            "heure_absence" => $ligne[12],
            "creneaux" => $ligne[13],
            "smsenvoye" => $ligne[14],
            "idrattrapage" => $ligne[15]
        ];
    }
}

$listeRetards = [];
if (is_array($retards)) {
    foreach ($retards as $ligne) {
        $date = dateForm($ligne[2]);
        $heure = timeForm($ligne[1]);
        $duree = $ligne[5];
        if ($duree == "-1") {
            $duree = $ligne[7];
        }
        $duree = preg_replace('/\./', 'h', $duree);
        $matiere = chercheMatiereNom($ligne[7]);
        $justifier = ($ligne[8] == '1' || $ligne[8] === 1) ? 'oui' : 'non';

        $listeRetards[] = [
            "date" => "$date",
            "heure" => "$heure",
            "duree" => "$duree",
            "motif" => "{$ligne[6]}",
            "matiere" => "$matiere",
            "justifie" => "$justifier",
            "date_saisie" => dateForm($ligne[3]),
            "origine" => "{$ligne[4]}",
            "heure_saisie" => timeForm($ligne[9]),
            "creneaux" => $ligne[10],
            "idrattrapage" => $ligne[11]
        ];
    }
}

$response = [
    "status" => "success",
    "idpers" => $idpers,
    "annee" => $annee,
    "nombre_absences" => count($listeAbsences),
    "nombre_retards" => count($listeRetards),
    "absences" => $listeAbsences,
    "retards" => $listeRetards
];

if (empty($listeAbsences) && empty($listeRetards)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune absence ou retard trouvé pour cet élève"], JSON_PRETTY_PRINT);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération absences/retards élève ID $idpers - ".count($listeAbsences)." absences, ".count($listeRetards)." retards");

echo json_encode($response, JSON_PRETTY_PRINT);
