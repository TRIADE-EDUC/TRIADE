<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if (isset($_POST["id"])) {
	$cnx=cnx();
	$data=cherchePassageCDI($_POST["id"]); // id,libelle
	PgClose();
	if (countTriade($data) > 0) {
		for($i=0;$i<countTriade($data);$i++) {
			$id=$data[$i][0];
			$libelle=urlencode($data[$i][1]);
			$data2[$i][0]=$id;
			$data2[$i][1]=rawurlencode($libelle);
		}
		echo serialize($data2);
	}else{
		echo "";
	}
}else{
	print "";
}
exit;
?>
