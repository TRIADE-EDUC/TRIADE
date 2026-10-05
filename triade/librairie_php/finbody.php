<?php
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("db_triade.php");
if (!isset($verif)) $verif="1";
if (($verif == 0) && (LAN == "oui")) {
	$cnx=cnx();
	$idref=recupcomptegoogleanalytic();
	if (trim($idref) != "") {
		include_once("googleanalyse.php");
		googleanalyse($idref);
	}
	PgClose();
}
?>
