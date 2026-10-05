<?php
session_start();
ob_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
include_once("./common/config2.inc.php");
include_once("./librairie_php/lib_get_init.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();

// Centralisation des variables POST — évite les Warnings PHP 8 (undefined array key)
$saisie_classe  = $_POST['saisie_classe']  ?? '';
$annee_scolaire = $_POST['annee_scolaire'] ?? '';
$examen         = trim($_POST['NoteExam']  ?? '');
$affrang        = $_POST['affrang']        ?? '0';
$noteexamen     = $_POST['noteexamen']     ?? '';
$affcolvide     = $_POST['affcolvide']     ?? 1;

if ($annee_scolaire !== '') {
	$anneeScolaire = $annee_scolaire;
	setcookie("anneeScolaire", $anneeScolaire, time()+36000*24*30);
} else {
	$anneeScolaire = '';
}

$id = php_ini_get("safe_mode");
if ($id != 1) { set_time_limit(900); }
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"imprtableau")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("3");
}
$valeur = visu_affectation_detail($saisie_classe, '');
if ((EXAMENJTC != "oui") && (MODNAMUR0 == "oui")) {
	$recupInfo      = recupCaractVieScolaire($saisie_classe);
	$persVieScolaire= $recupInfo[0][4];
	$coefBull       = $recupInfo[0][1];
	$coefProf       = $recupInfo[0][2];
	$coefVieScol    = $recupInfo[0][3];
}

// Compatibilité PHP 7/8 pour utf8_decode (deprecated PHP 8.2)
if (!function_exists('_xls_encode')) {
	function _xls_encode($str) {
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
		}
		return utf8_decode($str);
	}
}

if (count($valeur)) {
	// recupe du nom de la classe
	$data      = chercheClasse($saisie_classe);
	$classe_nom= trim($data[0][1]);
	// recup année scolaire
	$anneeScolaire = $annee_scolaire;
	include_once('librairie_php/recupnoteperiode.php');
	// recherche des dates de debut et fin
	$dateRecup = recupDateTrimByIdclasse("trimestre1", $saisie_classe);
	for ($j=0; $j<count($dateRecup); $j++) {
		$dateDebut = $dateRecup[$j][0];
		$dateFin   = $dateRecup[$j][1];
	}
	$T1dateDebut = dateForm($dateDebut);
	$T1dateFin   = dateForm($dateFin);
	$dateRecup = recupDateTrimByIdclasse("trimestre2", $saisie_classe);
	for ($j=0; $j<count($dateRecup); $j++) {
		$dateDebut = $dateRecup[$j][0];
		$dateFin   = $dateRecup[$j][1];
	}
	$T2dateDebut = dateForm($dateDebut);
	$T2dateFin   = dateForm($dateFin);
	$dateRecup = recupDateTrimByIdclasse("trimestre3", $saisie_classe);
	for ($j=0; $j<count($dateRecup); $j++) {
		$dateDebut = $dateRecup[$j][0];
		$dateFin   = $dateRecup[$j][1];
	}
	$T3dateDebut = dateForm($dateDebut);
	$T3dateFin   = dateForm($dateFin);
	$idClasse = $saisie_classe;
	$ordre    = ordre_matiere_visubull($saisie_classe, $anneeScolaire);

	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";
	$fichier  = "recap_moyen_".$_SESSION["id_pers"].".xls";
	$fname    = tempnam("./data/tmp", "$fichier");
	$workbook = new writeexcel_workbook($fname);
	$worksheet1 = $workbook->addworksheet('Listing');
	$worksheet2 = $workbook->addworksheet('Nbr de notes');
	$header = $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');

	$header2 = $workbook->addformat();
	$header2->set_color('white');
	$header2->set_align('center');
	$header2->set_align('vcenter');
	$header2->set_pattern();
	$header2->set_merge();
	$header2->set_fg_color('blue');

	$separateur = $workbook->addformat();
	$separateur->set_color('black');
	$separateur->set_align('center');
	$separateur->set_align('vcenter');
	$separateur->set_pattern();
	$separateur->set_fg_color('blue');

	$center = $workbook->addformat();
	$center->set_align('left');

	$centerrouge = $workbook->addformat();
	$centerrouge->set_color('red');
	$centerrouge->set_align('left');
	#
	# Sheet 1
	#
	$worksheet1->set_selection('A0');
	$nbeleve         = 0;
	$noteMoyEleG     = 0;
	$coefEleG        = 0;
	$coefscolaireG   = 0;
	$nbMat           = 0;
	$nbmatiere       = count($ordre);
	$nbaffichematiere= count($ordre);
	for ($i=0; $i<count($ordre); $i++) {
		$matiere      = chercheMatiereNom($ordre[$i][0]);
		$idMatiere    = $ordre[$i][0];
		$num_ordre    = $ordre[$i][2];
		$idgroupe     = verifMatierAvecGroupeRecupId2($idMatiere, $idClasse, $ordre[$i][2]);
		$datasousmatiere = verifsousmatierebull($idMatiere);
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale = $datasousmatiere[0][2];
			$nomSousMatiere       = $datasousmatiere[0][1];
		}
		if (trim($matiere) != "") { $tabmatiere2[$num_ordre]="$matiere##$num_ordre##$idMatiere"; }
		$nbMat++;
	}
	if ($nbaffichematiere >= $nbMat) {
		$afficheMoyen = 1;
	} else {
		$afficheMoyen = 0;
	}
	if ($nbMat >= 90) { $nbaffichematiere = 22; }
	while (count($ordre)) {
		$ju = 0;
		unset($tabmatiere);
		$eleveT = recupEleve($saisie_classe);
		// noms matières
		for ($i=0; $i<$nbaffichematiere; $i++) {
			$matiere   = chercheMatiereNom($ordre[$i][0]);
			$idMatiere = $ordre[$i][0];
			$num_ordre = $ordre[$i][2];
			$idgroupe  = verifMatierAvecGroupeRecupId2($idMatiere, $idClasse, $ordre[$i][2]);
			$datasousmatiere = verifsousmatierebull($idMatiere);
			if ($datasousmatiere != "0") {
				$nomMatierePrincipale = $datasousmatiere[0][2];
				$nomSousMatiere       = $datasousmatiere[0][1];
			}
			if (trim($idMatiere) == "") { break; }
			if (trim($matiere) != "") { $tabmatiere[$num_ordre]="$matiere##$num_ordre##$idMatiere"; }
		}

		$jo = 0;
		$worksheet1->write(0, $jo, _xls_encode("Nom Prénom"), $header);
		$worksheet2->write(0, $jo, "Classe", $header);
		$jo++;
		$worksheet1->write(0, $jo, _xls_encode("Période"), $header);
		$jo++;

		$nbp = 1;
		$nbMatiere = count($tabmatiere);
		$tabmatiere4 = $tabmatiere;
		$worksheet2->write(0, $nbp, "TRIMESTRE 1", $header2);
		foreach ($tabmatiere as $key => $nom_matiere) {
			$nbp++;
			list($nom_matiere,$num_ordre,$idMatiere,$nomSousMatiere) = preg_split("/##/", $nom_matiere);
			$worksheet2->write_blank(0, $nbp, $header2);
			$worksheet2->write(1, $nbp-1, "$nom_matiere", $header2);
		}
		$worksheet2->write(0, $nbp, "TRIMESTRE 2", $header2);
		foreach ($tabmatiere as $key => $nom_matiere) {
			$nbp++;
			list($nom_matiere,$num_ordre,$idMatiere,$nomSousMatiere) = preg_split("/##/", $nom_matiere);
			$worksheet2->write_blank(0, $nbp, $header2);
			$worksheet2->write(1, $nbp-1, "$nom_matiere", $header2);
		}
		$worksheet2->write(0, $nbp, "TRIMESTRE 3", $header2);
		foreach ($tabmatiere as $key => $nom_matiere) {
			$nbp++;
			list($nom_matiere,$num_ordre,$idMatiere,$nomSousMatiere) = preg_split("/##/", $nom_matiere);
			$worksheet2->write_blank(0, $nbp, $header2);
			$worksheet2->write(1, $nbp-1, "$nom_matiere", $header2);
		}
		$nbmat0 = 0;
		foreach ($tabmatiere4 as $key => $nom_matiere) {
			list($nom_matiere,$num_ordre,$idMatiere,$nomSousMatiere) = preg_split("/##/", $nom_matiere);
			$coeffaff = recupCoeff($idMatiere, $idClasse, $num_ordre);
			if ($examen != "") {
				$coeffaffEx = recup_coef_bulletin("bull0101b", $idClasse, $idMatiere, $num_ordre);
				if ($coeffaffEx != "") $coeffaff = $coeffaffEx;
			}
			if (trim($nomSousMatiere ?? '') != "") {
				$nom_matiere = _xls_encode(ucfirst($nom_matiere)." ".$nomSousMatiere);
			}
			$nomSousMatiere = "";
			$worksheet1->write(0, $jo, _xls_encode("$nom_matiere ($coeffaff)"), $header);
			$jo++;
			$nbmat0++;
		}
		$nbmat0 += 3;
		if ($affrang == "1") { $nbmat0++; }
		if ((EXAMENJTC != "oui") && (MODNAMUR0 == "oui")) {
			$worksheet1->write(0, $jo, "Vie Scol.", $header);
			$jo++;
			$worksheet1->write(0, $jo, "Moyen.", $header);
			$jo++;
		} else {
			$worksheet1->write(0, $jo, " ", $header);
			$jo++;
			$worksheet1->write(0, $jo, "Moyen.", $header);
			$jo++;
		}
		if ($affrang == "1") { $worksheet1->write(0, $jo, "Rang", $header); }
		$ligne  = 1;
		$ligne2 = 1;
		$afficMoyenEleve = "oui";
		for ($j=0; $j<count($eleveT); $j++) {
			$ligne2++;
			$colonne  = 2;
			$colonne2 = 1;
			$ju++;
			$nomEleve   = ucwords($eleveT[$j][0]);
			$prenomEleve= ucfirst($eleveT[$j][1]);
			$lv1Eleve   = $eleveT[$j][2];
			$lv2Eleve   = $eleveT[$j][3];
			$idEleve    = $eleveT[$j][4];

			$worksheet2->write($ligne2, 0, _xls_encode("$nomEleve $prenomEleve"), $center);
			$worksheet1->write($ligne,  0, _xls_encode("$nomEleve $prenomEleve"), $center);

			for ($Tr=1; $Tr<4; $Tr++) {
				$colonne = 1;
				$dateDebut = ""; $dateFin = "";
				if ($Tr==1) { $dateDebut=$T1dateDebut; $dateFin=$T1dateFin; }
				if ($Tr==2) { $dateDebut=$T2dateDebut; $dateFin=$T2dateFin; }
				if ($Tr==3) { $dateDebut=$T3dateDebut; $dateFin=$T3dateFin; }
				if ($Tr >= 4) break;

				$worksheet1->write($ligne, $colonne, "T$Tr", $center);
				$colonne++;

				foreach ($tabmatiere as $key => $value) {
					$nbmat++;
					$nbNotes = "";
					list($nom_matiere,$num_ordre,$idMatiere) = preg_split("/##/", $value);
					$matiere = $nom_matiere;
					$idprof  = profAff($idMatiere, $idClasse, $num_ordre);
					$verifGroupe = verifMatiereAvecGroupe3($idMatiere, $idEleve, $idClasse, $num_ordre);
					if ($examen != "") {
						if ($verifGroupe > 0) {
							$noteaff = moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof,$examen);
						} elseif ($verifGroupe == -1) {
							$noteaff = "";
						} else {
							$noteaff = moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
						}
					} else {
						if ($verifGroupe > 0) {
							if ($noteexamen == "oui") {
								$noteaff = moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof);
							} else {
								$noteaff = moyenneEleveMatiereGroupeSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof);
							}
						} elseif ($verifGroupe == -1) {
							$noteaff = "";
						} else {
							if ($noteexamen == "oui") {
								$noteaff = moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
							} else {
								$noteaff = moyenneEleveMatiereSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
							}
						}
					}

					$nbNotes = nombreDeNote($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
					$worksheet2->write($ligne2, $colonne2, "$nbNotes", $center);
					$colonne2++;

					$coeffaff = recupCoeff($idMatiere, $idClasse, $num_ordre);
					if ($examen != "") {
						$coeffaffEx = recup_coef_bulletin("bull0101b",$idClasse,$idMatiere,$num_ordre);
						if ($coeffaffEx != "") $coeffaff = $coeffaffEx;
					}

					$notetype = recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
					if ($notetype == "en") {
						$afficMoyenEleve = "non";
						$noteaff2 = recherche_note_en($noteaff);
						if ($noteaff2 == "?") { $err="-".$noteaff; } else { $err=""; }
						$worksheet1->write($ligne, $colonne, "$noteaff2.$err", $center);
						$colonne++;
					} else {
						if (($noteaff < 10) && ($noteaff != "")) { $noteaff = "0".$noteaff; }
						if ($noteaff < 10) {
							$worksheet1->write($ligne, $colonne, "$noteaff", $centerrouge);
						} else {
							$worksheet1->write($ligne, $colonne, "$noteaff", $center);
						}
						$colonne++;
					}

					if ($noteaff != "") {
						$noteMoyEleGTempo = $noteaff * $coeffaff;
						$noteMoyEleG      = $noteMoyEleG + $noteMoyEleGTempo;
						$coefEleG         = $coefEleG + $coeffaff;
					}
				}

				if ((EXAMENJTC != "oui") && (MODNAMUR0 == "oui")) {
					if ($Tr==1) { $TrA="trimestre1"; }
					if ($Tr==2) { $TrA="trimestre2"; }
					if ($Tr==3) { $TrA="trimestre3"; }
					$noteaff = calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$TrA);
					if ($noteaff != "") {
						$noteMoyEleGTempo = $noteaff * $coefBull;
						$noteMoyEleG      = $noteMoyEleG + $noteMoyEleGTempo;
						$coefEleG         = $coefEleG + $coefBull;
						if (($noteaff < 10) && ($noteaff != "")) { $noteaff = "0".$noteaff; }
						if ($noteaff < 10) {
							$worksheet1->write($ligne, $colonne, "$noteaff", $centerrouge);
						} else {
							$worksheet1->write($ligne, $colonne, "$noteaff", $center);
						}
						$colonne++;
					} else {
						$colonne++;
					}

					$moyenEleve = moyGenEleve($noteMoyEleG, $coefEleG);
					$moyenEleve = preg_replace('/,/', '.', $moyenEleve);
					if (trim($moyenEleve) != "") {
						$notescolaireG += $moyenEleve;
						$coefscolaireG++;
					}
				}

				$moyenEleve = moyGenEleve($noteMoyEleG, $coefEleG);
				if (($moyenEleve < 10) && ($moyenEleve != "")) { $moyenEleve = "0".$moyenEleve; }
				if ($afficMoyenEleve == "non") {
					$worksheet1->write($ligne, $colonne, "----", $center);
					$colonne++;
				} else {
					if ($Tr == 1) { $classement1[$j] = $moyenEleve; }
					if ($Tr == 2) { $classement2[$j] = $moyenEleve; }
					if ($Tr == 3) { $classement3[$j] = $moyenEleve; }
					if ($moyenEleve < 10) {
						$worksheet1->write($ligne, $colonne, "$moyenEleve", $centerrouge);
					} else {
						$worksheet1->write($ligne, $colonne, "$moyenEleve", $center);
					}
					$colonne++;
				}
				unset($moyenEleve);
				unset($noteMoyEleG);
				unset($coefEleG);
				$ligne++;
			} // fin du trimestre

			for ($joo=0; $joo<=$nbmat0; $joo++) {
				$worksheet1->write($ligne, $joo, "", $separateur);
			}
			$ligne++;
		} // fin du for on passe à l'eleve suivant

		for ($j=0; $j<$nbaffichematiere; $j++) {
			array_shift($ordre);
			$afficheMoyen = 1;
		}
	}
}

// classement
if ($affrang == "1") {
	$ligne = 1;
	arsort($classement1);
	arsort($classement2);
	arsort($classement3);
	$i = 1;
	foreach ($classement1 as $key => $val) { $place1[$key] = $i; $i++; }
	$i = 1;
	foreach ($classement2 as $key => $val) { $place2[$key] = $i; $i++; }
	$i = 1;
	foreach ($classement3 as $key => $val) { $place3[$key] = $i; $i++; }
	for ($j=0; $j<count($eleveT); $j++) {
		$worksheet1->write($ligne, $colonne, $place1[$j], $center); $ligne++;
		$worksheet1->write($ligne, $colonne, $place2[$j], $center); $ligne++;
		$worksheet1->write($ligne, $colonne, $place3[$j], $center); $ligne++;
		$ligne++;
	}
}

$workbook->close();

// Vider le buffer avant d'envoyer les headers binaires
ob_end_clean();

header("Content-Type: application/x-msexcel; name=\"moyenne_$classe_nom.xls\"");
header("Content-Disposition: inline; filename=\"moyenne_$classe_nom.xls\"");
if (HTTPS == "oui") {
	header("Cache-Control: public");
	header("Pragma:");
	header("Expires: 0");
} else {
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile($fname);

@unlink($fname);

history_cmd($_SESSION["nom"], "CREATION TABLEAU EXCEL", "Classe : $classe_nom");
Pgclose();
exit;
?>
