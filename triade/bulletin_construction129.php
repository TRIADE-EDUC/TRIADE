<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
	verif_profp_class($_SESSION["id_pers"],$_POST["saisie_classe"]);
}else{
	validerequete("2");
}
$debut=deb_prog();
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
if (count($valeur)) {

if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="Bulletin du 1er Trimestre"; $trimestreA="trimestre2"; $trimestreB="trimestre3"; $titreA="T2"; $titreB="T3"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="Bulletin du 2ème Trimestre"; $trimestreA="trimestre1"; $trimestreB="trimestre3"; $titreA="T1"; $titreB="T3"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre="Bulletin du 3ème Trimestre"; $trimestreA="trimestre1"; $trimestreB="trimestre2"; $titreA="T1"; $titreB="T2";}
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; }
}


$dateRecup=recupDateTrim("$trimestreA");
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutA=$dateRecup[$j][0];
	$dateFinA=$dateRecup[$j][1];
}
$dateDebutA=dateForm($dateDebutA);
$dateFinA=dateForm($dateFinA);


$dateRecup=recupDateTrim("$trimestreB");
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebutB=$dateRecup[$j][0];
	$dateFinB=$dateRecup[$j][1];
}
$dateDebutB=dateForm($dateDebutB);
$dateFinB=dateForm($dateFinB);

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=trim($data[0][1]);
// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print ucwords($textTrimestre)?><br /> <br />
      <?php print LANGBULL28?> : <?php print $classe_nom?><br /> <br />
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


$idliste="";
$data=aff_grp_bull_bonifacio("ens_generaux");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabens_generaux=explode(",",$idliste);


$idliste="";
$data=aff_grp_bull_bonifacio("sect_prof");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabsect_prof=explode(",",$idliste);


$idliste="";
$data=aff_grp_bull_bonifacio("spec_prof");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabspec_prof=explode(",",$idliste);


// recherche des dates de debut et fin
$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
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
		$ii=0;
		for($t=0;$t<count($ordre);$t++) {
			$idMatiere=$ordre[$t][0];
			$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$t][2]);
			$noteaff=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]);
			$ii++;
			if ( $noteaff != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coeffaff;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coeffaff;
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
//for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];


	$moyenens_generaux=0;
	$moyensect_prof=0;
	$nbnotesect_prof=0;
	$nbnoteens_generaux=0;
	$moyenspec_prof=0;
	$nbnotespec_prof=0;
	$noteens_generaux="";
	$notespec_prof="";
	$notesect_prof="";

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

	$moyenLiterraire=0;
        $nbnoteLiterraire=0;
	$moyenScientifique=0;
       	$nbnotescientifique=0;

	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	$coordonne4="E-mail : ".$mail;


	$titre=$textTrimestre;

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",35);


	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=trim(strtoupper($classe_nom));

	$appreciation=LANGBULL39;


$idprofp=rechercheprofp($_POST["saisie_classe"]);
$profp=recherche_personne($idprofp);
$com_visa_scolaire=recherche_com_scolaire($idEleve,$_POST["saisie_trimestre"]);
$com_visa_scolaire=trunchaine($com_visa_scolaire,135);

//$nbretard="___";
//$nbabs="___";
//$nbheureabs="___";
//conversion taille multicell taille papier : largeur page : 190 = 18.1 cm
//                                            en largeur 70 = 6.65cm ;120=11.4 cm
//                           				  en hauteur : 10 = 0.95 cm
$appreciation="Bilan Assiduité : $nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ";


$appreciation2="<br>    Observations et appréciations du conseil de classe  ";
$barre="";
$duplicata="ATTENTION: Ce bulletin est l'original, il doit être conservé par la famille ";
// FIN variables

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo
    // suppression trait en partie supérieure du bulletin original
	//$pdf->SetXY(10,9);
	//$pdf->MultiCell(190,0.1,'',1,'L',0);

	// mise en place du logo
	$logo="./image/banniere/logolpll.jpg";
	if (file_exists($logo)) {
		$xlogo=11;
		$ylogo=16;
		$xcoor0=30;
		$ycoor0=3;
		$pdf->Image($logo,10,11,$xlogo,$ylogo);
	}
	// fin du logo

	//

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	//cration du cadre autour du nom du lycee
		$x=10;
		$y=9;
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetFillColor(255,203,145);
		$pdf->MultiCell(70,21.5,"",1,'L',0);
		//cadre année scolaire trimestre
		$x=80;
		$y=9;
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetFillColor(255,203,145);
		$pdf->MultiCell(120,6.5,"",1,'L',0);
		//cadre adresse parent
		$x=80;
		$y=15.5;
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetFillColor(255,203,145);
		$pdf->MultiCell(120,15,"",1,'L',0);
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(21,11);
	$pdf->WriteHTML("<B>LP LOUIS LOUCHEUR</B>");
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(21,15);
	$pdf->WriteHTML("8, boulevard de lyon");
	$pdf->SetXY(21,18);
	$pdf->WriteHTML("59100 ROUBAIX");
	$pdf->SetXY(21,21);
	$pdf->WriteHTML("tél : 03 20 89 37 60");
	$pdf->SetXY(21,24);
	$pdf->WriteHTML("E-mail : 0590187h@ac-lille.fr");
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate=LANGBULL43." ".$anneeScolaire;
	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY(135,10);
	$pdf->WriteHTML($Pdate);
	// fin d'insertion
	// insertion du Trimestre
	$pdf->SetXY(85,10);
	$pdf->SetFont('Arial','B',10);
	$pdf->WriteHTML($titre);
	// fin d'insertion du trimestre
	
	// classe
	$pdf->SetXY(90,25);
	$pdf->SetFont('Courier','',11);
	$classe_nom=trunchaine($infoeleveclasse,30);
	$classe_nom=preg_replace('/_/','',$classe_nom);
	$pdf->WriteHTML("Classe : <B>".$classe_nom."</B>");
	
	
	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;
	if (file_exists($photo)) {
		$xlogo=25;
		$ylogo=10;
		$photowidth=10.8;
		$photoheight=16.3;
		$pdf->Image($photo,187,10,$photowidth,$photoheight);
	}
	

	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
	for($ik=0;$ik<=count($dataadresse);$ik++) {
		$nomtuteur=$dataadresse[$ik][1];
		if (trim($nomtuteur) != "") {
			$civ=civ($dataadresse[$ik][13]);
		}else{
			$civ="";
		}
		$prenomtuteur=ucfirst($dataadresse[0][2]);
		$adr1=$dataadresse[0][3];
		$code_post_adr1=$dataadresse[0][4];
		$commune_adr1=$dataadresse[0][5];
		$numero_eleve=$dataadresse[0][9];
		$regime=$dataadresse[0][12];
		$class_ant=trunchaine($dataadresse[$ik][10],20);

		//insertion état civil + adresse pour envoi
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(137,16);  // 137 correspond à 13 cm du ford gauche et 21 correspond à 2 cm du haut de la page
		$chaine=LANGBULL44." ".trim(strtoupper($nomtuteur));
		$pdf->WriteHTML(trunchaine($chaine,30));
		$pdf->SetXY(136,21);  //incrémentation de y de 5 pour mise en page . mise à x de 136 car décalage natif de l'addrese
		$chaine=trim($num_adr1)." ".trim($adr1);
		$pdf->WriteHTML(trunchaine($chaine,30));;
		$pdf->SetXY(137,26);
		$chaine=trim($code_post_adr1)." ".trim(strtoupper($commune_adr1));
		$pdf->WriteHTML(trunchaine($chaine,30));
		//fin d'insertion
		
		//cadre au dessus des notes
		$x=10;
		$y=35;
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetFillColor(255,203,145);
		$pdf->MultiCell(190,5,"",0,'L',0);
		// fin cadre au dessus des notes
		
		$pdf->SetXY($x,$y);
		$pdf->MultiCell(85,5,"Nom de l'élève : ",1,'L',0);
		$pdf->SetXY($x+85,$y);

		$pdf->MultiCell(40,5,"INE :",1,'L',0);
		$pdf->SetXY($x+85+40,$y);
		$pdf->MultiCell(65,5,"Date de Naissance : ",1,'L',0);

		$pdf->SetFont('Courier','',9);
		$nomEP=trunchaine("$nomEleve $prenomEleve",20);
		$pdf->SetXY($x+32,$y);
		$pdf->MultiCell(80,5,"$nomEP",0,'L',0);
		$pdf->SetXY($x+90+5,$y);
		$numero_eleve=$dataadresse[0][9];
		if (trim($numero_eleve) != "") { 
			$pdf->MultiCell(50,5,"$numero_eleve",0,'L',0);
		}
		$pdf->SetXY($x+90+40+33,$y);
		$datenaissance=$dataadresse[0][11];
		if (trim($datenaissance) != "") { 
			$datenaissance=dateForm($datenaissance); 
			$pdf->MultiCell(50,5,"$datenaissance",0,'L',0);
		}
		

	}
	$pdf->SetFillColor(255);
	// fin cadre du haut

	$y+=5;
	$x=10;

	$largeurMat=50;

	// cadre des notes
	// ---------------
	// Barre des titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(255,203,145);
	$pdf->SetXY($x,$y); // placement contenu titre
	$pdf->MultiCell($largeurMat,8,"Matières",1,'C',1);
	$pdf->SetXY($largeurMat+$x,$y);
	$pdf->MultiCell(15,8,"Elève",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15,$y);
	$pdf->MultiCell(15,4,"Moy. Classe",1,'C',1);
	$pdf->SetXY($largeurMat+$x+30,$y);
	$pdf->MultiCell(30,4,"Récapitulatif",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+45,$y);
	$pdf->MultiCell(80,8,"Appréciations",1,'C',1);
	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($largeurMat+$x+15+15,$y+4);
	$pdf->MultiCell(15,4,"$titreA",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+15+15,$y+4);
	$pdf->MultiCell(15,4,"$titreB",1,'C',1);
	// fin des sous-titres

	// mise en place des matieres
	
	$hauteurMatiere=8; // taille du cadre matiere

	// Mise en place des matieres et nom de prof
	$Xmat=10;
	$Ymat=$y+8;
	$Xmatcont=11;
	$Ymatcont=$y+8;

	$Xprof=15;
	$Yprof=$Ymat;
	$Xmoyeleve=$largeurMat + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=56;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=$largeurMat + 12;
	$YnotVal=$Ymat + 3;
	$XprofVal=20; // x en nom prof
	$YprofVal=$Ymat + 4; // y en nom du prof
	$XmoyMatGVal=$largeurMat + 26 ;
	$YmoyMatGVal=$Ymat + 3 ;

	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
	$coefEleG=0;
	$ii=0;
	for($i=0;$i<count($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


	

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
		
		$pdf->SetFillColor(255,255,255);
	
		$pdf->SetXY($Xmat,$Ymat);
		foreach($tabens_generaux as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(255,255,255);
				$liste_ens_generaux.=ucwords($matiere).",";
				break;
			}
		}

		foreach($tabsect_prof as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(255,255,255);
				$liste_sect_prof.=ucwords($matiere).",";
				break;
			
			}
		}

		foreach($tabspec_prof as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(255,255,255);
				$liste_spec_prof.=ucwords($matiere).",";
				break;
			}
		}

		$pdf->SetFont('Arial','',7);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',1);
		$pdf->SetXY($Xmatcont-1.5,$Ymatcont);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),35).'</B>');
		// $pdf->WriteHTML('<B>'.trunchaine(sansaccentmajuscule(strtoupper($matiere)),20).'</B>');
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		// mise en place de la colonne coeff
	//	$pdf->SetFont('Arial','',8);
	//	$pdf->SetXY($Xcoeff,$Ycoeff);
	//	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	//	$Ycoeff=$Ycoeff + $hauteurMatiere;
		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->SetFillColor(255,255,0);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmoyclasse+15,$Ymoyclasse);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmoyclasse+15+15,$Ymoyclasse);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;

		// mise en place du cadre commentaire
		$pdf->SetXY($Xnote+13,$Ynote);
		$pdf->MultiCell(80,$hauteurMatiere,'',1,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		
		
		
		// mise en place des notes
	
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		}
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($XnotVal-1,$YnotVal);
		$noteaff1=$noteaff;
		if (($noteaff1 < 10) && ($noteaff1 != "")) { $noteaff1="0".$noteaff1; }
		$pdf->WriteHTML($noteaff1);


		$YnotVal=$YnotVal + $hauteurMatiere;
		// mise en place des coeff
		$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
	//	$pdf->SetFont('Arial','',9);
	//	$pdf->SetXY($XcoeffVal,$YcoeffVal);
	//	$pdf->WriteHTML($coeffaff);
	//	$YcoeffVal=$YcoeffVal + $hauteurMatiere;

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
           		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
    		}

		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XmoyMatGVal+2,$YmoyMatGVal);

		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->WriteHTML($moyeMatGenaff);

		// -----------------------
		foreach($tabsect_prof as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyensect_prof+= $noteaff1 * $coeffaff;
					$nbnotesect_prof+= $coeffaff ;
				}
			}
		}

		foreach($tabens_generaux as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenens_generaux+= $noteaff1 * $coeffaff;
					$nbnoteens_generaux+= $coeffaff ;
				}
			}
		}

		foreach($tabspec_prof as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenspec_prof+= $noteaff1 * $coeffaff;
					$nbnotespec_prof+= $coeffaff ;
				}
			}
		}

		

		// ------------------------
		// calcul du min et du max
		/*
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
		 */
		// fin de la calcul de min et max
		


	$moyenneA=moyenEleveMat2($idEleve,$idMatiere,$dateDebutA,$dateFinA,$idClasse,$ordre[$i][2]);
	$moyenneB=moyenEleveMat2($idEleve,$idMatiere,$dateDebutB,$dateFinB,$idClasse,$ordre[$i][2]);

	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal+5,$YmoyMatGVal);
//	$moyeMatGenMinaff=$moyeMatGenMin;
	$moyeMatGenMinaff=$moyenneA;
//	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal+10,$YmoyMatGVal);
//	$moyeMatGenMinaff=$moyeMatGenMax;
	$moyeMatGenMaxaff=$moyenneB;
//	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> policy ; $confPolice[1] -> cadre

	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom+13,$Ycom+0.2);
	$pdf->MultiCell(80,$confPolice[1],$commentaireeleve,'','','L',0);
	
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($XprofVal,$YprofVal);
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}

}
// fin de la mise en place des matiere

// Note Vie Scolaire
if (MODNAMUR0 == "oui") {

	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<B>'.'NOTE DE VIE SCOLAIRE'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place de la colonne coeff
//	$pdf->SetFont('Arial','',8);
//	$pdf->SetXY($Xcoeff,$Ycoeff);
//	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
//	$Ycoeff=$Ycoeff + $hauteurMatiere;
	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(255,255,0);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
	$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmoyclasse+15,$Ymoyclasse);
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmoyclasse+15+15,$Ymoyclasse);
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);

	$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;
	// mise en place du cadre commentaire
	$pdf->SetXY($Xnote+13,$Ynote);
	$pdf->MultiCell(80,$hauteurMatiere,'',1,'',0);
	$Ynote=$Ynote + $hauteurMatiere;

	// mise en place des notes
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XnotVal,$YnotVal);
	if (($noteaff < 10) && ($noteaff != "")) { $noteaff="0".$noteaff; }
	$pdf->WriteHTML($noteaff);


	$YnotVal=$YnotVal + $hauteurMatiere;
	// mise en place des coeff
	$coeffaff=$coefBull;
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XcoeffVal,$YcoeffVal);
	$pdf->WriteHTML($coeffaff);
	$YcoeffVal=$YcoeffVal + $hauteurMatiere;
 
	// mise en place des moyennes de classe
        $moyeMatGen1=moyeMatGenVieScolaire($_POST["saisie_trimestre"],$idClasse); 
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XmoyMatGVal+2,$YmoyMatGVal);
	$moyeMatGenaff=$moyeMatGen1;
	if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
	$pdf->WriteHTML($moyeMatGenaff);


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
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal+5,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal+10,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom+12,$Ycom);
	$pdf->MultiCell(80,$confPolice[1],$commentaireeleve,'','','L',0);
	
	// mise en place du nom du prof
	$profAff=$persVieScolaire;
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal-3,$YprofVal);
	$pdf->WriteHTML('Equipe pédagogique & éducative');
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}

}

// fin notes
// --------





// fin notes
// --------

// cadre moyenne generale

//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
$moyenClasseGenaff=$moyenClasseGen;
$moyenClasseMinaff=$moyenClasseMin;
$moyenClasseMaxaff=$moyenClasseMax;
// fin de la calcul de min et max

// la moyenne generale eleve
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$moyenEleveaff=$moyenEleve;
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
// fin affichage moy eleve

$YmoyenneGeneral=$Ymoyclasse + 1;
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(10,$YmoyenneGeneral);
$pdf->SetFillColor(0,158,198);
$pdf->MultiCell($largeurMat,10,'MOYENNE GENERALE',1,'L',1);
$pdf->SetFont('Arial','',12);
$pdf->SetXY($largeurMat+10,$YmoyenneGeneral);
$pdf->MultiCell(15,10,"$moyenEleveaff",1,'C',1);
$pdf->SetXY($largeurMat+10+15,$YmoyenneGeneral);
$pdf->MultiCell(15,10,"$moyenClasseGenaff",1,'C',1);
$pdf->SetXY($largeurMat+10+15+15,$YmoyenneGeneral);
//$pdf->MultiCell(15,10,"$moyenClasseMinaff",1,'C',1);
$pdf->SetXY($largeurMat+10+15+15+15,$YmoyenneGeneral);
//$pdf->MultiCell(15,10,"$moyenClasseMaxaff",1,'C',1);

$pdf->SetFillColor(255,255,255);





// cadre moyenne 
$YmoyenneGeneral=$YmoyenneGeneral + 11;

if ($moyenspec_prof != ""){
	$notespec_prof = $moyenspec_prof / $nbnotespec_prof;
	$notespec_prof=number_format($notespec_prof,2,'.','');
	$notespec_prof=preg_replace('/\./',',',$notespec_prof);
}
if ($moyenens_generaux != ""){
	$noteens_generaux = $moyenens_generaux / $nbnoteens_generaux;
	$noteens_generaux=number_format($noteens_generaux,2,'.','');
	$noteens_generaux=preg_replace('/\./',',',$noteens_generaux);
}
if ($moyensect_prof != ""){
	$notesect_prof = $moyensect_prof / $nbnotesect_prof;
	$notesect_prof=number_format($notesect_prof,2,'.','');
	$notesect_prof=preg_replace('/\./',',',$notesect_prof);
}


$LargeurMG=$largeurMat;
$YmoyenneGeneralT=$YmoyenneGeneral + 2;
$XMoyGE= 10 + 15 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 10 + 6;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;

$pdf->SetFont('Arial','B',9);
$pdf->SetXY(10,$YmoyenneGeneral);
$pdf->MultiCell($LargeurMG/2,10,'MOYENNES',1,'C',0);
$pdf->SetFont('Arial','',9);

$pdf->SetFillColor(102,203,1);	
$pdf->SetXY(3+10+$LargeurMG/2,$YmoyenneGeneral);
$pdf->MultiCell(35,10,'',1,'L',1);  
$pdf->SetXY(3+10+$LargeurMG/2,$YmoyenneGeneral+2);
$pdf->MultiCell(35,3,'Enseignements Généraux*',0,'L',0);  
$pdf->SetXY(35+3+10+$LargeurMG/2,$YmoyenneGeneral);
$pdf->MultiCell(15,10,"$noteens_generaux",1,'C',0);   // note

$pdf->SetFillColor(82,255,51);
$pdf->SetXY(15+5+35+3+10+$LargeurMG/2,$YmoyenneGeneral);
$pdf->MultiCell(35,10,'',1,'L',1);  
$pdf->SetXY(15+5+35+3+10+$LargeurMG/2,$YmoyenneGeneral+2);
$pdf->MultiCell(35,3,'Secteur Professionnel**',0,'L',0);  
$pdf->SetXY(35+15+5+35+3+10+$LargeurMG/2,$YmoyenneGeneral);
$pdf->MultiCell(15,10,"$notesect_prof",1,'C',0);   // note

$pdf->SetFillColor(156,255,138);
$pdf->SetXY(15+5+35+15+5+35+3+10+$LargeurMG/2,$YmoyenneGeneral);
$pdf->MultiCell(35,10,'',1,'L',1);  
$pdf->SetXY(15+5+35+15+5+35+3+10+$LargeurMG/2,$YmoyenneGeneral+2);
$pdf->MultiCell(35,3,'Spécialité Professionnelle***',0,'L',0);  
$pdf->SetXY(35+15+5+35+15+5+35+3+10+$LargeurMG/2,$YmoyenneGeneral);
$pdf->MultiCell(15,10,"$notespec_prof",1,'C',0);   // note



// fin affichage



// cadre appréciation

$Ycom=$YmoyenneGeneral + 10 + 3;
$pdf->SetFont('Arial','',8);
$pdf->SetXY(10,$Ycom);
$pdf->MultiCell(190,6,"$appreciation $com_visa_scolaire",1,'L',0);

$Ycom=$Ycom + 9;
$EpaisCom=30;

$pdf->SetXY(10,$Ycom);
$pdf->MultiCell(150,$EpaisCom,'',1,'C',0);
$pdf->SetXY(13,$Ycom-4);
$pdf->WriteHTML("<B>".$appreciation2."</B>");

//rajout cadre avertissement
$appreciation3="Avertissement et récompenses  ";
$pdf->SetXY(115,$Ycom);
$pdf->MultiCell(45,$EpaisCom,"",1,'C',0);
$pdf->SetXY(115,$Ycom);
$pdf->MultiCell(45,$EpaisCom/2-0.5,"",1,'C',0);
$pdf->SetXY(116,$Ycom);
$pdf->WriteHTML("<B>".$appreciation3."</B>");

$Yrec=$Ycom+1;
$pdf->SetXY(118,$Yrec+5.5);
$pdf->MultiCell(3,3,'',1,'',$checkedmont3);
$pdf->SetXY(121,$Yrec+5.5);
$pdf->MultiCell(40,3,"Avertissement conduite",0,'L',0);
$pdf->SetXY(118,$Yrec+9.5);
$pdf->MultiCell(3,3,'',1,'',$checkedmont3);
$pdf->SetXY(121,$Yrec+9.5);
$pdf->MultiCell(40,3,"Avertissement travail",0,'L',0);
$pdf->SetXY(118,$Yrec+14.5);
$pdf->MultiCell(3,3,'',1,'',$checkedmont3);
$pdf->SetXY(121,$Yrec+14.5);
$pdf->MultiCell(40,3,"Encouragement",0,'L',0);
$pdf->SetXY(118,$Yrec+18.5);
$pdf->MultiCell(3,3,'',1,'',$checkedmont3);
$pdf->SetXY(121,$Yrec+18.5);
$pdf->MultiCell(40,3,"Tableau d'honneur",0,'L',0);
$pdf->SetXY(118,$Yrec+22.5);
$pdf->MultiCell(3,3,'',1,'',$checkedmont3);
$pdf->SetXY(121,$Yrec+22.5);
$pdf->MultiCell(40,3,"Félicitation",0,'L',0);
//fin ajout avertissement


$pdf->SetXY(160,$Ycom);
$pdf->MultiCell(40,$EpaisCom,"",1,'C',0);
$pdf->SetXY(160,$Ycom+2);
$pdf->MultiCell(40,3,"Le Chef d'établissement",0,'C',0);
$pdf->SetXY(160,$Ycom+$EpaisCom-4);
$pdf->SetFont('Arial','B',8);
$pdf->MultiCell(40,3,"$directeur",0,'C',0);

if (file_exists("./data/image_pers/logo_signature.jpg")){
	$taille = getimagesize("./data/image_pers/logo_signature.jpg");
	$logox=$taille[0]/25;
	$logoy=$taille[1]/25;
	$pdf->Image("./data/image_pers/logo_signature.jpg","165",$Ycom+6,$logox,$logoy);
}

$liste_ens_generaux=preg_replace('/,$/','',$liste_ens_generaux);
$liste_spec_prof=preg_replace('/,$/','',$liste_spec_prof);
$liste_sect_prof=preg_replace('/,$/','',$liste_sect_prof);


//duplicata et signature
$pdf->SetFont('Arial','',7);
$pdf->SetXY(10,$Ycom+$EpaisCom);
$pdf->MultiCell(190,3,"$duplicata",0,'C',0);
// fin duplicata
//
$liste_ens_generaux="";
$liste_spec_prof="";
$liste_sect_prof="";


// commentaire direction
// ---------------------

$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace('/\n/'," ",$commentairedirection);
$pdf->SetXY(13,$Ycom+5);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(140,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)


// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
$pdf->SetXY(13,$Ycom+17);
$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(140,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)







//FIN appréciation
// sortie dans le fichier
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
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();
?>
