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

if (isset($_POST["idperiode"])) {
	$cnx=cnx();
	global $cnx;
	global $prefixe;
	$data=rechercheStageCentralSouhait2($_POST["idperiode"]); 
	// id,datedemande,identreprise,sexe,service,observation,nbdemande,nomentreprisen,s.adresse,s.ville,s.code_p,s.contact,s.tel,s.fax,s.email,s.info_plu,idproductreserv,null,salaire,logement
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
