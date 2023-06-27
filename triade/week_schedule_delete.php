<?php
session_start();


include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");

if(isset($_GET['eventToDeleteId'])){
	$cnx=cnx();
	delete_edt($_GET['eventToDeleteId']);
	Pgclose();
}

?>
