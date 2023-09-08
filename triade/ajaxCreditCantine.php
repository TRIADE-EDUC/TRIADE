<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
	if (isset($_POST["idpers"])) {

		//idpers&membre&date&credit&detail,
		$cr=creditCantine($_POST["idpers"],$_POST["membre"],utf8_decode($_POST["detail"]),utf8_decode($_POST["date"]),$_POST["credit"]);
		if ($cr) {
			$idpers=$_POST["idpers"];
			$membre=$_POST["membre"];
			$credit=$_POST["credit"];
			history_cmd($_SESSION["nom"],"CANTINE","Credit $credit  ($idpers) - $membre");
			print "ok";
		}else{
			print "pasok";
		}
	
	}else{
		print "pasok";
	}
}else{
	print "pasok";
}
Pgclose();
sleep(1);
?>
