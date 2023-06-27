<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
include_once("./common/config.inc.php");
include_once('librairie_php/db_triade.php');
include_once("./common/config2.inc.php");
$cnx=cnx();

$fic="./data/parametrage/certificat_eleve_".$_SESSION["id_pers"].".rtf";
@unlink("$fic");

// recupe du nom de la classe
if (isset($_POST["idclasse"])) {
	$data=chercheClasse($_POST["idclasse"]);
	$classeNom=$data[0][1];
	$idClasse=$_POST["idclasse"];
	$laclasse=1;
	$fic="classe".$idclasse;
	$num_certif=$_POST["num_certif"];
}

if (isset($_GET["eid"])) {
	$ideleve=$_GET["eid"];
	$nomEleve=trim(strtoupper(recherche_eleve_nom($ideleve)));
	$prenomEleve=trim(ucwords(strtolower(recherche_eleve_prenom($ideleve))));
	$idclasse=chercheIdClasseDunEleve($ideleve);
	$data=chercheClasse($idclasse);
	$dateNaissanceEleve=dateForm(chercheDateNaissance($ideleve));
	$classeNom=$data[0][1];
	$classeNomLong=$data[0][2];
	$eleve=$ideleve."-".$idclasse;
	$laclasse=0;
	$AdresseEleve=rechercheAdresseEleve($ideleve);
	$CodePostalEleve=rechercheCodePostalEleve($ideleve);
	$VilleEleve=rechercheVilleEleve($ideleve);
	$LieuDeNaissance=rechercheLieuNaissanceEleve($ideleve);
	$Nationalite=rechercheNationaliteEleve($ideleve);	
	$num_certif=$_GET["num_certif"];
}



$TempFilename="./data/parametrage/certificat$num_certif.rtf";
$fichier=fopen($TempFilename,"r");
$longueur=9000000;
$data=fread($fichier,$longueur);
fclose($fichier);

$paramScolaire=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire
$anneeScolaire=$paramScolaire[0][11];
$datedujour=dateDMY();

$data=preg_replace('/NomEleve/',$nomEleve,$data);
$data=preg_replace('/PrenomEleve/',$prenomEleve,$data);
$data=preg_replace('/ClasseEleveLong/',$classeNomLong,$data);
$data=preg_replace('/ClasseEleve/',$classeNom,$data);
$data=preg_replace('/DateNaissanceEleve/',"$dateNaissanceEleve",$data);
$data=preg_replace('/AdresseEleve/',"$AdresseEleve",$data);
$data=preg_replace('/CodePostalEleve/',"$CodePostalEleve",$data);
$data=preg_replace('/VilleEleve/',ucwords($VilleEleve),$data);
$data=preg_replace('/LieuDeNaissance/',ucwords($LieuDeNaissance),$data);
$data=preg_replace('/DateDuJour/',$datedujour,$data);
$data=preg_replace('/AnneeScolaire/',$anneeScolaire,$data);
$data=preg_replace('/Nationalite/',$Nationalite,$data);

$fichier=fopen("$fic","a");
fwrite($fichier,$data);
fclose($fichier);

header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename='.$fic);   
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile($fic); 

Pgclose();
?>
