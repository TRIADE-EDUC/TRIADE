<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$liste=$_POST["info"];
if (isset($_POST["identreprise"])) {
	$cnx=cnx();
	global $cnx;
	global $prefixe;
	$sql="SELECT pers_id, civ, nom, prenom, identifiant, offline FROM ${prefixe}personnel WHERE type_pers='TUT' AND id_societe_tuteur='".$_POST["identreprise"]."' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$data2=array();
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			$data2[$i][0]=$data[$i][0];	
			$data2[$i][1]=$data[$i][1];	
			$data2[$i][2]=sansaccent($data[$i][2]);	
			$data2[$i][3]=sansaccent($data[$i][3]);	
			$data2[$i][4]=$data[$i][4];	
			$data2[$i][5]=$data[$i][5];	
		}
		echo serialize($data2);
	}else{
		echo "";
	}
        PgClose($cnx);
}
sleep(1);
?>
