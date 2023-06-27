<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] != "menuadmin") {
	print "pasok";
}else{
	if (isset($_POST["idclasse"])) {
		$cnx=cnx();
		// idclasse, idmatiere, datedebut, datefin
		$cr=enrVisaCahierdeText($_POST["idclasse"],$_POST["idmatiere"],$_POST["datedebut"],$_POST["datefin"],$_POST["classorgrp"],$_POST["idprof"]);
		if ($cr) {
			print "ok";
		}else{
			print "pasok";
		}
		PgClose($cnx);
	}else{
		print "pasok";
	}
}
sleep(1);
?>
