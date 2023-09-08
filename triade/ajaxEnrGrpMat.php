<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuadmin") {
	if (isset($_POST["idclasse"])) {
		$cnx=cnx();
		$cr=enrGrpMat($_POST["idclasse"],$_POST["listeMatiere"],utf8_decode($_POST["nomgrp"]),$_POST["leap"]);	
		if ($cr) {
			$nomclasse=chercheClasse_nom($_POST["idclasse"]);
			history_cmd($_SESSION["nom"],"ENREGISTREMENT","Groupement LEAP Matière classe $nomclasse ");
			print "ok";
		}else{
			print "pasok";
		}
		Pgclose();
	}else{
		print "";
	}
}else{
	print "pasok";
}
sleep(1);
?>
