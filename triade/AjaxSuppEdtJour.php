<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }
if ($_SESSION["membre"] != "menuadmin") { exit; } 

error_reporting(0);

$date=$_POST["date"];
$idclasse=$_POST["classe"];
$idressource=$_POST["idressource"];
$idprof=$_POST["enseignant"];
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
suppJourneeEdt($date,$idclasse,$idressource,$idprof);

?>
