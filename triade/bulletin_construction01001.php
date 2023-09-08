<?php
session_start();
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
<?php include("./librairie_php/lib_defilement.php"); ?></TD><td width="472" valign="middle" rowspan="3" align="center">
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


$afficheRemplacant=$_POST["afficheRemplacant"];
config_param_ajout($afficheRemplacant,"afficheRemplacant");


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
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2"; }
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3"; }
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; }
}

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];
$classe_nom_long=$data[0][2];

$hauteurphoto=$_POST["hauteurphoto"];
$largeurphoto=$_POST["largeurphoto"];
$hauteurlogo=$_POST["hauteurlogo"];
$largeurlogo=$_POST["largeurlogo"];
$avecexamenblanc=$_POST["avecexamenblanc"];
$moyensousmatiere=$_POST["moyensousmatiere"];
$affichemoyengeneral=$_POST["affichemoyengeneral"];
$affichematierecoefzero=$_POST["affichematierecoefzero"];
$abssconet=$_POST["abssconet"];
$hauteurMatiere=$_POST["hauteurMatiere"];
if ($hauteurMatiere == "") $hauteurMatiere="8";
$nbmaxMatierePageUne=$_POST["nbmaxMatierePageUne"];
$bullclassant=$_POST["bullclassant"];
$affnoteviescolaire=$_POST["noteviecmp"];
$notaffmoyclass=$_POST["notaffmoyclass"];
$moyensurdix=$_POST["moyensurdix"];

$bullregime=$_POST["bullregime"];
$bullnumele=$_POST["bullnumele"];
$bullprofp=$_POST["bullprofp"];
$infoMatiere=$_POST["infoMatiere"];

$notaffminmax=$_POST["notaffminmax"];

if (trim($hauteurphoto) == "") {
	$hauteurphoto=16.3;
	$largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
	$hauteurlogo=25;
	$largeurlogo=25;
}

if ($hauteurMatiere < 7) $hauteurMatiere="7";
if ($hauteurMatiere > 13) $hauteurMatiere="13";

config_param_ajout($hauteurlogo,"hauteurlogo");
config_param_ajout($largeurlogo,"largeurlogo");
config_param_ajout($hauteurphoto,"hauteurphoto");
config_param_ajout($largeurphoto,"largeurphoto");
config_param_ajout($avecexamenblanc,"avecexamenblanc");
config_param_ajout($moyensousmatiere,"moyensousmatiere");
config_param_ajout($affichemoyengeneral,"affichemoyengeneral");
config_param_ajout($affichematierecoefzero,"affichematierecoefzero");
config_param_ajout($abssconet,"abssconet");
config_param_ajout($bullregime,"bullregime");
config_param_ajout($bullnumele,"bullnumele");
config_param_ajout($bullprofp,"bullprofp");
config_param_ajout($hauteurMatiere,"hauteurMatiere");
config_param_ajout($nbmaxMatierePageUne,"nbmaxMatierePageUne");
config_param_ajout($bullclassant,"bullclassant");
config_param_ajout($affnoteviescolaire,"noteviescolaire");
config_param_ajout($notaffmoyclass,"notaffmoyclass");
config_param_ajout($moyensurdix,"moyensurdix");
config_param_ajout($infoMatiere,"infoMatiere");
config_param_ajout($notaffminmax,"notaffminmax");



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
$data=visu_paramViaIdSite(chercheIdSite($_POST["saisie_classe"]));
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
//$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
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
$moyenClasseMin=1000; // pour la calcul moyenne min classe
$moyenClasseMax=""; // pour la calcul moyenne max  classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general

// pour le calcul de moyenne classe

if  ($avecexamenblanc == "oui") {
	$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
}else{
	$moyenClasseGen=calculMoyenClasseSansExam($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
}
if (($moyensurdix == "oui") && ($moyenClasseGen != "")) { 
	$moyenClasseGen=$moyenClasseGen/2;
	$moyenClasseGen=number_format($moyenClasseGen,2,'.','');
}
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

			if ($affichematierecoefzero != "oui") {
				$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
				if ($coeffaff == "0.00") { continue; } 
			}


			if ($avecexamenblanc == "oui") {
				$noteaff=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			}else{
				$noteaff=moyenneEleveMatiereSansExam($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
			}


			if ( trim($noteaff) != "" ) {
				$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$t][2]);
				if ($coeffaff > 0) {
					$noteMoyEleGTempo = $noteaff * $coeffaff;
				       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
					$coefEleG=$coefEleG + $coeffaff;
		                }else{
        		                $noteaff = $noteaff - 10;
                        		if ($noteaff > 0) { $noteMoyEleG=$noteMoyEleG + $noteaff ; }
		                }
			}
			unset($noteaff);
			unset($coeffaff);
			
		}

		if (MODNAMUR0 == "oui") {
			$noteaff=calculNoteVieScolaire($idEleveMoyen,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
			if ( trim($noteaff) != "" ) {
 				$noteMoyEleGTempo = $noteaff * $coefBull;
			       	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
				$coefEleG=$coefEleG + $coefBull;
			}
		}
 
 		if (trim($noteMoyEleG) != "" ) {
			$moyenEleve2=moyGenEleveab($noteMoyEleG,$coefEleG);
			if (($moyensurdix == "oui") && ($moyenEleve2 != "")) { 
				$moyenEleve2=$moyenEleve2/2;
			}
			$moyenEleve2=number_format($moyenEleve2,2,'.','');
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

	$infoLibelPiedPage="";

	$dejafait=0;

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
		//---------------------------------//
	}


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


	$titre="<B><U>".LANGBULL30."</U></B>";
	$titreSuite="<B><U>".ucwords($textTrimestre)."</u></B>";

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="<b>$nomEleve</b> $prenomEleve";
	
	$effbordure=1;

	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
        if ($classe_nom_long != "") {
                $classe_nom_long=trim(preg_replace('/_/',' ',$classe_nom_long));
        }
        $infoeleveclasse=preg_replace('/_/',' ',trim($classe_nom));


	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4=LANGBULL34;
	$soustitre5=LANGBULL35;
	$soustitre6=LANGBULL36;
	$soustitre7=LANGBULL37;
	$soustitre8=LANGBULL38;


	$appreciation="Avis du Conseil de Classe";
	if ($abssconet == "oui") {
		$appreciationbis="($nbretard retard(s) / $nbabs absence(s) / $nbabsnonjustifier absence(s) non justifié(s) ) " ;
	}else{
		$appreciationbis="Nombre d'absences:  Demi-journée : $nbabs / Heures : $nbheureabs  / retards : $nbretard  " ;
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
	$photo=recup_photo_bulletin();
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xlogo=$largeurlogo;
			$ylogo=$hauteurlogo;
			$xcoor0=40;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$pdf->Image($logo,3,3,$xlogo,$ylogo);
		}
	}
	// fin du logo
	//

	

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


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
	$pdf->SetXY($xcoor0,$ycoor0+20);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	$Pdate=LANGBULL43." ".$anneeScolaire;
	$pdf->SetFont('Courier','',10);
	$pdf->SetXY(130,3);
	$pdf->WriteHTML($Pdate);
	// fin d'insertion

	// Titre
	$pdf->SetXY($xtitre+20,15);
	$pdf->SetFont('Courier','',18);
	$pdf->WriteHTML($titre);
	$pdf->SetXY($xtitre+20,25);
	$pdf->WriteHTML($titreSuite);
	// fin titre

	// cadre du haut
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(220);
	$pdf->SetXY(15,35); // placement du cadre du nom de l eleve
	$pdf->MultiCell(184,20,'',1,'L',1);

	$photoeleve=image_bulletin($idEleve);

	$photo=$photoeleve;
	$xphoto=17;
	$yphoto=36;
	//$photowidth=18;
	//$photoheight=18;
	$photowidth=$largeurphoto;
	$photoheight=$hauteurphoto;
	$Xv1=20;
	$Xv11=111;
	if (!empty($photo)) {
		$photo=$photoeleve;
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
		$Xv1=20+$photowidth;
		$Xv11=110;
	}
	$pdf->SetXY($Xv1,36); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	$pdf->SetXY($Xv1+80,44);
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetXY($Xv1+94,44);
	$pdf->WriteHTML($infoeleveclasse);
	$pdf->SetXY($Xv1+80,49);
	$pdf->SetFont('Arial','',8);
	//$pdf->WriteHTML($classe_nom_long);
	$pdf->SetFont('Arial','',10);


	// adresse de l'élève
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
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

		$pdf->SetXY($Xv1,40); 
		$pdf->SetFont('Arial','',8);
		if ($bullnumele == "oui") { $pdf->WriteHTML("N°: $numero_eleve "); }
		$pdf->SetXY($Xv1,44);
		$pdf->WriteHTML("Né(e) le $datenaissance");
		$pdf->SetXY($Xv1,48); 
		if ($bullregime == "oui") { $pdf->WriteHTML("Regime: $regime "); }
		$pdf->SetXY($Xv1+80,48);
		if ($bullclassant == "oui") {
			$class_ant=trunchaine($class_ant,40);
			$pdf->WriteHTML("Classe ant.: $class_ant ");
		}

		/*
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($Xv11,36);
		$chaine=LANGBULL44." ".trim(strtoupper($nomtuteur))." ".trim(ucwords(strtolower($prenomtuteur)));
		$pdf->WriteHTML(trunchaine($chaine,30));
		$pdf->SetXY($Xv11,42);
		$chaine=trim($num_adr1)." ".trim($adr1);
		$pdf->WriteHTML(trunchaine($chaine,30));;
		$pdf->SetXY($Xv11,48);
		$chaine=trim($code_post_adr1)." ".trim($commune_adr1);
		$pdf->WriteHTML(trunchaine($chaine,30));
		*/
	}
	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(220);
	$pdf->SetXY(05,60); //  placement  cadre titre
	$pdf->MultiCell(194,11,'',1,'C',1);
	$pdf->SetXY(25,62); // placement contenu titre
	$pdf->WriteHTML($titrenote1);
	$pdf->SetX(67);
	$pdf->WriteHTML($titrenote2);
	if (($notaffmoyclass != "oui") && ($notaffminmax != "oui")) { 
		$pdf->SetX(90);
		$pdf->WriteHTML($titrenote3);
		$pdf->SetX(125);
	}else{
		$pdf->SetX(100);
	}
	$pdf->WriteHTML($titrenote4);
	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(55,66);
	$pdf->WriteHTML($soustitre5);
	if ($notaffmoyclass != "oui") { 
		$pdf->SetX(82);
		$pdf->WriteHTML($soustitre6);
	}
	if ($notaffminmax != "oui") {
		$pdf->SetX(92);
		$pdf->WriteHTML($soustitre7);
		$pdf->SetX(102);
		$pdf->WriteHTML($soustitre8);
	}
	// fin des sous-titres

	$nbs=0;


	// Mise en place des matieres et nom de prof
	$Xmat=05;
	$Ymat=71;
	$Xmatcont=06;
	$Ymatcont=71;

	$Xprof=45;
	$Yprof=$Ymat;
	$Xcoeff=55;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=46;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=$Xcoeff + 12;
	$YnotVal=$Ycoeff + 3;

	
	$XcoeffVal=$Xcoeff + 1;
	$YcoeffVal=$Ymat + 3;
	$XprofVal=10; // x en nom prof
	$YprofVal=$Ymat + 4; // y en nom du prof
	$XmoyMatGVal=$Xcoeff + 26 ;
	$YmoyMatGVal=$Ycoeff + 3 ;

	$XprofVal=5; // x en nom prof
	if ($hauteurMatiere < 8) $YprofVal=$YprofVal-1;


	$nbNoteMin=0;
	$nbNotemax=0;

	$noteMoyEleG=0;
	$coefEleG=0;
	$ii=0;
	$iiii=0;

	$etoile=0;


	for($i=0;$i<count($ordre);$i++) {
		$TT=0;
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

		if ($affichematierecoefzero != "oui") {
			$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
			if ($coeffaff == "0.00") { continue; } 
		}

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


		// mise en place des matieres
		$largeurMat=60;
		$nbmaxmatiere=25;
		if ($hauteurMatiere > 8) $nbmaxmatiere=$nbmaxMatierePageUne; 

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
		$iiii++;
		if (($iiii == $nbmaxmatiere) && ($dejafait == 0)) {
			$pdf->AddPage();
			$dejafait=1;
			$Xmat=05;
			$Ymat=05;
			$Xmatcont=06;
			$Ymatcont=05;
			$Xprof=45;
			$Yprof=$Ymat;
			$Xcoeff=55;
			$Ycoeff=$Ymat;
			$Xmoyeleve=$Xcoeff + 10;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve + 15;
			$Ymoyclasse=$Ymat;
			$XnomProfcont=46;
			$YnomProfcont=$Ymatcont;
			$Xnote=$Xmoyclasse + 32;
			$Ynote=$Ymat;
			$XnotVal=$Xcoeff + 12;
			$YnotVal=$Ycoeff + 3;
			$XcoeffVal=$Xcoeff + 1;
			$YcoeffVal=$Ymat + 3;
			$XprofVal=10; // x en nom prof
			$YprofVal=$Ymat + 4; // y en nom du prof
			$XmoyMatGVal=$Xcoeff + 26 ;
			$YmoyMatGVal=$Ycoeff + 3 ;
			$iiii=0;
		}

		$sousmatiere=trim($ordre[$i][4]);   
		$libelleMatiere=$ordre[$i][5]; 
		$ordrematiere=$ordre[$i][3]; 
		$langue=$ordre[$i][7]; 
		$ii=$i;
		$TT=1;
		while(true) { 
			$ii++;

			if (verifMatiereAvecGroupeSansEleve($ordre[$ii][0],$idClasse,$ordre[$ii][2])) {
				if (verifMatiereAvecGroupe($ordre[$ii][0],$idEleve,$idClasse,$ordre[$ii][2])) { 
					continue;
				}
			} 
			if (($sousmatiere != "0") && ($sousmatiere != "")){
				if(!verifMatiereSuivanteCommeSousmatiere($ordre[$ii][0])) { $TT=1;break; }
				$matiereSuivante=chercheMatiereNom3($ordre[$ii][0]);
		//		 print "TT:$TT $libelleMatiere -- $matiereSuivante <br>";
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

//		print_r($tabsous);
//print "<hr>";

		$sousmatiere=$ordre[$i][4];
		if (($sousmatiere == "0") && ($sousmatiere=="")) { $sousmatiere=""; }

		//
		$nbs--;
		if ($nbs < 0) { $nbs=0; } 
		if($nbs == 0) {
		$deja=0;
		foreach($tabsous as $key4=>$val4) {
			if ($key4 == $libelleMatiere) {
				$effbordure=0;
				$nbs=$val4;
				break;
			}
		}
//print "$libelleMatiere $nbs <br>";
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
			$H=$hauteurMatiere*$nbs;
			if ($Ymat+$H > 280) {
				$pdf->AddPage();
				$Xmat=05;
				$Ymat=05;
				$Xmatcont=06;
				$Ymatcont=05;
				$Xprof=45;
				$Yprof=$Ymat;
				$Xcoeff=55;
				$Ycoeff=$Ymat;
				$Xmoyeleve=$Xcoeff + 10;
				$Ymoyeleve=$Ymat;
				$Xmoyclasse=$Xmoyeleve + 15;
				$Ymoyclasse=$Ymat;
				$XnomProfcont=46;
				$YnomProfcont=$Ymatcont;
				$Xnote=$Xmoyclasse + 32;
				$Ynote=$Ymat;
				$XnotVal=$Xcoeff + 12;
				$YnotVal=$Ycoeff + 3;
				$XcoeffVal=$Xcoeff + 1;
				$YcoeffVal=$Ymat + 3;
				$XprofVal=10; // x en nom prof
				$YprofVal=$Ymat + 4; // y en nom du prof
				$XmoyMatGVal=$Xcoeff + 26 ;
				$YmoyMatGVal=$Ycoeff + 3 ;
				$iiii=0;
			}
			$pdf->SetXY($Xmat,$Ymat);
			$posiNoteSous=$nbs;	
			$pdf->MultiCell($largeurMat,$H,'',1,'L',0);
			$deja++;
			$effbordure2=0;	
		}

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',$effbordure2,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$libelleMatiere=preg_replace('/0$/','',$libelleMatiere);
		$libelleMatiereLong=chercheMatiereLong($idMatiere);			
		if (trim($libelleMatiereLong) != "") {
			$etoile++;
			$infoLibel="($etoile)";
			$infoLibelPiedPage.="($etoile) $libelleMatiereLong / ";
		}else{
			$infoLibel="";
		}

		if ($infoMatiere != "oui") { $infoLibel=""; }

		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($libelleMatiere))),25).'</B> '.$infoLibel);

		if ($sousmatiere != "") {
			$pdf->SetXY($Xmat+($largeurMat/3)+3,$Ymat+2.5);
			$pdf->SetFont('Arial','',7);
			$sousmatiere=preg_replace('/0$/','',$sousmatiere);
			$pdf->WriteHTML('<I>'.trunchaine(strtolower($sousmatiere),20).'</I>');
		}
		$pdf->SetFont('Arial','',8);
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff,$Ycoeff);
		$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;
		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		
		if ($effbordure == 0) {
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(15,$H,'',1,'L',1);	
		}else{
			if($nbs == 0) {
				$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
			}
		}

		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
		if (($notaffmoyclass != "oui") && ($notaffminmax != "oui")) { 
			$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
		}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
			$pdf->MultiCell(11,$hauteurMatiere,'',1,'L',0);	
		}else{
			//$pdf->MultiCell(22,$hauteurMatiere,'',1,'L',0);
		}
	
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;

		if ($effbordure == 0 ){
			$H=$hauteurMatiere*$nbs;
			if (($notaffmoyclass != "oui")  && ($notaffminmax != "oui")) { 
				$pdf->SetXY($Xnote,$Ynote);
				$pdf->MultiCell(87,$H,'',1,'L',0);
			}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
				$pdf->SetXY($Xnote-21,$Ynote);
                                $pdf->MultiCell(87+21,$H,'',1,'L',0);
			}else{
				$pdf->SetXY($Xnote-32,$Ynote);
				$pdf->MultiCell(87+32,$H,'',1,'L',0);
			}
		}

		// mise en place du cadre commentaire
		if ($notaffmoyclass == "oui") { 
			$pdf->SetXY($Xnote-32,$Ynote);
			$pdf->MultiCell(87+32,$hauteurMatiere,'',$effbordure2,'',0);
		}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
			$pdf->SetXY($Xnote-21,$Ynote);
                        $pdf->MultiCell(87+21,$hauteurMatiere,'',$effbordure2,'',0);
		}else{
			$pdf->SetXY($Xnote,$Ynote);
			$pdf->MultiCell(87,$hauteurMatiere,'',$effbordure2,'',0);
		}	
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
		unset($noteaff);	
		if ($idgroupe == "0") {
			if ($avecexamenblanc == "oui") {
				$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
			}else{
				$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
			}
		}else{
			if ($avecexamenblanc == "oui") {
				$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			}else{
				$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			}
		}

		if ($posiNoteSous > 1){
			$tutu++;
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($XnotVal-2.5,$YnotVal);
			$noteaff1=$noteaff;
			if ($posiNoteSous != 1) { 
				if (($moyensurdix == "oui") && ($noteaff1 != "" )) { $noteaff1=$noteaff1/2; } 
				if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }	
				$pdf->WriteHTML($noteaff1); 
			}
			unset($noteaff1);
			$coefsous=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
			if (( $coefsous != "" ) && ($moyensousmatiere == "non")) {
				$coefEleGMat=$coefEleGMat + $coefsous;
			}
			if ($noteaff != "") {
				$notesous=$noteaff*$coefsous;
				$notesoustotal1=$notesoustotal1+$notesous;
				$coefsoustotal1=$coefsoustotal1+$coefsous;
			}
		
			$ip=$i+1;
		
			
		//	if (verifMatiereAvecGroupe($ordre[$ip][0],$idEleve,$idClasse,$ordre[$ip][2])) {
		//		$matiereSuivante="";
		//	}else{
				$matiereSuivante=chercheMatiereNom3($ordre[$ip][0]);
		//	}
			$matiereEnCours=$ordre[$i][5];
			if ( (trim($matiereEnCours) != trim($matiereSuivante)) || ($posiNoteSous == $tutu) ) {
				$tutu=0;
				$matierepre=$matiereEnCours;
				if ($notesoustotal1 != "") {
					$notesousmoyen=$notesoustotal1/$coefsoustotal1;
					$notesousmoyen=number_format($notesousmoyen,2,'.','');
					$noteaff1=$notesousmoyen;
					if ( ($moyensurdix == "oui") && ($noteaff1 != "")) { 
						$noteaff1=$noteaff1/2;
					}
				       	$noteaff1=number_format($noteaff1,2,'.','');
					if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
				}
				$pdf->SetFont('Arial','',12);
				if ($posiNoteSous == 5) {
					$ajus=$posiNoteSous*5.8;
				}elseif($posiNoteSous == 6){
					$ajus=$posiNoteSous*4;  //OK
				}elseif($posiNoteSous == 7){
					$ajus=$posiNoteSous*4;  //OK
				}elseif($posiNoteSous == 4){
					$ajus=$posiNoteSous*3;  //OK
				}elseif($posiNoteSous == 3){
					$ajus=$posiNoteSous*4;
				}elseif($posiNoteSous == 2){
					$ajus=$posiNoteSous*2;  //OK
				}elseif($posiNoteSous == 1){
					$ajus=$posiNoteSous;  //OK
				}elseif($posiNoteSous == 0){
					$ajus=$posiNoteSous;  //OK
				}elseif($posiNoteSous == ""){
					$ajus=$posiNoteSous;  //OK
				}else{
					$ajus=$posiNoteSous*2;
				}
				
				$pdf->SetXY($XnotVal,$YnotVal-$ajus);
				$pdf->WriteHTML($noteaff1);
				//$posiNoteSous=1;

				if (( $noteaff1 != "" ) && ($moyensousmatiere == "non")) {
					if ($coeffaff > 0) {
						$coefEleGMat=$coefEleGMat + $coeffaff;
						$noteMoyEleGTempo = $noteaff1 * $coefEleGMat;
						$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
						$coefEleG=$coefEleG + $coefEleGMat;
					}else{
						$noteaff1 = $noteaff1 - 10;
						if ($noteaff1 > 0) { $noteMoyEleG=$noteMoyEleG + $noteaff1 ; }
					}
				}
				unset($posiNoteSous);
				unset($coefEleGMat);
				unset($noteMoyEleGTempo);
				unset($noteaff1);
				unset($notesoustotal1);
				unset($coefsoustotal1);
			}
			$YnotVal=$YnotVal + $hauteurMatiere;

		}else{
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY($XnotVal,$YnotVal);
			$noteaff1=$noteaff;
			if (($moyensurdix == "oui") && ($noteaff1 != "")) { 
				$noteaff1=$noteaff1/2;
			       	$noteaff1=number_format($noteaff1,2,'.','');
			} 
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			$pdf->WriteHTML($noteaff1);
			unset($noteaff1);
			$YnotVal=$YnotVal + $hauteurMatiere;
			unset($matiereSuivante);
			$ajus=0;
			if (( $noteaff != "" ) && ($moyensousmatiere == "non")) {
				$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
				if ($coeffaff > 0) {	
			       	 	$noteMoyEleGTempo = $noteaff * $coeffaff;
		               	 	$noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                			$coefEleG=$coefEleG + $coeffaff;
				}else{
					$noteaff = $noteaff - 10;
					if ($noteaff > 0) { $noteMoyEleG=$noteMoyEleG + $noteaff ; }
				}
			}

		}

		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XcoeffVal,$YcoeffVal);
		$pdf->WriteHTML($coeffaff);
		$YcoeffVal=$YcoeffVal + $hauteurMatiere;

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
			// idMatiere,datedebut,dateFin,idclasse
			if ($avecexamenblanc == "oui") {
	           		$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
			}else{
				$moyeMatGen=moyeMatGenSansExam($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
			}
		}else {
			if ($avecexamenblanc == "oui") {
	           		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			}else{
				$moyeMatGen=moyeMatGenGroupeSansExam($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			}
    		}
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
		$moyeMatGenaff=$moyeMatGen;
		if ($notaffmoyclass != "oui") { 
			if (($moyensurdix == "oui") && ($moyeMatGenaff != "")) { 
				$moyeMatGenaff=$moyeMatGenaff/2;
			}
			$moyeMatGenaff=number_format($moyeMatGenaff,2,'.','');
			if (($moyeMatGenaff < 10) && ($moyeMatGenaff!="")) { $moyeMatGenaff="0".$moyeMatGenaff; }
			$pdf->WriteHTML($moyeMatGenaff);
		}

		// calcul du min et du max
		if ($idgroupe == "0") {   // non matiere affectée à un groupe
			$max="";
			$min=1000;
			for($g=0;$g<count($eleveT);$g++) {
				// variable eleve
				$idEleveMoyen=$eleveT[$g][4];
				if ($avecexamenblanc == "oui") {
					$valeur=moyenneEleveMatiere($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
				}else{
					$valeur=moyenneEleveMatiereSansExam($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof);
				}
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
				if ($avecexamenblanc == "oui") {
					$valeur=moyenneEleveMatiereGroupe($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}else{
					$valeur=moyenneEleveMatiereGroupeSansExam($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}
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


	// mise en place du min
	if ($notaffminmax != "oui") {
		$XmoyMatGenMinVal=$XmoyMatGVal + 11;
		$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
		$moyeMatGenMinaff=$moyeMatGenMin;
		if ($notaffmoyclass != "oui") { 
			if (($moyensurdix == "oui") && ($moyeMatGenMinaff != "")) { 
					$moyeMatGenMinaff=$moyeMatGenMinaff/2;
			} 
			$moyeMatGenMinaff=number_format($moyeMatGenMinaff,2,'.','');
			if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff!="")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
			$pdf->WriteHTML($moyeMatGenMinaff);
		}

		// mise en place du max
		$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
		$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
		$moyeMatGenMaxaff=$moyeMatGenMax;
		if ($notaffmoyclass != "oui") {
	  		if (($moyensurdix == "oui") && ($moyeMatGenMaxaff != "")) { 
				$moyeMatGenMaxaff=$moyeMatGenMaxaff/2;
			} 	
			$moyeMatGenMaxaff=number_format($moyeMatGenMaxaff,2,'.','');
			if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff!="")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
			$pdf->WriteHTML($moyeMatGenMaxaff);
		}
	}
	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPoliceViaHauteur($commentaireeleve,$hauteurMatiere);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy

	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);

	if (($notaffmoyclass == "oui") && ($notaffmoyminmax == "oui")) { 
		$pdf->SetXY($Xcom-32,$Ycom+1);
		$pdf->MultiCell(87+32,$confPolice[1],"$commentaireeleve",'','','L',0);
	}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
		$pdf->SetXY($Xcom-129,$Ycom+1);
                $pdf->MultiCell(87+21,$confPolice[1],$commentaireeleve,'','','L',0);
	}elseif (($notaffmoyclass != "oui") && ($notaffminmax != "oui")) {
		$pdf->SetXY($Xcom,$Ycom+1);
                $pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	}else{
		$pdf->SetXY($Xcom+70,$Ycom+1);
		$pdf->MultiCell(87+32,$confPolice[1],"$commentaireeleve",'','','L',0);
	}
	//$pdf->WriteHTML($commentaireeleve);
	
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	if ($afficheRemplacant == "oui") { $profAff=recupIdSuppleant($profAff); }
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal,$YprofVal);
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if (($noteaff != "" ) && ($moyensousmatiere == "oui")) {
		if ($coeffaff > 0) {
		       $noteMoyEleGTempo = $noteaff * $coeffaff;
		       $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
		       $coefEleG=$coefEleG + $coeffaff;	
		}else{
			$noteaff = $noteaff - 10;
			if ($noteaff > 0) { $noteMoyEleG=$noteMoyEleG + $noteaff ; }
		}
	}
}


// fin de la mise en place des matiere

// Note Vie Scolaire
if (($affnoteviescolaire == "oui") && (MODNAMUR0 == "oui")) {

	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	$pdf->WriteHTML('<B>'.'Vie Scolaire'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place de la colonne coeff
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff,$Ycoeff);
	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	$Ycoeff=$Ycoeff + $hauteurMatiere;
	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
	$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
	$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
	// mise en place moyenne classe
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
	if (($notaffmoyclass != "oui") && ($notaffminmax != "oui")) { 
		$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
	}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
		$pdf->MultiCell(12,$hauteurMatiere,'',1,'L',0);
	}elseif (($notaffmoyclass == "oui") && ($notaffminmax != "oui")) {
		$pdf->MultiCell(21,$hauteurMatiere,'',1,'L',0);
	}
	$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;
	// mise en place du cadre commentaire
	if (($notaffmoyclass == "oui")  && ($notaffminmax != "oui")) { 
		$pdf->SetXY($Xnote-32,$Ynote);
		$pdf->MultiCell(87+32,$hauteurMatiere,'',1,'',0);
	}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
		$pdf->SetXY($Xnote-32,$Ynote);
                $pdf->MultiCell(87+12,$hauteurMatiere,'',1,'',0);
	}elseif (($notaffmoyclass == "oui") && ($notaffminmax != "oui")) {
		$pdf->SetXY($Xnote-32,$Ynote);
                $pdf->MultiCell(87+22,$hauteurMatiere,'',1,'',0);
	}else{
		$pdf->SetXY($Xnote,$Ynote);
		$pdf->MultiCell(87,$hauteurMatiere,'',1,'',0);
	}
	$Ynote=$Ynote + $hauteurMatiere;

	// mise en place des notes
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XnotVal,$YnotVal);
	$noteaff1=$noteaff;
	if (($moyensurdix == "oui") && ($noteaff1 != "")) { 
		$noteaff1=$noteaff1/2;
	} 	
	$noteaff1=number_format($noteaff1,2,'.','');
	$pdf->WriteHTML($noteaff1);


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
	$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
	$moyeMatGenaff=$moyeMatGen1;
	if ($notaffmoyclass != "oui") { 
		if (($moyensurdix == "oui") && ($moyeMatGenaff != "")) { 
			$moyeMatGenaff=$moyeMatGenaff/2;
		} 
	       	$moyeMatGenaff=number_format($moyeMatGenaff,2,'.','');
		$pdf->WriteHTML($moyeMatGenaff);
	}

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

	if ($notaffminmax != "oui") { 
		// mise en place du min
		$XmoyMatGenMinVal=$XmoyMatGVal + 11;
		$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
		$moyeMatGenMinaff=$moyeMatGenMin;
		if ($notaffmoyclass != "oui") { 
			if (($moyensurdix == "oui") && ($moyeMatGenMinaff != "")) { 
				$moyeMatGenMinaff=$moyeMatGenMinaff/2;
			} 
			$moyeMatGenMinaff=number_format($moyeMatGenMinaff,2,'.','');
			$pdf->WriteHTML($moyeMatGenMinaff);
		}
	
		// mise en place du max
		$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
		$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
		$moyeMatGenMaxaff=$moyeMatGenMax;
		if ($notaffmoyclass != "oui") { 
			if (($moyensurdix == "oui") && ($moyeMatGenMaxaff != "")) { 
				$moyeMatGenMaxaff=$moyeMatGenMaxaff/2;
			}
			$moyeMatGenMaxaff=number_format($moyeMatGenMaxaff,2,'.','');
			$pdf->WriteHTML($moyeMatGenMaxaff);
		}
	}
	
	$Ycom=$YmoyMatGVal - 3;
	
	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPoliceViaHauteur($commentaireeleve,$hauteurMatiere);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	if ($notaffmoyclass == "oui") { 
		$pdf->SetXY($Xcom-32,$Ycom);
		$pdf->MultiCell(87+32,$confPolice[1],$commentaireeleve,'','','L',0);
	}else{
		$pdf->SetXY($Xcom,$Ycom);
		$pdf->MultiCell(87,$confPolice[1],"$commentaireeleve",'','','L',0);
	}
	
	// mise en place du nom du prof
	$profAff=$persVieScolaire;
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal,$YprofVal);
	$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if ( $noteaff != "" ) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}
	unset($posiNoteSous);
}

// fin notes
// --------

	// cadre moyenne generale
	if (count($ordre) >= 18) {
		$YmoyenneGeneral=$Ymoyclasse + 2;
	}else{
		$YmoyenneGeneral=$Ymoyclasse + 5;
	}
	if ($YmoyenneGeneral >= 245) {
		$pdf->AddPage();
		$YmoyenneGeneral=20;
	}


	$LargeurMG=$largeurMat;
	$YmoyenneGeneralT=$YmoyenneGeneral + 2;
	$XMoyGE=$LargeurMG;
	$YMoyGE=$YmoyenneGeneral - 10;

if ($affichemoyengeneral == "oui") {

	$YMoyGE=$YmoyenneGeneral;
	$XMoyCL=$XMoyGE + 15;

	$XmoyClasseGValue=$XMoyGE + 10 + 10;
	$YmoyClasseGValue=$YmoyenneGeneralT;
	$XmoyClasseMinValue=$XmoyClasseGValue + 10;
	$YmoyClasseMinValue=$YmoyenneGeneralT;
	$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
	$YmoyClasseMaxValue=$YmoyenneGeneralT;


	$pdf->SetFont('Arial','',9);
	$pdf->SetXY(5,$YmoyenneGeneral);
	$pdf->MultiCell($LargeurMG,10,'',1,'L',0);
	$pdf->SetXY(14,$YmoyenneGeneralT);
	$pdf->WriteHTML("<B>MOYENNE GENERALE</B>");
	$pdf->SetXY(5+$LargeurMG,$YMoyGE);
	$pdf->SetFillColor(220);
	$pdf->MultiCell(15,10,'',1,'L',1);
	$pdf->SetXY(5+$LargeurMG+15,$YMoyGE);
	if (($notaffmoyclass != "oui") && ($notaffminmax != "oui")) { 
		$pdf->MultiCell(32,10,'',1,'L',0);
	}elseif (($notaffmoyclass != "oui") && ($notaffminmax == "oui")) {
		$pdf->MultiCell(12,10,'',1,'L',0);
	}


// fin du cadre moyenne generale


	if ((file_exists("./data/image_pers/logo_signature.jpg")) && ($_POST["ajsignature"] == "oui")){
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY(120,$YmoyenneGeneralT+6.5);
		$pdf->WriteHTML("[ <I>Signature du directeur</I> ]");
		$taille = getimagesize("./data/image_pers/logo_signature.jpg");
		$logox=$taille[0]/25;
		$logoy=$taille[1]/25;
		$pdf->Image("./data/image_pers/logo_signature.jpg","150",$YmoyenneGeneralT-6,$logox,$logoy);
	}

	// affichage de la moyenne generale eleve
	$XmoyElValue=$LargeurMG + 7;
	$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
	$moyenEleve=moyGenEleveab($noteMoyEleG,$coefEleG);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XmoyElValue,$YmoyElGenValue);

	$moyenEleveaff=$moyenEleve;
	if (($moyensurdix == "oui") && ($moyenEleveaff != "")) { 
		$moyenEleveaff=$moyenEleveaff/2;
	}
	if ($moyenEleve < 10) { $moyenEleveaff="0".$moyenEleveaff; }
	$moyenEleveaff=number_format($moyenEleveaff,2,'.','');
	$pdf->WriteHTML("<B>".$moyenEleveaff."</B>");
	$noteMoyEleG=0; // pour la moyenne de l'eleve general
	$coefEleG=0; // pour la moyenne de l'eleve general
	// fin affichage moy eleve

	//affichage  du min et du max et moyenne general
	if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
	if ($moyenClasseGen == 0) {$moyenClasseGen="";}
	$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY($XmoyClasseGValue,$YmoyClasseGValue);
	
	$moyenClasseGenaff=$moyenClasseGen;
	if ($notaffmoyclass != "oui") {
		if ($moyenClasseGenaff < 10) { $moyenClasseGenaff="0".$moyenClasseGenaff; }
		$pdf->WriteHTML($moyenClasseGenaff);
	}
	
	$moyenClasseMinaff=$moyenClasseMin;
	$pdf->SetXY($XmoyClasseMinValue,$YmoyClasseMinValue);
	if ($notaffminmax != "oui") {
		if ($moyenClasseMinaff < 10) { $moyenClasseMinaff="0".$moyenClasseMinaff; }
		$pdf->WriteHTML($moyenClasseMinaff);
	}

	$moyenClasseMaxaff=$moyenClasseMax;
	$pdf->SetXY($XmoyClasseMaxValue,$YmoyClasseMaxValue);
	if ($notaffminmax != "oui") { 
		if ($moyenClasseMaxaff < 10) { $moyenClasseMaxaff="0".$moyenClasseMaxaff; }
		$pdf->WriteHTML($moyenClasseMaxaff);
	}
	// fin de la calcul de min et max
}


// fin affichage


// cadre appréciation
$Ycom=$YMoyGE + 10;

$pdf->SetFont('Arial','',8);
$pdf->SetFillColor(220);
$pdf->SetXY(5,$Ycom);
$pdf->WriteHTML($appreciationbis);


$Ycom+=10;
$EpaisCom=30;
$YcomP1=$Ycom + 1;
$YcomP2=$YcomP1 + 10;
$YcomP3=$YcomP2 + 5;

$montessori=recherchemontessori($idEleve,"cheneraie",$_POST["saisie_trimestre"],$anneeScolaire);
$montessori=$montessori[0][0];
if ($montessori == "felicitation")  { $okE1="1"; }
if ($montessori == "compliment")    { $okE2="1"; }
if ($montessori == "encouragement") { $okE3="1"; }
if ($montessori == "averttravail")  { $okE4="1"; }


$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(0);
$pdf->SetXY(65,$Ycom);
$pdf->MultiCell(134,$EpaisCom,'',1,'C',0);
$pdf->SetXY(66,$YcomP1);
$pdf->WriteHTML("<u>$appreciation</u>");
$pdf->SetFont('Arial','',8);

$pdf->SetFont('Arial','',12);
$pdf->SetXY(5,$Ycom);
$pdf->MultiCell(3,3,'',1,'C',$okE1);
$pdf->SetXY(8,$Ycom);
$pdf->MultiCell(80,3,"Félicitations",0,'L',0);

$pdf->SetXY(5,$Ycom+=6);
$pdf->MultiCell(3,3,'',1,'C',$okE2);
$pdf->SetXY(8,$Ycom);
$pdf->MultiCell(80,3,"Compliments",0,'L',0);

$pdf->SetXY(5,$Ycom+=6);
$pdf->MultiCell(3,3,'',1,'C',$okE3);
$pdf->SetXY(8,$Ycom);
$pdf->MultiCell(80,3,"Encouragements",0,'L',0);

$pdf->SetXY(5,$Ycom+=6);
$pdf->MultiCell(3,3,'',1,'C',$okE4);
$pdf->SetXY(8,$Ycom);
$pdf->MultiCell(80,5,"Avertissement de travail",0,'L',0);

$okE1=$okE2=$okE3=$okE4=0;
$pdf->SetFillColor(220);

// commentaire direction
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"cheneraie");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(67,$YcomP1+5);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(130,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)

$pdf->SetFont('Arial','',10);
if ($bullprofp == "oui") {
//	$pdf->SetXY(66,$YcomP2);
//	$pdf->WriteHTML($barre);
	$pdf->SetXY(66,$YcomP3);
	$pdf->WriteHTML("<u>$appreciation2</u>");
	$pdf->SetXY(66+74,$YcomP3);
	$pdf->SetFont('Arial','',8);
	$pdf->WriteHTML(" ( Professeur Principal : ". $profp ." )" );
	$pdf->SetFont('Arial','',9);

	// commentaire prof principal
	$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
	$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
	$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
	$pdf->SetXY(7,$YcomP1+20);
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->MultiCell(130,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)
}

//duplicata et signature
$YduplicaSign=$YcomP1 + $EpaisCom;
$pdf->SetFont('Arial','',5);
$pdf->SetXY(67,$YduplicaSign);
//$pdf->WriteHTML("<I>".$duplicata."</I>");

if ($infoMatiere == "oui") {
	$pdf->SetXY(67,$YduplicaSign+4);
//	$pdf->MultiCell(130,$confPolice[1],$infoLibelPiedPage,'','','L',0);
}

$pdf->SetFont('Arial','',8);
$pdf->SetXY(130,$YduplicaSign);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',5);
$pdf->SetXY(6,$YduplicaSign+3);
$pdf->WriteHTML($signature2);

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
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
