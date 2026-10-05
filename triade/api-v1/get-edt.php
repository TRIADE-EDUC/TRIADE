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
include_once('../librairie_php/timezone.php');
include_once('../librairie_php/langue-text-fr.php');

/* Forme URL de la requête : GET /api-v1/get-edt.php
   Paramètres obligatoires (au moins un) :
     - idclasse     : ID de la classe
     - idprof       : ID du professeur
     - idressource  : ID de la salle (id_resa_liste)
   Paramètres optionnels :
     - date_debut   : date de début au format AAAA-MM-JJ (défaut : lundi de la semaine courante)
     - date_fin     : date de fin au format AAAA-MM-JJ  (défaut : dimanche de la semaine courante)
*/

$prefixe = PREFIXE;
$cnx     = cnx();
$_tz_h   = intval(TIMEZONE);
$_tz_m   = intval(TIMEZONEMINUTE);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => "405", "message" => "Méthode non autorisée"]);
    exit;
}

$idclasse    = isset($_GET['idclasse'])    ? intval($_GET['idclasse'])    : 0;
$idprof      = isset($_GET['idprof'])      ? intval($_GET['idprof'])      : 0;
$idressource = isset($_GET['idressource']) ? intval($_GET['idressource']) : 0;

if ($idclasse === 0 && $idprof === 0 && $idressource === 0) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Au moins un filtre requis : idclasse, idprof ou idressource"]);
    exit;
}

// Calcul de la semaine courante par défaut
$lundi    = date('Y-m-d', strtotime('monday this week'));
$dimanche = date('Y-m-d', strtotime('sunday this week'));

$date_debut = isset($_GET['date_debut']) && $_GET['date_debut'] !== '' ? $_GET['date_debut'] : $lundi;
$date_fin   = isset($_GET['date_fin'])   && $_GET['date_fin']   !== '' ? $_GET['date_fin']   : $dimanche;

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Format de date invalide, utiliser AAAA-MM-JJ"]);
    exit;
}

$filtres = "WHERE s.date >= '$date_debut' AND s.date <= '$date_fin'";
if ($idclasse    > 0) $filtres .= " AND s.idclasse = '$idclasse'";
if ($idprof      > 0) $filtres .= " AND s.idprof = '$idprof'";
if ($idressource > 0) $filtres .= " AND s.id_resa_liste = '$idressource'";

$sql = "SELECT
            s.id,
            s.enseignement,
            s.date,
            s.heure,
            s.duree,
            s.idclasse,
            s.idprof,
            s.idmatiere,
            s.id_resa_liste,
            s.idgroupe,
            s.coursannule
        FROM {$prefixe}edt_seances s
        $filtres
        ORDER BY s.date ASC, s.heure ASC";

$curs = execSql($sql);

if (DB::isError($curs)) {
    http_response_code(500);
    echo json_encode(["code" => "500", "message" => "Erreur base de données"]);
    exit;
}

$contenu = [];
while ($ligne = $curs->fetchRow(DB_FETCHMODE_ASSOC)) {
    $heuredebut = timeForm($ligne['heure']);
    list($_h, $_m) = explode(':', $heuredebut);
    $heuredebut = date('H:i', mktime(intval($_h) + $_tz_h, intval($_m) + $_tz_m, 0, 1, 1, 2000));
    $heurefin   = calculerHeureDeFin($heuredebut, $ligne['duree']);
    $classe     = chercheClasse_nom($ligne['idclasse']);
    $matiere    = chercheMatiereNom($ligne['idmatiere']);
    $prof       = recherche_personne($ligne['idprof']);
    $salle      = chercheNomMatos($ligne['id_resa_liste']);
    $groupe     = ($ligne['idgroupe'] > 0) ? chercheGroupeNom($ligne['idgroupe']) : null;
    $annule     = ($ligne['coursannule'] == 1);

    if ($prof === "Message Automatique") $prof = null;

    $contenu[] = [
        "id"         => intval($ligne['id']),
        "date"       => $ligne['date'],
        "heuredebut" => $heuredebut,
        "heurefin"   => $heurefin,
        "matiere"    => $matiere,
        "enseignant" => $prof,
        "classe"     => $classe,
        "salle"      => $salle,
        "groupe"     => $groupe,
        "annule"     => $annule
    ];
}

if (empty($contenu)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Aucune séance trouvée pour cette période"]);
    exit;
}

history_cmd($_SESSION['api_client'] ?? 'API', "API-GET", "Récupération EDT $date_debut/$date_fin - ".count($contenu)." séances");

echo json_encode([
    "status"      => "success",
    "nombre"      => count($contenu),
    "date_debut"  => $date_debut,
    "date_fin"    => $date_fin,
    "contenu"     => $contenu
], JSON_PRETTY_PRINT);
?>
