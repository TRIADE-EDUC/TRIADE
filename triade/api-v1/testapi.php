<?php
session_start();
$_SESSION['api_access'] = true;
$_SESSION['api_client'] = 'test';
header("Location: http://dev.triade-v4.net/triade/api-v1/get-edt.php?idclasse=4&date_debut=2026-06-01&date_fin=2026-06-20");
?>