<?php
session_start();
error_reporting(0);

header('Content-Type: application/javascript; charset=utf-8');

if (empty($_SESSION['membre']) || ($_SESSION['membre'] !== 'menuadmin' && empty($_SESSION['admin1']))) {
    echo "var etat=0;";
    exit;
}

include_once("../common/crondump.inc.php");

if (!defined("BACKUPKEY")) {
    echo "var etat=0;";
    exit;
}

$ch = curl_init('https://support.triade-educ.org/support/crontab/verif.php?id=' . urlencode(BACKUPKEY));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
curl_close($ch);

echo ($response !== false && $response !== '') ? $response : 'var etat=0;';
?>
