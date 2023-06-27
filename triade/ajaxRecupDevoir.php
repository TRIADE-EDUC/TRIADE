<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (isset($_POST["idDevoir"])) {
	$cnx=cnx();
	$cr=RechercheCommentaireCahiertexte($_POST["idDevoir"],$_POST["type"]);
	print "$cr";
	PgClose($cnx);
}else{
	print "";
}
sleep(1);
?>
