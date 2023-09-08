<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuadmin") {
	$cnx=cnx();
	suppIdClasseSMS();
	$data=recupClassSMSnonConfig();
	print "<i>";
	foreach($data as $key=>$value) {
		if ($value != "") print chercheClasse_nom($value).", ";
	}
	print "</i>";
	Pgclose();
}

sleep(1);
?>
