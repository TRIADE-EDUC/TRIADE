<?php
session_start();
if (($_SESSION["membre"] == "menueleve") ||  
    ($_SESSION["membre"] == "menuprof") ||
    ($_SESSION["membre"] == "menuscolaire") ||
    ($_SESSION["membre"] == "menututeur") ||
    ($_SESSION["membre"] == "menupersonnel") ||
    ($_SESSION["membre"] == "menuparent") ||
    ($_SESSION["membre"] == "menuadmin"))  {
	if ((isset($_POST["ideleve"]) && ($_POST["ideleve"] > 0) )) {
		include_once("./common/config.inc.php");
		include_once("./librairie_php/db_triade.php");
		$cnx=cnx();
		$ideleve=$_POST["ideleve"];
		$val=$_POST['val'];
		if ($_SESSION["membre"] == "menueleve") updateCompteEleve($ideleve,$val);
		if ($_SESSION["membre"] == "menuadmin") updateComptePers($ideleve,$val);
		if ($_SESSION["membre"] == "menuprof") updateComptePers($ideleve,$val);
		if ($_SESSION["membre"] == "menuscolaire") updateComptePers($ideleve,$val);
		if ($_SESSION["membre"] == "menututeur") updateComptePers($ideleve,$val);
		if ($_SESSION["membre"] == "menupersonnel") updateComptePers($ideleve,$val);
		if ($_SESSION["membre"] == "menuparent") updateCompteParent($ideleve,$val,$_SESSION["idparent"]);
		PgClose($cnx);
	}
}else{
	exit;
}
sleep(1);
?>
