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
	set_time_limit(9000);
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

$nomdivision=$_POST["nomdivision"];

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
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
if (count($valeur)) {

	if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="1er Trimestre"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="2ème Trimestre"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre="3ème Trimestre"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="1er Semestre"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="2ème Semestre"; }
}

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=strtoupper($data[0][1]);



// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
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
$data=visu_param();
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
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

// recherche des dates de debut et fin
// $dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$profAffOld="";

$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve


$moyenClasseGen=""; // pour le calcul moyenne classe
$moyenClasseMin=1000; // pour la calcul moyenne min classe
$moyenClasseMax=""; // pour la calcul moyenne max  classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

// pour le calcul de moyenne classe
$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// ----------------------------


// calcul min et max general
//-------------------------
	$max="";
	$min=1000;
	for($g=0;$g<count($eleveT);$g++) {
		// variable eleve
		$idEleveMoyen=$eleveT[$g][4];
		$noteMoyEleG=0;
		$coefEleG=0;
		$moyenEleve2="";

		for($t=0;$t<count($ordre);$t++) {
			$idMatiere=$ordre[$t][0];
			$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$t][2]);

			$verifGroupe=verifMatiereAvecGroupe($ordre[$t][0],$idEleveMoyen,$idClasse,$ordre[$t][2]);
			if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

			$noteaff=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$t][2]);
	
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coeffaff;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coeffaff;
			}
		}

		if (MODNAMUR0 == "oui") {
			$noteaff=calculNoteVieScolaire($idEleveMoyen,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coefBull;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefBull;
			}
		}

		if ($noteMoyEleG != "") {
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
// fin min et max
// -------------

$plageEleve=$_POST["plageEleve"];
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
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];
	$ii=0;
	//---------------------------------//
	// recherche le nombre de retard
	$nbretard=0;
	$nbretard1=0;
	$nbheureabs=0;
	$nbjoursabs=0;
	$nbabs=0;
	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbretard=count($nbretard);
	// recherche le nombre d absence
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	for($o=0;$o<=count($nbabs);$o++) {
		if ($nbabs[$o][8] == 1) {
			if ($nbabs[$o][4] > 0) {
		       		$nbjoursabsJustifie = $nbjoursabsJustifie + $nbabs[$o][4];
			}else{
				$nbheureabsJustifie = $nbheureabsJustifie + $nbabs[$o][7];
				
			}
		}else{
			if ($nbabs[$o][4] > 0) {
		       		$nbjoursabsNonJustifie = $nbjoursabsNonJustifie + $nbabs[$o][4];
			}else{
				$nbheureabsNonJustifie = $nbheureabsNonJustifie + $nbabs[$o][7];	
			}
		}
		$nbjoursabsJustifie=$nbheureabsJustifie/2;
		$nbjoursabsNonJustifie=$nbjoursabsNonJustifie/2;
	}
	
	
		
		$nbabs=$nbjoursabs * 2;
	//---------------------------------//



	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
	$pdf->SetPiedPage(date("d/m/Y"));


	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	//$coordonne4="E-mail : ".$mail;


	$titre="<B> Bulletin  Scolaire - ".$textTrimestre."</B>";

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",38);
	
	$effbordure=1;

	$infoeleve="Nom - Prénom ";
	$infoeleveNP=$nomprenom;

	$infoeleveclasse=trim($classe_nom);


	$appreciation="Observations du conseil de classe : ";

	//$appreciationbis="($nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ) " ;

	$barre="____________________________________________________________________________________________________";
	$appreciation2="Avis du chef d'établissement : ";
	$duplicata=LANGBULL41. ". Aucun duplicata ne sera délivré.";
	$signature=LANGBULL42;
	$signature2="";
	$signature="";
	// FIN variables


	// mise en place du logo
       	$logo="./image/banniere/banniere-jeanperrin.jpg";
	$xlogo=25;
	$ylogo=20;
	$pdf->Image($logo,4,3,$xlogo,$ylogo);


	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);

	$xcoor0=33;
	$ycoor0=3;

	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($xcoor0,$ycoor0+5);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY($xcoor0,$ycoor0+10);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY($xcoor0,$ycoor0+15);
	$pdf->WriteHTML($coordonne3);
	//fin coordonnees


	// cadre eleve
	$ycoor0=3;
	$xcoor0=85;
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(230,230,255);
	$pdf->SetXY($xcoor0,$ycoor0); // placement du cadre du nom de l eleve
	//$pdf->MultiCell(120,20,'',1,'L',1);
	$pdf->RoundedRect($xcoor0, $ycoor0, 120, 23, 3.5, 'DF');

	$photoeleve=image_bulletin($idEleve);

	$photo=$photoeleve;
	$xphoto=185;
	$yphoto=5;
	$photowidth=10.8;
	$photoheight=16.3;

	if (!empty($photo)) {
		$photo=$photoeleve;
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
	}
	$pdf->SetXY($xcoor0+=2,5); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	$pdf->SetXY($xcoor0+25,5);
	$pdf->WriteHTML(" : $infoeleveNP");

	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
	for($ik=0;$ik<=count($dataadresse);$ik++) {
		$datenaissance=$dataadresse[$ik][11];
		if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
		$INE=$dataadresse[$ik][9];
		
		$pdf->SetXY($xcoor0,10); 
		$pdf->WriteHTML("Né(e) le ");
		$pdf->SetXY($xcoor0+25,10); 
		$pdf->WriteHTML(" : $datenaissance");
		$pdf->SetXY($xcoor0+25+25,11);
		$pdf->MultiCell(40,3,"INE : $INE",0,'L',0);

	}

	$pdf->SetXY($xcoor0,15);
	$pdf->WriteHTML("Classe ");
	$pdf->SetXY($xcoor0+25,15);
	$pdf->WriteHTML(" : <b>$infoeleveclasse</b>");


	$pdf->SetXY($xcoor0,20);
	$pdf->WriteHTML("Année scolaire ");
	$pdf->SetXY($xcoor0+25,20);
	$pdf->WriteHTML(" : $anneeScolaire");

	$xtitre=70;
	// Titre
	$pdf->SetXY($xtitre,27);
	$pdf->SetFont('Courier','',12);
	$pdf->WriteHTML($titre);
	// fin titre



	// fin cadre du haut
	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;
	// cadre des notes
	// ---------------
	// Barre des titres
	$pdf->SetFont('Arial','B',6);
	$pdf->SetXY(3,35); //  placement  cadre titre
	$pdf->MultiCell(204,6,'',1,'C',1);
	$pdf->SetXY(3,35); // placement contenu titre
	//$pdf->WriteHTML($titrenote1);
	$pdf->SetXY(59-10,35);
	$pdf->MultiCell(10,3,'Note élève',0,'C',0);

	$pdf->SetXY(71-7,35);
	$pdf->MultiCell(10,3,'Coef.',0,'C',0);

	$pdf->SetXY(81-9,35);
	$pdf->MultiCell(10,3,'Note mini',0,'C',0);
	$pdf->SetXY(92-12.5,35);
	$pdf->MultiCell(10,3,'Note maxi',0,'C',0);
	$pdf->SetXY(102-15,35);
	$pdf->MultiCell(10,3,'moy. classe',0,'C',0);
	$pdf->SetXY(110-15,36);
	$pdf->MultiCell(100,3,'Appréciation par Disciplines',0,'C',0);
	// fin des titres

	$nbs=0;

	// Mise en place des matieres et nom de prof
	$Xmat=3;
	$Ymat=35+6;
	$Xmatcont=$Xmat+1;
	$Ymatcont=$Ymat;

	$Xprof=55;
	$Yprof=$Ymat;
	$Ycoeff=$Ymat;
	$Xmoyeleve=55+$Xmat;
	$Xcoeff=$Xmoyeleve+15;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xcoeff + 7;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=56;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=55 + 4;
	$YnotVal=$Ycoeff + 3;
	$XcoeffVal=$Xcoeff + 1;
	$YcoeffVal=$Ymat + 3;
	$XprofVal=20; // x en nom prof
	$YprofVal=$Ymat + 4; // y en nom du prof
	$XmoyMatGVal=$Xcoeff + 7 ;
	$YmoyMatGVal=$Ycoeff + 3 ;

	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
	$coefEleG=0;
	$ii=0;
	$vv=0;$vv1=0;$vv11=0;
	$TT=1;

	$nbMatiere=nbMatiere($ordre,$idEleve,$idClasse);
	$haut=165/$nbMatiere;

	if ($haut > 10.2) {
		$policy=2;
		$juste=1;
	}else{
		$haut=10.2;
		$policy=0;
		$juste=0;
	}

	for($i=0;$i<count($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);


		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


		// mise en place des matieres

		$largeurMat=45;
		$hauteurMatiere=$haut; // taille du cadre matiere  default 10.2
		//si plus de 10 matieres on reinitalise les positions;

		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		//	print $datasousmatiere;
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}

		// fin de la gestion sous matiere
		// ------------------------------
		
		if ($Ymat >= 220) {
			$pdf->AddPage();
			$Xmat=3;
			$Ymat=35+6;
			$Xmatcont=$Xmat+1;
			$Ymatcont=$Ymat;

			$Xprof=55;
			$Yprof=$Ymat;
			$Ycoeff=$Ymat;
			$Xmoyeleve=55+$Xmat;
			$Xcoeff=$Xmoyeleve+15;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xcoeff + 7;
			$Ymoyclasse=$Ymat;


			$XnomProfcont=56;
			$YnomProfcont=$Ymatcont;
			$Xnote=$Xmoyclasse + 32;
			$Ynote=$Ymat;
			$XnotVal=55 + 4;
			$YnotVal=$Ycoeff + 3;
			$XcoeffVal=$Xcoeff + 1;
			$YcoeffVal=$Ymat + 3;
			$XprofVal=20; // x en nom prof
			$YprofVal=$Ymat + 4; // y en nom du prof
			$XmoyMatGVal=$Xcoeff + 7 ;
			$YmoyMatGVal=$Ycoeff + 3 ;
		}
	

		$sousmatiere=trim($ordre[$i][4]);   // NEW
		$libelleMatiere=$ordre[$i][5]; 
		$ordrematiere=$ordre[$i][3];  // NEW
		$ii=$i;
		while(true) {
			$ii++;
			if (verifMatiereAvecGroupe($ordre[$ii][0],$idEleve,$idClasse,$ordre[$ii][2])) { $TT=1;break; }
			if (($sousmatiere != "0") && ($sousmatiere != "")){
				if(!verifMatiereSuivanteCommeSousmatiere($ordre[$ii][0])) { $TT=1;break; }
				$matiereSuivante=chercheMatiereNom3($ordre[$ii][0]);
				// print "TT:$TT $libelleMatiere -- $matiereSuivante ";
				if ( trim($libelleMatiere) == trim($matiereSuivante)) {
					$TT=$TT+1;
					$tabsous["$libelleMatiere"]=$TT;
				}else{
					$TT=1;
					break;
				}
			}else{
				$TT=1;
				break;
			}
			$sousmatiere=trim($ordre[$ii][4]);
		 }

		 //print_r($tabsous);

		$sousmatiere=$ordre[$i][4];
		if (($sousmatiere == "0") || ($sousmatiere=="")) { $sousmatiere=""; }

		//
		$nbs--;
		if ($nbs < 0) { $nbs=0; } 
		if($nbs == 0) {
			$deja=0;
			foreach($tabsous as $key4=>$val4) {
				if ($key4 == $libelleMatiere) {
					$effbordure=0;
					$nbs=$val4;
				}
			}
		}else{
			$effbordure=1;
			$effbordure2=0;
		}
		//print $effbordure." $libelleMatiere <br>";

		if ($deja >= 1) {
			$libelleMatiere="";
			$effbordure2=0;
		}else{
			$effbordure2=1;
		}
	

		if ($effbordure == 0 ){
			$posiNoteSous=$nbs;	
			$deja++;
			$effbordure2=0;	
		}
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$libelleMatiere=preg_replace('/0$/','',$libelleMatiere);
		
		if (($sousmatiere != "") && ($sousmatiere != "0")) {
			$nblimit=14;
		}else{
			$nblimit=26;
		}

		$pdf->WriteHTML('<b>'.trunchaine(strtoupper(sansaccent(strtolower($libelleMatiere))),$nblimit).'</B>');

		if (($sousmatiere != "") && ($sousmatiere != "0")) {	
			$sousmatiere=preg_replace('/0$/','',$sousmatiere);
			$sousmatiere=trunchaine(ucwords($sousmatiere),10);	
		}
		unlink($sousmatiere);

		$pdf->SetFont('Arial','',8);
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		

		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve-10,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		$pdf->MultiCell(18,$hauteurMatiere,'',1,'L',0);


		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff-7,$Ycoeff);
		$pdf->MultiCell(7,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;
	


		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse-7,$Ymoyclasse);
		$pdf->MultiCell(22,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;


		if ($effbordure == 0 ){
			$pdf->SetXY($Xnote-17,$Ynote);
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(112,$H,'',1,'L',0);
		}

		// mise en place du cadre commentaire
		$pdf->SetXY($Xnote-17,$Ynote);
		$pdf->MultiCell(112,$hauteurMatiere,'',1,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
		unset($noteaff);	
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		}


		if (($sousmatiere != "0") && ($sousmatiere != "")){
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($XnotVal-11,$YnotVal-3);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			if ($noteaff1 != "") {  $noteaff1=arrondiaudixieme($noteaff1);  }
			$sousmatiere=trunchaine2($sousmatiere,5);
			$pdf->WriteHTML("$sousmatiere:$noteaff1");
			unset($noteaff1);
			$coefsous=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
			if ($noteaff != "") {
				$notesous=$noteaff*$coefsous;
				$notesoustotal1=$notesoustotal1+$notesous;
				$coefsoustotal1=$coefsoustotal1+$coefsous;
			}
		
			$ip=$i+1;

			if (verifMatiereAvecGroupe($ordre[$ip][0],$idEleve,$idClasse,$ordre[$ip][2])) {
				$matiereSuivante="";
			}else{
				$matiereSuivante=chercheMatiereNom3($ordre[$ip][0]);
			}
			$matiereEnCours=$ordre[$i][5];

			if ( trim($matiereEnCours) != trim($matiereSuivante)) {
				$matierepre=$matiereEnCours;
				if ($notesoustotal1 != "") {
					$notesousmoyen=$notesoustotal1/$coefsoustotal1;
					$notesousmoyen=number_format($notesousmoyen,2,'.','');
					$noteaff1=$notesousmoyen;
					if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
				}
				$pdf->SetFont('Arial','',12);
				if ($posiNoteSous == 5) {
					$ajus=$posiNoteSous*4;  //OK
				}elseif($posiNoteSous == 4){
					$ajus=$posiNoteSous*3;  //OK
				}elseif($posiNoteSous == 3){
					$ajus=$posiNoteSous*4;
				}elseif($posiNoteSous == 2){
					$ajus=$posiNoteSous*2;  //OK
				}else{
					$ajus=$posiNoteSous*2;
				}

				//$pdf->SetFont('Arial','',8);
				//$pdf->SetXY($XnotVal-$largeurMat,$YnotVal);
				//$pdf->WriteHTML("Moyenne : $noteaff1");
				$pdf->SetFont('Arial','',12);
				unset($noteaff1);
				unset($notesoustotal1);
				unset($coefsoustotal1);
				$YnotVal=$YnotVal - (3 * $vv);
				$YnotVal=$YnotVal + $hauteurMatiere;
				$vv=0;	
			}else{
				$YnotVal=$YnotVal + 3;	
				$vv++;
				$Ymat=$Ymat - $hauteurMatiere;
				$Ymatcont=$Ymatcont - $hauteurMatiere;
				$Ynote = $Ynote - $hauteurMatiere;
				$Ycoeff = $Ycoeff - $hauteurMatiere;
			}
			
		 	$policeSousMatiere=8;$top=3;
			
		}else{
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY($XnotVal-11,$YnotVal);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			if ($noteaff1 != "") { $noteaff1=arrondiaudixieme($noteaff1); }
			$pdf->WriteHTML($noteaff1);
			unset($noteaff1);
			$YnotVal=$YnotVal + $hauteurMatiere;
			$policeSousMatiere=9;
			$top=0;
		}

		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($XcoeffVal-8,$YcoeffVal-$top);
		if ($coeffaff == "0") {
			$pdf->WriteHTML("F");
		}else{
			$coeffaff=number_format($coeffaff,1,'.','');
			$pdf->WriteHTML($coeffaff);
		}
		
		if ($vv == 0 ) {
			$YcoeffVal=$YcoeffVal - (3*$vv11); 
			$YcoeffVal=$YcoeffVal + $hauteurMatiere; 
			$vv11=0;
		}else{
			$YcoeffVal=$YcoeffVal + 3; 
			$vv11++;
		}
		

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
           		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
    		}


		// calcul du min et du max
		if ($idgroupe == "0") {   // non matiere affectée à un groupe
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
		}else {
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
		// fin de la calcul de min et max
		
		// mise en place MoyenMatiereClasse
		
		$XmoyMatGenMinVal=$XmoyMatGVal + 11;
		$XmoyMatGenMaxVal=$XmoyMatGVal + 21;

		$pdf->SetFont('Arial','',$policeSousMatiere-2);
		$pdf->SetXY($XmoyMatGenMaxVal-13,$YmoyMatGVal-$top);
		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff!="")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		if ($moyeMatGenaff != "") { $moyeMatGenaff=arrondiaudixieme($moyeMatGenaff); }
		$pdf->WriteHTML($moyeMatGenaff);

		// mise en place du min
		$pdf->SetXY($XmoyMatGVal-6,$YmoyMatGVal-$top);
		$moyeMatGenMinaff=$moyeMatGenMin;
		if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff!="")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
		if ($moyeMatGenMinaff != "") { $moyeMatGenMinaff=arrondiaudixieme($moyeMatGenMinaff); }
		$pdf->WriteHTML($moyeMatGenMinaff);

		// mise en place du max
		$pdf->SetXY($XmoyMatGenMinVal-10,$YmoyMatGVal-$top);
		$moyeMatGenMaxaff=$moyeMatGenMax;
		if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff!="")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
		if ($moyeMatGenMaxaff != "") { $moyeMatGenMaxaff=arrondiaudixieme($moyeMatGenMaxaff);}
		$pdf->WriteHTML($moyeMatGenMaxaff);
	
		$Ycom=$YmoyMatGVal - 3;
	
	
		if ($vv == 0 ) {
			$YmoyMatGVal=$YmoyMatGVal - (3*$vv1); 
			$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere; 
			$vv1=0;
		}else{
			$YmoyMatGVal=$YmoyMatGVal + 3; 
			$vv1++;
		}

		// mise en place des commentaires
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
		$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
		$commentaireeleve=preg_replace("/^,/","",$commentaireeleve);
		$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy
	
		$Xcom=$XmoyMatGenMaxVal + 10;
		$pdf->SetFont('Arial','B',6);
		$pdf->SetXY($Xcom+35,$Ycom);
		if ($profAffOld == "") {
			$pdf->MultiCell(60,3,$nomprof,'0','R',0);
			$profAffOld=$nomprof;
		}else{
			if ($profAffOld != $nomprof) {
				$pdf->MultiCell(60,3,$nomprof,'0','R',0);
				$profAffOld=$nomprof;
			}else{
				if ($top == 0) {
					$pdf->MultiCell(60,3,$nomprof,'0','R',0);
				}
			}
		}
	
		$pdf->SetFont('Arial','',8+$policy);
		$pdf->SetXY($Xcom+1-17,$Ycom+2.2+$juste);
		$pdf->MultiCell(110,$confPolice[1]+$juste,$commentaireeleve,'0','L',0);
		
		// mise en place du nom du prof
		//$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
		$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
		//$pdf->SetFont('Arial','',6);
		//$pdf->SetXY($XprofVal,$YprofVal);
		//$profAff=recherche_personne2($profAff);
		//$pdf->WriteHTML(trunchaine($profAff,20));
		if ($vv == 0) { 
			$YprofVal=$YprofVal + $hauteurMatiere ; 
		}else{ 	
			$Ymoyeleve=$Ymoyeleve - $hauteurMatiere; 
			$Ymoyclasse=$Ymoyclasse - $hauteurMatiere;
		}
	
		// pour le calcul de la moyenne general de l'eleve
		if ( $noteaff != "" ) {
		        $noteMoyEleGTempo = $noteaff * $coeffaff;
	                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
	                $coefEleG=$coefEleG + $coeffaff;
		}

	}
	// fin de la mise en place des matiere

	// Note Vie Scolaire
/*
if (MODNAMUR0 == "oui") {

	$pdf->SetFont('Arial','B',9);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<B>'.'VIE SCOLAIRE'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place de la colonne coeff
	$pdf->SetFont('Arial','',8);
//	$pdf->SetXY($Xcoeff,$Ycoeff);
//	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
//	$Ycoeff=$Ycoeff + $hauteurMatiere;
	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
	$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
	$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
	$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
	$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;
	// mise en place du cadre note
	$pdf->SetXY($Xnote,$Ynote);
	$pdf->MultiCell(95,$hauteurMatiere,'',1,'',0);
	$Ynote=$Ynote + $hauteurMatiere;
	unlink($noteaff);
	// mise en place des notes
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XnotVal,$YnotVal);
	if (($noteaff < 10) && ($noteaff != "")) { $noteaff="0".$noteaff; }
	if ($noteaff != "") { $noteaff=arrondiaudixieme($noteaff);	}
	$pdf->WriteHTML($noteaff);


	$YnotVal=$YnotVal + $hauteurMatiere;
	// mise en place des coeff
	$coeffaff=$coefBull;
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($XcoeffVal,$YcoeffVal);
	$coeffaff=number_format($coeffaff,1,'.','');
	$pdf->WriteHTML($coeffaff);
	$YcoeffVal=$YcoeffVal + $hauteurMatiere;
	$pdf->SetFont('Arial','',9);
	// calcul du min et du max
	$max="";
	$min=1000;
	for($g=0;$g<count($eleveT);$g++) {
		// variable eleve
		$idEleveMoyen=$eleveT[$g][4];
		$valeur=calculNoteVieScolaire($idEleveMoyen,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
		if (trim($valeur) != "") {
			if ($valeur >= $max) { $max=$valeur; }
			if ($valeur <= $min) { $min=$valeur; }
		}
	}
	if ($min == 1000) { $min=""; }
	$moyeMatGenMin=$min;
	$moyeMatGenMax=$max;
	// fin de la calcul de min et max

	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal;
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal+$top);
	$moyeMatGenMinAff=$moyeMatGenMin;
	if (($moyeMatGenMinAff < 10) && ($moyeMatGenMinAff != "")) { $moyeMatGenMinAff="0".$moyeMatGenMinAff; }
	if ($moyeMatGenMinAff != "") { $moyeMatGenMinAff=arrondiaudixieme($moyeMatGenMinAff); }
	$pdf->WriteHTML($moyeMatGenMinAff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 11 ;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal+$top);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	if ($moyeMatGenMaxaff != "") { $moyeMatGenMaxaff=arrondiaudixieme($moyeMatGenMaxaff); }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	// mise en place des moyennes de classe
        $moyeMatGen1=moyeMatGenVieScolaire($_POST["saisie_trimestre"],$idClasse); 
	$pdf->SetFont('Arial','',9);
	$XmoyMatGVal= $XmoyMatGVal + 21 ;
	$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal+$top);
	$moyeMatGenAff=$moyeMatGen1;
	if (($moyeMatGenAff < 10) && ($moyeMatGenAff != "")) { $moyeMatGenAff="0".$moyeMatGenAff; }
	if ($moyeMatGenAff != "") { $moyeMatGenAff=arrondiaudixieme($moyeMatGenAff); }
	$pdf->WriteHTML($moyeMatGenAff);


	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGVal + 10;
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcom+1,$Ycom+2.2);
	$pdf->MultiCell(94,$confPolice[1],$commentaireeleve,'','','L',0);

	$profAff=strtoupper($persVieScolaire);
	$Xcom=$XmoyMatGVal + 10;
	$pdf->SetFont('Arial','B',6);
	$pdf->SetXY($Xcom+35,$Ycom);
	$pdf->MultiCell(60,3,$profAff,'0','R',0);

	

	// mise en place du nom du prof
	$pdf->SetFont('Arial','',6);
	//$pdf->SetXY($XprofVal,$YprofVal);
	//$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}
}
 */

// fin notes
// --------

	// cadre moyenne generale

$YmoyenneGeneral=$Ymoyclasse;

if ($Ymoyclasse > 210) {
	$pdf->AddPage();
	$YmoyenneGeneral=3;
}


$LargeurMG=$largeurMat;
$YmoyenneGeneralT=$YmoyenneGeneral + 2;
$XMoyGE= 3 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE;

$XmoyClasseGValue=$XMoyGE + 10 + 5 + 7;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;


$pdf->SetFont('Arial','',9);

$pdf->SetFillColor(230,230,230);
$pdf->SetXY(3,$YmoyenneGeneral);
$pdf->MultiCell(204,55,'',1,'L',1);

$pdf->SetFillColor(230,230,255);
$pdf->SetXY(3,$YmoyenneGeneral);
$pdf->MultiCell($LargeurMG,10,'',1,'L',1);
$pdf->SetXY(5,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>Moyenne générale</B>");
$pdf->SetXY($XMoyGE,$YMoyGE);
$pdf->MultiCell(18,10,'',1,'L',1);
$pdf->SetXY($XMoyCL+=18,$YMoyGE);
$pdf->MultiCell(39,10,'',1,'L',1);

// fin du cadre moyenne generale

// affichage de la moyenne generale eleve
$XmoyElValue=$LargeurMG;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$pdf->SetFont('Arial','',12);
$pdf->SetXY($XmoyElValue+4,$YmoyElGenValue);

$moyenEleveaff=$moyenEleve;
if (($moyenEleveaff < 10) && ($moyenEleveaff!="")) { $moyenEleveaff="0".$moyenEleveaff; }
$moyenEleveaff=preg_replace('/,/','.',$moyenEleveaff);
if ($moyenEleveaff != "") { $moyenEleveaff=arrondiaudixieme($moyenEleveaff); }

$pdf->WriteHTML("<B>".$moyenEleveaff."</B>");
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
// fin affichage moy eleve


//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);


$moyenClasseGenaff=$moyenClasseGen;
$moyenClasseMinaff=$moyenClasseMin;
$moyenClasseMaxaff=$moyenClasseMax;
if ($moyenClasseGenaff < 10) { $moyenClasseGenaff="0".$moyenClasseGenaff; }
if ($moyenClasseMaxaff < 10) { $moyenClasseMaxaff="0".$moyenClasseMaxaff; }
if ($moyenClasseMinaff < 10) { $moyenClasseMinaff="0".$moyenClasseMinaff; }


$pdf->SetFont('Arial','',7);
$pdf->SetXY($XmoyClasseGValue+3,$YmoyClasseGValue);
$moyenClasseMinaff=arrondiaudixieme($moyenClasseMinaff);
$pdf->WriteHTML($moyenClasseMinaff);
$pdf->SetXY($XmoyClasseMinValue+0.8,$YmoyClasseMinValue);
$moyenClasseMaxaff=arrondiaudixieme($moyenClasseMaxaff);
$pdf->WriteHTML($moyenClasseMaxaff);
$pdf->SetXY($XmoyClasseMaxValue-2,$YmoyClasseMaxValue);
$moyenClasseGenaff=arrondiaudixieme($moyenClasseGenaff);
$pdf->WriteHTML($moyenClasseGenaff);
// fin de la calcul de min et max
$pdf->SetFont('Arial','',8);
// fin affichage

// cadre vie scolaire
$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
$persVieScolaire=$recupInfo[0][4];
//$pdf->SetXY($XmoyClasseMaxValue+=11,$YmoyClasseMaxValue-=2);

//$pdf->MultiCell(95,20,'',1,'L',0);
$pdf->SetFillColor(255);
$pdf->RoundedRect($XmoyClasseMaxValue+=15, $YmoyClasseMaxValue-=2, 102, 20, 3.5, 'DF');

$pdf->SetXY($XmoyClasseMaxValue+3,$YmoyClasseMaxValue+1);
$pdf->SetFont('Arial','B',9);
$pdf->SetFont('Arial','U',9);
$pdf->MultiCell(40,3,'Vie Scolaire : ',0,'L',0);
$pdf->SetFont('Arial','',8);
$pdf->SetXY($XmoyClasseMaxValue+26,$YmoyClasseMaxValue+1);
$pdf->MultiCell(40,3,"$persVieScolaire",0,'L',0);

$pdf->SetXY($XmoyClasseMaxValue,$YmoyClasseMaxValue+5);
$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
$pdf->MultiCell(95,3,"$commentaireeleve",0,'L',0);
/*
$pdf->SetXY($XmoyClasseMaxValue+3,$YmoyClasseMaxValue+6);
$pdf->MultiCell(70,3,"Absences (en 1/2 journées) ",0,'L',0);
$pdf->SetXY($XmoyClasseMaxValue+52,$YmoyClasseMaxValue+6);
$pdf->MultiCell(40,3,": $nbjoursabsJustifie excusée(s)",0,'L',0);
$pdf->SetXY($XmoyClasseMaxValue+52,$YmoyClasseMaxValue+9);
$pdf->MultiCell(40,3,": $nbjoursabsNonJustifie non excusée(s)",0,'L',0);

$pdf->SetXY($XmoyClasseMaxValue+3,$YmoyClasseMaxValue+12);
$pdf->MultiCell(40,3,"Retard(s) ",0,'L',0);
$pdf->SetXY($XmoyClasseMaxValue+52,$YmoyClasseMaxValue+12);
$pdf->MultiCell(40,3,": $nbretard ",0,'L',0);
 */


// cadre appréciation
$Ycom=$YMoyGE+20;
$EpaisCom=45;
$YcomP1=$Ycom + 1;
$YcomP2=$YcomP1 + 10;
$YcomP3=$YcomP2 + 5;
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(255);
$pdf->SetXY(3,$Ycom);
$pdf->RoundedRect(3, $Ycom, 204, $EpaisCom, 3.5, 'DF');
//$pdf->MultiCell(204,$EpaisCom,'',1,'C',0);


// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
$pdf->SetXY(4,$YcomP1);
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML($appreciation);
$pdf->SetXY(145,$YcomP1);
$pdf->MultiCell(60,3," Prof. Principal : ". $profp,'0','R',0);
$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->SetXY(4,$YcomP1+5);
$pdf->MultiCell(180,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


$YcomP2+=1;
$pdf->SetFont('Arial','',10);
$pdf->SetXY(4,$YcomP2+5);
$pdf->SetFont('Arial','B',10);
$pdf->MultiCell(200,3,"$barre",'0','R',0);
$pdf->SetFont('Arial','',9);



// commentaire direction
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(4,$YcomP2+9);
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML($appreciation2);
$pdf->SetFont('Arial','B',9);
$pdf->SetXY(105,$YcomP2+9);
$pdf->MultiCell(100,3,"Le président du conseil de classe $nomdivision",'0','R',0);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->SetXY(4,$YcomP2+10);
$pdf->MultiCell(180,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)






//duplicata 
$YduplicaSign=$Ycom + $EpaisCom;
$pdf->SetFont('Arial','',5);
$pdf->SetXY(4,$YduplicaSign);
$pdf->WriteHTML("<I>".$duplicata."</I>");




// fin duplicata
// ----------------------------------------------------------------------------------------------------------------------
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
