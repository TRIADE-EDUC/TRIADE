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
<!-- // fin  -->
<br><br>
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

$opt1livretscolaire=$_POST["opt1livretscolaire"];
config_param_ajout($opt1livretscolaire,"opt1livretscolaire");
$opt2livretscolaire=$_POST["opt2livretscolaire"];
config_param_ajout($opt2livretscolaire,"opt2livretscolaire");


$debut=deb_prog();
$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
if (count($valeur)) {

	if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; }
}
/*
$hauteurphoto=$_POST["hauteurphoto"];
$largeurphoto=$_POST["largeurphoto"];
$hauteurlogo=$_POST["hauteurlogo"];
$largeurlogo=$_POST["largeurlogo"];
$avecexamenblanc=$_POST["avecexamenblanc"];
$affichemoyengeneral=$_POST["affichemoyengeneral"];
$affichematierecoefzero=$_POST["affichematierecoefzero"];
$abssconet=$_POST["abssconet"];
$afficherang=$_POST["afficherang"];
$npAfficheSousMatiere=$_POST["npAfficheSousMatiere"];
$hauteurMatiere=$_POST["hauteurMatiere"];
$npAfficheCoef=$_POST["npAfficheCoef"];
$coef100=$_POST["coef100"];
$adressebulletin=$_POST["adressebulletin"];
$noteviescolairedansmoyennegeneral=$_POST["noteviescolairedansmoyennegeneral"];
*/

if (trim($hauteurphoto) == "") {
	$hauteurphoto=16.3;
	$largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
	$hauteurlogo=25;
	$largeurlogo=25;
}

if ($hauteurMatiere == "") $hauteurMatiere=10; 
// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print ucwords($classe_nom)?><br> <br>
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


if (MODNAMUR0 == "oui") {
	$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
}

// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull_trim($_POST["saisie_classe"],$_POST["saisie_trimestre"]); // recup ordre matiere

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$pdf=new PDF();  // declaration du constructeur

$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve

$moyenClasseGen=""; // pour le calcul moyenne classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

// pour le calcul de moyenne classe
if  ($avecexamenblanc == "oui") {
	$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
}else{
	$moyenClasseGen=calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
}
if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// ----------------------------

$plageEleve=$_POST["plageEleve"];
if ($plageEleve == "") $plageEleve="tous"; 
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

	//---------------------------------//
	// recherche le nombre de retard
	$nbretard=0;
	$nbretard1=0;
	$nbheureabs=0;
	$nbjoursabs=0;
	$nbabs=0;
	$nbabsnonjustifier=0;
	if ($abssconet == "oui") {
		$nbretard=nombre_retard_sconet($idEleve,$triabsconet);
		$nbabs=nombre_abs_sconet($idEleve,$triabsconet);
		$nbabsnonjustifier=nombre_abs_nonjustifie_sconet($idEleve,$triabsconet);
	}else{
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
	}
	//---------------------------------//



	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	// declaration variable
	$coordonne00=strtoupper($academie);
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	$coordonne4="E-mail : ".$mail;


	$titre="<B><U>".LANGBULL30."</U> <U>".ucwords($textTrimestre)."</u></B>";

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",40);


	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=ucwords($classe_nom);

	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	$appreciation=LANGBULL39;
	if ($abssconet == "oui") {
		$appreciationbis="($nbretard retard(s) / $nbabs absence(s) / $nbabsnonjustifier absence(s) non justifié(s) ) " ;
	}else{
		$appreciationbis="($nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) ) " ;
	}

	$barre="____________________________________________________________________________________________";
	$appreciation2=LANGBULL40;
	$duplicata=LANGBULL41 . " - $urlsite - $mail";
	$signature=LANGBULL42;
	$signature2="";
	$signature="";
	// FIN variables

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo


	// mise en place du logo
	$logo="./image/commun/logo-educ-fr.jpg";
	if (file_exists($logo)) {
		$xlogo=30;
		$ylogo=50;
		$xcoor0=35;
		$ycoor0=3;
		$xtitre=90; // avec logo
		$pdf->Image($logo,3,3,$xlogo,$ylogo);
	}
	// fin du logo
	//

	$idprofp=rechercheprofp($_POST["saisie_classe"],$anneeScolaire);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->WriteHTML($coordonne00);
	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY($xcoor0,$ycoor0+7);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0+14);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY($xcoor0,$ycoor0+21);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY($xcoor0,$ycoor0+28);
	$pdf->WriteHTML($coordonne3);
	$pdf->SetXY($xcoor0,$ycoor0+36);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate="Année scolaire $anneeScolaire";
	// fin d'insertion

        $Y=60;

	$pdf->SetFillColor(226,240,250);
	$pdf->SetXY(3,$Y); // placement du cadre du nom de l eleve
	$pdf->MultiCell(203,45,'',0,'L',1);
	$pdf->SetXY(3,$Y+=3); // placement du cadre du nom de l eleve
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(203,3,"$Pdate",0,'C',1);
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY(3,$Y+=5); // placement du cadre du nom de l eleve
	$cycle=chercherNiveauClasse($idClasse);
	if (trim($cycle) != "") $cycleL="du $cycle";
	$pdf->MultiCell(203,3,"Bilan Trimestriel $cycleL - ".ucwords($textTrimestre)." ",0,'C',0);

	


	// adresse de l'élève
	// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance,email_eleve,adr_eleve,ccp_eleve,commune_eleve
	$dataadresse=chercheadresse($idEleve);
	$ik=0;
	$nomtuteur=$dataadresse[$ik][1];
	$prenomtuteur=$dataadresse[$ik][2];
	$adr1=$dataadresse[$ik][3];
	$code_post_adr1=$dataadresse[$ik][4];
	$commune_adr1=$dataadresse[$ik][5];
	$numero_eleve=$dataadresse[$ik][9];
	$datenaissance=$dataadresse[$ik][11];
	$adr_eleve=$dataadresse[$ik][21];
	$ccp_eleve=$dataadresse[$ik][22];
	$commune_eleve=$dataadresse[$ik][23];
	$regime=$dataadresse[$ik][12];
	$class_ant=trim(trunchaine($dataadresse[$ik][10],20));
	if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }

//	$pdf->WriteHTML("Né(e) le $datenaissance");

	$pdf->SetXY(3,$Y+=10);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(203,3,"$nomEleve $prenomEleve",0,'C',0);
	$pdf->SetXY(3,$Y+=5);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(203,3,"Né(e) le $datenaissance",0,'C',0);

	$pdf->SetXY(3,$Y+=10);
	$pdf->MultiCell(203,3,"Professeur principal : $profp",0,'C',0);
	$pdf->SetXY(3,$Y+=5);
	$pdf->MultiCell(203,3,"Classe de $classe_nom ",0,'C',0);
	
	$pdf->SetFillColor(0,148,218);
        $pdf->SetXY(3,$Y+=10); // placement du cadre du nom de l eleve
	$pdf->SetTextColor(255);
	$pdf->SetFont('Arial','B',12);
        $pdf->MultiCell(203,8,'Suivi des acquis scolaires de l\'élève',0,'C',1);
	$pdf->SetTextColor(0);
	


	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$X=3;
	$Y+=3;

	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
	$coefEleG=0;
	
	$largeurMat=40;

	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(226,240,250);
	if ($opt1livretscolaire == "oui") {
		$pdf->SetXY($largeurMat+$X,$Y+=10); //  placement  cadre titre
		$pdf->MultiCell(60,8,"",0,'L',1);
		$pdf->SetXY($largeurMat+$X,$Y+1); //  placement  cadre titre
		$pdf->MultiCell(60,3,"Elèments du programme travaillés durant la période (connaissances/compétences)",0,'L',0);
		$larg=73;
		$plus=60;
	}else{
		$Y+=10;
		$plus=0;
		$larg=73+60;

	}	
	$pdf->SetXY($largeurMat+$X+$plus,$Y); //  placement  cadre titre
	$pdf->MultiCell($larg,8,"",0,'L',1);
	$pdf->SetXY($largeurMat+$X+$plus,$Y+1); //  placement  cadre titre
	$pdf->MultiCell($larg,3,"Acquisitions, progrès et difficultés éventuelles",0,'L',0);

	$pdf->SetFillColor(204,223,240);
	$pdf->SetXY($largeurMat+$X+$plus+$larg,$Y); //  placement  cadre titre
	$pdf->MultiCell(15,8,"",0,'L',1);
	$pdf->SetXY($largeurMat+$X+$plus+$larg,$Y+1); //  placement  cadre titre
	$pdf->MultiCell(15,3,"Moyenne de l'élève",0,'L',0);

	$pdf->SetFillColor(226,240,250);
	$pdf->SetXY($largeurMat+$X+$plus+$larg+15,$Y); //  placement  cadre titre
	$pdf->MultiCell(15,8,"",0,'L',1);
	$pdf->SetXY($largeurMat+$X+$plus+$larg+15,$Y+1); //  placement  cadre titre
	$pdf->MultiCell(15,3,"Moyenne de classe",0,'L',0);


	$color=226;
	$Y+=8;
	for($i=0;$i<count($ordre);$i++) {


		if ($i > 13) {	
			$pdf->AddPage();
			$Y=5;
		}

		if ($color == 226) {
			$pdf->SetFillColor(240,247,253);	
		}else{			
			$pdf->SetFillColor(226,240,250);	
		}

		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',0,'L',1);
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurMat,3,trunchaine(strtoupper(sansaccent(strtolower($matiere))),25),0,'L',0);
		if (strlen($matiere) >= 22) {
			$pdf->SetXY($X+1,$Y+7);	
		}else{
			$pdf->SetXY($X+1,$Y+4);	
		}
		$pdf->SetFont('Arial','',6);
		$pdf->MultiCell($largeurMat,3,"$nomprof",0,'L',0);
		$pdf->SetFont('Arial','',8);


		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($X,$Y);
		// mise en place des notes
		$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);

		$pdf->SetDrawColor(255);

		$pdf->SetFont('Arial','',8);

		if ($opt1livretscolaire == "oui") {
	        	$pdf->SetXY($largeurMat+$X,$Y); //  placement  cadre titre
	        	$pdf->MultiCell(60,$hauteurMatiere,"",1,'L',1);
	        	$pdf->SetXY($largeurMat+$X,$Y+1); //  placement  cadre titre
			$commentaireeleve=cherche_com_eleve2($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe,'5',$anneeScolaire);
			$commentaireeleve=preg_replace('/\n/'," ",$commentaireeleve);
			$pdf->SetFont('Arial','',5);
	        	$pdf->MultiCell(60,3,"$commentaireeleve",0,'L',0);
			unset($commentaireeleve);
			$larg=73;
	                $plus=60;
		}else{
	                $plus=0;
        	        $larg=73+60;
		}

        	$pdf->SetXY($largeurMat+$X+$plus,$Y); //  placement  cadre titre
        	$pdf->MultiCell($larg,$hauteurMatiere,"",1,'L',1);
        	$pdf->SetXY($largeurMat+$X+$plus,$Y+1); //  placement  cadre titre
		$pdf->SetFont('Arial','',5);
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	        $commentaireeleve=preg_replace('/\n/'," ",$commentaireeleve);
        	$pdf->MultiCell($larg,3,"$commentaireeleve",0,'L',0);
		unset($commentaireeleve);

		$pdf->SetFont('Arial','',8);
		if ($idgroupe == "0") {
                	$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
                }else{
                        $noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
                }

        	$pdf->SetFillColor(204,223,240);
        	$pdf->SetXY($largeurMat+$X+$plus+$larg,$Y); //  placement  cadre titre
        	$pdf->MultiCell(15,$hauteurMatiere,"",1,'L',1);
        	$pdf->SetXY($largeurMat+$X+$plus+$larg,$Y+1); //  placement  cadre titre
        	$pdf->MultiCell(15,3,"$noteaff",0,'C',0);

		if ($color == 226) {
			$pdf->SetFillColor(240,247,253);	
			$color=220;
		}else{			
			$color=226;
			$pdf->SetFillColor(226,240,250);	
		}

		if ($idgroupe == "0") {
                	$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
                }else {
                        $moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
                }


        	$pdf->SetXY($largeurMat+$X+$plus+$larg+15,$Y); //  placement  cadre titre
        	$pdf->MultiCell(15,$hauteurMatiere,"",1,'L',1);
        	$pdf->SetXY($largeurMat+$X+$plus+$larg+15,$Y+1); //  placement  cadre titre
       		$pdf->MultiCell(15,3,"$moyeMatGen",0,'C',0);

		$Y=$Y + $hauteurMatiere;

	}

	// PAGE 2
	//----------------------------------------------------------------------------------------------------------------------
	$pdf->AddPage();
	$X=3;
	$Y=10;
	
	/***********************************************/
	$hautBilan=50;
	$hautFamille=90;
	$hautVisaFamille=30;
	if ($opt2livretscolaire == "oui") { 
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($X,$Y);
	        $pdf->SetFillColor(226,240,250);
	        $pdf->MultiCell(203,8,"Enseignements pratiques interdisciplinaires : projets réalisés et implication de l'élève",0,"L",1);
		$pdf->SetFillColor(240,247,253);
		$pdf->SetFont('Arial','',8);
		$dataEPI=recupComLivretEPIAP('EPI',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],"1");
		$commentaire=recupCommentaireLivretEPIAPIdeleve('EPI',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],'1',$idEleve);
		// intitule,thematique,idprof,commentaire,type_rubrique,annee_scolaire,trim,idclasse
		$intitule=$dataEPI[0][0];
		$thematique=$dataEPI[0][1];
	        $pdf->SetXY($X,$Y+=8);
	        $pdf->MultiCell($largeurMat,10,"",1,'L',1);
	        $pdf->SetXY($X,$Y+1);
	        if ($intitule != "") $pdf->MultiCell($largeurMat,3,"$intitule\n$thematique\n$profp",0,'L',0);

	        $pdf->SetXY($X+$largeurMat,$Y);
	        $pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
	        $pdf->SetXY($X+$largeurMat,$Y+1);
	        $pdf->MultiCell(203-$largeurMat,3,"$commentaire",0,'L',0);

		//-----
	
	        $pdf->SetFillColor(226,240,250);
		$dataEPI=recupComLivretEPIAP('EPI',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],"2");
		$commentaire=recupCommentaireLivretEPIAPIdeleve('EPI',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],'2',$idEleve);
		$intitule=$dataEPI[0][0];
                $thematique=$dataEPI[0][1];

	        $pdf->SetXY($X,$Y+=10);
	        $pdf->MultiCell($largeurMat,10,"",1,'L',1);
		$pdf->SetXY($X,$Y+1);
                if ($intitule != "") $pdf->MultiCell($largeurMat,3,"$intitule\n$thematique\n$profp",0,'L',0);

	        $pdf->SetXY($X+$largeurMat,$Y);
	        $pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
		$pdf->SetXY($X+$largeurMat,$Y+1);
                $pdf->MultiCell(203-$largeurMat,3,"$commentaire",0,'L',0);

		$Y+=20;
		$hautBilan=30;
		$hautFamille=70;
		$hautVisaFamille=15;
	}


	/***********************************************/
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($X,$Y);
	$pdf->SetFillColor(226,240,250);
	$pdf->MultiCell(203,8,"Accompagnement personnalisé : actions réalisées et implication de l'élève",0,"L",1);
	$pdf->SetFillColor(240,247,253);

	$pdf->SetFont('Arial','',8);
	$dataEPI=recupComLivretEPIAP('AP',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],"1");
	$commentaire=recupCommentaireLivretEPIAPIdeleve('AP',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],'1',$idEleve);
	$intitule=$dataEPI[0][0];
        $thematique=$dataEPI[0][1];

	$pdf->SetXY($X,$Y+=8);
	$pdf->MultiCell($largeurMat,10,"",1,'L',1);
	$pdf->SetXY($X,$Y+1);
        if ($intitule != "") $pdf->MultiCell($largeurMat,3,"$intitule",0,'L',0);
	$pdf->SetXY($X+$largeurMat,$Y);
	$pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
	$pdf->SetXY($X+$largeurMat,$Y+1);
        $pdf->MultiCell(203-$largeurMat,3,"$commentaire",0,'L',0);


	$dataEPI=recupComLivretEPIAP('AP',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],"2");
	$commentaire=recupCommentaireLivretEPIAPIdeleve('AP',$anneeScolaire,$idClasse,$idprofp,$_POST["saisie_trimestre"],'2',$idEleve);
        $intitule=$dataEPI[0][0];
        $thematique=$dataEPI[0][1];

	$pdf->SetFillColor(226,240,250);
	$pdf->SetXY($X,$Y+=10);
	$pdf->MultiCell($largeurMat,10,"",1,'L',1);
	$pdf->SetXY($X,$Y+1);
	if ($intitule != "") $pdf->MultiCell($largeurMat,3,"$intitule",0,'L',0);	

	$pdf->SetXY($X+$largeurMat,$Y);
        $pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
	$pdf->SetXY($X+$largeurMat,$Y+1);
        $pdf->MultiCell(203-$largeurMat,3,"$commentaire",0,'L',0);


	$Y+=20;
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($X,$Y);
	$pdf->SetFillColor(226,240,250);
	$pdf->MultiCell(203,8,"Parcours éducatifs : projet(s) mis en oeuvre et implication de l'élève",0,"L",1);

	$pdf->SetFillColor(240,247,253);
	$pdf->SetFont('Arial','',8);
        $pdf->SetXY($X,$Y+=8);
        $pdf->MultiCell($largeurMat,10,"Parcours avenir : ",1,'L',1);
        $pdf->SetXY($X+$largeurMat,$Y);
	unset($com);
	$com=recherche_com_profp($idEleve,$_POST["saisie_trimestre"],$anneeScolaire,"paravenir");
        $pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
        $pdf->SetXY($X+$largeurMat,$Y+1);
        $pdf->MultiCell(203-$largeurMat,3,"$com",0,'L',0);

        $pdf->SetFillColor(226,240,250);
        $pdf->SetXY($X,$Y+=10);
        $pdf->MultiCell($largeurMat,10,"Parcours citoyen : ",1,'L',1);
	unset($com);
	$com=recherche_com_profp($idEleve,$_POST["saisie_trimestre"],$anneeScolaire,"parcitoyen");
        $pdf->SetXY($X+$largeurMat,$Y);
        $pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
        $pdf->SetXY($X+$largeurMat,$Y+1);
        $pdf->MultiCell(203-$largeurMat,3,"$com",0,'L',0);
	
	$pdf->SetFillColor(240,247,253);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY($X,$Y+=8);
        $pdf->MultiCell($largeurMat,10,"",1,'L',1);
        $pdf->SetXY($X,$Y+1);
        $pdf->MultiCell($largeurMat,3,"Parcours d'éducation artistique et culturelle : ",0,'L',0);
        $pdf->SetXY($X+$largeurMat,$Y);
	unset($com);
	$com=recherche_com_profp($idEleve,$_POST["saisie_trimestre"],$anneeScolaire,"pareducart");
        $pdf->MultiCell(203-$largeurMat,10,"",1,'L',1);
        $pdf->SetXY($X+$largeurMat,$Y+1);
        $pdf->MultiCell(203-$largeurMat,3,"$com",0,'L',0);

	/************************************************************************************************************************/
	$Y+=10;			
	$pdf->SetFillColor(166,216,28);
        $pdf->SetXY(3,$Y+=10); // placement du cadre du nom de l eleve
	$pdf->SetTextColor(255);
	$pdf->SetFont('Arial','B',12);
        $pdf->MultiCell(203,8,"Bilan de l'acquisition des connaissances et compétences",0,'C',1);
	$pdf->SetTextColor(0);

	$pdf->SetFillColor(238,247,205);
	$pdf->SetXY(3,$Y+=10);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(203,$hautBilan,"",0,'C',1);	
	$pdf->SetXY(3,$Y+1);
	$pdf->MultiCell(203,3,"Synthèse de l'évolution des acquis scolaires et conseils pour progresser : ",0,'L',0);
	$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
        $commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
        $confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
	$pdf->SetXY(3,$Y+1);
	$pdf->MultiCell(203,3,"$commentaireprofp",0,'L',1);	


	/************************************************************************************************************************/

	$Y+=$hautBilan;
	$pdf->SetFillColor(246,135,18);
        $pdf->SetXY(3,$Y+=10); // placement du cadre du nom de l eleve
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial','B',12);
        $pdf->MultiCell(203,8,"Communication avec la famille",0,'C',1);
        $pdf->SetTextColor(0);

	$pdf->SetFillColor(254,235,210);
        $pdf->SetXY(3,$Y+=10);
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(203,$hautFamille,"",0,'C',1);

	$pdf->SetFont('Arial','',10);

	$pdf->SetXY(3+5,$Y+5);
	$pdf->SetFillColor(252,202,142);
	$pdf->MultiCell(150,52,"",1,'C',1);
	$pdf->SetXY(3+5,$Y+6);
	$pdf->MultiCell(150,3,"Vie scolaire (assiduité,ponctualité ; respect du règlement intérieur ;\nparticipation à la vie de l'établissement) :",0,'L',0);
	$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
	$commentairedirection=preg_replace('/\n/'," ",$commentairedirection);
	$pdf->SetXY(3+5,$Y+13);
	$pdf->MultiCell(150,3,"$commentairedirection",0,'L',0);

	$pdf->SetXY(3+5,$Y+33);
	$pdf->MultiCell(150,3,"Retards : [$nbretard]",0,'L',0);
	$pdf->SetXY(3+5,$Y+37);
	$pdf->MultiCell(150,3,"Absences justifiées par les responsables légaux : [$nbabs] demi-journées",0,'L',0);
	$pdf->SetXY(3+5,$Y+41);
	$pdf->MultiCell(150,3,"Absences non justifiées par les responsables légaux : [$nbabsnonjustifier] demi-journées",0,'L',0);
	$pdf->SetXY(3+5,$Y+45);
	$pdf->MultiCell(150,3,"Nbr d'heures de cours manquées du fait de ses absences : [....] heure(s)",0,'L',0);

        $pdf->SetXY(157,$Y+5);
	$pdf->MultiCell(45,52,"",1,'C',1);
	$pdf->SetXY(157,$Y+6);
	$pdf->MultiCell(45,3,"Date, nom et signature du chef d'établissement",0,'R',0);

	if (file_exists("./data/image_pers/logo_signature.jpg")){
                $taille = getimagesize("./data/image_pers/logo_signature.jpg");
                $logox=$taille[0]/25;
                $logoy=$taille[1]/25;
                $pdf->Image("./data/image_pers/logo_signature.jpg","160",$Y+6+10,$logox,$logoy);
		$pdf->SetXY(157,$Y+46);
		$date=dateDMY();
		$pdf->MultiCell(45,3,"$date",0,'R',0);
		
        }

	
	
	$pdf->SetXY(3+5,$Y+=50);
	$pdf->MultiCell(194,$hautVisaFamille,"",1,'C',1);
	$pdf->SetXY(3+5,$Y+1);
	$pdf->MultiCell(194,3,"Visa de la famille (Date, nom et signature des responsables légaux) ",0,'L',0);





	

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
