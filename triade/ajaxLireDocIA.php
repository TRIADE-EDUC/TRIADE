<?php
session_start();
if (empty($_SESSION["nom"])) { http_response_code(403); echo json_encode(['texte'=>'']); exit; }

include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");

$idpiecejointe = isset($_POST['idpiecejointe']) ? addslashes(htmlspecialchars(trim($_POST['idpiecejointe']))) : '';
if ($idpiecejointe === '') { echo json_encode(['texte'=>'']); exit; }

$cnx = cnx();
$sql = "SELECT nom, md5 FROM {$prefixe}piecejointe WHERE idpiecejointe='$idpiecejointe' AND etat='1' LIMIT 1";
$res = execSql($sql);
$data = chargeMat($res);

if (countTriade($data) == 0) { PgClose(); echo json_encode(['texte'=>'']); exit; }

$nom    = $data[0][0];
$md5    = $data[0][1];
$chemin = __DIR__ . '/data/DevoirScolaire/' . $md5;

if (!file_exists($chemin)) { PgClose(); echo json_encode(['texte'=>'']); exit; }

$ext   = strtolower(pathinfo($nom, PATHINFO_EXTENSION));
$texte = '';

if (in_array($ext, ['txt', 'html', 'htm'])) {
    $texte = file_get_contents($chemin);
    if ($ext !== 'txt') $texte = strip_tags($texte);

} elseif ($ext === 'pdf') {
    $tmp = sys_get_temp_dir() . '/triade_ia_' . $md5 . '.txt';
    shell_exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=txtwrite -sOutputFile=' . escapeshellarg($tmp) . ' ' . escapeshellarg($chemin) . ' 2>/dev/null');
    if (file_exists($tmp)) {
        $texte = file_get_contents($tmp);
        unlink($tmp);
    }

} elseif (in_array($ext, ['docx', 'odt', 'odp', 'ods'])) {
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($chemin) === true) {
            $xmlFile = ($ext === 'docx') ? 'word/document.xml' : 'content.xml';
            $xml = $zip->getFromName($xmlFile);
            $zip->close();
            if ($xml) {
                $xml   = preg_replace('/<\/[^>]+>/', ' ', $xml);
                $texte = strip_tags($xml);
                $texte = preg_replace('/\s+/', ' ', $texte);
            }
        }
    }
}

$texte = trim($texte);
if (mb_strlen($texte) > 4000) {
    $texte = mb_substr($texte, 0, 4000) . '...';
}

PgClose();
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['texte' => $texte, 'nom' => $nom], JSON_UNESCAPED_UNICODE);
?>
