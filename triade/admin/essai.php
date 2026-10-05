<?php

require_once __DIR__ . '/librairie_php/pclzip.lib.php';

$zipFile = __DIR__ . '/patch.zip';

if (!file_exists($zipFile)) {
    die('ZIP introuvable');
}

$targetDir = __DIR__ . '/../data/patch';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$archive = new PclZip($zipFile);

$result = $archive->extract(
    PCLZIP_OPT_PATH,
    $targetDir
);

if ($result === 0) {
    echo 'Erreur ZIP (' . $archive->errorCode() . ') : ';
    echo $archive->errorInfo(true);
} else {
    echo 'Extraction OK : ' . count($result) . ' fichiers';
}

