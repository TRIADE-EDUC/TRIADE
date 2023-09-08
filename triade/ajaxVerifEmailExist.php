<?php
session_start();
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (isset($_POST["email"])) {
	$cnx=cnx();
	$cr=verifEmailExistEleve($_POST["email"],$_POST["idpers"]);
	Pgclose();
	if ($cr == 1) {
		print "nook";
	}else{
		print "ok";
	}
}
sleep(1);
?>