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
$nofooterPDF=NOFOOTERPDF; 
$id=php_ini_get("safe_mode");
if ($id != 1) {	set_time_limit(900); }
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

$ismappadmis=$_POST["ismappadmis"];
config_param_ajout($ismappadmis,"ismappadmis");

$debut=deb_prog();
$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
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
$classe_nom=ucwords($data[0][1]);


// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
$textTrimestre="Bulletin annuel"
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
/*
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
 */
// recherche des dates de debut et fin


$dateFin=recupDateFinAnneeByClasse($_POST["saisie_classe"]);
$dateDebut=recupDateDebutAnneeByClasse($_POST["saisie_classe"]);
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


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general

$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve



$moyenClasseGen=""; // pour le calcul moyenne classe
$moyenClasseMin=1000; // pour la calcul moyenne min classe
$moyenClasseMax=""; // pour la calcul moyenne max  classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

// ----------------------------

$plageEleve=$_POST["plageEleve"];

if (preg_match('/^E_/',$plageEleve)) { 
	$plageEleve=preg_replace('/E_/','',$plageEleve);
	$eleveT=recupEleveViaIdEleve($plageEleve); 
	$dep=0; $nbEleveT=count($eleveT);
}

$classe_nom_aff=preg_replace('/_/',' ',$classe_nom);
if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
if ($plageEleve == "1") { $dep=0; $nbEleveT=1; }
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
	$dejafait=0;
	$couleurFond=192;
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
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	for($o=0;$o<=count($nbabs);$o++) {
		if ($nbabs[$o][4] > 0) {
	       		$nbjoursabs = $nbjoursabs + $nbabs[$o][4];
		}else{
			$nbheureabs = $nbheureabs + $nbabs[$o][7];	
		}
	}
	$nbabs=$nbjoursabs * 2;
	//---------------------------------//
	$nbrRtdNonJustifie=0;
	$nbAbsNonJustifie=0;
	$ptenmoinsMoyen=0;
	$nbrRtdNonJustifiept=0;
	$nbRtpoint=0;
	$nbrRtdNonJustifie=nombre_retardNonJustifie($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
	$nbAbsNonJustifie=nombre_absNonJustifieIsmapp($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
	
	$ptenmoinsMoyen=$ptenmoinsabs * $nbAbsNonJustifie;
	$appreciationbis2="$nbAbsNonJustifie (soit $ptenmoinsMoyen points en moins)";

	$nbrRtdNonJustifiept=$nbrRtdNonJustifie / $nbrtdaabs;

	if ($nbrRtdNonJustifiept >= 1) {
		$nbRtpoint=$nbrRtdNonJustifiept*$ptenmoinsabs;
		$nbRtpoint=number_format($nbRtpoint,'2',',','');
	}
	$appreciationbis3="$nbrRtdNonJustifie (soit $nbRtpoint points en moins)";
	$ptenmoinsMoyen+=$nbRtpoint;

	$nbAbsJustifieType=nombre_absJustifieTypeAbs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin),$motif_polspe_ip);

	if ($nbAbsJustifieType > $nbrabs) {
		$ptenmoisMoyenPOLSPE=($nbAbsJustifieType - $nbrabs) * $ptenmoinsabsjusti;
	}else{
		$ptenmoisMoyenPOLSPE=0;
	}
	$appreciationbis4="$nbAbsJustifieType (soit $ptenmoisMoyenPOLSPE points en moins)";
	$ptenmoinsMoyen+=$ptenmoisMoyenPOLSPE;

	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim(ucwords($prenomEleve));
	
	
	$effbordure=1;


	$appreciation=LANGBULL39;

	$appreciationbis="($nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ) " ;

	$barre="____________________________________________________________________________________________";
	$appreciation2=LANGBULL40;
	$duplicata=LANGBULL41 . " - $urlsite - $mail";
	$signature=LANGBULL42;
	$signature2="";
	$signature="";
	// FIN variables

	$xtitre=80;  // sans logo


	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);

	$xcoor0=3;   // sans logo
	$ycoor0=20;   // sans logo

	// Debut création PDF
	// mise en place des coordonnées
	
	$photo="./image/banniere/banniere-ismapp.jpg";
	$pdf->Image($photo,10,5,45,40);

	$pdf->SetTextColor(24,37,140);

	$pdf->SetFont('Arial','B',9);
	$pdf->SetXY(80,$ycoor0);
	$pdf->MultiCell(100,3,"Année académique $anneeScolaire",0,'R',0);

	$pdf->SetFont('Arial','B',12);
	$pdf->SetFillColor(255);
	$pdf->SetXY(80,$ycoor0+10);
	$pdf->MultiCell(100,3,"RELEVE DE NOTES ET RESULTATS",0,'R',0);	

	$pdf->SetFont('Arial','B',11);
	$pdf->SetXY(10,$ycoor0+30);
	$pdf->MultiCell(100,3,"Etudiant(e) : $prenomEleve $nomEleve",0,'L',0);
	$pdf->SetXY(10,$ycoor0+35);
	$pdf->MultiCell(100,3,"Inscrit(e) en $classe_nom_aff",0,'L',0);







	$largeurMat=90;
	$largeurEpreuve=15;
	$largeurNote=15;
	$largeurPoints=15;
	$largeurMoy=15;
	$largeurCoeff=15;
	$largeurResultat=15;
	$largeurECTS=15;
	$hauteurTitre=10;

	$totalLargeur=$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurCoeff+$largeurMoy+$largeurResultat+$largeurECTS;

	// cadre des notes
	// ---------------
	// Barre des titres
	$xcoor0=10;
	$ycoor0=35;
	$pdf->SetTextColor(255);
	$pdf->SetFillColor(24,37,140);
	$pdf->SetXY($xcoor0,$ycoor0+=24); //  placement  cadre titre
	$pdf->MultiCell(190,$hauteurTitre,'',1,'L',1);

	$pdf->SetFont('Arial','',8);
	$pdf->SetTextColor(255);
	$pdf->SetXY(5,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurMat,5,"Matières",0,'C',0);
	$pdf->SetXY($largeurMat,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell(5+$largeurEpreuve,5,"Epreuves\n(Coeff)",0,'C',0);
	$pdf->SetXY(5+$largeurMat+$largeurEpreuve,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurNote,5,"Note\n/20",0,'C',0);
	$pdf->SetXY(5+$largeurMat+$largeurEpreuve+$largeurNote,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurPoints,5,"Points",0,'C',0);
	$pdf->SetXY(5+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurMoy,5,"Moy.\nMatière",0,'C',0);
	$pdf->SetXY(5+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurCoeff,5,"Coeff.\nMatière",0,'C',0);
	$pdf->SetXY(5+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy+$largeurCoeff,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurResultat,5,"Résultats\n(points)",0,'C',0);
	$pdf->SetXY(5+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy+$largeurCoeff+$largeurResultat,$ycoor0+0.3); // placement contenu titre
	$pdf->MultiCell($largeurECTS,5,'ECTS',0,'C',0);
	// fin des titres



	$nbs=0;

	$pdf->SetTextColor(24,37,140);

	// Mise en place des matieres et nom de prof
	$Xmat=$xcoor0;
	$Ymat=$ycoor0+=$hauteurTitre;
	$Xmatcont=$Xmat+1;
	$Ymatcont=$Ymat+1;

	$hauteurMatiere=9; // taille du cadre matiere

	$nbmatiere='0';
	for($i=0;$i<count($ordre);$i++) {
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		if ($verifGroupe) { continue; }
		$nbmatiere++;
	}
	if ($nbmatiere >= 20) { $hauteurMatiere=8; }

	$Xcoeff=$largeurMat+3;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 13 + 7;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=56;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=$Xcoeff + 12 + 9;
	$YnotVal=$Ycoeff + 3;
	$XcoeffVal=$Xcoeff + 1;
	$YcoeffVal=$Ymat + 3;
	$XprofVal=20; // x en nom prof
	$YprofVal=$Ymat + 4; // y en nom du prof
	$XmoyMatGVal=$Xcoeff + 26 ;
	$YmoyMatGVal=$Ycoeff + 3 ;

	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
	$coefEleG=0;
	$ii=0;
	$iiii=0;

	$TT=1;
	for($i=0;$i<count($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		$noteectsOK=$ordre[$i][6];
		$option=$ordre[$i][7];




		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);

		$coefMatiere=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);


		// mise en place des matieres

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
	
		$sousmatiere=trim($ordre[$i][4]);   
		$libelleMatiere=$ordre[$i][5]; 
		$ii=$i+1;
		while($ii < count($ordre)) {
			$verifGroup=verifMatiereGroupe($ordre[$ii][0],$idEleve,$idClasse,$ordre[$ii][2]);
			if ($verifGroup == 0) {  break; }
			if (verifElevDansGroupe($verifGroup,$idEleve)) { break; }
			$ii++;
		}

		$matiereSuivante=chercheMatiereNom3($ordre[$ii][0]);
		$matiereSuivante=preg_replace('/0$/','',$matiereSuivante);
		$matiereSuivante=preg_replace('/&#8217;/',"'",$matiereSuivante);
		$libelleMatiere=preg_replace('/0$/','',$libelleMatiere);
		$libelleMatiere=preg_replace('/&#8217;/',"'",$libelleMatiere);
		$ordrematiere=$ordre[$i][3]; 

		if ($aff == 1) {
			$pdf->SetTextColor(255,102,0); // orange
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY($Xmat,$Ymat);
			$pdf->MultiCell($totalLargeur-5,$hauteurMatiere-5,"$titreEU1",0,'L',0);
			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(24,37,140);
			$Ymat+=$hauteurMatiere-5;
			$couleurFond=255;
			$aff=0;
		}

		if ((trim(strtolower($libelleMatiere)) != trim(strtolower($matiereSuivante))) && ($dejafait == 1)) {
			$titreEU1=$matiereSuivante;
			$aff=1;
		}

		if ($dejafait == 0) {
			$titreEU=$libelleMatiere;
			$pdf->SetTextColor(255,102,0); // orange
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY($Xmat,$Ymat);
			$pdf->MultiCell($totalLargeur-5,$hauteurMatiere-5,"$titreEU",0,'L',0);
			$pdf->SetFont('Arial','',8);
			$pdf->SetTextColor(24,37,140);
			$dejafait=1;
			$Ymat+=$hauteurMatiere-5;
			$couleurFond=255;
		}
		
		$couleurFond=($couleurFond == 255) ? '192' : '255' ;
		$pdf->SetFillColor($couleurFond,$couleurFond,$couleurFond);
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat-5,$hauteurMatiere,'',0,'L',1);
		


	
		if ($sousmatiere != "") {
			if ($libelleMatiere == "") {
				$pdf->SetXY($Xmatcont,$Ymat);
				$YECTS=$Ymat-1;
			}else{
				$pdf->SetXY($Xmatcont,$Ymat);
			}
			$pdf->SetFont('Arial','',7);
			$sousmatiere=preg_replace('/0$/','',$sousmatiere);
			$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
			$profAff=recherche_personne2($profAff);
			$sousmatiere=html_vers_text($sousmatiere);
			if ($option != "OPT1") {
				$infoaff=trunchaine(ucfirst($sousmatiere).' - '.$profAff,70);
			}else{
				$infoaff=trunchaine(ucfirst($sousmatiere),70);
			}
			$pdf->WriteHTML("- $infoaff");
		}
		$pdf->SetFont('Arial','',8);
			


		$listeExamen=array("CC","DST","Dad","Soutenance","Rapport","Fiche de lecture","Exposé","Partiel","Lecture","Examen écrit","Recopiage vocabulaire","Mémoire Ip","Evaluation Tutorat");

		$epreuve="";
		$moyenneTT="";
		$coef="";
		$noteEpr="";
		$ptEpr="";
		$valpt="";
		foreach($listeExamen as $key=>$value) {
			if ($idgroupe == "0") {
				$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$value);
			}else{
				$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$value);
			}

			if ($value == "CC") 	  		{ $valcoef="1"; }
			if ($value == "DST") 	  		{ $valcoef="2"; }
		 	if ($value == "Partiel") 		{ $valcoef="3"; }
		 	if ($value == "Soutenance") 		{ $valcoef="3"; }
		 	if ($value == "Rapport") 		{ $valcoef="3"; }
		 	if ($value == "Fiche de lecture") 	{ $valcoef="2"; }
		 	if ($value == "Exposé")  		{ $valcoef="1"; }
		 	if ($value == "Dad")     		{ $valcoef="1"; }
		 	if ($value == "Lecture")	 	{ $valcoef="3"; }
                        if ($value == "Examen écrit")   	{ $valcoef="2"; }
                        if ($value == "Recopiage vocabulaire") { $valcoef="1"; }
                        if ($value == "Mémoire Ip") 		{ $valcoef="2"; }
                        if ($value == "Evaluation Tutorat") 	{ $valcoef="2"; }

			if (trim($noteaff) != "") {
				$epreuve.="$value ($valcoef)\n";
				$valpt=$noteaff*$valcoef;
				$valpt=number_format($valpt,2,'.','');
				$ptEpr.="$valpt\n";
				$noteEpr.="$noteaff\n";
				$moyenneTT+=$noteaff*$valcoef;
				$coef+=$valcoef;
			}else{
				$noteaff=verifSiAbsExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$value);
				if ($noteaff == "ABS") { 
					$epreuve.="$value ($valcoef)\n";
					$noteEpr.="$noteaff\n";
				
				}
			}
		}

		$pdf->SetXY($largeurMat,$Ymat);
		$pdf->MultiCell($totalLargeur-$largeurMat+5,$hauteurMatiere,"",0,'L',1);


		$pdf->SetFont('Arial','',6);
		// mise en place de la colonne Epreuve
		$pdf->SetXY(5+$largeurMat,$Ymat+0.2);
		$pdf->MultiCell($largeurEpreuve,2.3,"$epreuve",0,'L',0);
		$visibleinfo=1;
                if ("immersion pro & stages" == strtolower($sousmatiere)) {
                        $visibleinfo=0;
                }



		// Mise en place de notes
		$pdf->SetXY(10+$largeurMat+$largeurEpreuve,$Ymat+0.2);
		if ($visibleinfo == 1) {
			$pdf->MultiCell($largeurNote,2.3,"$noteEpr",0,'L',0);	// NOTES
		}
		// Mise en place Points
		$pdf->SetXY(10+$largeurMat+$largeurEpreuve+$largeurNote,$Ymat);
		if ($visibleinfo == 1) {
			$pdf->MultiCell($largeurPoints,2.3,"$ptEpr",0,'L',0); // POINTS
		}
		// Mise en place Moy. Matiere
		$pdf->SetFont('Arial','B',7);
		$noteaff=$moyenneTT/$coef;
		$noteaff1=$noteaff;
		unset($moyenneTT);unset($coef);
		if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
		if ($noteaff1 != "") {	$noteaff1=number_format($noteaff1,2,'.',''); }

		 if ("immersion pro & stages" == strtolower($sousmatiere)) {
                        if ($noteaff1 >= 10) {
                                $noteaff1="VALIDE";
                        }else{
                                if (($noteaff1 < 10) && ($noteaff1 != ""))  {
                                        $noteaff1="NON VALIDE";
                                }else{
                                        $noteaff1="";
                                }
                        }
                }
                $pdf->SetXY(10+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints,$Ymat);
                $pdf->MultiCell($largeurMoy,2.3,"$noteaff1",0,'L',0); // Moy Matiere



		// Coef Matiere
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY(10+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy,$Ymat);
		if (trim($noteaff1) == "") { $coefMatiere=""; }
		if ($visibleinfo == 1) {
			$pdf->MultiCell($largeurCoeff,2.3,"$coefMatiere",0,'L',0); // Coeff
		}
		$coefTotal+=$coefMatiere;
		// Résultt (points)
		if ($visibleinfo == 1) {
			$RP=$coefMatiere*$noteaff1;
			$resultatTotal+=$RP;
			$RP=number_format($RP,2,',','');
		}
		$pdf->SetXY(10+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy+$largeurCoeff,$Ymat);
		if ($visibleinfo == 1) {
			$pdf->MultiCell($largeurResultat,2.3,"$RP",0,'L',0); // Resultat	
		}
		// ECTS		
		if ($noteaff >= 10) { 
                	$noteects=$noteectsOK;
			$noteectsglobal+=$noteects;
                }else{
                       	$noteects="0";
                }
		$pdf->SetXY(10+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy+$largeurCoeff+$largeurResultat,$Ymat);
		$pdf->SetFont('Arial','',7);
		$pdf->MultiCell($largeurECTS,2.3,"$noteects",0,'L',0); // ECTS

		$Ymat=$Ymat + $hauteurMatiere;
	} // fin de la mise en place des matiere

$ycoor0=$Ymat+3;
$couleurFond='192';
$pdf->SetFillColor($couleurFond,$couleurFond,$couleurFond);
$pdf->SetFont('Arial','B',8);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->MultiCell($totalLargeur-5,8,"",0,'L',1);
$pdf->SetXY($Xmat-5,$ycoor0+1);
$pdf->MultiCell($largeurMat,3,"Total",0,'R',0);
$pdf->SetXY($Xmat-5,$ycoor0+4.3);
$pdf->MultiCell($largeurMat,3,"RESULTAT /20",0,'R',0);
$coefTotalAff=number_format($coefTotal,2,',','');
$pdf->SetXY(3+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy,$ycoor0+0.5);
$pdf->MultiCell($largeurCoeff,3,"$coefTotalAff",0,'R',0);
if ($coefTotal > 0) {
	$noteMoyEleG=$resultatTotal/$coefTotal;
	$noteMoyEleG=number_format($noteMoyEleG,2,',','');
	$resultatTotal=number_format($resultatTotal,2,',','');
}

$pdf->SetXY(4+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy+$largeurCoeff,$ycoor0+1);
$pdf->MultiCell($largeurResultat,2.3,"$resultatTotal",0,'R',0); // Resultat
$pdf->SetXY(4+$largeurMat+$largeurEpreuve+$largeurNote+$largeurPoints+$largeurMoy+$largeurCoeff,$ycoor0+4.3);
$pdf->MultiCell($largeurResultat,2.3,"$noteMoyEleG",0,'R',0); // Resultat

$coefTotal="";
$resultatTotal="";

$xcoor0=$largeurMat+5;
$ycoor0+=11;

if ($ismappadmis == 1) {
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->SetFillColor(24,37,140);
	$pdf->RoundedRect($xcoor0, $ycoor0, 100, 12, 3.5, 'DF');


	$pdf->SetFont('Arial','',9);
	$pdf->SetTextColor(255);
	$pdf->SetFillColor(255);
	if ($noteMoyEleG >= 10) {
		$adm=1;
		$ajo=0;
		$noteectsglobal=60;
	}else{
		$adm=0;
		$ajo=1;
	}
	$pdf->SetXY($xcoor0+7,$ycoor0+2);
	$pdf->MultiCell(3,3,"",1,'L',$adm);
	$pdf->SetXY($xcoor0+10,$ycoor0+2);
	$pdf->MultiCell(100,3,"Admis-e ",0,'L',0);
	
	$pdf->SetXY($xcoor0+37,$ycoor0+2);
	$pdf->MultiCell(3,3,"",1,'L',$ajo);
	$pdf->SetXY($xcoor0+40,$ycoor0+2);
	$pdf->MultiCell(100,3,"Ajourné-e ",0,'L',0);
	
	$pdf->SetXY($xcoor0+7,$ycoor0+6.5);
	$pdf->MultiCell(100,3,"Total ECTS : $noteectsglobal",0,'L',0);
	
	$noteectsglobal="";
	$noteMoyEleG="";
	
	$pdf->SetTextColor(24,37,140);
	$pdf->SetXY($xcoor0+3,$ycoor0+15);
	$pdf->SetFont('Arial','',6);
	$pdf->MultiCell(100,1,"Important : Aucun duplicata ne sera délivré",0,'L',0);

	$infoplus="\n\n\n\nLe Chef d'Etablissement";
}	
$pdf->SetTextColor(24,37,140);
$pdf->SetXY(5,$ycoor0);
$date=dateDMY();
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(70,3,"Fait à Paris, le $date$infoplus",0,'C',0);

// pied de page ISMAPP
/*
$ycoor0=238;
$pdf->SetXY(0,$ycoor0+=20);
$pdf->SetFont('Arial','B',6);
$pdf->MultiCell(210,3,"Institut Supérieur du Management Public et Politique",0,'C',0);
$pdf->SetFont('Arial','',6);
$pdf->SetXY(0,$ycoor0+=3);
$pdf->MultiCell(210,3,"Etablissement d'Enseignement Supérieur privé, reconnu par l'Etat",0,'C',0);
$pdf->SetXY(0,$ycoor0+=3);
$pdf->MultiCell(210,3,"Organisme de formation déclaré sous le n° 11 75 54276 75",0,'C',0);
$pdf->SetXY(0,$ycoor0+=3);
$pdf->MultiCell(210,3,"80, rue Taitbout - 75009 PARIS",0,'C',0);
$pdf->SetXY(0,$ycoor0+=3);
$pdf->MultiCell(210,3,"Tel. +33(0)1 55 50 12 40 - Fax. +33(0)1 55 50 12 49",0,'C',0);
$pdf->SetXY(0,$ycoor0+=3);
$pdf->MultiCell(210,3,"Courriel : direction@ismapp.com - Web : www.ismapp.com",0,'C',0);
*/

// fin duplicata
// ----------------------------------------------------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$classe_nom=preg_replace('/\(/',"_",$classe_nom);
$classe_nom=preg_replace('/\)/',"_",$classe_nom);
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
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();

?>
