<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");
if (isset($_POST["idcodebarre"]) && ($_POST["idcodebarre"] != "NON-ACTIF")  && (trim($_POST["idcodebarre"]) != "") ) {
	$cnx=cnx();
	$data=recupInfoIdEdt(trim($_POST["idcodebarre"])); // date,heure,duree,idclasse,idprof,idmatiere
	$date=$data[0][0];
	$heure=$data[0][1];
	$duree=$data[0][2];
	$heure_fin=calcul_hours(conv_en_seconde($heure)+conv_en_seconde($duree));
	$idclasse=$data[0][3];
	$idprof=$data[0][4];
	$idmatiere=$data[0][5];
	$data2[0][0]=dateForm($date);
	$data2[0][1]=timeForm($heure);
	$data2[0][2]=timeForm($heure_fin);
	$data2[0][3]=$idmatiere;
	$matiere=chercheMatiereNom($idmatiere);
	$data2[0][4]=$matiere;
	$data2[0][5]=$idprof;
	$prof=recherche_personne_nom($idprof,'ENS')." ".recherche_personne_prenom($idprof,'ENS');
	$data2[0][6]=$prof;
	print dateForm($date)."#".timeForm($heure)."#".timeForm($heure_fin)."#$idmatiere#$matiere#$idprof#$prof";
//	print serialize($data2); //date,heure_debut,heure_fin,idmatiere,matiere,idprof,prof
	Pgclose();
}
?>
