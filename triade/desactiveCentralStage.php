<?php
session_start();
if ($_SESSION["membre"] == "menuadmin") {
	@unlink("./common/config.centralStage.php");
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();
	suppAffiliationCentralStage();
	pgClose();
}
sleep(1);
?>
