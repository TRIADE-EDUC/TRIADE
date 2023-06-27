<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/recupnoteperiode.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {	set_time_limit(300);}
$cnx=cnx();

$tri=$_POST["saisie_trimestre"];
$idclasse=$_POST["saisie_classe"];
$anneeScolaire=$_POST["anneeScolaire"];
// recherche des dates de debut et fin
//$ordre=ordre_matiere($idclasse); // recup ordre matiere
$ordre=ordre_matiere_visubull_trim($idclasse,$tri,$anneeScolaire);
$eleveT=recupEleve($idclasse); // recup liste eleve
$moyenClasseGenT1="";
$moyenClasseGenT2="";
$moyenClasseGenT3="";

if ($tri == "trimestre1") {
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
       	 	$dateDebut=$dateRecup[$j][0];
	       	 $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT1=dateForm($dateDebut);
	$dateFinT1=dateForm($dateFin);
	//-----/
	$moyenClasseGenT1=calculMoyenClasse($idclasse,$eleveT,$dateDebutT1,$dateFinT1,$ordre);
	if (($moyenClasseGenT1 == "") || ($moyenClasseGenT1 < 0)) { $moyenClasseGenT1=""; }
	print "$moyenClasseGenT1";
}

if ($tri == "trimestre2") {
	$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
	       	 $dateDebut=$dateRecup[$j][0];
        	$dateFin=$dateRecup[$j][1];	
	}
	$dateDebutT2=dateForm($dateDebut);
	$dateFinT2=dateForm($dateFin);
	//-----/
	$moyenClasseGenT2=calculMoyenClasse($idclasse,$eleveT,$dateDebutT2,$dateFinT2,$ordre);
	if (($moyenClasseGenT2 == "") || ($moyenClasseGenT2 < 0))  {$moyenClasseGenT2=""; }
	print "$moyenClasseGenT2";
}
	
if ($tri == "trimestre3") {
	$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
        	$dateDebut=$dateRecup[$j][0];
	        $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT3=dateForm($dateDebut);
	$dateFinT3=dateForm($dateFin);
	//-----/
	// idclasse,tableaueleve,datedebut,datefin,ordrematriere
	$moyenClasseGenT3=calculMoyenClasse($idclasse,$eleveT,$dateDebutT3,$dateFinT3,$ordre);
	if (($moyenClasseGenT3 == "") || ($moyenClasseGenT3 < 0)) {$moyenClasseGenT3=""; }
	print "$moyenClasseGenT3";
}

	
Pgclose(); 

?>
