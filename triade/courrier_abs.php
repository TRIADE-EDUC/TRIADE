<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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

// recupe du nom de la classe
if (isset($_POST["idclasse"])) {
	$data=chercheClasse($_POST["idclasse"]);
	$classeNom=$data[0][1];
	$idClasse=$_POST["idclasse"];
	$laclasse=1;
	$fic="classe".$idclasse;
}

if (isset($_GET["eid"])) {
	$ideleve=$_GET["eid"];
	$nomEleve=trim(strtoupper(recherche_eleve_nom($ideleve)));
	$prenomEleve=trim(ucwords(strtolower(recherche_eleve_prenom($ideleve))));
	$idclasse=chercheIdClasseDunEleve($ideleve);
	$data=chercheClasse($idclasse);
	$classeNom=$data[0][1];
	$dateNaissanceEleve=dateForm(chercheDateNaissance($ideleve));
	$eleve=$ideleve."-".$idclasse;
	$laclasse=0;
}

// recuperation des coordonnées
// de l etablissement
$data=visu_param();
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
}
// fin de la recup


$fic="./data/parametrage/certificat_eleve_".$_SESSION["id_pers"].".rtf";
@unlink("$fic");

$TempFilename="./data/parametrage/certificat.rtf";
$fichier=fopen($TempFilename,"r");
$longueur=9000000;
$data=fread($fichier,$longueur);
fclose($fichier);

$data=preg_replace("/NomEleve/",$nomEleve,$data);
$data=preg_replace("/PrenomEleve/",$prenomEleve,$data);
$data=preg_replace("/ClasseEleve/",$classeNom,$data);
$data=preg_replace("/DateNaissanceEleve/","$dateNaissanceEleve",$data);

$fichier=fopen("$fic","a");
fwrite($fichier,$data);
fclose($fichier);

Header('Content-Type: application/msword');
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
