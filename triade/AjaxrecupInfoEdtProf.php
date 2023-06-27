<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) { exit; }

error_reporting(0);
$idprof=$_POST["idprof"];
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$affiche="<table border=1 bgcolor='#FFFFFF' bordercolor='#000000' width='100%' height='100%' ><td id='bordure' valign='top' ><font class='T1'><b>".ucwords(recherche_personne_nom($idprof,'ENS'))." ".ucwords(recherche_personne_prenom($idprof,'ENS'))."</b><br><br>";
$data=recupCommandeVacation($idprof); // nbheure,idmatiere,type_prestation,idclasse,id
list($annee0,$annee1)=preg_split('/-/',anneeScolaire());
$dateDebut=trim($annee0)."-09-01";
$dateFin=trim($annee1)."-08-31";
$aff=0;
for ($i=0;$i<count($data);$i++) {
	if ($data[$i][0] == "-1") continue;
	$aff=1;
	$idmatiere=$data[$i][1];
	$idclasse=$data[$i][3];
	$classe=chercheClasse_nom($idclasse);
	$matiere=chercheMatiereNom($idmatiere);
	$nbsecondeplanifier=0;
	$tabnbheureplanifier=nbHeureVacationMatiereParDate($idprof,$idmatiere,$dateDebut,$dateFin,$idclasse); // hh:mm:ss
	for($j=0;$j<count($tabnbheureplanifier);$j++) {
		$nbheureplanifier=$tabnbheureplanifier[$j][0];
		$nbsecondeplanifier+=conv_en_seconde($nbheureplanifier);
	}
	$nbsecondecommander=conv_en_seconde($data[$i][0].":00:00");
	$nbseconde=$nbsecondecommander-$nbsecondeplanifier;
	$nbheure=calcul_hours2($nbseconde);
	// id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,type_prestation,idmatiere,coursannule
	$affiche.="$classe : $matiere $nbheure à planifier <br><br>";
}
$affiche.="</font></td></tr></table>";
sleep(1);
if ($aff == 1) { print $affiche; }else{ print ""; }
?>
