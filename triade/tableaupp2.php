<?php
session_start();
if (isset($_POST["annee_scolaire"])) {
        $anneeScolaire=$_POST["annee_scolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<br /><br />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" align="center" >
<tr id='coulBar0' ><td height="2" id='menumodule1' ><b><font   id='menumodule1' >Impression du tableau de bulletin </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  --><br> <br>
<?php

include_once('librairie_php/db_triade.php');
$cnx=cnx();

$examen=trim($_POST["NoteExam"]);

if ($_SESSION["membre"] == "menupersonnel") {
	if ((!verifDroit($_SESSION["id_pers"],"ficheeleve")) && (!verifDroit($_SESSION["id_pers"],"imprtableau"))) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("3");
}

$valeur=visu_affectation_detail($_POST["saisie_classe"]);

$nbaffichematiere=19;

if (MODNAMUR0 == "oui") {
	$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
}

if (isset($_POST["affcolvide"])) {
	$affcolvide=$_POST["affcolvide"];
}else{
	$affcolvide=1;
}

if (count($valeur)) {
	if ($_POST["typetrisem"] == "trimestre") {
		if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; }
		if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; }
		if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; }
	}

	if ($_POST["typetrisem"] == "semestre") {
		if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; }
		if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; }
	}


	// recupe du nom de la classe
	$data=chercheClasse($_POST["saisie_classe"]);
	$classe_nom=$data[0][1];
	// recup année scolaire
	
	include_once('librairie_php/recupnoteperiode.php');
	
	// recherche des dates de debut et fin
//	$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
	$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebut=$dateRecup[$j][0];
		$dateFin=$dateRecup[$j][1];
	}
	$dateDebut=dateForm($dateDebut);
	$dateFin=dateForm($dateFin);

	$idClasse=$_POST["saisie_classe"];
	if ($examen == "") {
                $ordre=ordre_matiere_visubull($_POST["saisie_classe"],$anneeScolaire); // recup ordre matiere
        }else{
                $ordre=ordre_matiere_visubull_btsblanc($_POST["saisie_classe"],$anneeScolaire);
        }

	
	// creation PDF
	//
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');
	//include_once('./librairie_pdf/lib.php');
	$pdf=new PDF('L','mm','A4');  // declaration du constructeur
	

	$moyenClasseGen=""; // pour le calcul moyenne classe
	$moyenClasseMin=1000; // pour la calcul moyenne min classe
	$moyenClasseMax=""; // pour la calcul moyenne max  classe
	$nbeleve=0;
	$nbeleve2=0;
	$noteMoyEleG=0; // pour la moyenne  general
	$coefEleG=0; // pour la moyenne  general


	$nbMat=0;

	for($i=0;$i<count($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		$idgroupe=verifMatierAvecGroupeRecupId2($idMatiere,$idClasse,$ordre[$i][2]);
		$datasousmatiere=verifsousmatierebull($idMatiere);
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}
		//if (verifNoteDansMatiere($idMatiere,$idClasse,$idgroupe,$dateDebut,$dateFin)) { continue; }
		if (trim($matiere) != "") { $tabmatiere2[$num_ordre]="$matiere##$num_ordre##$idMatiere"; }
		$nbMat++;
	}

	if ($nbaffichematiere >= $nbMat) {
		$afficheMoyen=1;
	}else{
		$afficheMoyen=0;
	}

	if ($nbMat >= 40) { $nbaffichematiere=22 ; } 


while(count($ordre)) {
	$ju=0;
	
	$pdf->AddPage();
	unset($tabmatiere);
	$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve
	$pdf->SetTextColor(0,0,0);
	$XnotVal=35;
	$YnotVal=20;
	$largeurnote=11;
	$xnomeleve=5;
	$ynomeleve=20;
	$hauteurnomeleve=5;
	$xmatiere=$XnotVal;
	$ymatiere=$YnotVal-5;
	//TITRE
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY(100,5);
	$pdf->writehtml('<B>NOTES de '.$classe_nom.' du '.$textTrimestre.'</B> ('.$anneeScolaire.')'.$examen);
	// noms matières
	for($i=0;$i<$nbaffichematiere;$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$num_ordre=$ordre[$i][2];
		//$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);

		$idgroupe=verifMatierAvecGroupeRecupId2($idMatiere,$idClasse,$ordre[$i][2]);

		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}
		// fin de la gestion sous matiere
		// ------------------------------
		if (trim($idMatiere) == "") { break; }
		//if (verifNoteDansMatiere($idMatiere,$idClasse,$idgroupe,$dateDebut,$dateFin)) { $nbaffichematiere++; continue; }
		if (trim($matiere) != "") { $tabmatiere[$num_ordre]="$matiere##$num_ordre##$idMatiere"; }
	}


	while (list($key, $nom_matiere) = each($tabmatiere)) {
		list($nom_matiere,$num_ordre,$idMatiere,$nomSousMatiere)=preg_split("/##/",$nom_matiere);
		$pdf->SetFillColor(220);
		$pdf->SetXY($xmatiere,$ymatiere-3);
		$pdf->SetFont('Arial','B',6);
  		$pdf->MultiCell($largeurnote,8,"",1,'L',1);	
		$pdf->SetXY($xmatiere,$ymatiere-3);
		if (trim($nomSousMatiere) != "") {
			$nom_matiere=trunchaine(ucfirst($nom_matiere." ".$nomSousMatiere),13);
		}else{
			$nom_matiere=preg_replace('/education/i','educ',$nom_matiere);
			$nom_matiere=trunchaine(ucfirst($nom_matiere),13);
		}
		$codeMatiere=chercheCodeMatiere($idMatiere);
		if ($codeMatiere != "") $nom_matiere=$codeMatiere;
		$pdf->MultiCell($largeurnote,2.7,"$nom_matiere",0,'L',0);
	//	$pdf->writehtml(trunchaine(ucfirst($nom_matiere),8));
		$xmatiere=$xmatiere + 11;
		$pdf->SetFont('Arial','',6);
	
	}
/*
	while (list($key, $nom_matiere) = each($tabmatiere)) {
		list($nom_matiere,$num_ordre,$idMatiere)=preg_split("/##/",$nom_matiere);
		$pdf->SetFillColor(220);
		$pdf->SetXY($xmatiere,$ymatiere);
		$pdf->SetFont('Arial','',6);
  		$pdf->MultiCell($largeurnote,5,"",1,'L',1);	
		$pdf->SetXY($xmatiere,$ymatiere);
		$pdf->writehtml(trunchaine(ucfirst($nom_matiere),8));
		$xmatiere=$xmatiere + 11;
	
	}


 */
	if (($afficheMoyen) && ($nbaffichematiere > count($ordre))) {

		if (MODNAMUR0 == "oui") {
			$pdf->SetXY($xmatiere,$ymatiere); // placement du cadre notes
		  	$pdf->MultiCell($largeurnote,5,'Vie Scol.',1,'L',1);	
			$pdf->SetFont('Arial','',6);

			$pdf->SetFillColor(220);
  			$pdf->SetXY($xmatiere+$largeurnote,$ymatiere); // placement du cadre notes
		  	$pdf->MultiCell($largeurnote,5,'Moyen.',1,'L',1);	
			$pdf->SetFont('Arial','',6);

		}else {
  			$pdf->SetFillColor(220);
  			$pdf->SetXY($xmatiere,$ymatiere); // placement du cadre notes
	  		$pdf->MultiCell($largeurnote,5,'Moyen.',1,'L',1);	
  			$pdf->SetFont('Arial','',6);
		}

		
	}
	

	$ymatiere=$ynomeleve;
        $pdf->SetXY($xnomeleve,$ynomeleve);
        $pdf->SetFont('Arial','',8);
        $pdf->MultiCell(30,5," Coef : ",1,'R',0);
        $xmatiere=$xnomeleve+30;


        while (list($key, $nom_matiere) = each($tabmatiere2)) {
                list($nom_matiere,$num_ordre,$idMatiere,$nomSousMatiere)=preg_split("/##/",$nom_matiere);
                $coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
                if ($examen != "") {
                        $coeffaffEx=recup_coef_bulletin("bull0101b",$idClasse,$idMatiere,$num_ordre);
                        if ($coeffaffEx != "") $coeffaff=$coeffaffEx;
                }
                $pdf->SetXY($xmatiere,$ymatiere);
                $pdf->MultiCell($largeurnote,$hauteurnomeleve,"($coeffaff)",1,'C',0);
        //      $pdf->writehtml(trunchaine(ucfirst($nom_matiere),8));
                $xmatiere=$xmatiere + $largeurnote;
        }

        $ynomeleve+=$hauteurnomeleve;
        $YnotVal+=$hauteurnomeleve;



	//eleves
	$afficMoyenEleve="oui";	
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$ju++;
		$nomEleve=ucwords($eleveT[$j][0]);
		$prenomEleve=ucfirst($eleveT[$j][1]);
		$lv1Eleve=$eleveT[$j][2];
		$lv2Eleve=$eleveT[$j][3];
		$idEleve=$eleveT[$j][4];

		if ($ju >= 33) { $pdf->AddPage();$ju=0; $ynomeleve=20;$YnotVal=20;$ymatiere=$YnotVal-5;}

		$pdf->SetFillColor(220);
		$pdf->SetXY($xnomeleve,$ynomeleve); // placement du cadre du nom de l eleve
		$pdf->MultiCell(30,5,'',1,'L',0);

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($xnomeleve,$ynomeleve);
		$pdf->WriteHTML(trunchaine(ucwords($nomEleve)." ".$prenomEleve,20));
		$ynomeleve=$hauteurnomeleve + $ynomeleve;
		$XnotVal=35;
	
		//moyennes matières
		foreach ($tabmatiere as $key => $value)  {
			$nbmat++;
			list($nom_matiere,$num_ordre,$idMatiere)=preg_split("/##/",$value);
			$matiere=$nom_matiere;
			//$idMatiere=$key;

			$idprof=profAff($idMatiere,$idClasse,$num_ordre);
			$verifGroupe=verifMatiereAvecGroupe3($idMatiere,$idEleve,$idClasse,$num_ordre);
			if ($examen != "") {
                                if ($verifGroupe > 0) {
                                        $noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof,$examen);
                                }elseif ($verifGroupe == -1) {
                                        $noteaff="";
                                }else{
                                        $noteaff=moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
                                }
                        }else{
				if ($verifGroupe > 0) {
					if ($_POST["noteexamen"] == "oui") {
						$noteaff=moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof);
					}else{
						$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof);
					}
				}elseif ($verifGroupe == -1) {
					$noteaff="";
				}else{
					if ($_POST["noteexamen"] == "oui") {
						$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
					}else{
						$noteaff=moyenneEleveMatiereSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
					}
				}
			}
			// mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
			if ($examen != "") {
                                $coeffaffEx=recup_coef_bulletin("bull0101b",$idClasse,$idMatiere,$num_ordre);
                                if ($coeffaffEx != "") $coeffaff=$coeffaffEx;
                        }

			$pdf->SetFillColor(220);
  			$pdf->SetXY($XnotVal,$YnotVal); // placement du cadre notes
  			$pdf->MultiCell($largeurnote,5,'',1,'L',0);	
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY($XnotVal,$YnotVal);
			$notetype=recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($notetype=="en") {
				$afficMoyenEleve="non";	
				$noteaff2=recherche_note_en($noteaff);
				if ($noteaff2 == "?") { $err="-".$noteaff; }else{ $err=""; }
				$pdf->WriteHTML($noteaff2.$err);
			}else{	
				if ($noteaff < 10) {
					if (($noteaff < 10) && ($noteaff != "")) { $noteaff="0".$noteaff; } 
					$pdf->WriteHTML("<font color=red>".$noteaff."</font>");
				}else {
					$pdf->WriteHTML($noteaff);
				}
			}
			$XnotVal=$XnotVal + $largeurnote;
		}

		if (($afficheMoyen) && ($nbaffichematiere > count($ordre))) {
			$noteMoyEleG=0; // pour la moyenne  general
			$coefEleG=0; // pour la moyenne  general


			if (MODNAMUR0 == "oui") {
			
				$nombreviescol = 0;
       				$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
				if ( $noteaff != "" ) {
 					 $noteMoyEleGTempo = $noteaff * $coefBull;
       		        		 $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
					 $coefEleG=$coefEleG + $coefBull;
				}

				$pdf->SetFont('Arial','',10);
				//if (MODNAMUR0 == "oui") {
    					$pdf->SetXY($XnotVal,$YnotVal);
					$pdf->MultiCell($largeurnote,5,"$noteaff",1,'L',0);

					$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
					$moyenEleve=preg_replace('/,/','.',$moyenEleve);
					$pdf->SetXY($XnotVal += 11,$YnotVal);
					$pdf->SetFont('Arial','',10);

					 if ($moyenEleve != "") {
						 $notescolaireG+=$moyenEleve;
						 $coefscolaireG++;
					}

				//}
				
				/*else {
  					$pdf->SetXY($XnotVal,$YnotVal);	
					$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
					$moyenEleve=preg_replace('/,/','.',$moyenEleve);
					$pdf->SetXY($XnotVal,$YnotVal);
					$pdf->SetFont('Arial','',10);
				}*/
    			}


		

			foreach ($tabmatiere2 as $key => $value)  {
				list($nom_matiere,$num_ordre,$idMatiere)=preg_split("/##/",$value);
				$matiere=$nom_matiere;
				//$idMatiere=$key;

				$idprof=profAff($idMatiere,$idClasse,$num_ordre);
				$verifGroupe=verifMatiereAvecGroupe3($idMatiere,$idEleve,$idClasse,$num_ordre);
				if ($examen != "") {
                                	if ($verifGroupe > 0) {
	                                        $noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof,$examen);
	                                }elseif ($verifGroupe == -1) {
	                                        $noteaff="";
	                                }else{
	                                        $noteaff=moyenneEleveMatiereExamen($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
	                                }
                        	}else{
					if ($verifGroupe > 0) { 
						if ($_POST["noteexamen"] == "oui") {
							$noteaff=moyenneEleveMatiereGroupe($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof);
						}else{
							$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$verifGroupe,$idprof);
						}
					}elseif ($verifGroupe == -1) {
						$noteaff="";
	
					}else{
						if ($_POST["noteexamen"] == "oui") {
							$noteaff=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
						}else{
							$noteaff=moyenneEleveMatiereSansExam($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
						}
					}
				}
				// mise en place des coeff
				$coeffaff=recupCoeff($idMatiere,$idClasse,$num_ordre);
				if ($examen != "") {
                	                $coeffaffEx=recup_coef_bulletin("bull0101b",$idClasse,$idMatiere,$num_ordre);
        	                        if ($coeffaffEx != "") $coeffaff=$coeffaffEx;
	                        }

				$notetype=recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
				if ($notetype=="en") {
					$afficMoyenEleve="non";	
					$noteaff2=recherche_note_en($noteaff);
					if ($noteaff2 == "?") { $err="-".$noteaff; }else{ $err=""; }
				}
				if ( $noteaff != "" ) {
				        $noteMoyEleGTempo = $noteaff * $coeffaff;
       		        		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
					$coefEleG=$coefEleG + $coeffaff;
				}
			}

			$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
			if (($moyenEleve < 10) && ($moyenEleve != "")) { $moyenEleve="0".$moyenEleve; } 
			$classement[$j]=$moyenEleve;
	
			$pdf->SetXY($XnotVal,$YnotVal);
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY($XnotVal,$YnotVal);
			if ($afficMoyenEleve=="non") { 
				$pdf->MultiCell($largeurnote,5,"----",1,'L',0);	
				//$pdf->WriteHTML("<B>----</B>");
			}else{
				if ($moyenEleve < 10) { 
					$pdf->SetTextColor(255,0,0);
				//	$pdf->WriteHTML("<font color=red>".$moyenEleve."</font>");
				}else {
					$pdf->SetTextColor(0,0,0);
				//	$pdf->WriteHTML($moyenEleve);
				}
				$pdf->MultiCell($largeurnote,5,"$moyenEleve",1,'L',0);	
			}
			$pdf->SetTextColor(0,0,0);
			if (trim($moyenEleve) != "") {
				$moyenEleve=preg_replace('/,/','.',$moyenEleve);
				$moyenClasseGen+=$moyenEleve;
				$nbeleve2++;
			}
		}
		// fin affichage moy eleve




		$YnotVal=$YnotVal+$hauteurnomeleve;
	
		
	
	
	} // fin du for on passe à l'eleve suivant

	//Moyennes matières
	$pdf->SetFillColor(220);
	$pdf->SetXY($xnomeleve,$ynomeleve); // placement du cadre moyenne
	$pdf->MultiCell(30,6,'',1,'L',1);

	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($xnomeleve,$ynomeleve);
	$pdf->WriteHTML('Moyenne :');
	$XnotVal=35;


	foreach ($tabmatiere as $key => $value)  {
		list($nom_matiere,$num_ordre,$idMatiere)=preg_split("/##/",$value);
		$matiere=$nom_matiere;
		//$idMatiere=$key;
		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,"",$idClasse,$num_ordre);
		$idprof=profAff($idMatiere,$idClasse,$num_ordre);
                if ($examen != "") {
                        if ($idgroupe == "0") {
                                $moyeMatGen=moyeMatGenExamen($idMatiere,$dateDebut,$dateFin,$idClasse,$idprof,$examen);
                                $notetype=recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
                        }else{
                                $moyeMatGen=moyeMatGenGroupeExamen($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
                                $notetype=recherchetypenotegroupe($idMatiere,$dateDebut,$dateFin,$idgroupe);
                        }
                }else{
			if ($idgroupe == "0") {
		           	// idMatiere,datedebut,dateFin,idclasse
				$moyeMatGen=moyeMatGen($idMatiere,$dateDebut,$dateFin,$idClasse,$idprof);
				$notetype=recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
       			}else {
				$moyeMatGen=moyeMatGenGroupe($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				$notetype=recherchetypenotegroupe($idMatiere,$dateDebut,$dateFin,$idgroupe);
        		}
		}
	  	$pdf->SetXY($XnotVal,$YnotVal); // placement du cadre des matières
  		//$pdf->MultiCell($largeurnote,6,'',1,'L',1);
		$xmatiere=$xmatiere + 15;
		$pdf->SetXY($XnotVal,$YnotVal);

		if ($notetype=="en") { 
			$moyeMatGenAff=recherche_note_en($moyeMatGen);
			if ($moyeMatGen == 0) { $moyeMatGenAff=""; }
			if ($moyeMatGenAff == "?") { $err="-".$moyeMatGen; }else{ $err=""; } 
			$pdf->WriteHTML($moyeMatGenAff.$err);
		}else{	
			if ($moyeMatGen < 10) { 
				$pdf->SetTextColor(255,0,0);
				//$pdf->WriteHTML("<font color=red>".$moyeMatGen."</font>");
			}else {
				$pdf->SetTextColor(0,0,0);
				//$pdf->WriteHTML($moyeMatGen);
			}
		}	
  		$pdf->MultiCell($largeurnote,6,"$moyeMatGen",1,'L',1);
		$pdf->SetFont('Arial','',10);
		$XnotVal=$XnotVal+$largeurnote;
	}
	if (($afficheMoyen) && ($nbaffichematiere > count($ordre))) {
		if (MODNAMUR0 == "oui") {
			$pdf->SetXY($XnotVal,$YnotVal);
			$moyenEleve="";
			if ($notescolaireG != "") { $moyenEleve=moyGenEleve($notescolaireG,$coefscolaireG); }
			$moyenEleve=preg_replace('/,/','.',$moyenEleve);
			if ($moyenEleve < 10) { 
				$pdf->SetTextColor(255,0,0);
			}else {
				$pdf->SetTextColor(0,0,0);
			}
			$pdf->MultiCell($largeurnote,6,"$moyenEleve",1,'L',1);
			$pdf->SetXY($XnotVal += 11,$YnotVal);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(0,0,0);
		}
	}
	//print_r($ordre);
	for($j=0;$j<$nbaffichematiere;$j++) {
		array_shift($ordre);
		$afficheMoyen=1;
	}
}

	$moyenClasseGen=number_format($moyenClasseGen/$nbeleve2,2,'.','');

  	$pdf->SetXY($XnotVal,$YnotVal);
  	if ($afficMoyenEleve=="oui" )	 { 
		$pdf->MultiCell($largeurnote,6,"$moyenClasseGen",1,'L',1);
	}else{
		$pdf->MultiCell($largeurnote,6,"----",1,'L',1);
	}


	// cclassement
if ($_POST["affrang"] == "1") {
	if (count($eleveT)<32) {
		$XnotVal=$XnotVal+$largeurnote;	
		$YnotVal = 20;	 
		arsort($classement);
		$i=1;
		foreach ($classement as $key => $val) {	
			$place[$key] = $i;
			$i = $i+1;
		}
		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($XnotVal,$ymatiere-3);
		$pdf->MultiCell(10,8,'Rang',1,'C',1);
		$pdf->SetFont('Arial','',10);
		for($j=0;$j<count($eleveT);$j++) {
			$pdf->SetXY($XnotVal,$YnotVal);
			$pdf->MultiCell(10,$hauteurnomeleve,'',1,'L',0);
			$pdf->SetXY($XnotVal+2,$YnotVal);
   			$pdf->WriteHTML($place[$j]);
			$YnotVal=$YnotVal+$hauteurnomeleve;	
		}
	}else{
		$pdf->AddPage();
		$XnotVal = 10;	
		$YnotVal = 10;	 
		arsort($classement);
		$i=1;
		foreach ($classement as $key => $val) {	
			$place[$key] = $i;
			$i = $i+1;
		}
		$pdf->SetXY($XnotVal+5,$YnotVal-$hauteurnomeleve);
		$pdf->MultiCell(46,$hauteurnomeleve,'Classement :',1,'L',0);
		for($j=0;$j<count($eleveT);$j++) {
			$nomEleve=ucwords($eleveT[$j][0]);
			$prenomEleve=ucfirst($eleveT[$j][1]);
			$pdf->SetXY($XnotVal+5,$YnotVal);
			$pdf->MultiCell(40,$hauteurnomeleve,'',1,'L',1);
			$pdf->SetXY($XnotVal+5,$YnotVal);
			$pdf->WriteHTML(trunchaine(ucwords($nomEleve),10)." ".trunchaine($prenomEleve,5));
			$pdf->SetXY($XnotVal+45,$YnotVal);
			$pdf->MultiCell(6,$hauteurnomeleve,'',1,'L',0);
			$pdf->SetXY($XnotVal+45,$YnotVal);
		   	$pdf->WriteHTML($place[$j]);
			$YnotVal=$YnotVal+$hauteurnomeleve;	
		}
	}
}

	$classe_nom=TextNoAccent($classe_nom);
	$classe_nom=TextNoCarac($classe_nom);
	$fichier="./data/pdf_bull/tableaupp_".$classe_nom."_".$_POST["saisie_trimestre"].".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);


	?>
	<br><ul><ul>
	<?php
	if ($_SESSION["membre"] == "menuadmin") {
	?>
		<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="R&eacute;cup&eacute;rer le fichier PDF"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
	<?php
	}
	if ($_SESSION["membre"] == "menupersonnel") {
	?>
		<input type=button onclick="open('visu_pdf_personnel.php?id=<?php print $fichier?>','_blank','');" value="R&eacute;cup&eacute;rer le fichier PDF"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
	<?php
	}
	if ($_SESSION["membre"] == "menuprof") {
	?>
		<input type=button onclick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');" value="R&eacute;cup&eacute;rer le fichier PDF"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
	<?php } ?>

	<?php
	if ($_SESSION["membre"] == "menuscolaire") {
	?>
		<input type=button onclick="open('visu_pdf_scolaire.php?id=<?php print $fichier?>','_blank','');" value="R&eacute;cup&eacute;rer le fichier PDF"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
	<?php } ?>

	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type=button onclick="history.go(-1)" value="Retour"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">

	</ul></ul></form><br /><br />
	<?php
	// gestion d'historie
	@destruction_bulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
	$cr=historyBulletin($fichier,$classe_nom." Prof Principal",$_POST["saisie_trimestre"],$dateDebut,$dateFin);
	if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION TABLEAU PP","Classe : $classe_nom");
	}else{
		error(0);
	}
	Pgclose();
}else {
?>
	<br />
	<center>
	<?php print LANGMESS14?> <br>
	<br><br>
	<font size=3><?php print LANGMESS15?><br><br>
	<?php print LANGMESS16?><br>
	</center>
	<br /><br /><br />
<?php
}
?>
<!-- // fin  -->
</td></tr></table>
<script language=JavaScript>attente_close();</script>
</BODY></HTML>
