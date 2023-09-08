<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (isset($_POST["idcodebarre"]) && ($_POST["idcodebarre"] != "NON-ACTIF")  && (trim($_POST["idcodebarre"]) != "") ) {
	$cnx=cnx();
	$data=rechercheIdEleveViaCodeBarre(trim($_POST["idcodebarre"])); // id,valide
	$ideleve=$data[0][0];
	$valide=$data[0][1];
	if ($valide == 1) {
		$cr=rechercheEleveNomPrenom($ideleve);
		PgClose($cnx);
		print "$ideleve:$cr";	
	}elseif ($valide == 0) {
		$cr=rechercheEleveNomPrenom($ideleve);
		Pgclose();
		print "ERROR:$cr";
	}else{	
		print "???:???";
	}
}else{
	print "???:???";
}
?>
