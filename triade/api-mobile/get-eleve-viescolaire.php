<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["connecte"]) || $_SESSION["connecte"] !== true || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès interdit"]);
    die();
}

$allowedMembres = ['menuprof', 'menuadmin', 'menuscolaire'];
if (!in_array($_SESSION["membre"], $allowedMembres)) {
    http_response_code(403);
    echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
    die();
}

$cnx     = cnx();
$prefixe = PREFIXE;
$idprof  = intval($_SESSION["id_pers"]);

$elevId    = intval($_GET['elev_id']   ?? 0);
$categorie = trim($_GET['categorie']   ?? '');

if (!$elevId || !$categorie) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètres elev_id et categorie requis"]);
    exit;
}

// Récupérer la classe de l'élève
$classeData = chargeMat(execSql("SELECT classe FROM {$prefixe}eleves WHERE elev_id='$elevId' AND (compte_inactif IS NULL OR compte_inactif != '1')"));
if (empty($classeData)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Étudiant introuvable"]);
    exit;
}
$idclasse     = trim($classeData[0][0]);
$idclasseSafe = addslashes($idclasse);

// Pour les enseignants : vérifier l'affectation à la classe
if ($_SESSION["membre"] === 'menuprof') {
    $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$idclasseSafe'"));
    if (intval($chk[0][0]) == 0) {
        http_response_code(403);
        echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
        exit;
    }
}

$annee  = anneeScolaireViaIdClasse($idclasse);
$viesco = ["nombre" => "0", "contenu" => ["init" => "init"]];

// Résoudre un ID numérique en nom depuis tria_personnel
function resolveNomPersonnel($val) {
    global $cnx, $prefixe;
    $v = trim($val);
    if ($v === '' || !ctype_digit($v)) return $v;
    $d = chargeMat(execSql("SELECT CONCAT(trim(prenom),' ',trim(nom)) FROM {$prefixe}personnel WHERE pers_id='$v'"));
    return (!empty($d) && trim($d[0][0]) !== '') ? trim($d[0][0]) : $v;
}

if ($categorie === 'absences') {

    $data = affAbsence($elevId, $annee);
    $nb   = count($data);
    $viesco = ["nombre" => "$nb", "contenu" => ["init" => "init"]];
    for ($i = 0; $i < $nb; $i++) {
        $duree = $data[$i][4];
        if ($duree == "-1") $duree = $data[$i][7];
        $duree = preg_replace('/\./', "h", $duree);
        array_push($viesco["contenu"], [
            "date"   => dateForm($data[$i][1]),
            "saisie" => dateForm($data[$i][2]),
            "duree"  => "$duree",
            "motif"  => $data[$i][6],
        ]);
    }

} elseif ($categorie === 'retards') {

    $data = affRetard($elevId, $annee);
    $nb   = count($data);
    $viesco = ["nombre" => "$nb", "contenu" => ["init" => "init"]];
    for ($i = 0; $i < $nb; $i++) {
        $duree = $data[$i][5];
        if ($duree == "-1") $duree = $data[$i][7];
        $duree = preg_replace('/\./', "h", $duree);
        array_push($viesco["contenu"], [
            "date"  => dateForm($data[$i][2]),
            "heure" => timeForm($data[$i][1]),
            "duree" => "$duree",
            "motif" => $data[$i][6],
        ]);
    }

} elseif ($categorie === 'discipline-sanctions') {

    $data = affSanction_par_eleve($elevId, $annee);
    $nb   = count($data);
    $viesco = ["nombre" => "$nb", "contenu" => ["init" => "init"]];
    for ($i = 0; $i < $nb; $i++) {
        array_push($viesco["contenu"], [
            "saisie"      => dateForm($data[$i][4]),
            "auteur"      => resolveNomPersonnel($data[$i][7]),
            "categorie"   => rechercheSanction($data[$i][3]),
            "description" => $data[$i][2],
            "devoir"      => $data[$i][8],
        ]);
    }

} elseif ($categorie === 'discipline-retenues') {

    $data = affRetenuTotal_par_eleve($elevId, $annee);
    $nb   = count($data);
    $viesco = ["nombre" => "$nb", "contenu" => ["init" => "init"]];
    for ($i = 0; $i < $nb; $i++) {
        $retenuEffectue = ($data[$i][6] == "0") ? "non" : "oui";
        array_push($viesco["contenu"], [
            "saisie"       => dateForm($data[$i][3]),
            "auteur"       => resolveNomPersonnel($data[$i][8]),
            "categorie"    => rechercheSanction($data[$i][5]),
            "description"  => $data[$i][7],
            "devoir"       => $data[$i][11],
            "effectue"     => "$retenuEffectue",
            "dateretenue"  => dateForm($data[$i][1]),
            "heureretenue" => timeForm($data[$i][2]),
            "dureeretenue" => timeForm($data[$i][10]),
        ]);
    }

} else {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Catégorie inconnue"]);
    Pgclose();
    exit;
}

Pgclose();
echo json_encode($viesco, JSON_UNESCAPED_UNICODE);
?>
