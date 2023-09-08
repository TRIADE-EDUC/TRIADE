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
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre="Bulletin 1er Trimestre"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre="Bulletin 2eme Trimestre"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre="Bulletin 3eme Trimestre"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; }
}

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



$idliste="";
$data=aff_grp_bull_bonifacio("module1");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabmodule1=explode(",",$idliste);

$idliste="";
$data=aff_grp_bull_bonifacio("module2");
$idliste=$data[0][1];
$idliste=preg_replace("/[\{\}]/",'',$idliste);
$tabmodule2=explode(",",$idliste);

$idliste="";
$data=aff_grp_bull_bonifacio("module3");
$idliste=$data[0][1];
$idliste=preg_replace("/[\{\}]/",'',$idliste);
$tabmodule3=explode(",",$idliste);

$idliste="";
$data=aff_grp_bull_bonifacio("module4");
$idliste=$data[0][1];
$idliste=preg_replace("/[\{\}]/",'',$idliste);
$tabmodule4=explode(",",$idliste);


// recherche des dates de debut et fin
//$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
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


$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve


// calcul min et max general
//-------------------------
for($g=0;$g<count($eleveT);$g++) {
	$idEleve=$eleveT[$g][4];
	$noteMoyEleG=0; // pour la moyenne de l'eleve general
	$coefEleG=0; // pour la moyenne de l'eleve general
	
	$nbeleve=0;
	$noteMoyEleG1=0; // pour la moyenne  general
	$coefEleG1=0; // pour la moyenne  general

	$nbCC=0;
	$moyenCC=0;
	$nbGMatiere=0;
	$moyenGMatiere=0;

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
		// print $datasousmatiere;
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}
	
		// mise en place des coeff
		$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
	
		$nbCC=0;
		$nbGMatiere=0;
		$moyenCC="";
		$moyenGMatiere=0;


		// ---------------------------------------------
		// mise en place moyenne DS1 eleve
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS1");
		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS1");
		}
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}

		// ---------------------------------------------
		// mise en place moyenne DS2 eleve
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS2");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS2");
    		}
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
	
		// ---------------------------------------------
		// mise en place moyenne DS3 eleve
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS3");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS3");
    		}
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
	
		// ---------------------------------------------
		// mise en place moyenne DS4 eleve
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS4");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS4");
    		}
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
		// ---------------------------------------------

		// ---------------------------------------------
		// mise en place Moy. CC
		if ($nbCC != 0) { $moyenCC=$moyenCC/$nbCC; }
		if (($moyenCC < 10) && ($moyenCC != "")) { $moyenCC="0".$moyenCC; }
		if ($moyenCC != "") {
			$moyenGMatiere=$moyenCC * 4;
			$nbGMatiere=4;
		}
		// ---------------------------------------------	

		// ---------------------------------------------
		// mise en place moyenne Partiel
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"Partiel");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"Partiel");
    		}
		if ($moyeMatGen != "") {
			$moyenGMatiere=$moyenGMatiere+($moyeMatGen*6);
			$nbGMatiere=$nbGMatiere+6;
		}
		// ---------------------------------------------
		// ---------------------------------------------
		// mise en place moyenne  Moy.
		if ($nbGMatiere != 0) { 
			$moyenGMatiere=$moyenGMatiere/$nbGMatiere; 
			if (($moyenGMatiere < 10) && (trim($moyenGMatiere) != "")) { $moyenGMatiere="0".$moyenGMatiere; }
		}
		// ---------------------------------------------
		// ---------------------------------------------

		// mise en place du nom du prof
		$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
		// pour le calcul de la moyenne general de l'eleve
		if ( $moyenGMatiere != "" ) {
		        $noteMoyEleGTempo = $moyenGMatiere * $coeffaff;
       	       		$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
       		        $coefEleG=$coefEleG + $coeffaff;	
		}
	}
	if (trim($noteMoyEleG) != "") {
		$noteMoyEleG=$noteMoyEleG/$coefEleG;
		$classementG[]=$noteMoyEleG;
	}
}
// fin min et max
// -------------

$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

$nbCC=0;
$moyenCC=0;
$nbGMatiere=0;
$moyenGMatiere=0;


$plageEleve=$_POST["plageEleve"];
if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
if ($plageEleve == "10") { $dep=0; $nbEleveT=9; }
if ($plageEleve == "20") { $dep=9; $nbEleveT=19; }
if ($plageEleve == "30") { $dep=19; $nbEleveT=29; }
if ($plageEleve == "40") { $dep=29; $nbEleveT=39; }
if ($plageEleve == "50") { $dep=39; $nbEleveT=49; }
if ($plageEleve == "60") { $dep=49; $nbEleveT=59; }
if ($nbEleveT > count($eleveT)) { $nbEleveT=count($eleveT); }
for($j=$dep;$j<$nbEleveT;$j++) { // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];


	$moyenmodule1="";
	$moyenmodule2="";
	$moyenmodule3="";
	$moyenmodule4="";
	$nbnotemodule1=0;
	$nbnotemodule2=0;
	$nbnotemodule3=0;
	$nbnotemodule4=0;
	$notemodule1="";
	$notemodule2="";
	$notemodule3="";
	$notemodule4="";

	$liste_module1="";
	$liste_module2="";
	$liste_module3="";
	$liste_module4="";


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


	$titre=ucwords($textTrimestre);

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

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo

	$pdf->SetXY(10,9);
	$pdf->MultiCell(190,0.1,'',1,'L',0);

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY(10,10);
	$pdf->WriteHTML("<B>$coordonne0</B>");
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(10,14);
	$pdf->WriteHTML("$coordonne1");
	$pdf->SetXY(10,17);
	$pdf->WriteHTML("$coordonne2");
	$pdf->SetXY(10,20);
	$pdf->WriteHTML("$coordonne3");
	$pdf->SetXY(10,23);
	$pdf->WriteHTML("$coordonne4");
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate=LANGBULL43." ".$anneeScolaire;
	$pdf->SetFont('Courier','',10);
	$pdf->SetXY(125,10);
	$pdf->WriteHTML($Pdate);
	// fin d'insertion
	

	// Titre
	$pdf->SetXY(128,18);
	$pdf->SetFont('Courier','B',11);
	$pdf->WriteHTML($titre);
	// fin titre



	// classe
	$pdf->SetXY(90,25);
	$pdf->SetFont('Courier','',11);
	$classe_nom=trunchaine($infoeleveclasse,30);
	$classe_nom=preg_replace('/_/','',$classe_nom);
	$pdf->WriteHTML("Classe : <B>".$classe_nom."</B>");

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

		$x=10;
		$y=35;
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Courier','B',9);
		$pdf->SetFillColor(255,203,145);
		$pdf->MultiCell(190,5,"",0,'L',0);

		
		$pdf->SetXY($x,$y);
		$pdf->MultiCell(85,5,"Nom de l'élève : ",1,'L',0);
		$pdf->SetXY($x+85,$y);

		$pdf->MultiCell(40,5,"Code :",1,'L',0);
		$pdf->SetXY($x+85+40,$y);
		$pdf->MultiCell(60,5,"Date de Naissance : ",1,'L',0);

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
	$pdf->MultiCell(15,8,"Coef",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15,$y);
	$pdf->MultiCell(45+15,4,"Note CC",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+45+15,$y);
	$pdf->MultiCell(15,8,"Moy. CC",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+45+15+15,$y);
	$pdf->MultiCell(15,8,"Partiel",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+45+15+15+15,$y);
	$pdf->MultiCell(15,8,"Moy.",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+45+15+15+15+15,$y);
	$pdf->MultiCell(15,8,"Classmnt",1,'C',1);

	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($largeurMat+$x+15,$y+4);
	$pdf->MultiCell(15,4,"DS1",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+15,$y+4);
	$pdf->MultiCell(15,4,"DS2",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+15+15,$y+4);
	$pdf->MultiCell(15,4,"DS3",1,'C',1);
	$pdf->SetXY($largeurMat+$x+15+15+15+15,$y+4);
	$pdf->MultiCell(15,4,"DS4",1,'C',1);
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


		if ($idgroupe == "0") {
			$classement=Rangs($idMatiere,$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
        		$classement=RangsGroupe($idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
    		}

		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		// print $datasousmatiere;
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}

		// fin de la gestion sous matiere
		// ------------------------------
		$pdf->SetFillColor(255,255,255);
	
		$pdf->SetXY($Xmat,$Ymat);
		foreach($tabmodule1 as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(102,203,1);
				$liste_module1.=ucwords($matiere).",";
				$nommodule="M1";
				break;
			}
		}

		foreach($tabmodule1 as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(82,255,51);
				$liste_module2.=ucwords($matiere).",";
				$nommodule="M2";
				break;
			
			}
		}

		foreach($tabmodule3 as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(156,255,138);
				$liste_module3.=ucwords($matiere).",";
				$nommodule="M3";
				break;
			}
		}


		foreach($tabmodule4 as $value) {
			if ($idMatiere == $value) {
				$pdf->SetFillColor(51,204,204);     // changer la couleur
				$liste_module4.=ucwords($matiere).",";
				$nommodule="M4";
				break;
			}
		}

		$pdf->SetXY(10,$Ymatcont);
		$pdf->SetFont('Arial','',7);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont-1.5,$Ymatcont);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),30).'</B> - '.$nommodule.'');
		

		$pdf->SetFont('Arial','',9);
		
		$pdf->SetFillColor(255,255,0);

		// mise en place des coeff
		$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetXY($largeurMat+10,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$coeffaff",1,'C',0);

		$nbCC=0;
		$nbGMatiere=0;
		$moyenCC="";
		$moyenGMatiere="";


		// ---------------------------------------------
		// mise en place moyenne DS1 eleve
		$pdf->SetXY($Xmoyclasse,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS1");
		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS1");
    		}
		$moyeMatGenaff=$moyeMatGen ;
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->SetXY($Xmoyclasse,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$moyeMatGenaff",0,'C',0);
		

		// ---------------------------------------------
		// mise en place moyenne DS2 eleve
		$pdf->SetXY($Xmoyclasse+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS2");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS2");
    		}
		$moyeMatGenaff=$moyeMatGen;
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->SetXY($Xmoyclasse+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$moyeMatGenaff",0,'C',0);

		// ---------------------------------------------
		// mise en place moyenne DS3 eleve
		$pdf->SetXY($Xmoyclasse+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS3");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS3");
    		}
		$moyeMatGenaff=$moyeMatGen;
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->SetXY($Xmoyclasse+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$moyeMatGenaff",0,'C',0);

		// ---------------------------------------------
		// mise en place moyenne DS4 eleve
		$pdf->SetXY($Xmoyclasse+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"DS4");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"DS4");
    		}
		$moyeMatGenaff=$moyeMatGen;
		if ($moyeMatGen != "") {
			$moyenCC=$moyenCC+$moyeMatGen;
			$nbCC++;
		}
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->SetXY($Xmoyclasse+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$moyeMatGenaff",0,'C',0);
		// ---------------------------------------------

		// ---------------------------------------------
		// mise en place Moy. CC
		$pdf->SetXY($Xmoyclasse+15+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->SetXY($Xmoyclasse+15+15+15+15,$Ymatcont);
		if ($nbCC != 0) { $moyenCC=$moyenCC/$nbCC; }
		if (($moyenCC < 10) && ($moyenCC != "")) { $moyenCC="0".$moyenCC; }
		$pdf->MultiCell(15,$hauteurMatiere,"$moyenCC",0,'C',0);
		if ($moyenCC != "") {
			$moyenGMatiere=$moyenCC * 4;
			$nbGMatiere=4;
		}
		// ---------------------------------------------	

		// ---------------------------------------------
		// mise en place moyenne Partiel
		$pdf->SetXY($Xmoyclasse+15+15+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"Partiel");
    		}else {
			$moyeMatGen=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"Partiel");
    		}
		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->SetXY($Xmoyclasse+15+15+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$moyeMatGenaff",0,'C',0);
		if ($moyeMatGen != "") {
			$moyenGMatiere=$moyenGMatiere+($moyeMatGen*6);
			$nbGMatiere=$nbGMatiere+6;
		}
		// ---------------------------------------------

		// ---------------------------------------------
		// mise en place moyenne  Moy.
		$pdf->SetXY($Xmoyclasse+15+15+15+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmoyclasse+15+15+15+15+15+15,$Ymatcont);
		if ($nbGMatiere != 0) { 
			$moyenGMatiere=$moyenGMatiere/$nbGMatiere; 
			if (($moyenGMatiere < 10) && (trim($moyenGMatiere) != "")) { $moyenGMatiere="0".$moyenGMatiere; }
		}
		$pdf->MultiCell(15,$hauteurMatiere,"$moyenGMatiere",0,'C',0);
		// ---------------------------------------------

		
		$moyenGMatiereRANG=moyenneEleveMatiere($idEleve,$idMatiere,$dateDebut,$dateFin,$idprof);
		// ---------------------------------------------
		// mise en place Rang
		// RANG

		if (trim($classement) != "") {
		rsort($classement);

		$i=1;
		$rangG="";
		$rangGT=count($classement);
		foreach ($classement as $key => $val) {	
		//	print "$key => $val --- $moyenGMatiereRANG ---  <br>";
			if ($val == $moyenGMatiereRANG) { 
				$rangG = $key + 1; 
				break;
			}
		}
		}
		$pdf->SetXY($Xmoyclasse+15+15+15+15+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmoyclasse+15+15+15+15+15+15+15,$Ymatcont);
		$pdf->MultiCell(15,$hauteurMatiere,"$rangG / $rangGT",0,'C',0);
		$rangG="";
		$rangGT="";
		$moyenGMatiereRANG="";

		// ---------------------------------------------

		$noteaff1=$moyenGMatiere;
		foreach($tabmodule1 as $value) {
			//print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenmodule1+= $noteaff1 * $coeffaff;
					$nbnotemodule1+= $coeffaff ;
				}
			}
		}

		foreach($tabmodule2 as $value) {
			//print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenmodule2+= $noteaff1 * $coeffaff;
					$nbnotemodule2+= $coeffaff ;
				}
			}
		}

		foreach($tabmodule3 as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenmodule3+= $noteaff1 * $coeffaff;
					$nbnotemodule3+= $coeffaff ;
				}
			}
		}

		foreach($tabmodule4 as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenmodule4+= $noteaff1 * $coeffaff;
					$nbnotemodule4+= $coeffaff ;
				}
			}
		}

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY($XprofVal,$YprofVal);
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;


	// pour le calcul de la moyenne general de l'eleve
	if ( $moyenGMatiere != "" )  {
	        $noteMoyEleGTempo = $moyenGMatiere * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		$coefEleG=$coefEleG + $coeffaff;
	}
	
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
}
// fin de la mise en place des matiere



$XX=20;
if ($nbnotemodule1 > 0) { $moyenmodule1=$moyenmodule1/$nbnotemodule1; }
$pdf->SetXY($XX,$Ymatcont+=3);
$pdf->SetFillColor(102,203,1);
$pdf->MultiCell(35,5,"Moy. Module 1 : $moyenmodule1 ",1,'C',0);

$pdf->SetXY($XX+=45,$Ymatcont);
if ($nbnotemodule2 > 0) { $moyenmodule2=$moyenmodule2/$nbnotemodule2; }
$pdf->SetFillColor(82,255,51);
$pdf->MultiCell(35,5,"Moy. Module 2 : $moyenmodule2 ",1,'C',0);

$pdf->SetXY($XX+=45,$Ymatcont);
if ($nbnotemodule3 > 0) { $moyenmodule3=$moyenmodule3/$nbnotemodule3; }
$pdf->SetFillColor(156,255,138);
$pdf->MultiCell(35,5,"Moy. Module 3 : $moyenmodule3 ",1,'C',0);

$pdf->SetXY($XX+=45,$Ymatcont);
if ($nbnotemodule4 > 0) { $moyenmodule4=$moyenmodule4/$nbnotemodule4; }
$pdf->SetFillColor(51,204,204); 
$pdf->MultiCell(35,5,"Moy. Module 4 : $moyenmodule4 ",1,'C',0);

$pdf->SetFillColor(255,255,255);
$pdf->SetXY($XX+=5,$Ymatcont);

// RANG
if ($coefEleG > 0) { $noteMoyEleG=$noteMoyEleG/$coefEleG; }

rsort($classementG);
$i=1;
$rangG="";
$rangGT=count($classementG);
foreach ($classementG as $key => $val) {	
	//print "$key => $val --- $noteMoyEleG ---  <br>";
	if ($val == $noteMoyEleG) { 
		$rangG = $key + 1; 
		break;
	}
}


$XX=40;
$Ymatcont+=7;
$pdf->SetXY($XX+=25,$Ymatcont);
$pdf->SetFont('Arial','B',11);
$noteMoyEleG=number_format($noteMoyEleG,2,'.','');
$pdf->MultiCell(55,5,"Moyenne Générale : $noteMoyEleG ",1,'C',0);
$pdf->SetXY($XX+=65,$Ymatcont);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(25,5,"Rang : $rangG / $rangGT ",1,'C',0);

$rangG="";
$rangGT="";

$liste_module1=preg_replace('/,$/','',$liste_module1);
$liste_module2=preg_replace('/,$/','',$liste_module2);
$liste_module3=preg_replace('/,$/','',$liste_module3);
$liste_module4=preg_replace('/,$/','',$liste_module4);


$Ymatcont+=7;



// commentaire direction
// ---------------------
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(10,$Ymatcont);
$pdf->MultiCell(185,40,"",1,'C',0);
$pdf->SetFont('Arial','',6);
$pdf->SetXY(11,$Ymatcont+1);
$pdf->MultiCell(190,3,"Module 1 : ".$liste_module1." / Module 2 : ".$liste_module2." / Module 3 : ".$liste_module3. " / Module 4 : ".$liste_module4,0,'L',0);

$pdf->SetFont('Arial','',10);
$pdf->SetXY(11,$Ymatcont+7);
$pdf->MultiCell(183,5,"Appréciation : $commentairedirection",'','L',0); // commentaire de la direction (visa)






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
