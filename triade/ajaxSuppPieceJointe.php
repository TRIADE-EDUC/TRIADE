<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$idpiecejointe=$_POST["idpiecejointe"];
$cnx=cnx();
$ficmd5=$_POST["ficmd5"];
suppPieceJointe($ficmd5);
@unlink("./data/fichiersj/$ficmd5");
$data=recupPieceJointe($idpiecejointe); //md5,nom,etat
print "&nbsp;&nbsp;";
for ($i=0;$i<count($data);$i++) {
	$ficName=$data[$i][1];
	$md5=$data[$i][0];
	$ficJ="./data/fichiersj/".$data[$i][0];
	if (file_exists($ficJ)) {
		print "$ficName (<a href='accessfichier.php?id=$md5' target='_blank' title='Télécharger' ><img src='image/commun/download.png' border='0' /></a>";
		print " - <a href='#' title='Supprimer' onclick=\"suppPieceJointe('$md5','listingpiecejointe','$idpiecejointe');\" >";
		print "<img src='image/commun/trash.png' border='0'  /></a> ";
		print ") - ";
	}
}
Pgclose();
sleep(1);
?>
