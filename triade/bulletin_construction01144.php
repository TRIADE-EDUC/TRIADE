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
	set_time_limit(0);
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
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$anneeScolaire=$_POST["annee_scolaire"];
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
$valeur=visu_affectation_detail($_POST["saisie_classe"],$anneeScolaire);
$affmoyclasse=$_POST["affmoyclasse"];
$affmoyclasse="oui";

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
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      Bulletin du : <?php print ucwords($textTrimestre)?><br> <br>
      Section :  <?php print $classe_nom?><br> <br>
      Année Scolaire : <?php print $anneeScolaire?></br></br>
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

// recuperation des coordonnées
// de l'etablissement
$data=visu_param();
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
}
// fin de la recup


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


$hauteurmatiereP=$_POST["hauteurmatiere"];
config_param_ajout($hauteurmatiereP,"hauteurMatBull209");
$policecaractere=$_POST["policecaractere"];
config_param_ajout($policecaractere,"policecaractere");
$photobulleleve=$_POST["photobulleleve"];
config_param_ajout($photobulleleve,"photobulleleve");



//INSERTION RANG AB LE 310309


//

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
			//$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]);
			$coeffaff=1;
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
			$classementG[]=$moyenEleve2; //ab 310309
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

if (($moyenClasseMax < 10) && ($moyenClasseMax!="")) { $moyenClasseMax="0".$moyenClasseMax; }
if (($moyenClasseMin < 10) && ($moyenClasseMin!="")) { $moyenClasseMin="0".$moyenClasseMin; }
if (($moyenClasseGen < 10) && ($moyenClasseGen!="")) { $moyenClasseGen="0".$moyenClasseGen; }

for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=stripslashes(ucwords(TextNoAccent($eleveT[$j][0])));
	$prenomEleve=stripslashes(ucfirst(TextNoAccent($eleveT[$j][1])));
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];
	$date_naissance=$eleveT[$j][5];
	$lieu_naissance=$eleveT[$j][6];
	


// recherche le nombre de sanction

$nbsanctions=nombre_Sanc($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
$nbsanctions=count($nbsanctions);
//

$nbexclusions=nombre_Exclu($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
$nbexclusions=count($nbexclusions);


//---------------------------------//



$pdf->AddPage();
$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
$pdf->SetCreator("AGR");
$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
$pdf->SetAuthor("AGR - http://www.triade-educ.com"); 


// declaration variable
$coordonne0=strtoupper($nom_etablissement);
$coordonne1=$adresse;
$coordonne2=$postal." - ".ucwords($ville);
$coordonne3="Téléphone : ".$tel;
$coordonne4="";


$titre="Bulletin scolaire du ".ucwords($textTrimestre)."";

$nomEleve=strtoupper(trim($nomEleve));
$prenomEleve=trim($prenomEleve);
$nomprenom=trunchaine("<b>$nomEleve $prenomEleve</b>",40);
$abnais=explode('-',$date_naissance);
$abdtnjj=$abnais[2];
$abdtnmm=$abnais[1];
$abdtnaa=$abnais[0];
$datenaissance=$abdtnjj."/".$abdtnmm."/".$abdtnaa;
$lieu_naissance=trim($lieu_naissance);

$infoeleve="$nomprenom    "; // Date Naissance: <b>$datenaissance</b>";
$infoeleve2="Classe : ";
$infoeleveclasse=stripslashes($classe_nom);

$titrenote1="Disciplines";
$titrenote2="";
$titrenote3="";
$titrenote4="";
$soustitre4b="Sujets";
$soustitre5="";
$soustitre6="Notes";
//AB
$soustitre61="";
$soustitre62="Coef.";
//
$soustitre7="Moyenne";
$soustitre8="Moyenne";
$soustitre9="";


$idprofp=rechercheprofp($_POST["saisie_classe"]);
$profp=recherche_personne($idprofp);

///FIN MODIFICATION
$appreciation="Bilan des absences et retards : ";
$appreciation2=stripslashes("<br>Observations et appréciations de l'équipe pédagogique : ");

$duplicata=LANGBULL41;
$signature="La Directrice : $directeur";
// FIN variables

$xtitre=150;  // sans logo
$xcoor0=3;  // sans logo
$ycoor0=3;   // sans logo

$xlogo=0;
$ylogo=0;
$logowidth=55;
if (file_exists("./image/banniere/banniere-agr.jpg")) {
	$logo="./image/banniere/logoagr.jpg";
	$pdf->Image($logo,$xlogo,$ylogo,$logowidth);
}
 
//FIN
// Debut création PDF
// mise en place des coordonnées
/*
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0+2,$ycoor0+11);
$pdf->WriteHTML(stripslashes("Diplôme Certifié Niveau II"));
$pdf->SetFont('Arial','',8);
$pdf->SetXY($xcoor0+2,$ycoor0+15);
$pdf->WriteHTML($coordonne1);
$pdf->SetXY($xcoor0+2,$ycoor0+19);
$pdf->WriteHTML($coordonne2);
$pdf->SetXY($xcoor0+2,$ycoor0+23);
$pdf->WriteHTML($coordonne3);
$pdf->SetXY($xcoor0+2,$ycoor0+27);
$pdf->WriteHTML($coordonne4);
*/
//fin coordonnees

//AJOUT AB 030408
$pdf->SetFont('Arial','B',7);
$pdf->SetXY($xlogo-10,$ylogo-3);
$pdf->WriteHTML($devise1);
$pdf->SetXY($xlogo-10,$ylogo+20);
$pdf->WriteHTML($devise2);

$margeEtiquette=5;
// Cadre pour l'adresse
$Xv11=121;
$pdf->SetFillColor(255);
$pdf->RoundedRect($Xv11+5, 17+$margeEtiquette, 80, 24, 3.5, 'DF');

        $dataadresse=chercheadresse($idEleve);
// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance
        for($ik=0;$ik<=count($dataadresse);$ik++) {
                $nomtuteur=stripslashes($dataadresse[$ik][1]);
                $prenomtuteur=$dataadresse[$ik][2];
		$civtuteur=$dataadresse[$ik][13];
                $adr1=strtolower(stripslashes(TextNoAccent($dataadresse[$ik][3])));
                $code_post_adr1=$dataadresse[$ik][4];
                $commune_adr1=stripslashes(strtolower($dataadresse[$ik][5]));
                $numero_eleve=$dataadresse[$ik][9];
                $datenaissance=$dataadresse[$ik][11];
                if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
                if ($datenaissance == "00/00/0000") { $datenaissance=""; }
                $regime=$dataadresse[$ik][12];
                $class_ant=trim(trunchaine($dataadresse[$ik][10],20));
                $pdf->SetFont('Arial','',9);
                $pdf->SetXY($Xv11,36);

		if ($nomtuteur != "") {
	                $chaine=civ($civtuteur)." ".ucfirst($nomtuteur)." ".ucfirst(strtolower($prenomtuteur));
		}
                $pdf->SetXY($Xv11+6,21+$margeEtiquette);
		$pdf->MultiCell(90,3,"$chaine",0,'L',0);
                $pdf->SetXY($Xv11+6,26+$margeEtiquette);
                $chaine=trim($num_adr1)." ".trim(strtolower($adr1));
                $chaine=trunchaine($chaine,50);
                $pdf->WriteHTML($chaine);
                $pdf->SetXY($Xv11+6,32+$margeEtiquette);
                $chaine=trunchaine($chaine,50);
                $chaine=trim($code_post_adr1)." ".ucwords(trim(strtolower($commune_adr1)));
                $pdf->WriteHTML($chaine);
		break;
        }



// cadre du haut
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,230,255);
//$pdf->SetFillColor(220);

$Y=17+$margeEtiquette;
$X=60;
$pdf->SetXY($X,$Y); // placement du cadre du Annee de l eleve
//$pdf->MultiCell(148,8,'',1,'L',1);
$pdf->RoundedRect($X,$Y, 60, 24, 3.5, 'DF');

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY($X-5+8,$Y+3); // placement du nom de l'eleve
$pdf->WriteHTML(strtoupper($infoeleve));
$pdf->SetXY($X-5+8,$Y+11); // placement du prenom de l'eleve
$pdf->WriteHTML($infoeleve2);
$pdf->SetXY($X+17,$Y+11); 
$pdf->WriteHTML("$infoeleveclasse");





if ($photobulleleve == "oui") {
	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;
	$xphoto=195;
	$yphoto=13;
	$photowidth=10.8;
	$photoheight=16.3;
	if (!empty($photo)) {
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
	}
}
												
// Titre
$periode=$titre;
$pdf->SetFont('Arial','B',17);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(70,3);
$pdf->MultiCell(130,8,"$periode",0,'C',0);
$pdf->SetFont('Arial','',14);
$Pdate=stripslashes("Année Scolaire ".$anneeScolaire);
$pdf->SetXY(70,10);
$pdf->MultiCell(130,8,"$Pdate",0,'C',0);
// fin titre


$Y=30+$margeEtiquette;
// cadre des notes
// ---------------
// Barre des titres
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(230,230,255);
$X=30;
$pdf->SetXY($X,$Y+15); //  placement  cadre titre
$pdf->MultiCell(148,7,'',1,'C',1);
$pdf->SetXY($X+=2,$Y+16); // placement contenu titre
$pdf->WriteHTML($titrenote1);
if ($affmoyclasse == "oui") {
	$pdf->SetX($X+=54);
	$pdf->WriteHTML("Sujets et notes des devoirs");
	$pdf->SetX($X+=54);
	$pdf->WriteHTML(stripslashes("Moy. Elève"));
	$pdf->SetXY($X+=18,$Y+17);
	$pdf->MultiCell(20,3,"Moy. Classe",0,'C',0);
//	$pdf->SetXY(153,$Y+15);
//	$pdf->MultiCell(55,7,'',1,'C',1);
//	$pdf->SetXY(153,$Y+17);
//	$pdf->MultiCell(55,3,"Commentaires",0,'C',0);
}else{
        $pdf->SetX($X+=113);
        $pdf->WriteHTML("Sujets et notes des devoirs");
        $pdf->SetXY($X+=67.5,$Y+17);
        $pdf->MultiCell(20,3,"Moy. Elève",0,'C',0);
}



// fin des titres


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

		//-------------------------------------------------------------------------------------------------------------------------------//
		// recherche le nombre de retard
		$nbretardJ=0;
		$nbretardNonJ=0;
		$nbretard1=0;
		$nbretard2=0;//
		$nbabs=0;
		$nbabsnj=0;
		$nbsanctions=0;
		$nbheureabs="0";
		//$nbretardJ=nombre_retardJustifie($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
		//$nbretardNonJ=nombre_retardNonJustifie($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
		//recherche le nombre d'absence
		$tabnbabs=nombre_absJustifie2($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); 
		// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier
		$nbheureabs=0;
		$nbabstotal=0;
		for($o=0;$o<=count($tabnbabs);$o++) {
			if ($tabnbabs[$o][4] == "-1") { 
				$nbheureabs+=$tabnbabs[$o][7]; 
				$nbabstotal+=0.5;
			}else{
				$nbabstotal+=$tabnbabs[$o][4];
			}
		}
		if ($nbabstotal != 0) {	$nbabs=$nbabstotal*2; }
		if (trim($nbabs) == "") { $nbabs=0; }
		$tabnbabs=nombre_absNonJustifie2($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); 
		// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure,justifier
		$nbheureabsNJ=0;
		$nbabstotalNJ=0;
		for($o=0;$o<=count($tabnbabs);$o++) {
			if ($tabnbabs[$o][4] == "-1") { 
				$nbheureabsNJ+=$tabnbabs[$o][7]; 
				$nbabstotalNJ+=0.5;
			}else{
				$nbabstotalNJ+=$tabnbabs[$o][4];
			}
		}
		if ($nbabstotalNJ != 0)   { $nbabsNJ=$nbabstotalNJ*2; }
		if (trim($nbabsNJ) == "") { $nbabsNJ=0; }
		//-------------------------------------------------------------------------------------------------------------------------------//

		$nbretard=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
		$nbretard=count($nbretard);
	}
	//$appreciationretard="ASSIDUITE : $nbretard retard(s) / $nbabs demi-journée d'absence(s) / $nbheureabs heure(s) d'absence(s) " ;
	$appreciationretard=stripslashes("ASSIDUITE : $nbretard retard(s) / $nbabsNJ demi-journée d'absence(s) non justifiée(s) / $nbabs demi-journée d'absence(s) justifié(s) " );
	unset($nbabsNJ);
	unset($nbabs);
	unset($nbretard);
	//---------------------------------//


// Mise en place des matieres et nom de prof
$Xmat=30;//15
$Ymat=$Y+21.5;
$Xmatcont=31;//16
$Ymatcont=$Y+22;

$hauteurmatiereP=22;
$largeurMat=55;
if ($affmoyclasse == "oui") {
	$largeursujet=53;
}else{
	$largeursujet=73;
}
$ii=0;

//
//J'enlève du décompte la dernière matière (ORDRE D'AFFICHAGE) qui doit obligatoirement être les commentaires généraux avec ma variable $ledecompte
//$ledecompte=count($ordre) - 1;
$ledecompte=count($ordre);
	for($i=0;$i<$ledecompte;$i++) {
	$matiere=stripslashes(chercheMatiereNom($ordre[$i][0]));
	$idMatiere=$ordre[$i][0];
	$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
//	$nomprof=recherche_personne($ordre[$i][1]);
	$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$ii][2]); //AB AJOUT COEFF
	if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere

        // recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
        $idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);

	// mise en place des matieres
	$hauteurMatiere=$hauteurmatiereP; // taille du cadre matiere
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


	// cadre discipline
	$pdf->SetTextColor(000);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetXY($Xmat,$Ymat+0.5);
	$pdf->MultiCell($largeurMat,$hauteurMatiere-2,'',1,'L',0);
	$pdf->SetXY($Xmatcont-1.5,$Ymatcont+2);
	$pdf->SetFont('Arial','B',8);
	$pdf->MultiCell($largeurMat-2,3,"$matiere ($coeffaff)",0,'L',0);


	// cadre sujet
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($Xmat+$largeurMat,$Ymat+0.5);
	$pdf->MultiCell($largeursujet,$hauteurMatiere-2,'',1,'L',0);

	$pdf->SetFont('Arial','',7);
	$note=recupNote($idEleve,$ordre[$i][0],$dateDebut,$dateFin);
	// note,elev_id,code_mat,date,sujet,typenote,notationsur
	$Ynote=$Ymat+1;
	for($b=0;$b<count($note);$b++) {
		$noteaff=$note[$b][0];
		$sujet=stripslashes($note[$b][4]);
		$typenote=$note[$b][5];
		$notationSur=$note[$b][6];
		if ($ordre[$i][0] == $note[$b][2]) {
			if ($note[$b][0] == -1.00) { $noteaff="abs"; }
			if ($note[$b][0] == -2.00) { $noteaff="disp"; }
			if ($note[$b][0] == -3.00) { $noteaff="";$sujet=""; }
			if ($note[$b][0] == -4.00) { $noteaff="DNN"; }
			if ($note[$b][0] == -5.00) { $noteaff="DNR"; }
			if ($note[$b][0] == -5.00) { $noteaff="VAL"; }

			if (trim($typenote) == "en") {
				$noteaff=recherche_note_en($note[$b][0]);
			}else{
			//	$noteaff=preg_replace("/.00/","",$noteaff);
			//	$noteaff=preg_replace("/.50/",".5",$noteaff);
				if ($notationSur == "") { $notationSur=20; }
				if (($noteaff != "") && ($noteaff != "abs") &&($noteaff != "disp") &&($noteaff != "DNN")&& ($noteaff != "DNR")&& ($noteaff != "VAL") ){  
					$notationSur="$notationSur"; 
				}else{
					$notationSur="";
				}
			
			}
			
			if (count($note) <= 6) {
				if ($sujet != "") {
					$pdf->SetXY($Xmat+$largeurMat,$Ynote);
					$pdf->MultiCell($largeursujet,3,"- $sujet",0,'L',0);
					$pdf->SetXY($Xmat+$largeurMat+40,$Ynote);
					$pdf->MultiCell($largeursujet,3," : $noteaff",0,'L',0);
					$Ynote+=3;
				}
			}else{
				if (trim($sujet) != "") { 	
					$liste.=strtolower($sujet)." ($noteaff) - ";
				}
			}
		}
	}
	$pdf->SetXY($Xmat+$largeurMat,$Ynote);
	if ($affmoyclasse == "oui") {
		$pdf->MultiCell($largeursujet,3,"$liste",0,'L',0);
	}else{
		$pdf->MultiCell($largeursujet,3,"$liste",0,'L',0);
	}

	$liste="";
	
	
	// moyenne matiere
	$pdf->SetTextColor(000);
	$pdf->SetFillColor(255,255,255);

	if ($idgroupe == "0") {
		$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	}else{
		$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
	}

	if ($affmoyclasse == "oui") {
		$pdf->SetXY($Xmat+$largeurMat+($largeursujet),$Ymat+0.5);
		$pdf->SetFillColor(230,230,255);
		$pdf->MultiCell(20,$hauteurMatiere-2,'',1,'L',1);
		$pdf->SetXY($Xmat+$largeurMat+($largeursujet)+4,$Ymatcont+2+8);
		$pdf->SetFont('Arial','B',12);
		$pdf->SetFillColor(255,255,255);
		$pdf->MultiCell(20,3,"$noteaff",0,'L',0);
		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
			// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
		}else {
			$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		}

		$pdf->SetXY($Xmat+$largeurMat+($largeursujet)+20,$Ymat+0.5);
		$pdf->MultiCell(20,$hauteurMatiere-2,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+($largeursujet)+20+4,$Ymatcont+2+8);
		$pdf->MultiCell(20,3,"$moyeMatGen",0,'L',0);

/*		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($Xmat+$largeurMat+($largeursujet)+20+20,$Ymatcont);
		$pdf->MultiCell(55,$hauteurMatiere-2,'',1,'C',1);
        	$pdf->SetXY($Xmat+$largeurMat+($largeursujet)+20+20,$Ymatcont+1);
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	        $commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
        	$confPolice=confPolice($commentaireeleve);
	        $pdf->MultiCell(55,3,"$commentaireeleve",0,'L',0);
*/
	

		$pdf->SetFont('Arial','B',12);

	}else{
		$pdf->SetXY($Xmat+$largeurMat+($largeursujet),$Ymat+0.5);
                $pdf->SetFillColor(230,230,255);
                $pdf->MultiCell(20,$hauteurMatiere-2,'',1,'L',1);
                $pdf->SetXY($Xmat+$largeurMat+($largeursujet)+4,$Ymatcont+2+8);
                $pdf->SetFont('Arial','B',12);
                $pdf->SetFillColor(255,255,255);
                $pdf->MultiCell(20,3,"$noteaff",0,'L',0);
	}
	$Ymat=$Ymat-2 + $hauteurMatiere;
	$Ymatcont=$Ymatcont-2 + $hauteurMatiere;
	$ii++;


	if ($Ymat > 250) { 
		$pdf->AddPage(); 
		$Ymat=10;
		$Ymatcont=10;
	}

}
// fin de la mise en place des matiere
// cadre moyenne generale
// fin duplicata

if ($Ymat > 260) { 
	$pdf->AddPage(); 
	$Ymat=10;
	$Ymatcont=10;
}

$Xmat=5;

$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(220);
$pdf->SetXY($Xmat,$Ymat+2);
$pdf->MultiCell(203,35,'',1,'C',0);
$pdf->SetXY($Xmat+1,$Ymat+3);
$pdf->MultiCell(200,4,"$appreciationretard",0,'L',0);
$pdf->SetXY($Xmat+1,$Ymat+9+3);
$pdf->WriteHTML(stripslashes("Observations générales : "));
// commentaire direction
$commentairedirection=stripslashes(recherche_com($idEleve,$_POST["saisie_trimestre"],"default"));
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY($Xmat+1,$Ymat+9+9+3);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(146,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)
$pdf->SetXY($Xmat+1,$Ymat+33);
//$pdf->WriteHTML("Orientation envisagée : ");

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
} // fin du for on passe à'eleve suivant
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

<br /> </br />
<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$_POST[saisie_trimestre],$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom,$_POST[saisie_trimestre],$dateDebut,$dateFin);
if($cr == 1){
		history_cmd($_SESSION[nom],"CREATION BULLETIN","Classe : $classe_nom");
        //alertJs("Bulletin créé -- Service Triade");
}
else{
	error(0);
}
Pgclose();
?>

<?php
}
else {
?>
<center>
<br>
<?php print LANGMESS14?></br>
<br>
<?php print LANGMESS15?></br>
<br>
<?php print LANGMESS16?><br>
</br>
<?php
        }
?>
</center>
</form>

<!-- // fin  --></td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language=JavaScript>attente_close();</script>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();
?>
