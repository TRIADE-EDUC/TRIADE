<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$liste=$_POST["info"];
if (isset($_POST["identreprise"])) {
        $cnx=cnx();
        $data=recupInfoEntreprise($_POST["identreprise"]);
        // id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent
        PgClose($cnx);
	if ($_POST["info"] == "nom") {
		if (VATEL == 1) { 
			$liste=utf8_encode($data[0][1]);
		}else{
		       $liste=$data[0][1];
		}
        }
        if ($_POST["info"] == "lieu") {
		if (VATEL == 1) { 
			$liste=utf8_encode($data[0][3]);
		}else{
		       $liste=$data[0][3];
		}
        }
	if ($_POST["info"] == "ville") {
		if (VATEL == 1) {
			$liste=utf8_encode($data[0][5]);
		}else{
			$liste=$data[0][5];
		}
        }
	if ($_POST["info"] == "postal") { $liste=$data[0][4]; }
	if ($_POST["info"] == "pays") {
		if (VATEL == 1) {
	               $liste=utf8_encode($data[0][14]);
		}else{
		       $liste=$data[0][14];
		}
        }
 	if ($_POST["info"] == "responsable") {
		if (VATEL == 1) {
	                $liste=utf8_encode($data[0][2]);
		}else{
	                $liste=$data[0][2];
		}
        }
        if ($_POST["info"] == "tel") { $liste=$data[0][8]; }
        if ($_POST["info"] == "fax") { $liste=$data[0][9]; }
}
print $liste;
sleep(1);
?>
