<?php
session_start();
error_reporting(0);
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
	set_time_limit(300);
}

?>
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >

<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
	$data=aff_enr_parametrage("autorisebulletinprof"); 
	if ($data[0][1] == "oui") {
		validerequete("3");
	}else{
		verif_profp_class($_SESSION["id_pers"],$_POST["saisie_classe"]);
	}
}else{
	validerequete("2");
}
$debut=deb_prog();
$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
if (count($valeur)) {
	if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; $sem=1; $tricoef='trimestre1'; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2"; $sem=2; $tricoef='trimestre2'; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3"; $tricoef='trimestre3';  }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; $sem=1; $tricoef='trimestre1'; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; $sem=2; $tricoef='trimestre2'; }
	if (($_POST["saisie_trimestre"] == "trimestre3" ) || ($_POST["saisie_trimestre"] == "annuel")) { $textTrimestre="Annuel"; $triabsconet=""; $sem='12'; $tricoef=''; }
}



$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
	$dateDebutS1=$dateDebut;
	$dateFinS1=$dateFin;
}

$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
	$dateDebutS2=$dateDebut;
	$dateFinS2=$dateFin;
}

if ($_POST["saisie_trimestre"] == "trimestre1" ) {
	$dateDebut=$dateDebutS1;
	$dateFin=$dateFinS1;
}

if ($_POST["saisie_trimestre"] == "trimestre2" ) {
	$dateDebut=$dateDebutS2;
	$dateFin=$dateFinS2;
}

if (($_POST["saisie_trimestre"] == "trimestre3") || ($_POST["saisie_trimestre"] == "annuel"))  {
	$dateDebut=$dateDebutS1;
	$dateFin=$dateFinS2;
	$saisie_trimestre="trimestre2";
	$textTrimestre="Annuel";
}

$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);



// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
$classenomlong=$data[0][2];


if ($_POST["saisie_trimestre"] != "trimestre1" ) $affECTS="oui";

// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
      <?php print "Date" ?> : <?php print "du $dateDebut au $dateFin" ?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l etablissement
$data=visu_paramViaIdSite(chercheIdSite($_POST["saisie_classe"]));
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim(TextNoAccent($data[$i][0]));
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
}
// fin de la recup


if (MODNAMUR0 == "oui") {
	$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
}


$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"],$anneeScolaire); // recup ordre matiere

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

$moyenClasseGen=""; // pour le calcul moyenne classe
$moyenClasseMin=1000; // pour la calcul moyenne min classe
$moyenClasseMax=""; // pour la calcul moyenne max  classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$noteMoyEleG2=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

// pour le calcul de moyenne classe
//$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);

if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }


$plageEleve=$_POST["plageEleve"];
if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
if ($plageEleve == "10") { $dep=0; $nbEleveT=9; }
if ($plageEleve == "20") { $dep=9; $nbEleveT=19; }
if ($plageEleve == "30") { $dep=19; $nbEleveT=29; }
if ($plageEleve == "40") { $dep=29; $nbEleveT=39; }
if ($plageEleve == "50") { $dep=39; $nbEleveT=49; }
if ($plageEleve == "60") { $dep=49; $nbEleveT=59; }
if ($nbEleveT > count($eleveT)) { $nbEleveT=count($eleveT); }
// nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve
for($j=$dep;$j<$nbEleveT;$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];
	$datenaissance=dateForm($eleveT[$j][5]);
	$lieunaissance=$eleveT[$j][6];
	$numero_eleve=$eleveT[$j][11];

	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

	$logo="./image/banniere/ESMK.jpeg";
	if (file_exists($logo)) {
		$pdf->Image($logo,10,3,20,20);	
	}
	$logo="./image/banniere/ESGCI.jpg";
	if (file_exists($logo)) {
		$pdf->Image($logo,80,3,25,7);	
	}
	$logo="./image/banniere/UPA_carre.jpg";
	if (file_exists($logo)) {
		$pdf->Image($logo,160,3,40,10);	
	}

	// insertion de la Annee SCOLAIRE
	$pdf->SetFont('Arial','B',14);
	$pdf->SetXY(100,30);
	$pdf->MultiCell(100,10,"RELEVE DE NOTES ET RESULTATS",1,'C',0);
	$pdf->SetXY(100,39);
	$pdf->SetFont('Arial','',12);
	$pdf->MultiCell(100,10,"Session $anneeScolaire",0,'C',0);
	// fin d'insertion


	$Y=30;
	$X=5;
	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(210,3,"Nom Prénom : $nomEleve $prenomEleve",0,'L',0);
	$pdf->SetXY($X,$Y+=5);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(110,3,"Né(e) le : $datenaissance à $lieunaissance ",0,'L',0);
	$pdf->SetXY($X,$Y+=5);
	$pdf->MultiCell(70,3,"Numéro de document : $numero_eleve",0,'L',0);
	$pdf->SetXY($X,$Y+=5);
	$pdf->MultiCell(70,3,"Inscrit(e) en ",0,'L',0);
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+15,$Y);
	$classe_nom2=preg_replace('/_/',' ',$classe_nom);
	$pdf->MultiCell(70,3,"  $classe_nom2",0,'L',0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X,$Y+=5);
	$pdf->MultiCell(70,3,"Sur le site de ",0,'L',0);
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY($X+17,$Y);
	$pdf->MultiCell(70,3,"$ville",0,'L',0);
	$pdf->SetFont('Arial','',8);

	$Y+=5;
	$Xorigine=3;


// -------------------------------------------------------------------------------------------
	// Barre des titres
	$X=$Xorigine;
	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(220);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,5,"Cours",1,'C',0);
	$pdf->SetXY($X+=20,$Y);
	$pdf->MultiCell(100,5,"Intitulé des cours",1,'C',0);
	$pdf->SetXY($X+=100,$Y); 
	$pdf->MultiCell(30,5,"Note/Barème",1,'C',0);
	$pdf->SetXY($X+=30,$Y); 
	$pdf->MultiCell(30,5,"Résultat",1,'C',0);
	$pdf->SetXY($X+=30,$Y); 
	$pdf->MultiCell(20,5,"Crédits",1,'C',0);
	$pdf->SetXY($X+=30,$Y); 

	$X=$Xorigine;
	$Y+=5;
	
// ----------------------------------------------------

	$recupUE=recupUE($idClasse,$sem); //code_ue,nom_ue,coef_ue,ects_ue
	
	$ectsTOTALP1=0;
	$ectsTOTALP2=0;

	$hauteurMatiere=6; // taille du cadre matiere


	for($f=0;$f<count($recupUE);$f++) {
		$code_ue=$recupUE[$f][0];
		$nom_ue=$recupUE[$f][1];
		$coef_ue=$recupUE[$f][2];
		$ects_ue=$recupUE[$f][3];
		$listeMatiere=recupMatiereUE($code_ue,$idClasse);  
		// u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage , a.visubull
	
		// Verification si saut de page
		// ---------------------------
		$nbmatiere=0;
		for($i=0;$i<count($listeMatiere);$i++) {
			if ($listeMatiere[$i][4] == 0) continue;
			$idmatiere=$idMatiere=$listeMatiere[$i][0];
			$ordreaffichage=$listeMatiere[$i][3];
			$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
			if ($verifGroupe) { continue; } 
			$nbmatiere++;
		}
		if ($nbmatiere*$hauteurMatiere+$Y > 230) {
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY(3,260);
			$pdf->MultiCell(200,3,"International Management School Group présente sur les sites de Kinshasa-Pointe-Noire-Shanghai.\nPartenaire du Groupe ESG - www.univpro-afrique.com \n\nAucun duplicata de ce document ne pourra être édité. Veillez à faire des copies.",0,'C',0);
			

			$pdf->AddPage();
			$Y=7;
		}
		// ---------------------------
 		$X=$Xorigine;
		$pdf->SetFont('Arial','B',7);
		$pdf->SetFillColor(220);
		$pdf->SetXY($X,$Y);

		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(120,5,"$nom_ue",1,'L',0);
		$pdf->SetXY($X+=120,$Y);

		



	

		$Xmemo=$X;	
		$Ymemo=$Y;
		
		$Y+=$hauteurMatiere;

		$pdf->SetFillColor(255);

		// u.code_matiere,m.libelle
		for($i=0;$i<count($listeMatiere);$i++) {
			if ($listeMatiere[$i][4] == 0) continue;
			$X=$Xorigine;
			
			$idmatiere=$listeMatiere[$i][0];
			$idMatiere=$listeMatiere[$i][0];
			$matierelong=chercheMatiereLong($idMatiere);
			$matiere=$listeMatiere[$i][1];
			$idprof=$listeMatiere[$i][2];
			$ordreaffichage=$listeMatiere[$i][3];

			$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
			if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

			// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffichage);

			// gestion pour les sous matiere
			// -----------------------------
			// cod_mat,sous_matiere,libelle
			$datasousmatiere=verifsousmatierebull($idMatiere);
			//	print $datasousmatiere;
			if ($datasousmatiere != "0") {
				$nomMatierePrincipale=$datasousmatiere[0][2];
				$nomSousMatiere=$datasousmatiere[0][1];
				$matiere="$nomMatierePrincipale $nomSousMatiere";
			}

			// mise en place du nom du prof


			$pdf->SetFont('Arial','B',7);
			$pdf->SetXY($X,$Y);
			$pdf->MultiCell(20,$hauteurMatiere,"",0,'L',0);  // cours
			$pdf->SetXY($X,$Y+1);
			$pdf->MultiCell(20,2,"$matiere",0,'L',0);  //  cours

			$profAff=recherche_personne2($idprof);
			$pdf->SetFont('Arial','',7);
			$pdf->SetXY($X+=20,$Y);
			$pdf->MultiCell(100,$hauteurMatiere,"",0,'L',0);  // Intitulé du cours
			$pdf->SetXY($X,$Y+1);
			$pdf->MultiCell($largeurMat,2,"$matierelong ($profAff)",0,'L',0);  // Intitulé du cours
			$pdf->SetXY($X+=100,$Y);

			$ects=recupECTS($idmatiere,$idClasse,$saisie_trimestre);
			$coef=recupCoefUE($idmatiere,$idClasse,$saisie_trimestre);
			$coefMatiere=recupCoeffViaTrim($idmatiere,$idClasse,$ordreaffichage,$tricoef);
			$coef=preg_replace('/\.00$/','',$coef);

			$ectsTOTAL+=$ects;
			$ectsGLOBAL+=$ects;

			// mise en place du cadre note / barème
			// ------------------------------------------------------
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {
				$noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
			}else{
				$noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
			if ($_POST["saisie_trimestre"] == "trimestre3" ) saveMoyenneAnnuel($idEleve,$idmatiere,$idClasse,'',$noteaffP1,$anneeScolaire);
			$pdf->MultiCell(30,$hauteurMatiere,"$noteaffP1",0,'C',0);
			$pdf->SetXY($X+=30,$Y);

			if (trim($noteaffP1) != "") { 
	                       	$moyenEU0=moyenEleveUE($code_ue,$idClasse,$idEleve,$saisie_trimestre,$dateDebut,$dateFin,$ordreaffichage);
				if ($_POST["saisie_trimestre"] == "trimestre3" ) saveMoyenneAnnuel($idEleve,'',$idClasse,$code_ue,$moyenEU0,$anneeScolaire);
                                if (($noteaffP1 >= 10) || (($moyenEU0 >= 10) && ($noteaffP1 >= 8)))  {
					$resultat="ACQUIS";
                                }else{
					$resultat="NON ACQUIS";
					$ects=0;
				}
				$ectsGeneral+=$ects;
				$moyenUE+=$noteaffP1*$coefMatiere;
				$coefUE+=$coefMatiere;
			//	$moyenGeneral+=$noteaffP1;
			//	$coefGeneral+=$coefMatiere;
			//	$coefGeneral+=1;

			}else{
				$noteaffP1="?";
				$resultat="?";
				$ects=0;
			}
			$pdf->MultiCell(30,5,"$resultat",0,'C',0);
			$pdf->SetXY($X+=30,$Y); 
			$pdf->MultiCell(20,5,"$ects",0,'C',0);
			$ectsUE+=$ects;
			$pdf->SetXY($X+=30,$Y); 
			unset($resultat);
			$Y+=$hauteurMatiere;
		}

		

		if ($moyenUE != "") { 
			$moyenUETotal=$moyenUE/$coefUE;
			if (($moyenUETotal >= 10) && ($UENONACQUIS != 1)){
				$resultat="ACQUIS"; 
			}else{
				$resultat="NON ACQUIS"; 
				//$ectsUE=0;
			}
			$moyUEP1Aff=$moyenUETotal;
			if (($moyenUETotal < 10) && ($moyenUETotal != "")) { $moyUEP1Aff="0".$moyenUETotal; }
			$moyUEP1Aff = number_format($moyUEP1Aff, 2, '.', '');
			$moyenGeneral+=$moyUEP1Aff;
			$coefGeneral+=1;
		}else{
			$moyUEP1Aff="?";
		}

		unset($UENONACQUIS);
		unset($moyenUE);
		unset($coefUE);


		$pdf->SetXY($Xmemo,$Ymemo); 
		$pdf->MultiCell(30,5,"$moyUEP1Aff",1,'C',0); // Note / barème UE 
		$pdf->SetXY($Xmemo+=30,$Ymemo);
		$pdf->MultiCell(30,5,"$resultat",1,'C',0); // resultat	
		$pdf->SetXY($Xmemo+=30,$Ymemo);
		$pdf->MultiCell(20,5,"$ectsUE",1,'C',0);	//credit	
		$pdf->SetXY($Xmemo+=20,$Ymemo);


		
	
		unset($resultat);
		unset($moyUEP1Aff);
		unset($ectsUE);

	
}
// fin de la mise en place des matiere


$X=$Xorigine;

if ($moyenGeneral != "") { 
	$moyenGeneral=$moyenGeneral/$coefGeneral;
	if ($moyenGeneral >= 10) {
		$resultatGeneral="ADMIS"; 
	}else{
		$resultatGeneral="NON ADMIS"; 
	}
	$moyenGeneralAff=$moyenGeneral;	
	if (($moyenGeneral < 10) && ($moyenGeneral != "")) { $moyenGeneral="0".$moyenGeneral; }
	$moyenGeneralAff = number_format($moyenGeneral, 2, '.', '');

}else{
	$moyenGeneralAff="?";
}

$infoplus="";
if ($ectsGeneral < 60) $resultatGeneral="NON ACQUIS";
if (($ectsGeneral > 45) && ($moyenGeneralAff > 10) && ($ectsGeneral < 60))  {
		$nbc=60-$ectsGeneral;	
		$resultatGeneral="ADMIS *";
		$infoplus="* $nbc crédits à valider en N+1";
}

$pdf->SetFont('Arial','B',9);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(120,5,"Résultats : ",1,'L',0);
$pdf->SetXY($X+=120,$Y);
$pdf->MultiCell(30,5,"$moyenGeneralAff",1,'C',0);
$pdf->SetXY($X+=30,$Y); 
$pdf->MultiCell(30,5,"$resultatGeneral",1,'C',0);
$pdf->SetXY($X+=30,$Y); 
$pdf->MultiCell(20,5,"$ectsGeneral",1,'C',0);
if ($infoplus != "") {
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($X-40,$Y+=6);
	$pdf->MultiCell(50,5,"$infoplus",0,'C',0);
}
$pdf->SetFont('Arial','B',9);


$pdf->SetXY($X=5,$Y+=10);
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(150,5,"Participation à la vie associative de l'école     : Insuffisant       : Satisfaisant       : Excellent");

$montessori=recherchemontessori($idEleve,"univproafrique",$_POST["saisie_trimestre"]);
$montessori=$montessori[0][0];
if (trim($montessori) == "Insuffisant")  { $checkedmont1="1"; }else{ $checkedmont1="0"; }
if ($montessori == "Satisfaisant")  { $checkedmont2="1"; }else{ $checkedmont2="0"; }
if ($montessori == "Excellent") { $checkedmont3="1"; }else{ $checkedmont3="0"; }


$pdf->SetFillColor(0);
$pdf->SetXY($X+=68,$Y+1);
$pdf->MultiCell(3,3,"",1,'C',$checkedmont1);
$pdf->SetXY($X+=25,$Y+1);
$pdf->MultiCell(3,3,"",1,'C',$checkedmont2);
$pdf->SetXY($X+=27,$Y+1);
$pdf->MultiCell(3,3,"",1,'C',$checkedmont3);
$pdf->SetFillColor(255);



unset($moyenGeneral);
unset($resultatGeneral);
unset($coefGeneral);


$pdf->SetXY($Xorigine+20,$Y+20);
$pdf->MultiCell(40,5,"$directeur\nDirecteur général",0,'C',0); 
$logo="./image/banniere/ESMK.jpeg";
if (file_exists($logo)) {
	$pdf->Image($logo,$Xorigine+30,$Y+31,20,20);
}


$pdf->SetXY($Xorigine+140,$Y+10);
$datefait=dateLettre(date("d/m/Y"));
$pdf->MultiCell(60,5,"Fait à Kinshasa le $datefait",0,'C',0);

$pdf->SetXY($Xorigine+140,$Y+20);
$datefait=dateLettre(date("d/m/Y"));
$pdf->MultiCell(60,5,"Dr Richard DELAYE\nDoyen, président du jury",0,'C',0); 
$logo="./image/banniere/tampon_upa_signature.jpg";
if (file_exists($logo)) {
        $pdf->Image($logo,$Xorigine+145,$Y+31,55,30);
}


$pdf->SetFont('Arial','',6);
$pdf->SetXY(3,260);
$pdf->MultiCell(200,3,"International Management School Group est une association à vocation francophone\nprésente sur les site de Brazzaville-Kinshasa-Pointe-Noire. Partenaire du Groupe ESG - www.univpro-afrique.com \n\nAucun duplicata de ce document ne pourra être édité. Veillez à faire des copies.",0,'C',0);

if (($_POST["affdocattestation"] == "oui") && ($ectsGeneral >= 60)) {


	$pdf->AddPage();

	$X=80;
	$Y=5;

	// mise en place du logo

	$logo="./image/banniere/ESMK.jpeg";
        if (file_exists($logo)) {
                $pdf->Image($logo,10,3,20,20);
        }


	$logo="./image/banniere/ESGCI.jpg";
        if (file_exists($logo)) {
                $pdf->Image($logo,80,3,25,7);
        }
        $logo="./image/banniere/UPA_carre.jpg";
        if (file_exists($logo)) {
                $pdf->Image($logo,160,3,40,10);
        }


	$X=0;

	$pdf->SetXY($X+30,$Y+=30);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(150,5,"International Management School Group\nBRAZZAVILLE-KINSHASA-POINTE-NOIRE",0,'C',0);

	$pdf->SetXY($X+30,$Y+=20);
	$pdf->SetFont('Arial','B',14);
	$pdf->MultiCell(150,10,"ATTESTATION DE REUSSITE",1,'C',0);

	$pdf->SetXY($X+30,$Y+=10);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(150,10,"La Direction de la Scolarité atteste que",0,'C',0);


	$pdf->SetXY($X+30,$Y+=10);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(150,10,"$nomEleve $prenomEleve",0,'C',0);

	$pdf->SetXY($X+30,$Y+=5);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(150,10,"Né(e) le : $datenaissance à $lieunaissance a validé le",0,'C',0);
	 
	$pdf->SetXY($X+30,$Y+=10);
	$pdf->SetFont('Arial','B',10);
	$pdf->MultiCell(150,10,"$classenomlong",0,'C',0);	


	$pdf->SetXY($X+30,$Y+=10);
	$pdf->SetFont('Arial','',10);
	$info="";
	$info2="";
	if (chercherNiveauClasse($idClasse) == "A3") $info="\net à ce titre obtient le grade de Licence";
	if (chercherNiveauClasse($idClasse) == "M2") $info="\net à ce titre obtient le grade de Master";
	if (chercherNiveauClasse($idClasse) == "A2") $info2="portant à 120 crédits ECTS";
	if (chercherNiveauClasse($idClasse) == "A3") $info2="portant à 180 crédits ECTS";
	if (chercherNiveauClasse($idClasse) == "M1") $info2="portant à 240 crédits ECTS";
	if (chercherNiveauClasse($idClasse) == "M2") $info2="portant à 300 crédits ECTS";
	$pdf->MultiCell(150,5,"au titre de l'année universitaire $anneeScolaire\npar l'acquisition de $ectsGeneral crédits ECTS $info2 (crédits européens)$info",0,'C',0);


	$pdf->SetXY($Xorigine+20,180);
	$pdf->MultiCell(40,5,"$directeur\nDirecteur général",0,'C',0); 
	$logo="./image/banniere/ESMK.jpeg";
	if (file_exists($logo)) {
		$pdf->Image($logo,$Xorigine+30,192,20,20);	
	}

	$pdf->SetXY($Xorigine+150,170);
	$datefait=dateLettre(date("d/m/Y"));
	$pdf->MultiCell(50,5,"Fait à Kinshasa le $datefait ",0,'C',0); 


	$pdf->SetXY($Xorigine+150,180);
	$datefait=dateLettre(date("d/m/Y"));
	$pdf->MultiCell(50,5,"Dr Richard DELAYE\nDoyen, président du jury",0,'C',0); 

	$logo="./image/banniere/tampon_upa_signature.jpg";
	if (file_exists($logo)) {
        	$pdf->Image($logo,$Xorigine+145,192,55,30);
        //	$pdf->Image($logo,$Xorigine+160,192,55,30);
	}


	$pdf->SetFont('Arial','',6);
	$pdf->SetXY(3,260);
	$pdf->MultiCell(200,3,"L'Université professionnelle d’Afrique est une association à vocation francophone\nprésente sur les site de Brazzaville-Kinshasa-Pointe-Noire.\nPartenaire du Groupe ESG - www.univpro-afrique.com \n\nAucun duplicata de ce document ne pourra être édité. Veillez à faire des copies.",0,'C',0);

}

unset($ectsGeneral);
unset($ectsGLOBAL);



// ----------------------------------------------------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$nomEleve=TextNoCarac($nomEleve);
$nomEleve=TextNoAccent($nomEleve);
$prenomEleve=TextNoCarac($prenomEleve);
$prenomEleve=TextNoAccent($prenomEleve);
$nomEleve=trim($nomEleve);
$prenomEleve=trim($prenomEleve);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$classe_nom=preg_replace('/&/',"_",$classe_nom);
$nomEleve=preg_replace('/\//',"_",$nomEleve);
$prenomEleve=preg_replace('/\//',"_",$prenomEleve);

if ($_POST["saisie_trimestre"] == "trimestre1" ) $trie="semestre1";
if ($_POST["saisie_trimestre"] == "trimestre2" ) $trie="semestre2";
if ($_POST["saisie_trimestre"] == "trimestre3" ) $trie="annuel";

if (!is_dir("./data/pdf_bull/$classe_nom")) { mkdir("./data/pdf_bull/$classe_nom"); }
$fichier=urlencode($fichier);
$fichier="./data/pdf_bull/$classe_nom/bulletin_".$nomEleve."_".$prenomEleve."_".$trie.".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage($trie,$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { $merge->add("$fichier"); }
$listing.="$fichier ";
$pdf=new PDF();
} // fin du for on passe à l'eleve suivant
$merge->output("./data/pdf_bull/$classe_nom/liste_complete.pdf");
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
	$cmd="gs -q -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=./data/pdf_bull/$classe_nom/liste_complete.pdf -dBATCH $listing";
	$null=system("$cmd",$retval);
}
include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/'.$classe_nom.'.zip';
$bttexte="Récupérer le fichier ZIP des bulletins";
@nettoyage_repertoire('./data/pdf_bull/'.$classe_nom);
@rmdir('./data/pdf_bull/'.$classe_nom);
// --------------------------------------------------------------------------------------------------------------------------
?>
<br><ul><ul>
<input type=button onclick="open('visu_pdf_bulletin.php?id=<?php print $fichier?>&idclasse=<?php print $_POST["saisie_classe"] ?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul></ul>
<?php // ----------------------------------------------------------------------------------------------------------------------------   ?>



<br /><br />
<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom,$_POST["saisie_trimestre"],$dateDebut,$dateFin);
if($cr == 1){
		history_cmd($_SESSION["nom"],"CREATION BULLETIN","Classe : $classe_nom");
        	// alertJs("Bulletin créé -- Service Triade");
}else{
	error(0);
}
Pgclose();
?>

<?php
}else {
?>
<br />
<center>
<?php print LANGMESS14?> <br>
<br><br>
<font size=3><?php print LANGMESS15?><br>
<br>
<?php print LANGMESS16?><br>
</center>
<br /><br /><br />
<?php
        }
?>
<!-- // fin  -->
</td></tr></table>
<script language=JavaScript>attente_close();</script>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
// deconnexion en fin de fichier
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();

?>
