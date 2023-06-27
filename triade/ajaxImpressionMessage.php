<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if ( ($_SESSION["membre"] == "menuadmin") ||  ($_SESSION["membre"] == "menuscolaire")) {
	if (isset($_POST["id"])) {
		$cnx=cnx();
		imprMessage($_POST["id"]);
		Pgclose();
	}
}
print "";
sleep(1);
?>
