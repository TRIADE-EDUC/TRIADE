<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/recupnoteperiode.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {	set_time_limit(900);}
$cnx=cnx();

$tri=$_POST["saisie_trimestre"];
$idEleve=$_POST["saisie_eleve"];
$idclasse=$_POST["saisie_classe"];
$anneeScolaire=$_POST["anneeScolaire"];
// recherche des dates de debut et fin

$moyenClasseGenT1="";
$moyenClasseGenT2="";
$moyenClasseGenT3="";

function calculMoyenGeneralEleve($idClasse,$idEleveP,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;

	$ordre=ordre_matiere($idClasse);
	//$ordre=ordre_matiere_visubull_trim($idClasse,$tri,$anneeScolaire);
	$eleveT=recupEleve($idClasse); // recup liste eleve
	for($i=0;$i<count($ordre);$i++) {
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$tabmatiere[$num_ordre]="$num_ordre##$idMatiere";
	}

	//eleves
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$idEleve=$eleveT[$j][4];
		
		if ($idEleve != $idEleveP) continue; 
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			list($num_ordre,$idMatiere)=preg_split('/##/',$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre,$tri);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre);
			if ($verifGroupe) { 
		       		$noteaff="";
			}else{
				$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
			}
			// mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
			if ( $noteaff != "" ) {
			        $noteMoyEleGTempo = $noteaff * $coeffaff;
       	        		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		                $coefEleG=$coefEleG + $coeffaff;
			}
		}
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
		if (trim($moyenEleve) != "") {
			$moyenEleve=preg_replace('/,/','.',$moyenEleve);
		}
		$coefEleG="";
		$noteMoyEleG="";
	} // fin du for on passe à l'eleve suivant
        if ($moyenEleve != "") {
		$moyenClasseGen=number_format($moyenEleve,2,'.','');
	}else{
		$moyenClasseGen="";
	}	
	return $moyenClasseGen;
}


function calculMoyenGeneralEleveISMAPP($idClasse,$idEleveP,$dateDebut,$dateFin){
	global $cnx;
	global $prefixe;

//	$ordre=ordre_matiere($idClasse);
	$ordre=ordre_matiere_visubull_trim($idClasse,$tri,$anneeScolaire);
	$eleveT=recupEleve($idClasse); // recup liste eleve
	for($i=0;$i<count($ordre);$i++) {
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$tabmatiere[$num_ordre]="$num_ordre##$idMatiere";
	}

	//eleves
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$idEleve=$eleveT[$j][4];
		
		if ($idEleve != $idEleveP) continue; 
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			list($num_ordre,$idMatiere)=preg_split('/##/',$value);
			$idprof=recherche_prof($idMatiere,$idClasse,$num_ordre,$tri);
			$verifGroupe=verifMatiereAvecGroupe($idMatiere,$idEleve,$idClasse,$num_ordre);
			$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$num_ordre);

			$listeExamen=array("CC","DST","Dad","Soutenance","Rapport","Fiche de lecture","Exposé","Partiel","Lecture","Examen écrit","Recopiage vocabulaire","Mémoire Ip","Evaluation Tutorat");
			$epreuve="";
			$moyenneTT="";
			$coef="";
			foreach($listeExamen as $key=>$value) {
				if ($idgroupe == "0") {
					$noteaff=moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$value);
				}else{
					$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$value);
				}
				if (trim($noteaff) != "") {
					 if ($value == "CC") 	  { $valcoef="1"; }
					 if ($value == "DST") 	  { $valcoef="2"; }
					 if ($value == "Partiel") { $valcoef="3"; }
					 if ($value == "Soutenance") { $valcoef="3"; }
					 if ($value == "Rapport") { $valcoef="3"; }
					 if ($value == "Fiche de lecture") { $valcoef="2"; }
					 if ($value == "Exposé")  { $valcoef="1"; }
					 if ($value == "Dad")     { $valcoef="1"; }
					 if ($value == "Lecture") { $valcoef="3"; }
                                 	 if ($value == "Examen écrit")   { $valcoef="2"; }
                                	 if ($value == "Recopiage vocabulaire") { $valcoef="1"; }
					 if ($value == "Mémoire Ip")            { $valcoef="2"; }
	                                 if ($value == "Evaluation Tutorat")    { $valcoef="2"; }


					$moyenneTT+=$noteaff*$valcoef;
					$coef+=$valcoef;
				}
			}
			$noteaff=$moyenneTT/$coef;

			$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
                        if ( $noteaff != "" )  {
				$noteaff=number_format($noteaff,2,'.','');
                                $noteMoyEleGTempo = $noteaff * $coeffaff;
                                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                                $coefEleG=$coefEleG + $coeffaff;
                        }
		}
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
                if (trim($moyenEleve) != "") {
                        $moyenEleve=preg_replace('/,/','.',$moyenEleve);
                }
        } // fin du for on passe à l'eleve suivant

        $moyenClasseGen=number_format($moyenEleve,2,'.','');
        return($moyenClasseGen);
}


if ($tri == "trimestre1") {
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
       	 	$dateDebut=$dateRecup[$j][0];
	       	  $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT1=dateForm($dateDebut);
	$dateFinT1=dateForm($dateFin);
	//-----/
	if (ISMAPP == 1) {
		$moyenClasseGenT1=calculMoyenGeneralEleveISMAPP($idclasse,$idEleve,$dateDebutT1,$dateFinT1);
	}else{
		$moyenClasseGenT1=calculMoyenGeneralEleve($idclasse,$idEleve,$dateDebutT1,$dateFinT1);
	}
	if (($moyenClasseGenT1 == "") || ($moyenClasseGenT1 < 0)) { 
		$moyenClasseGenT1=""; 
	}else{
		if ($moyenClasseGenT1 < 10) { 
			print "<font color='red'>$moyenClasseGenT1</font>";
		}else{
			print "$moyenClasseGenT1";
		}
	}
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
	if (ISMAPP == 1) {
		$moyenClasseGenT2=calculMoyenGeneralEleveISMAPP($idclasse,$idEleve,$dateDebutT2,$dateFinT2);
	}else{
		$moyenClasseGenT2=calculMoyenGeneralEleve($idclasse,$idEleve,$dateDebutT2,$dateFinT2);
	}
        if (($moyenClasseGenT2 == "") || ($moyenClasseGenT2 < 0)) {
                $moyenClasseGenT2="";
        }else{
                if ($moyenClasseGenT2 < 10) {
                        print "<font color='red'>$moyenClasseGenT2</font>";
                }else{
                        print "$moyenClasseGenT2";
                }
        }
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
	if (ISMAPP == 1) {
		$moyenClasseGenT3=calculMoyenGeneralEleveISMAPP($idclasse,$idEleve,$dateDebutT3,$dateFinT3);
	}else{
		$moyenClasseGenT3=calculMoyenGeneralEleve($idclasse,$idEleve,$dateDebutT3,$dateFinT3);
	}
        if (($moyenClasseGenT3 == "") || ($moyenClasseGenT3 < 0)) {
                $moyenClasseGenT3="";
        }else{
                if ($moyenClasseGenT3 < 10) {
                        print "<font color='red'>$moyenClasseGenT3</font>";
                }else{
                        print "$moyenClasseGenT3";
                }
        }
}

	
Pgclose(); 

?>
