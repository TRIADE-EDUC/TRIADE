<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$idpiecejointe=$_POST["idpiecejointe"];
$cnx=cnx();
$ficmd5=$_POST["ficmd5"];
$number=$_POST["number"];
suppPieceJointe($ficmd5);
@unlink("./data/DevoirScolaire/$ficmd5");
$data=recupPieceJointe($idpiecejointe); //md5,nom,etat
print "&nbsp;&nbsp;";
for ($i=0;$i<count($data);$i++) {
	$ficName=$data[$i][1];
	$md5=$data[$i][0];
	$ficJ="./data/DevoirScolaire/".$data[$i][0];
	if (file_exists($ficJ)) {
		print "$ficName (<a href='accessfichierCahierText.php?id=$md5' target='_blank' title='Télécharger' ><img src='image/commun/download.png' border='0' /></a>";
		print " - <a href='#' title='Supprimer' onclick=\"suppPieceJointeCahierText$number$number('$md5','listingpiecejointe','$idpiecejointe','$number');\" >";
		print "<img src='image/commun/trash.png' border='0'  /></a> ";
		print ") - ";
	}
}
Pgclose();
sleep(1);
?>
