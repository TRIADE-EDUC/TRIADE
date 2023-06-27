<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if (!file_exists("./common/config.centralStage.php")) {
	$productid=$_POST["productid"];
	$p=$_POST["p"];
	$cnx=cnx();
	verifAccesCentrale("$productid","$p");
	Pgclose();
}else{
	session_start();
	if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }
}
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if (isset($_POST["idperiode"])) {
	$cnx=cnx();
	global $cnx;
	global $prefixe;
	$data=rechercheDateStageCentralSouhait2($_POST["idperiode"]); 
	//  nomstage,datedebut,datefin,id
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
