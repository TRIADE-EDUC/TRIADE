<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2024
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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
$nofooterPDF=NOFOOTERPDF;
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(300);
}

// recup année scolaire
$anneeScolaire=$_COOKIE['bulletinannee'];
if (isset($_POST["annee_scolaire"])) { $anneeScolaire=$_POST["annee_scolaire"]; }
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

$affECTS="oui";
$choixcommentaire="non";
$affnbabsmat="non";
$affcommentaire="non";
$affnbabsmat="non";
$affminmaxgeneral="non";
$hauteurmatiere=5;
$affphotoeleve="non";
$calculmoyenbrute="non";
$affTextDirPedago="non";
$affhautbasgeneral="non";



$debut=deb_prog();
$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"],$anneeScolaire);
if (count($valeur)) {
	if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; $sem=1; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2"; $sem=2; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; $sem=1; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; $sem=2; }
}


$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"],$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutP1=$dateRecup[$j][0];
	$dateFinP1=$dateRecup[$j][1];
}
$dateDebutP1=dateForm($dateDebutP1);
$dateFinP1=dateForm($dateFinP1);

$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"],$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutP2=$dateRecup[$j][0];
	$dateFinP2=$dateRecup[$j][1];
}
$dateDebutP2=dateForm($dateDebutP2);
$dateFinP2=dateForm($dateFinP2);

$dateRecup=recupDateTrimByIdclasse("trimestre3",$_POST["saisie_classe"],$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutP3=$dateRecup[$j][0];
	$dateFinP3=$dateRecup[$j][1];
}
$dateDebutP3=dateForm($dateDebutP3);
$dateFinP3=dateForm($dateFinP3);

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];

?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
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

// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$idClasse=$_POST["saisie_classe"];

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

if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }

// ------------------------
// calcul min et max general
//-------------------------

$max="";
$min=1000;

$recupUE=recupUE($idClasse,$sem);
for($g=0;$g<count($eleveT);$g++) {
	// variable eleve
       	$idEleve=$eleveT[$g][4];
        $noteMoyEleG=0;
        $coefEleG=0;
        $moyenEleve2="";
	for($f=0;$f<count($recupUE);$f++) {
                $code_ue=$recupUE[$f][0];
                $nom_ue=$recupUE[$f][1];
                $coef_ue=$recupUE[$f][2];
       	        $ects_ue=$recupUE[$f][3];
                $dejapasse=0;
		if ($calculmoyenbrute == "oui") {
			$moyenEU=moyenEleveUE($code_ue,$idClasse,$idEleve,$_POST["saisie_trimestre"],$dateDebutP1,$dateFinP1,$ordreaffichage);
                        if (trim($moyenEU) != "") {
        			$dataUE=recupUE($idClasse,$sem);
				$coef=$dataUE[0][2];
                                $noteMoyEleG+=$moyenEU*$coef;
                                $coefEleG+=$coef;
                        }
			continue;
		}

	        $listeMatiere=recupMatiereUE($code_ue,$idClasse);  // u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull, a.langue	
                // Verification si saut de page
                // ---------------------------
                $nbmatiere=0;
                for($i=0;$i<count($listeMatiere);$i++) {
                        $idmatiere=$idMatiere=$listeMatiere[$i][0];
       	                $ordreaffichage=$listeMatiere[$i][3];
                        $verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
                        if ($verifGroupe) { continue; }
                        $nbmatiere++;
                }
		for($i=0;$i<count($listeMatiere);$i++) {
	                $idmatiere=$listeMatiere[$i][0];
	                $idMatiere=$listeMatiere[$i][0];
	                $matierelong=chercheMatiereLong($idMatiere);
	                $matiere=$listeMatiere[$i][1];
	                $idprof=$listeMatiere[$i][2];
	                $ordreaffichage=$listeMatiere[$i][3];
	                $option=$listeMatiere[$i][5];
	                $verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
	                if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
	                // recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
        	        $idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffichage);
		        // cod_mat,sous_matiere,libelle
	                $datasousmatiere=verifsousmatierebull($idMatiere);
	                $coef=recupCoefUEviaGrp($idmatiere,$idClasse,$_POST["saisie_trimestre"],$idgroupe);
        	        $coef=preg_replace('/\.00$/','',$coef);
                        // ------------------------------------------------------
		        if (($idgroupe == "0") || (trim($idgroupe) == "")) {
		                $noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebutP1,$dateFinP1,$idprof);
	        	}else{
			        $noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebutP1,$dateFinP1,$idgroupe,$idprof);
		        }
			if (trim($noteaffP1) != "") {
				$coef=recupCoefUEviaGrp($idmatiere,$idClasse,$_POST["saisie_trimestre"],$idgroupe);
	                       	$coefEleG+=$coef;
		                $noteMoyEleG+=$noteaffP1*$coef;
			}
		}
	}

        if (trim($noteMoyEleG) != "") {
		$moyenEleve2=moyGenEleve($noteMoyEleG,$coefEleG);
        }

        if (trim($moyenEleve2) != "") {
		$moyenEleve2=preg_replace('/,/','.',$moyenEleve2);
	        $min=preg_replace('/,/','.',$min);
                $max=preg_replace('/,/','.',$max);
                if ($moyenEleve2 <= $min) { $min=$moyenEleve2; }
                if ($moyenEleve2 >= $max) { $max=$moyenEleve2; }
       	}
}

if ($min == 1000) { $min=""; }
$min=preg_replace('/\./',',',$min);
$max=preg_replace('/\./',',',$max);
$moyenClasseMin=$min;
$moyenClasseMax=$max;

unset($noteaffP1);
unset($coefEleG);
unset($noteMoyEleG);
unset($nbmatiere);

 
// fin min et max
// -------------

//$plageEleve=$_POST["plageEleve"];
$plageEleve="tous";
if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
if ($plageEleve == "10") { $dep=0; $nbEleveT=9; }
if ($plageEleve == "20") { $dep=9; $nbEleveT=19; }
if ($plageEleve == "30") { $dep=19; $nbEleveT=29; }
if ($plageEleve == "40") { $dep=29; $nbEleveT=39; }
if ($plageEleve == "50") { $dep=39; $nbEleveT=49; }
if ($plageEleve == "60") { $dep=49; $nbEleveT=59; }
if ($nbEleveT > count($eleveT)) { $nbEleveT=count($eleveT); }
for($j=$dep;$j<$nbEleveT;$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=strtoupper($eleveT[$j][0]);
	$prenomEleve=strtoupper($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	$moyenGenEleveNonBrute=0;
	$moyenGenP1NonBrute=0;
	$moyenGenClasseNonBrute=0;
	$moyenGenMinNonBrute=0;
	$moyenGenMaxNonBrute=0;
	$nbNoteNonBrute=0;
	$nbNoteNonBruteP1=0;
	$nbNoteNonBruteClasse=0;
	$nbNoteMoyenGeneralNonBrute=0;
	$moyenGeneralNonBrute=0;
	$dejapasse=0;
	
	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.org"); 


	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	$coordonne4="E-mail : ".$mail;


	$titre=LANGBULL30." ".ucwords($textTrimestre);

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="$prenomEleve $nomEleve";


	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	$appreciation=LANGBULL39;

	$appreciation2=LANGBULL40;
	$duplicata=LANGBULL41 . " - $urlsite - $mail";
	$signature=LANGBULL42;
	$signature2="";
	$signature="";
	// FIN variables

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo


	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);

	$Y=20;

	$X=10;
	$largeurInfo=90;
	$hauteurInfo=6;

	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell($largeurInfo,3,"Noms et prenoms / Name and surname ",0,'R',0);
	$pdf->SetXY($X+$largeurInfo,$Y);
	$pdf->SetFont('Arial','',12);
	$pdf->SetTextColor(255,0,0);
	$pdf->MultiCell(120,3,": $nomEleve $prenomEleve",0,'L',0);
	$pdf->SetTextColor(0,0,0);
	$Y+=$hauteurInfo;$X=10;

	// adresse de l'élève
	// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance,email_eleve,adr_eleve,ccp_eleve,commune_eleve
	$dataadresse=chercheadresse($idEleve);
	for($ik=0;$ik<=count($dataadresse);$ik++) {
		$nomtuteur=$dataadresse[$ik][1];
		$prenomtuteur=$dataadresse[$ik][2];
		$adr1=$dataadresse[$ik][3];
		$code_post_adr1=$dataadresse[$ik][4];
		$commune_adr1=$dataadresse[$ik][5];
		$matricule=$dataadresse[$ik][9];
		$datenaissance=$dataadresse[$ik][11];
		$lieunaissance=$dataadresse[$ik][19];
		if ($datenaissance != "") {  
			$datenaissance=dateForm($datenaissance); 
		}
		$regime=$dataadresse[$ik][12];
		$class_ant=trim(trunchaine($dataadresse[$ik][10],20));
		break;
	}

	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY($X,$Y);        
	$pdf->MultiCell($largeurInfo,3,"Date et lieu de naissance / Date and place of birth ",0,'R',0);
	$pdf->SetXY($X+$largeurInfo,$Y);
	$pdf->MultiCell(120,3,": $datenaissance     à/at  $lieunaissance",0,'L',0);
	$Y+=$hauteurInfo;$X=10;


	$pdf->SetFont('Arial','B',10);
        $pdf->SetXY($X,$Y);
        $pdf->MultiCell($largeurInfo,3,"Matricule / Register number ",0,'R',0);
        $pdf->SetXY($X+$largeurInfo,$Y);
        $pdf->MultiCell(120,3,": $matricule        Année académique / Academic year : $anneeScolaire",0,'L',0);
        $Y+=$hauteurInfo;$X=10;

	$level=chercherNiveauClasse($idClasse);

	$pdf->SetFont('Arial','B',10);
        $pdf->SetXY($X,$Y);
        $pdf->MultiCell($largeurInfo,3,"Niveau / Level ",0,'R',0);
        $pdf->SetXY($X+$largeurInfo,$Y);
        $pdf->MultiCell(120,3,": $level           Semestre / Semester  : $textTrimestre",0,'L',0);
        $Y+=$hauteurInfo;$X=10;


	$classeNOM=preg_replace('/_/',' ',$classe_nom);

        $pdf->SetFont('Arial','B',10);
        $pdf->SetXY($X,$Y); 
        $pdf->MultiCell($largeurInfo,3,"Filière / Discipline ",0,'R',0);
        $pdf->SetXY($X+$largeurInfo,$Y);        
	$pdf->MultiCell(120,3,": $classeNOM",0,'L',0);
        $Y+=$hauteurInfo;$X=10;

	// fin cadre du haut

	$Y+=5;
	$Xorigine=3;


// -------------------------------------------------------------------------
	// Barre des titres
	$X=$Xorigine;
	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(220);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(100,5,"INTITULE / TITLE",1,'C',0);
	$pdf->SetXY($X+=100,$Y);
	$pdf->MultiCell(15,15,"CODE",1,'C',0);
	$pdf->SetXY($X+=15,$Y); 
	$pdf->MultiCell(20,15,"",1,'C',0);
	$pdf->SetXY($X,$Y); 
	$pdf->MultiCell(20,5,"Crédits affectés / Credits ???? ",0,'C',0);
	$pdf->SetXY($X+=20,$Y); 
	$pdf->MultiCell(15,15,"",1,'C',0);
	$pdf->SetXY($X,$Y+3); 
	$pdf->MultiCell(15,5,"Note sur 20 ",0,'C',0);
	$pdf->SetXY($X+=15,$Y); 
	$pdf->MultiCell(25,15,"",1,'C',0);
	$pdf->SetXY($X,$Y+3); 
	$pdf->MultiCell(25,5,"Crédits acquis / Credits acquired ",0,'C',0);
	$pdf->SetXY($X+=25,$Y); 
	$pdf->MultiCell(30,15,"",1,'C',0);
	$pdf->SetXY($X,$Y+3); 
	$pdf->MultiCell(30,3,"Sessions de validation / Validation session ",0,'C',0);
	$pdf->SetXY($X,$Y); 

	$X=$Xorigine;
	$Y+=5;
	$pdf->SetXY($X,$Y); 
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(50,10,"CENTRE D'INTERETS / CENTER OK INTEREST",1,'C',0);
	$pdf->SetXY($X+=50,$Y);
	$pdf->MultiCell(50,10,"UNITES D'ENSEIGNEMENT / COURSES ",1,'C',0);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(50,5,"",0,'C',0);

	$Y+=10;

// ----------------------------------------------------

	if ($sem == 1) {
	        $recupUE=recupUE($idClasse,$sem);
	}else{
        	$recupUE=recupUETRIADE($idClasse,$sem); // code_ue,nom_ue,coef_ue,ects_ue,nom_ue_en
	}

	$ectsTOTALP1=0;
	$ectsTOTALP2=0;
	// mise en place des matieres
	$largeurMat=50;
	$hauteurMatiere=$hauteurmatiere; // taille du cadre matiere


	for($f=0;$f<count($recupUE);$f++) {
		$code_ue=$recupUE[$f][0];
		$nom_ue=$recupUE[$f][1];
		$coef_ue=$recupUE[$f][2];
		$ects_ue=$recupUE[$f][3];
		$dejapasse=0;
		if ($sem == 1) { 
			$listeMatiere=recupMatiereUE($code_ue,$idClasse);  // u.code_matiere,m.libelle,u.code_enseignant,a.ordre_affichage, a.visubull, a.langue	
		}else{
			$listeMatiere=recupMatiereUE2($nom_ue,$idClasse); 
		}
	
		// Verification si saut de page
		// ---------------------------
		$nbmatiere=0;
		for($i=0;$i<count($listeMatiere);$i++) {
			$idmatiere=$idMatiere=$listeMatiere[$i][0];
			$ordreaffichage=$listeMatiere[$i][3];
			$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
			if ($verifGroupe) { continue; } 
			$nbmatiere++;
		}
		if ($nbmatiere*$hauteurMatiere+$Y > 230) {
			$pdf->AddPage();
			$Y=7;
		}
		// ---------------------------
 		$X=$Xorigine;
		$pdf->SetFont('Arial','B',7);
//		$pdf->SetFillColor(220);
		$pdf->SetXY($X,$Y);
		$nom_ue=trunchaine($nom_ue,68);
		$nbmatiere=count($listeMatiere);
		$pdf->MultiCell($largeurMat,$hauteurMatiere*$nbmatiere,"",1,'L',0);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell($largeurMat,3,"$nom_ue",0,'L',0);
                

		$Xmemo=$X;	
		$Ymemo=$Y;
		$XorigineMatiere=$X+$largeurMat;
		
		$pdf->SetFillColor(255);


		$moyUEP1="";
		$coefUEP1="";

		$moyUEP2="";
		$coefUEP2="";
		
		$moyUECLASS="";
		$minUECLASS="";
		$maxUECLASS="";
		$coefUECLASS="";

		// u.code_matiere,m.libelle
		for($i=0;$i<count($listeMatiere);$i++) {
			$X=$XorigineMatiere;
			
			$idmatiere=$listeMatiere[$i][0];
			$idMatiere=$listeMatiere[$i][0];
			$matierelong=chercheMatiereLong($idMatiere);
			$matiere=$listeMatiere[$i][1];
			$idprof=$listeMatiere[$i][2];
			$ordreaffichage=$listeMatiere[$i][3];
			$option=$listeMatiere[$i][5];

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

			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($X,$Y);
			$matiere=$matierelong;
			$pdf->MultiCell($largeurMat,$hauteurMatiere,"",1,'L',0);
			$pdf->SetXY($X,$Y+1);
			//$profAff=recherche_personne2($idprof);
			$pdf->MultiCell($largeurMat,2,"$matiere",0,'L',0);
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY($X,$Y+4);
			$pdf->SetXY($X+=$largeurMat,$Y);
			
			$code=recupCodeMatiere($idmatiere);
			$pdf->MultiCell(15,$hauteurMatiere,"$code",1,'C',0); // coef ects
			$pdf->SetXY($X+=15,$Y);
			$ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
			$pdf->MultiCell(20,$hauteurMatiere,"$ects",1,'C',0); // coef ects
			$pdf->SetXY($X+=20,$Y);

			// mise en place du cadre note P1
			// ------------------------------------------------------
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {
				$noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebutP1,$dateFinP1,$idprof);
			}else{
				$noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebutP1,$dateFinP1,$idgroupe,$idprof);
			}
			unset($noteGen);
			// ------------------------------------------------
			$noteaffP11=$noteaffP1;
			if (($noteaffP1 < 10) && ($noteaffP1 != "")) { $noteaffP11="0".$noteaffP1; }
			$largA=15;
			$pdf->MultiCell($largA,$hauteurMatiere,"$noteaffP11",1,'C',0);
			$pdf->SetXY($X+=$largA,$Y);
			// GRADE
			// ---------------------------------------------------
			if ($noteaffP1 >= 10) { 
				$ectsvalider=$ects; 
			}else{ 
				//$moyenEU=moyenEleveUE($code_ue,$idClasse,$idEleve,$_POST["saisie_trimestre"],$dateDebutP1,$dateFinP1,$ordreaffichage);
				if (($moyenEU >= 10) && ($noteaffP1 >= 10)) {
					$ectsvalider=$ects; 
				}else{
					$ectsvalider="0"; 
				}
			}

			if ($ectsvalider == "0") {
				if ($noteaffP1 == "0") {
					$grade="F";
				}else{
					$grade="FX";
				}
			}
				

			$ectsTOTALP1+=$ects;
			$pdf->MultiCell(25,$hauteurMatiere,"",1,'C',0);
			$pdf->SetXY($X+=25,$Y);
			

			if (trim($noteaffP11) != "") {
				$moyUEP1+=$noteaffP1*$coef;
				$coefUEP1+=$coef;
			}

			// mise en place des moyennes de classe	
			if (($idgroupe == "0") || (trim($idgroupe) == "")) { 
				// idMatiere,datedebut,dateFin,idclasse
	       	  		$moyeMatGen=moyeMatGen($idmatiere,$dateDebutP1,$dateFinP1,$idClasse,$idprof);
			}else {
        			$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebutP1,$dateFinP1,$idgroupe,$idprof);
			}

			$moyeMatGenaff=$moyeMatGen;
			if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }

			if (1) {					
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
					$noteaffP2=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebutP2,$dateFinP2,$idprof);
				}else{
					$noteaffP2=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebutP2,$dateFinP2,$idgroupe,$idprof);
				}
				// ---------------------------------------------------------------------------------------
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
					$classement=Rangs($idmatiere,$dateDebutP2,$dateFinP2,$idClasse,$idprof);
    				}else {
        				$classement=RangsGroupe($idmatiere,$dateDebutP2,$dateFinP2,$idgroupe,$idprof);
				}
				$noterang=$noteaffP2;
				// ---------------------------------------------------------------------------------------
				$noteaff2=$noteaffP2;
				if (($noteaffP2 < 10) && ($noteaffP2 != "")) { $noteaff2="0".$noteaffP2; }

				$pdf->MultiCell(30,$hauteurMatiere," AAA ",1,'C',0);
				// --------------------------------------------------
			
				if ($noteaff2 != "") {
					$moyUEP2+=$noteaff2*$coef;
					$coefUEP2+=$coef;
				}

				// GRADE
				// ---------------------------------------------------
				if ($noteaff2 >= 10) { 
					$ectsvalider=$ects; 
				}else{ 
				       $moyenEU=moyenEleveUE($code_ue,$idClasse,$idEleve,$_POST["saisie_trimestre"],$dateDebutP2,$dateFinP2,$ordreaffichage);
				       if (($moyenEU >= 10) && ($noteaff2 >= 6)) {
						$ectsvalider=$ects; 
				       }else{
						$ectsvalider="0"; 
				       }
				}

				if ($dejapasse == 0) {
					$noteGen=moyenEleveUESansOPT4($code_ue,$idClasse,$idEleve,$_POST["saisie_trimestre"],$dateDebutP1,$dateFinP2,$ordreaffichage);
					if ( $noteGen != "" ) {
				    		$noteMoyEleGTempo = $noteGen * $coef_ue;
						$noteGen2=$noteGen2 + $noteMoyEleGTempo;
						$coefGen2=$coefGen2 + $coef_ue ;
					}
					$dejapasse=1;
				}		

				$nrang=0;
				$rang="";
				foreach ($classement as $key => $val) {	
					$nrang++;
					if ($val == $noterang){ break; }
				}		
				$nbtotalRang=count($classement);

				$pourcentA=ceil(($nbtotalRang/100)*10);
				if ($nrang > 0 && $nbrang <= $pourcentA) { $grade="A"; }
				$pourcentB=ceil(($nbtotalRang/100)*25);
				if ($nrang > $pourcentA && $nbrang <= $pourcentB) { $grade="B"; }
				$pourcentC=ceil(($nbtotalRang/100)*30);
				$pourcentBB+=$pourcentA;
				if ($nrang > $pourcentBB && $nbrang <= $pourcentC) { $grade="C"; }
				$pourcentD=ceil(($nbtotalRang/100)*25);
				$pourcentCC+=$pourcentB;
				if ($nrang > $pourcentCC && $nbrang <= $pourcentD) { $grade="D"; }
				$pourcentE=ceil(($nbtotalRang/100)*10);
				$pourcentDD+=$pourcentC;
				if ($nrang > $pourcentDD && $nbrang <= $pourcentE) { $grade="E"; }

				if ($ectsvalider == "0") {
					if ($noteaff2 == 0) {
						$grade="F";
					}else{
						$grade="FX";
					}
				}
				if ($noteaff2 == "") {
					$ectsvalider="";
					$grade="";
				}			

				$ectsTOTALP2+=$ectsvalider;

				// mise en place des moyennes de classe	
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
					// idMatiere,datedebut,dateFin,idclasse
	        	   		$moyeMatGen=moyeMatGen($idmatiere,$dateDebutP2,$dateFinP2,$idClasse,$idprof);
				}else {
	           			$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebutP2,$dateFinP2,$idgroupe,$idprof);
				}

				$moyeMatGenaff=$moyeMatGen;
				if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
				// --------------------------------------------------
			}

			if ($_POST["saisie_trimestre"] == "trimestre1") { $dateDebut=$dateDebutP1; $dateFin=$dateFinP1; }
			if ($_POST["saisie_trimestre"] == "trimestre2") { $dateDebut=$dateDebutP2; $dateFin=$dateFinP2; }
			
			// calcul du min et du max
			if (($idgroupe == "0") || (trim($idgroupe) == "")) {    // non matiere affectée à un groupe
				$max="";
				$min=1000;
				for($g=0;$g<count($eleveT);$g++) {
					// variable eleve
					$idEleveMoyen=$eleveT[$g][4];
					$valeur=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
					if (trim($valeur) != "") {
						if ($valeur >= $max) { $max=$valeur; }
						if ($valeur <= $min) { $min=$valeur; }
					}
				}
				if ($min == 1000) { $min=""; }
				$moyeMatGenMin=$min;
				$moyeMatGenMax=$max;
			}else{
				$max="";
				$min=1000;
				$eleveTg=listeEleveDansGroupe($idgroupe);
				for($g=0;$g<count($eleveTg);$g++) {
					$idEleveMoyen=$eleveTg[$g];
					$valeur=moyenneEleveMatiereGroupe($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
					if (trim($valeur) != "") {	
						if ($valeur >= $max) { $max=$valeur; }
						if ($valeur <= $min) { $min=$valeur; }
					}
				}
				if ($min == 1000) { $min=""; }
				$moyeMatGenMin=$min;
				$moyeMatGenMax=$max;
			}
			$moyeMatGenMinaff=$moyeMatGenMin;
			$moyeMatGenMaxaff=$moyeMatGenMax;
			if (($moyeMatGenMin < 10) && ($moyeMatGenMin != "")) { $moyeMatGenMinaff="0".$moyeMatGenMin; }
			if (($moyeMatGenMax < 10) && ($moyeMatGenMax != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMax; }
			// fin de la calcul de min et max

			if (is_numeric($moyeMatGenaff)) {
				$coefUECLASS+=$coef;
				$moyUECLASS+=$moyeMatGenaff*$coef;
				$minUECLASS+=$moyeMatGenMinaff*$coef;
				$maxUECLASS+=$moyeMatGenMaxaff*$coef;
			}

			if (is_numeric($noteaffP1)) {
	    			$noteMoyEleGTempo = $noteaffP1 * $coef;
           		     	$noteMoyEleG1=$noteMoyEleG1 + $noteMoyEleGTempo;
           		    	$coefEleG=$coefEleG + $coef;
			}


			if (is_numeric($noteaffP2)) {
	    			$noteMoyEleGTempo = $noteaffP2 * $coef;
				$noteMoyEleG2=$noteMoyEleG2 + $noteMoyEleGTempo;
				$coefEleG2=$coefEleG2 + $coef;
			}

			

			$Y+=$hauteurMatiere;


		}


	
	


		// ligne Unité enseignement


		// ----------
		if ($_POST["saisie_trimestre"] == "trimestre1") {
			if (trim($moyUEP1) != "") {
				$moyUEP1=$moyUEP1/$coefUEP1;
				$moyUEP1Aff=$moyUEP1;

		        	$moyenGenEleveNonBrute+=$moyUEP1*$coef_ue;
				$nbNoteNonBrute+=$coef_ue;

				if (($moyUEP1Aff < 10) && ($moyUEP1Aff != "")) { $moyUEP1Aff="0".$moyUEP1; }
				$moyUEP1Aff = number_format($moyUEP1Aff, 2, '.', '');

			}else{
				$moyUEP1Aff="";
			}
			$pdf->SetFillColor(220);
			$pdf->SetXY($Xmemo+=10,$Ymemo); 



		}else{
			if (is_numeric($moyUEP1)) {
				$moyUEP1=$moyUEP1/$coefUEP1;
				$moyUEP1Aff=$moyUEP1;
				if (($moyUEP1Aff < 10) && ($moyUEP1Aff != "")) { $moyUEP1Aff="0".$moyUEP1; }
				$moyUEP1Aff = number_format($moyUEP1Aff, 2, '.', '');
				$moyenGenP1NonBrute+=$moyUEP1Aff*$coef_ue;
				$nbNoteNonBruteP1+=$coef_ue;
				
				$nbNoteMoyenGeneralNonBrute+=$coef_ue;
				$moyenGeneralNonBrute+=$moyUEP1Aff*$coef_ue;
			}else{
				$moyUEP1Aff="";
			}
			$pdf->SetFillColor(220);
			$pdf->SetXY($Xmemo+=10,$Ymemo); 

			if (is_numeric($moyUEP2)) {
				$moyUEP2=$moyUEP2/$coefUEP2;
				$moyUEP2Aff=$moyUEP2;
				if (($moyUEP2Aff < 10) && ($moyUEP2Aff != "")) { $moyUEP2Aff="0".$moyUEP2; }
				$moyUEP2Aff = number_format($moyUEP2Aff, 2, '.', '');
				$moyenGenEleveNonBrute+=$moyUEP2Aff*$coef_ue;		
				$nbNoteNonBrute+=$coef_ue;

				$nbNoteMoyenGeneralNonBrute+=$coef_ue;
				$moyenGeneralNonBrute+=$moyUEP2Aff*$coef_ue;
			}else{
				$moyUEP2Aff="";
			}
			$pdf->SetXY($Xmemo+=10,$Ymemo); 
			$larg=10;

			$moyUEP2Aff="";
			unset($moyUEP2);
			unset($coefUEP2);
			// -----------
			$pdf->SetXY($Xmemo+=$larg,$Ymemo);
		}

//		$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
		$coef=recupCoefUEviaGrp($idmatiere,$idClasse,$_POST["saisie_trimestre"],$idgroupe);
		if (is_numeric($moyUECLASS)) {

			$moyUECLASS=$moyUECLASS/$coefUECLASS;
			$moyUECLASSG+=$moyUECLASS*$coef;
			$coefUECLASSG+=$coef;
			$moyUECLASSAff=$moyUECLASS;

			if (($moyUECLASSAff < 10) && ($moyUECLASSAff != "")) { $moyUECLASSAff="0".$moyUECLASS; }
			$moyUECLASSAff = number_format($moyUECLASSAff, 2, '.', '');
			$moyenGenClasseNonBrute+=$moyUECLASSAff*$coef_ue;
			$nbNoteNonBruteClasse+=$coef_ue;
		}else{
			$moyUECLASSAff="";
		}

		if (is_numeric($minUECLASS)) {
			$minUECLASS=$minUECLASS/$coefUECLASS;
			$minUECLASSAff=$minUECLASS;
			$minUECLASSG+=$minUECLASS*$coef;

			if (($minUECLASSAff < 10) && ($minUECLASSAff != "")) { $minUECLASSAff="0".$minUECLASS; }
			$minUECLASSAff = number_format($minUECLASSAff, 2, '.', '');
			$moyenGenMinNonBrute+=$minUECLASSAff*$coef_ue;
		}else{
			$minUECLASSAff="";
		}

		if (is_numeric($maxUECLASS)) {
			$maxUECLASS=$maxUECLASS/$coefUECLASS;
			$maxUECLASSAff=$maxUECLASS;
			$maxUECLASSG+=$maxUECLASS*$coef;

			if (($maxUECLASSAff < 10) && ($maxUECLASSAff != "")) { $maxUECLASSAff="0".$maxUECLASS; }
			$maxUECLASSAff = number_format($maxUECLASSAff, 2, '.', '');
        		$moyenGenMaxNonBrute+=$maxUECLASSAff*$coef_ue;
		}else{
			$maxUECLASSAff="";
		}

}
// fin de la mise en place des matiere

$X=3;
$pdf->SetXY($X,$Y); 
$pdf->MultiCell($largeurMat,20," ",1,'C',0);
$pdf->SetXY($X,$Y+3); 
$pdf->MultiCell($largeurMat,20,"Bilan",0,'L',0);
$pdf->SetXY($X+=$largeurMat,$Y); 
$pdf->MultiCell($largeurMat,10,"Total des crédits / Total Credits",1,'C',0);
$pdf->SetXY($X+=$largeurMat,$Y); 
$pdf->MultiCell(15,10,"Moyenne",1,'C',0);
$pdf->SetXY($X+=15,$Y); 
$pdf->MultiCell(20,10,"Grade point",1,'C',0);
$pdf->SetXY($X+=20,$Y); 
$pdf->MultiCell(15,10,"Grade",1,'C',0);
$pdf->SetXY($X+=15,$Y); 
$pdf->MultiCell(25,10,"MENTION",1,'C',0);
$pdf->SetXY($X+=25,$Y); 
$pdf->MultiCell(30,10,"DECISION",1,'C',0);
$Y+=10;
$X=3+$largeurMat;


$pdf->SetXY($X,$Y); 
$pdf->MultiCell($largeurMat,10,"QQQQ",1,'C',0);
$pdf->SetXY($X+=$largeurMat,$Y); 
$pdf->MultiCell(15,10,"ZZZZ",1,'C',0);
$pdf->SetXY($X+=15,$Y); 
$pdf->MultiCell(20,10,"EEEE",1,'C',0);
$pdf->SetXY($X+=20,$Y); 
$pdf->MultiCell(15,10,"RRRR",1,'C',0);
$pdf->SetXY($X+=15,$Y); 
$pdf->MultiCell(25,10,"TTTT",1,'C',0);
$pdf->SetXY($X+=25,$Y); 
$pdf->MultiCell(30,10,"YYYY",1,'C',0);





$coefUECLASS=0;
unset($maxUECLASSG);
unset($minUECLASSG);
unset($moyUECLASSG);
unset($moyEleveG1);
unset($moyEleveG2);
unset($noteMoyEleG1);
unset($noteMoyEleG2);
unset($coefEleG);
unset($coefGen2);
unset($coefEleG2);
unset($noteGen2);
unset($coefUECLASSG);
unset($nbMoyenGeneral);

// ---------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$nomEleve=TextNoCarac($nomEleve);
$nomEleve=TextNoAccent($nomEleve);
$prenomEleve=TextNoCarac($prenomEleve);
$prenomEleve=TextNoAccent($prenomEleve);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$nomEleve=preg_replace('/\//',"_",$nomEleve);
$prenomEleve=preg_replace('/\//',"_",$prenomEleve);
if (!is_dir("./data/pdf_bull/$classe_nom")) { mkdir("./data/pdf_bull/$classe_nom"); }
$fichier=urlencode($fichier);
$fichier="./data/pdf_bull/$classe_nom/bulletin_".$nomEleve."_".$prenomEleve."_".$_POST["saisie_trimestre"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage($_POST["saisie_trimestre"],$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { $merge->add("$fichier"); }
$listing.="$fichier ";
$pdf=new PDF();
} // fin du for on passe à l'eleve suivant
$merge->output("./data/pdf_bull/$classe_nom/liste_complete.pdf");
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
	$listing=preg_replace('/\(/',"\(",$listing);
	$listing=preg_replace('/\)/',"\)",$listing);
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
       	//alertJs("Bulletin créé -- Service Triade");
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
