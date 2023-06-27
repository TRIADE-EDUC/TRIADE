<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
$id=$_POST["id"];
$heure=$_POST["heure"];
$duree=$_POST["duree"];
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

if ((trim($heure) == "") && (trim($duree) == "")){
	$cr=edtSuppHoraire($id);
	if ($cr) { // si 1 OK
		$affiche=utf8_encode("&nbsp;<i>Suppression effectu&eacute;e</i>)");
	}else{
		$affiche=utf8_encode("&nbsp;<i>Horaire non conforme</i>");
	}
}else{
	edtUpdateHoraire($id,$heure,$duree); 
	$cr=verifHoraire($id,$heure,$duree);
	
	if ($cr) { // si 1 OK
		$affiche=utf8_encode("&nbsp;<i>Modification effectu&eacute;e</i>");
	}else{
		$affiche=utf8_encode("&nbsp;<i>Horaire non conforme</i>");
	}
}
sleep(1);
print $affiche;
?>
