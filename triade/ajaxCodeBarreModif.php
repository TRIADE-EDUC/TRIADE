<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	if (isset($_POST["idpers"])) {
		$cnx=cnx();
		ModifCodeBar($_POST["idpers"],$_POST["membre"],$_POST["code"]);
		print "Code validé";
		PgClose($cnx);
	}
}else{
	print "Code non validé";
}
sleep(1);
?>
