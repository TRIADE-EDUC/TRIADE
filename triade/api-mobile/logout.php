<?php
session_start();

/**
 * 
 *  Mettre les includes si nécessaire
 * 
 * 
 */

/* Rajouter d'autres scripts (de supression de données) dans la procédure de déconnexion si nécessaire */

session_set_cookie_params(0);
$_SESSION=array();
session_unset();
session_destroy();

header('Content-Type: application/json; charset=utf-8');
$message = ["code" => "200", "message" => "Déconnexion effectué avec succès"];
echo json_encode($message);
die();

?>
