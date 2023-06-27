<?php
session_start();
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]))) { exit; }
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$idclasse=$_POST["idclasse"];
if ($idclasse != "") {
	$sql="SELECT elev_id,nom,prenom FROM ${prefixe}eleves  WHERE classe='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (count($data) == 0) $data=array(); 
	print (serialize($data));

}
PgClose($cnx);
?>
