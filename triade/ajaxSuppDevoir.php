<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if ($_SESSION["membre"] != "menuprof") {
	print "pasok";
}else{
	if (isset($_POST["idDevoir"])) {
		$cnx=cnx();
		$cr=SuppDevoir($_POST["idDevoir"],$_POST["type"]);
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
