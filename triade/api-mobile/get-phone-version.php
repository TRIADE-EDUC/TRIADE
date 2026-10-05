<?php
/*
 * get-phone-version.php — Public (sans auth, sans session, sans DB)
 * Retourne la version de l'API TRIADE-PHONE attendue par ce serveur.
 */
include_once("../common/config-phone.php");

header('Content-Type: application/json; charset=utf-8');
echo json_encode(["version" => constant("API-PHONE-VERSION")]);
?>
