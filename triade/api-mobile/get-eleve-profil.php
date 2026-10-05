<?php
session_start();
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");

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

$elevId = intval($_GET['elev_id'] ?? 0);
if (!$elevId) {
    http_response_code(400);
    echo json_encode(["code" => "400", "message" => "Paramètre elev_id requis"]);
    exit;
}

// Récupérer la classe de l'élève
$classeData = chargeMat(execSql("SELECT classe FROM {$prefixe}eleves WHERE elev_id='$elevId' AND (compte_inactif IS NULL OR compte_inactif != '1')"));
if (empty($classeData) || !isset($classeData[0][0]) || $classeData[0][0] === '') {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Étudiant introuvable"]);
    exit;
}
$classeEleve = addslashes($classeData[0][0]);

// Pour les enseignants : vérifier l'affectation à la classe
if ($_SESSION["membre"] === 'menuprof') {
    $chk = chargeMat(execSql("SELECT COUNT(*) FROM {$prefixe}affectations WHERE code_prof='$idprof' AND code_classe='$classeEleve'"));
    if (intval($chk[0][0]) == 0) {
        http_response_code(403);
        echo json_encode(["code" => "403", "message" => "Accès non autorisé"]);
        exit;
    }
}

// Profil complet
// Indices : 0=nom, 1=prenom, 2=date_naissance, 3=lieu_naissance, 4=departementnais, 5=nationalite, 6=sexe
//           7=email_eleve, 8=tel_eleve, 9=adr_eleve, 10=commune_eleve, 11=ccp_eleve, 12=pays_eleve
//           13=numero_eleve, 14=ine, 15=classe, 16=lv1, 17=lv2, 18=option, 19=regime, 20=boursier
//           21=information
//           22=nomtuteur, 23=prenomtuteur, 24=civ_1, 25=email(parent1), 26=tel_port_1
//           27=profession_pere, 28=tel_prof_pere, 29=adr1, 30=code_post_adr1, 31=commune_adr1
//           32=nom_resp_2, 33=prenom_resp_2, 34=civ_2, 35=email_resp_2, 36=tel_port_2
//           37=profession_mere, 38=tel_prof_mere, 39=adr2, 40=code_post_adr2, 41=commune_adr2
$sql = "SELECT
    nom, prenom, date_naissance, lieu_naissance, departementnais, nationalite, sexe,
    email_eleve, tel_eleve, adr_eleve, commune_eleve, ccp_eleve, pays_eleve,
    numero_eleve, ine, classe, lv1, lv2, `option`, regime, boursier, information,
    nomtuteur, prenomtuteur, civ_1, email, tel_port_1, profession_pere, tel_prof_pere,
    adr1, code_post_adr1, commune_adr1,
    nom_resp_2, prenom_resp_2, civ_2, email_resp_2, tel_port_2,
    profession_mere, tel_prof_mere, adr2, code_post_adr2, commune_adr2
FROM {$prefixe}eleves WHERE elev_id='$elevId' LIMIT 1";
$data = chargeMat(execSql($sql));

if (empty($data)) {
    http_response_code(404);
    echo json_encode(["code" => "404", "message" => "Étudiant introuvable"]);
    exit;
}
$e = $data[0];

// Libellé classe
$classLib = trim(chargeMat(execSql("SELECT libelle FROM {$prefixe}classes WHERE code_class='$classeEleve'"))[0][0] ?? $classeEleve);

// Âge
$age = null;
if (!empty(trim($e[2]))) {
    try {
        $dob = new DateTime(trim($e[2]));
        $age = (new DateTime())->diff($dob)->y;
    } catch (Exception $ex) {}
}

// Nom parent 1 : civ + nomtuteur + prenomtuteur
$civLabel = ['M' => 'M.', 'Mme' => 'Mme', 'Mme.' => 'Mme'];
$np1 = trim(trim($e[22]) . ' ' . trim($e[23]));
$np2 = trim(trim($e[32]) . ' ' . trim($e[33]));

$profil = [
    "id"               => $elevId,
    // Identité
    "nom"              => strtoupper(trim($e[0])),
    "prenom"           => ucfirst(strtolower(trim($e[1]))),
    "age"              => $age,
    "date_naissance"   => (!empty(trim($e[2]))) ? date('d/m/Y', strtotime(trim($e[2]))) : '',
    "lieu_naissance"   => trim($e[3]),
    "dept_naissance"   => trim($e[4]),
    "nationalite"      => trim($e[5]),
    "sexe"             => trim($e[6]),
    // Scolarité
    "libclasse"        => $classLib,
    "ine"              => trim($e[14]),
    "numero_eleve"     => trim($e[13]),
    "lv1"              => trim($e[16]),
    "lv2"              => trim($e[17]),
    "option"           => trim($e[18]),
    "regime"           => trim($e[19]),
    "boursier"         => trim($e[20]),
    // Contact étudiant
    "email_eleve"      => trim($e[7]),
    "tel_eleve"        => trim($e[8]),
    "adr_eleve"        => trim($e[9]),
    "commune_eleve"    => trim($e[10]),
    "cp_eleve"         => trim($e[11]),
    "pays_eleve"       => trim($e[12]),
    // Parent 1
    "nom_parent1"      => $np1,
    "email_parent1"    => trim($e[25]),
    "tel_parent1"      => trim($e[26]),
    "tel_prof_parent1" => trim($e[28]),
    "prof_parent1"     => trim($e[27]),
    "adr_parent1"      => trim($e[29]),
    "cp_parent1"       => trim($e[30]),
    "ville_parent1"    => trim($e[31]),
    // Parent 2
    "nom_parent2"      => $np2,
    "email_parent2"    => trim($e[35]),
    "tel_parent2"      => trim($e[36]),
    "tel_prof_parent2" => trim($e[38]),
    "prof_parent2"     => trim($e[37]),
    "adr_parent2"      => trim($e[39]),
    "cp_parent2"       => trim($e[40]),
    "ville_parent2"    => trim($e[41]),
    // Informations
    "information"      => trim($e[21]),
];

// RGPD : un enseignant n'a pas accès aux coordonnées personnelles des parents
if ($_SESSION["membre"] === 'menuprof') {
    $parentSensitiveFields = [
        'email_parent1','tel_parent1','tel_prof_parent1','prof_parent1',
        'adr_parent1','cp_parent1','ville_parent1',
        'nom_parent2','email_parent2','tel_parent2','tel_prof_parent2',
        'prof_parent2','adr_parent2','cp_parent2','ville_parent2',
    ];
    foreach ($parentSensitiveFields as $_f) { $profil[$_f] = null; }
}

Pgclose();
echo json_encode($profil, JSON_UNESCAPED_UNICODE);
?>
