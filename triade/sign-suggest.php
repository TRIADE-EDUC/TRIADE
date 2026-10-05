<?php
session_start();
if (!isset($_SESSION['sign_token'])) {
    header('Content-Type: application/json');
    echo '[]';
    exit;
}
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

$cnx   = cnx();
$field = (isset($_GET['field']) && $_GET['field'] === 'email') ? 'email' : 'nom';
$q     = isset($_GET['q']) ? trim($_GET['q']) : '';

header('Content-Type: application/json; charset=utf-8');

if (strlen($q) < 2) { echo '[]'; exit; }

$qesc = $cnx->escapeSimple(strtolower($q));
$results = array();

if ($field === 'nom') {
    // Élèves
    $sql = "SELECT nom, prenom, email_eleve, tel_eleve
            FROM {$prefixe}eleves
            WHERE LOWER(nom) LIKE '%$qesc%' OR LOWER(prenom) LIKE '%$qesc%'
               OR LOWER(CONCAT(nom,' ',prenom)) LIKE '%$qesc%'
               OR LOWER(CONCAT(prenom,' ',nom)) LIKE '%$qesc%'
            ORDER BY nom LIMIT 6";
    foreach (chargeMat(execSql($sql)) as $r) {
        $n = trim($r[0].' '.$r[1]);
        $results[] = array('label'=>$n.' (élève)','nom'=>$n,'email'=>$r[2],'tel'=>$r[3]);
    }
    // Parents (tuteurs)
    $sql = "SELECT nomtuteur, prenomtuteur, email, telephone
            FROM {$prefixe}eleves
            WHERE nomtuteur != '' AND (LOWER(nomtuteur) LIKE '%$qesc%' OR LOWER(prenomtuteur) LIKE '%$qesc%'
               OR LOWER(CONCAT(nomtuteur,' ',prenomtuteur)) LIKE '%$qesc%')
            GROUP BY nomtuteur, prenomtuteur, email, telephone
            ORDER BY nomtuteur LIMIT 5";
    foreach (chargeMat(execSql($sql)) as $r) {
        $n = trim($r[0].' '.$r[1]);
        $results[] = array('label'=>$n.' (parent)','nom'=>$n,'email'=>$r[2],'tel'=>$r[3]);
    }
    // Personnel
    $sql = "SELECT nom, prenom, email, tel
            FROM {$prefixe}personnel
            WHERE LOWER(nom) LIKE '%$qesc%' OR LOWER(prenom) LIKE '%$qesc%'
               OR LOWER(CONCAT(nom,' ',prenom)) LIKE '%$qesc%'
            ORDER BY nom LIMIT 5";
    foreach (chargeMat(execSql($sql)) as $r) {
        $n = trim($r[0].' '.$r[1]);
        $results[] = array('label'=>$n.' (personnel)','nom'=>$n,'email'=>$r[2],'tel'=>$r[3]);
    }
} else {
    // Élèves par email_eleve
    $sql = "SELECT nom, prenom, email_eleve, tel_eleve
            FROM {$prefixe}eleves
            WHERE email_eleve != '' AND LOWER(email_eleve) LIKE '%$qesc%'
            ORDER BY nom LIMIT 5";
    foreach (chargeMat(execSql($sql)) as $r) {
        $n = trim($r[0].' '.$r[1]);
        $results[] = array('label'=>$r[2].' — '.$n.' (élève)','nom'=>$n,'email'=>$r[2],'tel'=>$r[3]);
    }
    // Parents par email tuteur
    $sql = "SELECT nomtuteur, prenomtuteur, email, telephone
            FROM {$prefixe}eleves
            WHERE nomtuteur != '' AND email != '' AND LOWER(email) LIKE '%$qesc%'
            GROUP BY nomtuteur, prenomtuteur, email, telephone
            ORDER BY nomtuteur LIMIT 5";
    foreach (chargeMat(execSql($sql)) as $r) {
        $n = trim($r[0].' '.$r[1]);
        $results[] = array('label'=>$r[2].' — '.$n.' (parent)','nom'=>$n,'email'=>$r[2],'tel'=>$r[3]);
    }
    // Personnel par email
    $sql = "SELECT nom, prenom, email, tel
            FROM {$prefixe}personnel
            WHERE email != '' AND LOWER(email) LIKE '%$qesc%'
            ORDER BY nom LIMIT 5";
    foreach (chargeMat(execSql($sql)) as $r) {
        $n = trim($r[0].' '.$r[1]);
        $results[] = array('label'=>$r[2].' — '.$n.' (personnel)','nom'=>$n,'email'=>$r[2],'tel'=>$r[3]);
    }
}

echo json_encode(array_slice($results, 0, 10), JSON_UNESCAPED_UNICODE);
exit;
