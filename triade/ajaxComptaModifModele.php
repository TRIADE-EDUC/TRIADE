<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");
if ($_SESSION["membre"] == "menuadmin") {
	if (isset($_POST["id"])) {
		$cnx=cnx();	
		modifComptaModele($_POST["id"],utf8_decode($_POST["libelle"]),$_POST["montant"],$_POST["date"]);
		Pgclose();
	}

	if (isset($_POST["idsupp"])) {
		$cnx=cnx();
		suppComptaModele($_POST["idsupp"]);
		Pgclose();
	}
}
print "";
sleep(1);
?>
