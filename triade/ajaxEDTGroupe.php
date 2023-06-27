<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if (isset($_POST["idclasse"])) {
	$cnx=cnx();
	global $cnx;
	global $prefixe;
	$data2=chercheClasse($_POST["idclasse"]);
	$data=matGroup2($data2[0][1]);
	for($i=0;$i<count($data);$i++) {
		if (count($data) > 0) {
			echo serialize($data);
		}else{
			echo "";
		}
	}
       Pgclose();
}
sleep(1);
?>
