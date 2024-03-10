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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
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
<?php
include_once("./common/config.inc.php");
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


$affmatieresansnote=$_POST["affmatieresansnote"];
config_param_ajout($affmatieresansnote,"affmatieresansnote");

$affmoyengeneralexls=$_POST["affmoyengeneralexls"];
config_param_ajout($affmoyengeneralexls,"affmoyengeneralexls");

$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
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

	$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebutP1=$dateRecup[$j][0];
		$dateFinP1=$dateRecup[$j][1];
		$dateDebut=$dateDebutP1;
		$dateFin=$dateFinP1;
	}
	$dateDebutP1=dateForm($dateDebutP1);
	$dateFinP1=dateForm($dateFinP1);

	$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebutP2=$dateRecup[$j][0];
		$dateFinP2=$dateRecup[$j][1];
		$dateDebut=$dateDebutP2;
		$dateFin=$dateFinP2;
	}
	$dateDebutP2=dateForm($dateDebutP2);
	$dateFinP2=dateForm($dateFinP2);
	
	$dateRecup=recupDateTrimByIdclasse("trimestre3",$_POST["saisie_classe"]);
	for($j=0;$j<count($dateRecup);$j++) {
		$dateDebutP3=$dateRecup[$j][0];
		$dateFinP3=$dateRecup[$j][1];
		$dateDebut=$dateDebutP3;
		$dateFin=$dateFinP3;
	}
	$dateDebutP3=dateForm($dateDebutP3);
	$dateFinP3=dateForm($dateFinP3);


	// recupe du nom de la classe
	$data=chercheClasse($_POST["saisie_classe"]);
	$classe_nom=$data[0][1];

	// recup année scolaire
	$anneeScolaire=$_POST["annee_scolaire"];

	include_once("./librairie_php/class.writeexcel_workbook.inc.php");
	include_once("./librairie_php/class.writeexcel_worksheet.inc.php");

	setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');


	if (!is_dir("./data/bull_xls/")) @mkdir("./data/bull_xls/");
	$fichier="./data/bull_xls/bulletin_.xls";
	$fichiername="bulletin_$classe_nom.xls";

	@unlink($fichier);

	$workbook = &new writeexcel_workbook($fichier);

	/* --------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------- */
	$titre1 =& $workbook->addformat(array(  border => 0, 
						fg_color => 'white', 
						pattern => 1,  
						italic => 0, 
						bold => 1, 
						underline => 0,  
						color => 'black', 
						size => 15, 
						font => 'Trebuchet MS'));
	$titre1->set_align('left');
	$titre1->set_align('vcenter');
	/* ---------------------------------- */
	$normal =& $workbook->addformat(array(  border => 0, 
						fg_color => 'white', 
						pattern => 1,  
						italic => 0, 
						bold => 0, 
						underline => 0,  
						color => 'black', 
						size => 11, 
						font => 'Trebuchet MS'));
	$normal->set_align('left');
	$normal->set_align('vcenter');
	/* ---------------------------------- */
	$normal2 =& $workbook->addformat(array(  border => 0, 
						fg_color => 'white', 
						pattern => 1,  
						italic => 0, 
						bold => 1, 
						underline => 0,  
						color => 'black', 
						size => 12, 
						font => 'Trebuchet MS'));
	$normal2->set_align('right');
	$normal2->set_align('vcenter');
	/* ---------------------------------- */
	$normal3 =& $workbook->addformat(array(  border => 0, 
						fg_color => 'white', 
						pattern => 1,  
						italic => 0, 
						bold => 0, 
						underline => 0,  
						color => 'black', 
						size => 12, 
						font => 'Trebuchet MS'));
	$normal3->set_align('right');
	$normal3->set_align('vcenter');
	/* ---------------------------------- */
	$normal4 =& $workbook->addformat(array(  border => 0, 
						fg_color => 'white', 
						pattern => 1,  
						italic => 0, 
						bold => 1, 
						underline => 0,  
						color => 'black', 
						size => 15, 
						font => 'Trebuchet MS'));
	$normal4->set_align('right');
	$normal4->set_align('vcenter');
	/* ---------------------------------- */
	$titre2 =& $workbook->addformat(array(  border => 0, 
						fg_color => 'white', 
						pattern => 1,  
						italic => 0, 
						bold => 1, 
						underline => 0,  
						color => 'black', 
						size => 15, 
						font => 'Trebuchet MS'));
	$titre2->set_align('right');
	$titre2->set_align('vcenter');
	/* ---------------------------------- */
	$colonne1 =& $workbook->addformat(array(  border => 1, 
						fg_color => 'white', 
						pattern => 2,  
						italic => 0, 
						bold => 0,
					    
						underline => 0,  
						color => 'black', 
						size => 11, 
						font => 'Trebuchet MS'));
	$colonne1->set_align('center');
	$colonne1->set_align('vcenter');
	/* ---------------------------------- */
	$colonne2 =& $workbook->addformat(array(  border => 1, 
						fg_color => 'white', 
						pattern => 2,  
						italic => 0, 
						bold => 0,	
						underline => 0,  
						color => 'black', 
						size => 9, 
						font => 'Trebuchet MS'));
	$colonne2->set_align('center');
	$colonne2->set_align('vcenter');
	/* ---------------------------------- */
	$matiere1 =& $workbook->addformat();
	$matiere1->set_color('black');
	$matiere1->set_size(9);
	$matiere1->set_pattern(0x1);
	$matiere1->set_fg_color('white');
	$matiere1->set_align('center');
	$matiere1->set_align('vcenter');
	$matiere1->set_font('Trebuchet MS');
	$matiere1->set_merge();
	$matiere1->set_border(1);
	/* ---------------------------------- */
	$matiere0 =& $workbook->addformat();
	$matiere0->set_color('white');
	$matiere0->set_size(9);
	$matiere0->set_pattern(0x1);
	$matiere0->set_fg_color('gray');
	$matiere0->set_align('left');
	$matiere0->set_align('top');
	$matiere0->set_font('Trebuchet MS');
	$matiere0->set_bold(1);
	$matiere0->set_border(1);
	$matiere0->set_text_wrap();
	/* ---------------------------------- */
        $matiere0status =& $workbook->addformat();
        $matiere0status->set_color('white');
        $matiere0status->set_size(9);
        $matiere0status->set_pattern(0x1);
        $matiere0status->set_fg_color('gray');
        $matiere0status->set_align('center');
        $matiere0status->set_align('top');
        $matiere0status->set_font('Trebuchet MS');
        $matiere0status->set_bold(1);
        $matiere0status->set_border(1);
        $matiere0status->set_text_wrap();

	/* ---------------------------------- */
	$matiere2 =& $workbook->addformat();
	$matiere2->set_color('white');
	$matiere2->set_size(9);
	$matiere2->set_pattern(0x1);
	$matiere2->set_fg_color('gray');
	$matiere2->set_align('center');
	$matiere2->set_align('top');
	$matiere2->set_font('Trebuchet MS');
	$matiere2->set_bold(1);
	$matiere2->set_border(1);
	$matiere2->set_text_wrap();
	/* ---------------------------------- */
	$matiere3 =& $workbook->addformat();
	$matiere3->set_color('black');
	$matiere3->set_size(9);
	$matiere3->set_pattern(0x1);
	$matiere3->set_fg_color('white');
	$matiere3->set_align('left');
	$matiere3->set_align('top');
	$matiere3->set_font('Trebuchet MS');
	$matiere3->set_bold(0);
	$matiere3->set_border(1);
	$matiere3->set_text_wrap();
	/* ---------------------------------- */
	$matiere4 =& $workbook->addformat();
	$matiere4->set_color('black');
	$matiere4->set_size(9);
	$matiere4->set_pattern(0x1);
	$matiere4->set_fg_color('white');
	$matiere4->set_align('center');
	$matiere4->set_align('top');
	$matiere4->set_font('Trebuchet MS');
	$matiere4->set_bold(0);
	$matiere4->set_border(1);
	$matiere4->set_text_wrap();
	/* ---------------------------------- */
	$matiere44 =& $workbook->addformat();
	$matiere44->set_color('red');
	$matiere44->set_size(9);
	$matiere44->set_pattern(0x1);
	$matiere44->set_fg_color('white');
	$matiere44->set_align('center');
	$matiere44->set_align('top');
	$matiere44->set_font('Trebuchet MS');
	$matiere44->set_bold(0);
	$matiere44->set_border(1);
	$matiere44->set_text_wrap();
	/* ---------------------------------- */
	$commentaire0 =& $workbook->addformat();
	$commentaire0->set_color('white');
	$commentaire0->set_size(9);
	$commentaire0->set_pattern(0x1);
	$commentaire0->set_fg_color('gray');
	$commentaire0->set_align('left');
	$commentaire0->set_align('top');
	$commentaire0->set_font('Trebuchet MS');
	$commentaire0->set_bold(1);
	$commentaire0->set_border(1);
	$commentaire0->set_merge();
	/* ---------------------------------- */
	$M1 =& $workbook->addformat();
	$M1->set_color('black');
	$M1->set_size(9);
	$M1->set_pattern(0x1);
	$M1->set_fg_color('white');
	$M1->set_align('right');
	$M1->set_align('top');
	$M1->set_font('Trebuchet MS');
	$M1->set_bold(1);
	$M1->set_border(0);
	$M1->set_merge();
	/* ---------------------------------- */
	$M2 =& $workbook->addformat();
	$M2->set_color('black');
	$M2->set_size(12);
	$M2->set_pattern(0x1);
	$M2->set_fg_color('white');
	$M2->set_align('center');
	$M2->set_align('vcenter');
	$M2->set_font('Trebuchet MS');
	$M2->set_bold(1);
	$M2->set_border(1);
	/* ---------------------------------- */
	$M3 =& $workbook->addformat();
	$M3->set_color('black');
	$M3->set_size(9);
	$M3->set_pattern(0x1);
	$M3->set_fg_color('white');
	$M3->set_align('center');
	$M3->set_align('vcenter');
	$M3->set_font('Trebuchet MS');
	$M3->set_bold(1);
	$M3->set_border(1);
	/* ---------------------------------- */
	$commentaire1 =& $workbook->addformat(array(  border => 1, 
						fg_color => 'white', 
						pattern => 2,  
						italic => 0, 
						bold => 0,	
						underline => 0,  
						color => 'black', 
						size => 9, 
						font => 'Trebuchet MS'));
	$commentaire1->set_align('left'); // ESAD center
	$commentaire1->set_align('vcenter');
	$commentaire1->set_merge();
	$commentaire1->set_text_wrap();
	/* ---------------------------------- */
	/* ---------------------------------- */
	$commentaire2 =& $workbook->addformat(array(  border => 1, 
						fg_color => 'white', 
						pattern => 2,  
						italic => 0, 
						bold => 0,
					
						underline => 0,  
						color => 'black', 
						size => 9, 
						font => 'Trebuchet MS'));
	$commentaire2->set_align('left');
	$commentaire2->set_align('top');
	$commentaire2->set_merge();
	$commentaire2->set_text_wrap();
	/* ---------------------------------- */
	$commentaireDirection =& $workbook->addformat(array(  border => 1, 
						fg_color => 'white', 
						pattern => 2,  
						italic => 0, 
						bold => 0,
						underline => 0,  
						align=> 'left',
						color => 'black', 
						size => 9, 
						font => 'Trebuchet MS'));
	$commentaireDirection->set_align('left');
//	$commentaireDirection->set_valign('top');
	$commentaireDirection->set_merge();
	$commentaireDirection->set_text_wrap();
	/* ---------------------------------- */
	
	/* --------------------------------------------------------------------------------------------------------- */

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
	$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere

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
	for($j=$dep;$j<$nbEleveT;$j++) {  // premiere ligne de la creation PDF
		
		// variable eleve
		$nomEleve=ucwords($eleveT[$j][0]);
		$prenomEleve=ucfirst($eleveT[$j][1]);
		
		$datenaissance=$eleveT[$j][5];// ESAD
			if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }// ESAD
			
		$lv1Eleve=$eleveT[$j][2];
		$lv2Eleve=$eleveT[$j][3];
		$idEleve=$eleveT[$j][4];

		$dataadresse=chercheadresse($idEleve);
		$ik=0;
        	$nomtuteur=$dataadresse[$ik][1];
	        $prenomtuteur=$dataadresse[$ik][2];
	        $adr1=$dataadresse[$ik][3];
	        $code_post_adr1=$dataadresse[$ik][4];
	        $commune_adr1=$dataadresse[$ik][5];
	        $numero_eleve=$dataadresse[$ik][9];
		$INE=$numero_eleve;
	        $datenaissance=dateForm($dataadresse[$ik][11]);
	        $adr_eleve=$dataadresse[$ik][21];
	        $ccp_eleve=$dataadresse[$ik][22];
	        $villeEleve=$dataadresse[$ik][23];


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
		// mise en place du logo
		$photo=recup_photo_bulletin_idsite(chercheIdSite($_POST["saisie_classe"]));
		if (count($photo) > 0) {
			$logo="./data/image_pers/".$photo[0][0];
			if (file_exists($logo)) {
			}
		}
		$idprofp=rechercheprofp($_POST["saisie_classe"]);
		$profp=recherche_personne2($idprofp);
		// insertion de la Annee SCOLAIRE
		$Pdate="School year ".$anneeScolaire;
		$photoeleve=image_bulletin($idEleve);
		
		/*
		$pdf->MultiCell(70,3,"$nomprenom",0,'R',0);
		$pdf->MultiCell(70,3,"$classe_nom",0,'R',0);
		$pdf->MultiCell(70,3,"$titre",0,'L',0);
		 */


		$worksheet =& $workbook->addworksheet("$nomprenom");
		$worksheet->insert_bitmap('A1', 'image/banniere_triade/banniere-imsg.bmp', 0, 0); 


		$ligne=9;

		$worksheet->write("A$ligne","Innovation Park",$titre1);
		$ligne++;
		$worksheet->write("A$ligne","Campus Biotech Bât F2 F3",$normal);
		$ligne++;
		$worksheet->write("A$ligne","Avenue de Sécheron 15",$normal);
		$ligne++;
		$worksheet->write("A$ligne","CH-1202 Genève",$normal);
		$ligne++;
		$worksheet->write("A$ligne","+41 22 545 12 80",$normal);
		$ligne++;
		$worksheet->write("A$ligne","info@imsgeneva.ch / www.imsgeneva.ch",$normal);
		$ligne++;

		$worksheet->write('I2',"$Pdate",$normal2);
		$worksheet->write('I6',"$classe_nom",$normal3);


		$worksheet->write("F9","$prenomEleve $nomEleve");
		$worksheet->write("F10","$adr_eleve");
		$worksheet->write("F11","$ccp_eleve $villeEleve");


	/*	$dataadresse=chercheadresse($idEleve);
		for($ik=0;$ik<=count($dataadresse);$ik++) {
			$nomtuteur=$dataadresse[$ik][1];
			$prenomtuteur=$dataadresse[$ik][2];
			$adr1=$dataadresse[$ik][3];
			$code_post_adr1=$dataadresse[$ik][4];
			$commune_adr1=$dataadresse[$ik][5];
			$numero_eleve=$dataadresse[$ik][9];
			$datenaissance=$dataadresse[$ik][11];
			if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
			$regime=$dataadresse[$ik][12];
			$class_ant=trim(trunchaine($dataadresse[$ik][10],20));



		}
		*/
	// -------------------------------------------------------------------------------------------
	
		$worksheet->set_column('A:B',40);
		$worksheet->set_column('B:C',20);	// ESAD 5	
		$worksheet->set_column('C:D',8);		
		$worksheet->set_column('D:E',8);
		$worksheet->set_column('E:F',8);
		$worksheet->set_column('F:G',8);
		$worksheet->set_column('G:H',8);
		$worksheet->set_column('H:I',10);
		$worksheet->set_column('I:J',8);


		$worksheet->write('I4',"$prenomEleve $nomEleve",$normal4);
		$worksheet->write('I5',"Date de naissance : $datenaissance",$normal2); // ESAD

		$worksheet->write("A7","Bulletin $textTrimestre",$titre1);
		$ligne++;
	

		$worksheet->write("A16","Registration number : $INE",$titre1);
		$ligne++;
		$ligne++;

		$worksheet->write($ligne,0,"Contents",$matiere1);
		$worksheet->write_blank($ligne,1,$matiere1);

		$worksheet->write($ligne,2,"Appreciations",$matiere1); // ESAD
		$worksheet->write_blank($ligne,3,$matiere1);
		$worksheet->write_blank($ligne,4,$matiere1);
		$worksheet->write_blank($ligne,5,$matiere1);
//		$worksheet->write_blank(8,6,$matiere1);
		$ligne++;
		

		$ligne++;


		$worksheet->write("A$ligne","Title",$colonne2);
		$worksheet->write("B$ligne","Teacher",$colonne2); // ESAD coef
		
	//	$worksheet->write_blank(8,7,$matiere1);
	//	$worksheet->write_blank(8,8,$matiere1);

		$worksheet->write("G$ligne","Results",$colonne2);
		$worksheet->write("H$ligne","Status",$colonne2);
		$worksheet->write("I$ligne","ECTS",$colonne2);
		
/*		$worksheet->write(8,4,"Classe",$matiere1);
		$worksheet->write_blank(8,5,$matiere1);
		$worksheet->write_blank(8,6,$matiere1);

		$worksheet->write('E10',"Moy.",$colonne2);
		$worksheet->write('F10',"Mini",$colonne2);
		$worksheet->write('G10',"Maxi",$colonne2);
 */
		
		$recupUE=recupUE($idClasse,$sem); //code_ue,nom_ue,coef_ue,ects_ue
	
		$ectsTOTALP1=0;
		$ectsTOTALP2=0;
		// mise en place des matieres

		$ligne=21;
		$ligneN=$ligne-1;
		for($f=0;$f<count($recupUE);$f++) {
			$code_ue=$recupUE[$f][0];
			$nom_ue=$recupUE[$f][1];
			$coef_ue=$recupUE[$f][2];
			$ects_ue=$recupUE[$f][3];
			$listeMatiere=recupMatiereUE($code_ue,$idClasse);
			
			$worksheet->write("A$ligne","$nom_ue",$matiere0);
			$ligneUE=$ligne;
			$worksheet->write("B$ligne","",$matiere2); // ESAD $coef_ue
		
		/*	$worksheet->write("C$ligne","$moyElevUE",$matiere2);
			$worksheet->write("D$ligne","$ects_ue",$matiere2);
			$worksheet->write("E$ligne","$moyClassEU",$matiere2);
			$worksheet->write("F$ligne","$minClassEU",$matiere2);
			$worksheet->write("G$ligne","$maxClassEU",$matiere2);
		 */
			$worksheet->write($ligneN,2," ",$commentaire0);
			$worksheet->write_blank($ligneN,3,$commentaire0);
			$worksheet->write_blank($ligneN,4,$commentaire0);
			$worksheet->write_blank($ligneN,5,$commentaire0);
			$worksheet->write_blank($ligneN,6,$commentaire0);

			if (!isset($ligneUE)) $ligneUE=$ligne;

			$ligne++;
			$ligneN++;

			for($i=0;$i<count($listeMatiere);$i++) {
				$idmatiere=$listeMatiere[$i][0];
				$idMatiere=$listeMatiere[$i][0];
				$matiere=$listeMatiere[$i][1];
				$idprof=$listeMatiere[$i][2];  
				$nomprof=recherche_personne2($idprof);

				$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordre[$i][2]);
				if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

				$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);

				$datasousmatiere=verifsousmatierebull($idMatiere);
				if ($datasousmatiere != "0") {
					$nomMatierePrincipale=$datasousmatiere[0][2];
					$nomSousMatiere=$datasousmatiere[0][1];
					$matiere="$nomMatierePrincipale - $nomSousMatiere";// ESAD  - $nomSousMatiere
				}

				//$matiere=trunchaine($matiere,85);
		



				
				



				// mise en place de la colonne moyenne
				// ------------------------------------------------------
				unset($noteaffP11);
				unset($noteaffP1);
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
					$noteaffP1=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
				}else{
					$noteaffP1=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}

				if ($affmatieresansnote == "oui")  {
					$worksheet->write("A$ligne","$matiere",$matiere3);
					$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
					$worksheet->write("B$ligne","$nomprof",$matiere4); // ESAD coef
					unset($nomprof);
				}else{
					if ($noteaffP1 != "") {
						$worksheet->write("A$ligne","$matiere",$matiere3);
						$coef=recupCoefUE($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
						$worksheet->write("B$ligne","$nomprof",$matiere4); // ESAD coef
						unset($nomprof);
					}else{
						continue;
					}
				}

				$noteaffP11=$noteaffP1;
				if (($noteaffP1 < 10) && ($noteaffP1 != "")) { $noteaffP11="0".$noteaffP1; }

				$nbabs=nombre_abs_matiere($idEleve,$dateDebut,$dateFin,$idmatiere);
				$nbabs=count($nbabs);
				if ($nbabs > 0) { $nbabs="($nbabs Abs)"; }else{ $nbabs=""; }

					
				
				if (($noteaffP11 < 3) && ($noteaffP11 != "")) { 
					$worksheet->write("G$ligne","$noteaffP11",$matiere44);
					$ects=0;	
				}else{
					if ($noteaffP11 != "") {
						$ects=recupECTS($idmatiere,$idClasse,$_POST["saisie_trimestre"]);
						$worksheet->write("G$ligne","$noteaffP11",$matiere4);
						if ($noteaffP11 >= 10) {
							$ectsTOTAL+=$ects;
						        $ectsUE+=$ects;	
						}
					}else{
						$worksheet->write("G$ligne","",$matiere4);
						$ects="";	
					}
				}


				if ($noteaffP11 !=  "") { 
					if ($noteaffP11 < 3) {
						$status="F";
						$statusUE="F";
					}elseif (($noteaffP11 >= 3) && ($noteaffP11 < 4)) {
						 $status="C";
						 if ($statusUE == "") $statusUE="V";
					}elseif ($noteaffP11 >= 4) { 
						$status="V";		
						if ($statusUE == "") $statusUE="V";
					}else{
	
					}
				}

				$worksheet->write("H$ligne","$status",$matiere4);	
				$worksheet->write("H$ligneUE","$statusUE",$matiere0status);	

				unset($status);
				unset($statusUE);

				$worksheet->write("I$ligne","$ects",$matiere4);
			

				if ($noteaffP1 != "") {
					$moyUEP1+=$noteaffP1*$coef;
					$coefUEP1+=$coef;
				}

		
			
				// mise en place des moyennes de classe	
				if (($idgroupe == "0") || (trim($idgroupe) == "")) { 
		       	  		$moyeMatGen=moyeMatGen($idmatiere,$dateDebut,$dateFin,$idClasse,$idprof);
				}else {
	        			$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}
	
				$moyeMatGenaff=$moyeMatGen;
				if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
//				$worksheet->write("E$ligne","$moyeMatGenaff",$matiere4);

				if ($moyeMatGenaff != "") {
					$moyenMatGenTotal+=$moyeMatGenaff*$coef;
				}


				// calcul du min et du max
				// -----------------------
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
				// ------------------------------


//				$worksheet->write("F$ligne","$moyeMatGenMinaff",$matiere4);
//				$worksheet->write("G$ligne","$moyeMatGenMaxaff",$matiere4);



				if ($moyeMatGenMinaff != "") { $moyeMatGenMinaffUE+=$moyeMatGenMinaff*$coef; }
				if ($moyeMatGenMaxaff != "") { $moyeMatGenMaxaffUE+=$moyeMatGenMaxaff*$coef; }


				$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
				$worksheet->write($ligneN,2," $commentaireeleve",$commentaire2);
				$worksheet->write_blank($ligneN,3,$commentaire2);
				$worksheet->write_blank($ligneN,4,$commentaire2);
				$worksheet->write_blank($ligneN,5,$commentaire2);

				$ligne++;
				$ligneN++;

				if ( $noteaffP1 != "" ) {
	    				$noteMoyEleGTempo = $noteaffP1 * $coef;
           		     		$noteMoyEleG1=$noteMoyEleG1 + $noteMoyEleGTempo;
           		    		$coefEleG=$coefEleG + $coef;
				}

				if ($moyeMatGenMinaff != "") { $moyeMatGenMinTotalaff+=$moyeMatGenMinaff*$coef; }
				if ($moyeMatGenMaxaff != "") { $moyeMatGenMaxTotalaff+=$moyeMatGenMaxaff*$coef; }
				if ($moyeMatGenaff != "") { $moyeMatGenTotalaff+=$moyeMatGenaff*$coef; }

				unset($moyeMatGenMinaff);
				unset($moyeMatGenMaxaff);
			}

			if ($coefUEP1 != 0) {
				$moyElevUE=$moyUEP1/$coefUEP1;
				$moyElevUE=number_format($moyElevUE,'2',',','');

				$moyenMatGenTotal=$moyenMatGenTotal/$coefUEP1;
				$moyenMatGenTotal=number_format($moyenMatGenTotal,'2',',','');

				$minClassEU=$moyeMatGenMinaffUE/$coefUEP1;
				$minClassEU=number_format($minClassEU,'2',',','');

				$maxClassEU=$moyeMatGenMaxaffUE/$coefUEP1;
				$maxClassEU=number_format($maxClassEU,'2',',','');
			}

			


				

			$worksheet->write("H$ligneUE","$moyElevUE",$matiere2);
			$worksheet->write("I$ligneUE","$ectsUE",$matiere2);
//			$worksheet->write("E$ligneUE","$moyenMatGenTotal",$matiere2);
//			$worksheet->write("F$ligneUE","$minClassEU",$matiere2);
//			$worksheet->write("G$ligneUE","$maxClassEU",$matiere2);
			unset($ligneUE);
			unset($ectsUE);
			unset($moyUEP1);
			unset($coefUEP1);
			unset($moyenMatGenTotal);
			unset($moyeMatGenMinaffUE);
			unset($moyeMatGenMaxaffUE);
			unset($minClassEU);
			unset($maxClassEU);
			unset($moyElevUE);
		
		}

		if ($coefEleG != 0) {
			$moyEleveG=$noteMoyEleG1/$coefEleG;
			$moyEleveG=number_format($moyEleveG,'2',',','');
			
			$moyeMatGenaff=$moyeMatGenTotalaff/$coefEleG;
			$moyeMatGenaff=number_format($moyeMatGenaff,'2',',','');

			$moyeMatGenMaxTotalaff=$moyeMatGenMaxTotalaff/$coefEleG;
			$moyeMatGenMaxaff=number_format($moyeMatGenMaxTotalaff,'2',',','');

			$moyeMatGenMinTotalaff=$moyeMatGenMinTotalaff/$coefEleG;
			$moyeMatGenMinaff=number_format($moyeMatGenMinTotalaff,'2',',','');

		}

		$ligne+=2;
		
		/* ESAD */
		if ($affmoyengeneralexls == "oui") {	
			$worksheet->write("E$ligne","Overall Average : ",$M1);
			$worksheet->write("G$ligne","$moyEleveG",$M2);
			$worksheet->write("H$ligne","",$M2);
			$worksheet->write("I$ligne","$ectsTOTAL",$M3);
//			$worksheet->write("E$ligne","$moyeMatGenaff",$M3);
//			$worksheet->write("F$ligne","$moyeMatGenMinaff",$M3);
//			$worksheet->write("G$ligne","$moyeMatGenMaxaff",$M3);
	
			unset($moyeMatGenTotalaff);
			unset($moyeMatGenMinTotalaff);
			unset($moyeMatGenMaxTotalaff);
			unset($coefEleG);
			unset($ectsTOTAL);
			unset($moyEleveG);
			unset($noteMoyEleG1);

			$ligne+=3;
		}
		/*	ESAD */


		// commentaire direction
		$commentairedirec=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
		$commentairedirec=preg_replace("/\n/"," ",$commentairedirec);

		$commentairedir="Appréciation du conseil de classe : 
$commentairedirec"; // ESAD " /n "

		$ligneN=$ligne-1;
		$worksheet->write("A$ligne","$commentairedir",$commentaireDirection);
		$worksheet->write_blank($ligneN,1,$commentaireDirection);
		$worksheet->write_blank($ligneN,2,$commentaireDirection);
		$worksheet->write_blank($ligneN,3,$commentaireDirection);
		$worksheet->write_blank($ligneN,4,$commentaireDirection);
	
	}

	$workbook->close($fname);

	Pgclose();
	?>

	<br />
	<ul>
	<font class="T2">
	      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
	      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
	      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
	</font>
	</ul>
	
	
	<input type=button onClick="open('telecharger.php?fichier=<?php print $fichier?>&fichiername=<?php print $fichiername ?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2">

<?php 
}else{
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

<br /><br /><br />
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
