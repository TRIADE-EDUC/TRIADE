<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$element=$_POST["element"];
$id=$_POST["id"];
$value=$_POST["value"];
$cnx=cnx();
if ($id > 0) {
	if ($element == "d") {
		$value=dateFormBase($value);
		$sql="UPDATE ${prefixe}absrtdrattrapage SET date='$value' WHERE id='$id'";
		execSql($sql);
		print dateForm($value);
	}
	if ($element == "h") {
		$sql="UPDATE ${prefixe}absrtdrattrapage SET heure_depart='$value' WHERE id='$id'";
		execSql($sql);
		print timeForm($value);
	}
	if ($element == "t") {
		$sql="UPDATE ${prefixe}absrtdrattrapage SET duree='$value' WHERE id='$id'";
		execSql($sql);
		print timeForm($value);
	}
}else{
	print "erreur";
}
Pgclose();
sleep(1);
?>
