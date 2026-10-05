<?php
include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$cd = $_GET['cd'] ?? '';

// Format QR mobile : "protocol,adresseurl,compte,nom,prenom,QR"
// Généré par triade-phone.php : $text="$protocol,$adresseurl,$compte,$nom,$prenom,$flag"
$parts = explode(',', $cd);
if (count($parts) === 6 && $parts[5] === 'QR') {
    header('Content-Type: application/json; charset=utf-8');

    [$protocol, $hostpath, $compte, $nom, $prenom, $flag] = $parts;

    $server = rtrim($protocol) . '://' . rtrim($hostpath, '/') . '/';

    echo json_encode([
        'server' => $server,
        'nom'    => $nom,
        'prenom' => $prenom,
        'profil' => $compte,
    ]);
    exit;
}

// Format original (lecture badge/code-barres) : cd = identifiant numérique
if (!is_numeric($cd)) $cd = '0';
$data = recupInfoCompte(intval($cd));
if (!$data || !isset($data[0])) { print 'Error'; exit; }

$nom          = $data[0][1];
$prenom       = $data[0][2];
$compte_inactif = $data[0][4];

if ($compte_inactif == 0) {
    print htmlspecialchars("$nom $prenom", ENT_QUOTES, 'UTF-8');
} else {
    print 'Error';
}
?>
