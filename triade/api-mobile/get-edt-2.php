<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../librairie_php/langue-text-fr.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit, vous n'êtes pas connecté"]);
    die();
}

if (!isset($_GET["date"]) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["date"])) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Parametre date manquant (YYYY-MM-DD)"]);
    exit;
}

$idClasse   = $_SESSION["idClasse"];
$idPersSess = intval($_SESSION["id_pers"] ?? 0);
$isProf     = ($_SESSION["membre"] === 'menuprof');
$prefixe    = PREFIXE;
$dateDebut  = $_GET["date"];

$cnx = cnx();

$joursNoms = [
    'Monday'    => 'Lundi',
    'Tuesday'   => 'Mardi',
    'Wednesday' => 'Mercredi',
    'Thursday'  => 'Jeudi',
    'Friday'    => 'Vendredi',
    'Saturday'  => 'Samedi',
    'Sunday'    => 'Dimanche',
];

$parJour = [];

$selectBase = "SELECT id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,idgroupe,id_resa_liste,docdst,reportle,reporta FROM {$prefixe}edt_seances";

for ($d = 0; $d <= 5; $d++) {
    $date = date('Y-m-d', strtotime($dateDebut . " +$d days"));
    $parJour[$date] = [];

    if ($isProf) {
        $sql = "$selectBase WHERE (coursannule != '1' OR coursannule IS NULL) AND date='$date' AND idprof='$idPersSess'";
    } else {
        $sql = "$selectBase WHERE (coursannule != '1' OR coursannule IS NULL) AND date='$date' AND idclasse='$idClasse'";
    }

    $res  = execSql($sql);
    $data = chargeMat($res);

    for ($i = 0; $i < count($data); $i++) {
        $id          = $data[$i][0];
        $idclasse    = $data[$i][7];
        $idprof      = $data[$i][8];
        $idmatiere   = $data[$i][10];
        $coursannule = ($data[$i][11] == 1);
        $idgroupe    = $data[$i][12];
        $idsalle     = $data[$i][13];
        $heure       = timeForm($data[$i][4]);
        $duree       = $data[$i][5];
        $bgcolor     = trim($data[$i][6] ?? '');
        $docdst      = ($data[$i][14] == 1);
        $reportle    = $data[$i][15] ?? '';
        $reporta     = $data[$i][16] ?? '';

        // Décoder la description (HTML → texte)
        $desc = $data[$i][2] ?? '';
        $desc = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $desc = preg_replace('/<br\s*\/?>/i', "\n", $desc);
        $desc = strip_tags($desc);
        $desc = trim($desc);

        $matiere   = chercheMatiereNom($idmatiere);
        $nomclasse = chercheClasse_nom($idclasse);
        $prof     = recherche_personne($idprof);
        if ($prof == "Message Automatique") $prof = "";
        $salle    = chercheNomMatos($idsalle);
        $heureFin = substr(calculerHeureDeFin($heure, $duree), 0, 5);
        $groupe   = ($idgroupe && $idgroupe != "0") ? chercheGroupeNom($idgroupe) : "";

        $parJour[$date][] = [
            "id"          => "$id",
            "idclasse"    => "$idclasse",
            "idmatiere"   => "$idmatiere",
            "nomclasse"   => "$nomclasse",
            "matiere"     => "$matiere",
            "enseignant"  => "$prof",
            "salle"       => "$salle",
            "groupe"      => "$groupe",
            "heuredebut"  => "$heure",
            "heurefin"    => "$heureFin",
            "annule"      => $coursannule,
            "bgcolor"     => $bgcolor,
            "description" => $desc,
            "docdst"      => $docdst,
            "reportle"    => ($reportle && $reportle !== '0000-00-00') ? dateForm($reportle) : '',
            "reporta"     => $reporta ?: '',
        ];
    }
}

$jours = [];
foreach ($parJour as $date => $cours) {
    $nomAnglais = date('l', strtotime($date));
    $jours[] = [
        "date"  => $date,
        "nom"   => isset($joursNoms[$nomAnglais]) ? $joursNoms[$nomAnglais] : $nomAnglais,
        "cours" => $cours,
    ];
}

Pgclose();

echo json_encode([
    "semaine" => $dateDebut,
    "jours"   => $jours,
], JSON_UNESCAPED_UNICODE);
?>
