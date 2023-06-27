<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
if (file_exists("./common/config.inc.php")) {
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
}

if (file_exists("../common/config.inc.php")) {
	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
}

if (isset($_POST["idclasse"])) {
	$cnx=cnx();
	$trimestre=recupTrimestreNote($_POST["idclasse"],$_POST["date"]);
	PgClose($cnx);
	print "$trimestre";
}
sleep(1);
?>
