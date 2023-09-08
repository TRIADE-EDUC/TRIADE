<?php
session_start();
error_reporting(0);

include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");

if ( ($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel") ) {
	$cnx=cnx();
	if ($_SESSION["membre"] == "menupersonnel") {
		if (!verifDroit($_SESSION["id_pers"],"cahiertextes")) {
			Pgclose();
			exit();
		}
	}
// ----------------------------------------------------------------------------------------------------------------
	if ($_SESSION["membre"] == "menuprof")  { $id_pers=$_SESSION["id_pers"];        }
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel"))  { 
			$id_pers=$_SESSION["idprofAdminCdT"];  
			if (isset($_SESSION["idprofviaadmin"])) $id_pers=$_SESSION["idprofviaadmin"]; 
	}

	if ($_POST["etape"] == "1") {
		$clsorgrp=0;
		$sClasseGrp=$_POST["sClasseGrp"];
		$sMat=$_POST["saisie_idmatiere"];
		$date=$_POST["date_contenu"];
		if ($_POST["saisie_clsorgrp"] != 0 ) { $clsorgrp=1 ; }
		$cr=create_cahiertexteContenu($_POST["saisie_idclsorgrp"],$_POST["saisie_idmatiere"],$_POST["saisie_date"],$_POST["date_contenu"],$clsorgrp,$_POST["number"],$id_pers,filtreCopierColler($_POST["saisie_contenu"]),$_POST["saisie_clsorgrp"]);
		if($cr == 1){
			if ($clsorgrp == 0) {
				$info=chercheClasse_nom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}else{
				$info=chercheGroupeNom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}
			history_cmd($_SESSION["nom"],"Cahier de texte","AJOUT $info");
			print "ok";
		}	
	}
// ----------------------------------------------------------------------------------------------------------------
	if ($_POST["etape"] == "2" ) {
		$clsorgrp=0;
		$sClasseGrp=$_POST["sClasseGrp"];
		$sMat=$_POST["saisie_idmatiere"];
		$date=$_POST["date_contenu"];
		if ($_POST["saisie_clsorgrp"] != 0 ) { $clsorgrp=1 ; }
		$cr=create_cahiertexteObjectif($_POST["saisie_idclsorgrp"],$_POST["saisie_idmatiere"],$_POST["saisie_date"],$_POST["date_contenu"],$clsorgrp,$_POST["number"],$id_pers,filtreCopierColler($_POST["saisie_contenu"]),$_POST["saisie_clsorgrp"]);
		if($cr == 1){
			if ($clsorgrp == 0) {
				$info=chercheClasse_nom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}else{
				$info=chercheGroupeNom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}
			history_cmd($_SESSION["nom"],"Cahier de texte","AJOUT $info");
			print "ok";
		}
	}
// ----------------------------------------------------------------------------------------------------------------	
	if ($_POST["etape"] == "3") {
		$clsorgrp=0;
		$sClasseGrp=$_POST["sClasseGrp"];
		$sMat=$_POST["saisie_idmatiere"];
		$date=$_POST["date_contenu"];
		$tempsestime=$_POST["tempsestime"];
		if ($_POST["saisie_clsorgrp"] != 0 ) { $clsorgrp=1 ; }
		$cr=create_devoirscolaire($_POST["saisie_idclsorgrp"],$_POST["saisie_idmatiere"],date("d/m/Y"),$_POST["date_devoir"],filtreCopierColler($_POST["saisie_contenu"],"$tag"),$clsorgrp,$_POST["number"],$id_pers,$_POST["tempsestime"],$_POST["saisie_clsorgrp"]);	
		if($cr == 1){
			if ($clsorgrp == 0) {
				$info=chercheClasse_nom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}else{
				$info=chercheGroupeNom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}
			history_cmd($_SESSION["nom"],"Cahier de texte","AJOUT $info");
			print "ok";
		}
		
	}
// ----------------------------------------------------------------------------------------------------------------
	if ($_POST["etape"] == "4") {	
		$clsorgrp=0;
		$sClasseGrp=$_POST["sClasseGrp"];
		$sMat=$_POST["saisie_idmatiere"];
		$date=$_POST["date_contenu"];
		if ($_POST["saisie_clsorgrp"] != 0 ) { $clsorgrp=1 ; }
		$cr=create_cahiertexteBlocNote($_POST["saisie_idclsorgrp"],$_POST["saisie_idmatiere"],$_POST["date_contenu"],$clsorgrp,$id_pers,filtreCopierColler($_POST["saisie_contenu"]));

		if($cr == 1){
			if ($clsorgrp == 0) {
				$info=chercheClasse_nom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}else{
				$info=chercheGroupeNom($_POST["saisie_idclsorgrp"])." ".chercheMatiereNom($_POST["saisie_idmatiere"]);
			}
			history_cmd($_SESSION["nom"],"Cahier de texte","AJOUT $info");
			print "ok";
		}
	}		
// ----------------------------------------------------------------------------------------------------------------
	Pgclose();
}
sleep(1);
?>
