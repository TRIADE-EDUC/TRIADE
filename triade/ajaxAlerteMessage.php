<?php
session_start();
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (isset($_POST["id"])) {
	$cnx=cnx();
	$cr=alerteMessage($_POST["id"]);
	Pgclose();
	if ($cr == 1) {
		print "Alerte enregistrée";
	}else{
		print "Alerte supprimée";
	}
}
sleep(1);
?>
