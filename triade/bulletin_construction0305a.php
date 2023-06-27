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



$nbrtdaabs=$_POST["nbrtdaabs"];
$ptenmoinsabs=$_POST["ptenmoinsabs"];
$nbrabs=$_POST["nbrabs"];
$motif_polspe_ip=$_POST["motif_polspe_ip"];
$ptenmoinsabsjusti=$_POST["ptenmoinsabsjusti"];
$ismappdatefin=$_POST["saisie_date_fin"];
$ismappdatedebut=$_POST["saisie_date_debut"];
$ismappects=$_POST["ects"];

config_param_ajout($ptenmoinsabs,"ptenmoinsabs");
config_param_ajout($nbrtdaabs,"nbrtdaabs");
config_param_ajout($nbrabs,"nbrabs");
config_param_ajout($motif_polspe_ip,"motif_polspe_ip");
config_param_ajout($ptenmoinsabsjusti,"ptenmoinsabsjusti");
config_param_ajout($ismappdatefin,"ismappdatefin");
config_param_ajout($ismappdatedebut,"ismappdatedebut");
config_param_ajout($ismappects,"ismappects");

$textTrimestre="Relevé annuel";

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=ucwords($data[0][1]);

/*
$hauteurphoto=$_POST["hauteurphoto"];
$largeurphoto=$_POST["largeurphoto"];
$hauteurlogo=$_POST["hauteurlogo"];
$largeurlogo=$_POST["largeurlogo"];

if (trim($hauteurphoto) == "") {
	$hauteurphoto=16.3;
	$largeurphoto=10.8;
}
if (trim($hauteurlogo) == "") {
	$hauteurlogo=25;
	$largeurlogo=25;
}

config_param_ajout($hauteurlogo,"hauteurlogo");
config_param_ajout($largeurlogo,"largeurlogo");
config_param_ajout($hauteurphoto,"hauteurphoto");
config_param_ajout($largeurphoto,"largeurphoto");
 */

// recup année scolaire
$anneeScolaire=$_POST["annee_scolaire"];
?>
<ul>
<font class="T2">
      <?php print "Bulletin"?> : <?php print $textTrimestre?><br> <br>
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
// fin de la recup
 */
/*
if (MODNAMUR0 == "oui") {
	$recupInfo=recupCaractVieScolaire($_POST["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
}
 */
// recherche des dates de debut et fin
//$dateRecup=recupDateTrim($_POST["saisie_trimestre"]);
$dateDebut=$ismappdatedebut;
$dateFin=$ismappdatefin;

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
//$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
//if ($moyenClasseGen ==  -1 ) { $moyenClasseGen=""; }
// Fin du Calcul moyenne classe
// ----------------------------

/*
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
*/



$plageEleve=$_POST["plageEleve"];

if (preg_match('/^E_/',$plageEleve)) { 
	$plageEleve=preg_replace('/E_/','',$plageEleve);
	$eleveT=recupEleveViaIdEleve($plageEleve); 
	$dep=0; $nbEleveT=count($eleveT);
}


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
	$noteectsglobal=0;
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
	$xcoor0=7;   // sans logo
	$ycoor0=7;   // sans logo

	// mise en place du logo
	/*
	$photo=recup_photo_bulletin();
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xlogo=$largeurlogo;
			$ylogo=$hauteurlogo;
			$xcoor0=30;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$pdf->Image($logo,3,3,$xlogo,$ylogo);
		}
	}
	// fin du logo
	*/

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place des coordonnées
	$pdf->SetFont('Arial','',11);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->SetFillColor(230,230,255);
	$pdf->RoundedRect($xcoor0, $ycoor0, 100, 23, 3.5, 'DF');
	
	$pdf->SetXY($xcoor0,$ycoor0+2);
	$pdf->MultiCell(100,3,"Institut Supérieur du Management Public et Politique",0,'C',0);
	$pdf->SetXY($xcoor0,$ycoor0+6);
	
	$pdf->SetFont('Arial','I',8);
	$pdf->MultiCell(100,3,"Etablissement d'enseignement supérieur privé reconnu par l'état",0,'C',0);

	$pdf->SetFont('Arial','',11);
	$pdf->SetXY($xcoor0,$ycoor0+17);
	$pdf->MultiCell(100,3,"Année académique $anneeScolaire",0,'C',0);
	$pdf->SetFillColor(255);
	$pdf->SetXY($xcoor0,$ycoor0+13);
	$pdf->SetFont('Arial','',9);
	$descriptionLong=chercheClasse_description($idClasse);
	$pdf->MultiCell(100,3,"$descriptionLong",0,'C',0);

	$pdf->SetFont('Arial','B',12);
	$pdf->SetXY($xcoor0,$ycoor0+12);
	$textTrimestre=ucwords($textTrimestre);
	$pdf->MultiCell(100,3,"",0,'C',0);



	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0+=100+3,$ycoor0);
	$pdf->SetFillColor(230,230,255);
	$pdf->RoundedRect($xcoor0, $ycoor0, 95, 23, 3.5, 'DF');
	$pdf->SetFillColor(255);
	$pdf->SetXY($xcoor0,$ycoor0+2);
	$textTrimestre=ucfirst(strtolower($textTrimestre));
	$pdf->MultiCell(100,3,"RELEVE ANNUEL \n\n de notes",0,'C',0);	
	$pdf->SetXY($xcoor0+=5,$ycoor0+17);
	$pdf->MultiCell(100,3,"Etudiant(e) : ",0,'L',0);
	$pdf->SetXY($xcoor0+=25,$ycoor0+17);
	$pdf->SetFont('Arial','B',12);
	$pdf->MultiCell(100,3,"$prenomEleve $nomEleve",0,'L',0);
	$pdf->SetFont('Arial','',10);


	$pdf->SetXY($xcoor0=7,$ycoor0+=25);
	$pdf->SetFillColor(230,230,255);
	$pdf->RoundedRect($xcoor0, $ycoor0, 165, 20, 3.5, 'DF');
	$pdf->SetFillColor(255);

	$pdf->SetXY($xcoor0,$ycoor0+2);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(145,3,"  Absences non justifiées : $appreciationbis2",0,'L',0);

	$pdf->SetXY($xcoor0,$ycoor0+6);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(145,3,"  Retards non justifiées : $appreciationbis3",0,'L',0);

	$pdf->SetXY($xcoor0,$ycoor0+10);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(145,3,"  Absences justifiées POLSPE-IP : $appreciationbis4",0,'L',0);

	$pdf->SetXY($xcoor0,$ycoor0+14);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(145,3,"  soit : $appreciationbis",0,'L',0);


	$photo="./image/banniere/banniere-ismapp.jpg";
	$pdf->Image($photo,$xphoto=175,$ycoor0,30,23);





/*	$photoeleve=image_bulletin($idEleve);

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
*/
/*
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
	}
 */
	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$xcoor0=7;
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(84,132,171);
	$pdf->SetXY($xcoor0,$ycoor0+=24); //  placement  cadre titre
	$pdf->MultiCell(200,5,'',1,'L',1);
	$pdf->SetTextColor(255);
	$pdf->SetXY(9,$ycoor0+0.3); // placement contenu titre
	$pdf->WriteHTML("Enseignements");
	$pdf->SetX(97);
	$pdf->WriteHTML("Epreuves");
	$pdf->SetX(117);
	$pdf->WriteHTML("Moyenne");
	$pdf->SetX(133);
if ($ismappects == 1) $pdf->WriteHTML("ects");
	$pdf->SetX(154);
	$pdf->WriteHTML("Appréciations");
	// fin des titres
	$pdf->SetTextColor(0);
	$nbs=0;


	// Mise en place des matieres et nom de prof
	$Xmat=$xcoor0;
	$Ymat=$ycoor0+=5;
	$Xmatcont=$Xmat+1;
	$Ymatcont=$Ymat+1;

	$largeurMat=90;
	$hauteurMatiere=10.5; // taille du cadre matiere

        $nbmatiere='0';
        for($i=0;$i<count($ordre);$i++) {
                $verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
                if ($verifGroupe) { continue; }
                $nbmatiere++;
        }
        if ($nbmatiere >= 19) { $hauteurMatiere=8; }


	$Xcoeff=$largeurMat+7;
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




		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


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
		$ordrematiere=$ordre[$i][3]; 
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

	//	print_r($tabsous);

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
			$pdf->SetXY($Xmat,$Ymat);
			$H=$hauteurMatiere*$nbs;
			$posiNoteSous=$nbs;	
			$pdf->MultiCell($largeurMat,$H,'',1,'L',0);
			$deja++;
			$effbordure2=0;	
		}

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',$effbordure2,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$YYmat=$Ymatcont;
		$libelleMatiere=preg_replace('/0$/','',$libelleMatiere);
                $libelleMatiere=preg_replace('/&#8217;/',"'",$libelleMatiere);
		$pdf->WriteHTML('<B>'.strtoupper(sansaccent(strtolower($libelleMatiere))).'</B>');

		if ($sousmatiere != "") {
			$pdf->SetXY($Xmat+($largeurMat/2)-43,$Ymat+4);
			$pdf->SetFont('Arial','',7);
			$sousmatiere=preg_replace('/0$/','',$sousmatiere);
			$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
			$profAff=recherche_personne2($profAff);
			$sousmatiere=html_vers_text($sousmatiere);
			$infoaff=trunchaine(ucfirst($sousmatiere).' - '.$profAff,70);
			$pdf->WriteHTML('<I>'.$infoaff.'</I>');
		}
		$pdf->SetFont('Arial','',8);
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		


		$listeExamen=array("CC","DST","Dad","Soutenance","Rapport","Fiche de lecture","Exposé","Partiel","Lecture","Examen écrit","Recopiage vocabulaire","Mémoire Ip","Evaluation Tutorat");

		$epreuve="";
		$moyenneTT="";
		$coef="";
		foreach($listeExamen as $key=>$value) {
			if ($idgroupe == "0") {
				$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$value);
			}else{
				$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$value);
			}
			if (trim($noteaff) != "") {
				 if ($value == "CC") 	  { $valcoef="1"; }
				 if ($value == "DST") 	  { $valcoef="2"; }
				 if ($value == "Partiel") 	  { $valcoef="3"; }
				 if ($value == "Soutenance") { $valcoef="3"; }
				 if ($value == "Rapport") 	  { $valcoef="3"; }
				 if ($value == "Fiche de lecture") { $valcoef="2"; }
				 if ($value == "Exposé")     { $valcoef="1"; }
				 if ($value == "Dad")        { $valcoef="1"; }
				 if ($value == "Lecture") { $valcoef="3"; }
                                 if ($value == "Examen écrit")   { $valcoef="2"; }
                                 if ($value == "Recopiage vocabulaire") { $valcoef="1"; }
 				 if ($value == "Mémoire Ip")            { $valcoef="2"; }
                                 if ($value == "Evaluation Tutorat")    { $valcoef="2"; }



				$epreuve.="$value:$noteaff ($valcoef)\n";
				$moyenneTT+=$noteaff*$valcoef;
				$coef+=$valcoef;
			}

		}
		// mise en place de la colonne Epreuve
		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($Xcoeff,$Ycoeff+0.2);
		$pdf->MultiCell(20,2.3,"$epreuve",0,'L',0);
		$pdf->SetXY($Xcoeff,$Ycoeff);
		$pdf->MultiCell(20,$hauteurMatiere,"",1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;

		

		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->SetFillColor(84,132,171);
		if ($effbordure == 0) {
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(15,$H,'',1,'L',1);
			
		}else{
			if($nbs == 0) {
				$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
				$posiNoteSous=1;
			}
		}
		$larg=75;

		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		if ($effbordure == 0 ){
			$pdf->SetXY($Xnote-32,$Ynote);
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell($larg,$H,'',1,'L',0);  //commentaire
		}


		$effbordure2=1;

		if ($ismappects == 1) {
			$pdf->SetXY($Xnote-22,$Ynote);				
		}else{
			$pdf->SetXY($Xnote-32,$Ynote);
		}

		if ($ismappects == 1) { $larg=75-10; }
		$pdf->MultiCell($larg,$hauteurMatiere,'',$effbordure2,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
		unset($noteaff);	
		
		
		$noteaff=$moyenneTT/$coef;
//print $noteaff."($coef) <br>";
		$ip=$i+1;
		if (($sousmatiere != "0") && ($sousmatiere != "")){
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($XnotVal-2.5,$YnotVal);
		//	$noteaff1=$noteaff;
		//	if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
		//	$pdf->WriteHTML($noteaff1);
		//	unset($noteaff1);
			$coefsous=$coef;
			//recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		
			if ($noteaff != "") {
//			print "$notesous=$noteaff*$coefsous<br>";
				$notesous=$noteaff*$coefsous;
//			print "$notesoustotal1=$notesoustotal1+$notesous;<br>";
				$notesoustotal1=$notesoustotal1+$notesous;
//			print "$coefsoustotal1=$coefsoustotal1+$coefsous;<br>"; 
				$coefsoustotal1=$coefsoustotal1+$coefsous;
			}
		
			$matiereSuivante=chercheMatiereNom3($ordre[$ip][0]); //code_mat,trim(libelle),trim(sous_matiere)
			if (verifMatiereAvecGroupe($ordre[$ip][0],$idEleve,$idClasse,$ordre[$ip][2])) {
				$matiereSuivante="";
			}else{
				$matiereSuivante=chercheMatiereNom3($ordre[$ip][0]);
			}
			$matiereEnCours=$ordre[$i][5];
			if ( trim($matiereEnCours) != trim($matiereSuivante)) {
				$matierepre=$matiereEnCours;
				if ($notesoustotal1 != "") {
//				print "#".$notesoustotal1." ".$coefsoustotal1."#<br>";
					$notesousmoyen=$notesoustotal1/$coefsoustotal1;
					$notesousmoyen=number_format($notesousmoyen,2,'.','');
					$noteaff1=$notesousmoyen;
					if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
					$noteaff5=$noteaff1;
//					print "-->$noteaff1<br>";
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
				}elseif($posiNoteSous == 6){
					$ajus=$posiNoteSous*5;  //OK
				}elseif($posiNoteSous == 1){
					$ajus=$posiNoteSous*1.3;  //OK
				}else{
					$ajus=$posiNoteSous*2;
				}
				$pdf->SetXY($XnotVal,$YnotVal-$ajus);
				$pdf->SetTextColor(255);
				$pdf->WriteHTML($noteaff1);
				$pdf->SetTextColor(0);
				unset($noteaff1);
				unset($notesoustotal1);
				unset($coefsoustotal1);
			}else{
				if (verifMatiereSuivanteCommeSousmatiere($ordre[$ip][0]) == 0) {
					$pdf->SetFont('Arial','',12);
					$pdf->SetXY($XnotVal,$YYmat);
					$noteaff1=$noteaff;
					if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
					if ($noteaff1 != "") {	$noteaff1=number_format($noteaff1,2,'.',''); }
					$pdf->SetTextColor(255);
					$pdf->WriteHTML("$noteaff1");
					$noteaff5=$noteaff1;
					$pdf->SetTextColor(0);
					unset($noteaff1);
				}
			}
			$YnotVal=$YnotVal + $hauteurMatiere;

		}else{
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY($XnotVal,$YYmat);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			if ($noteaff1 != "") {	$noteaff1=number_format($noteaff1,2,'.',''); }
			$pdf->SetTextColor(255);
			$pdf->WriteHTML("$noteaff1");
			$noteaff5=$noteaff1;
			$pdf->SetTextColor(0);
			unset($noteaff1);
			$YnotVal=$YnotVal + $hauteurMatiere;
		}


		$Ycom=$YmoyMatGVal - 3;
	
		$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

		// mise en place de ects
		if ($ismappects == 1) {
			$pdf->SetFont('Arial','',10);
			$Xcom=$XmoyMatGenMaxVal + 10;
			$pdf->SetXY($Xcom-88,$Ycom);
			$noteects="";
			if (trim($noteaff5) != "") {
				if ($noteaff5 >= 10) {
					$noteects=$noteectsOK;
					$noteectsglobal+=$noteects;
				}else{
					$noteects="0";				
				}
			}
			$pdf->MultiCell(10,$hauteurMatiere,"$noteects",1,'C',0);
			
		}

		// mise en place des commentaires
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
		$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
		$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy
		$Xcom=$XmoyMatGenMaxVal + 10;
		if ($ismappects == 1) { $Xcom+=10; }
		$pdf->SetFont('Arial','',$confPolice[0]);
		$pdf->SetXY($Xcom-88,$Ycom);
		$larg=74;
		if ($ismappects == 1) { $larg=74-10; }
		$pdf->MultiCell($larg,$confPolice[1],$commentaireeleve,'','','L',0);
		
		// pour le calcul de la moyenne general de l'eleve
		if ( $noteaff5 != "" ) {
			//print "$noteMoyEleG=$noteMoyEleG + $noteaff5<br>";
       	        	$noteMoyEleG=$noteMoyEleG + $noteaff5;
			//print "($coefEleG)";
       	         	$coefEleG++;
		}
		$noteaff5="";
	}
// fin de la mise en place des matiere

$noteMoyEleG=$noteMoyEleG/$coefEleG;

$noteMoyEleG=$noteMoyEleG-$ptenmoinsMoyen; // point en moins par assuidité

$noteMoyEleGAff=number_format($noteMoyEleG,2,',','');

$xcoor0=7;
$ycoor0=$Ycom+20;

$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->SetFillColor(230,230,255);
$pdf->RoundedRect($xcoor0, $ycoor0, 100, 25, 3.5, 'DF');
$pdf->SetXY($xcoor0,$ycoor0+4);

$pdf->MultiCell(100,3,"MOYENNE : $noteMoyEleGAff / 20",0,'C',0);

$pdf->SetFont('Arial','',9);
if ($noteMoyEleG >= 10) {
	$pdf->SetFillColor(84,132,171);
}else{
	$pdf->SetFillColor(255);
}
$pdf->SetXY($xcoor0+7,$ycoor0+11);
$pdf->MultiCell(3,3,'',1,'L',1);
$pdf->SetXY($xcoor0+11,$ycoor0+10);
$pdf->MultiCell(20,5,'Admis(e)',0,'L',0);

if ($noteMoyEleG < 10) {
	$pdf->SetFillColor(84,132,171);
}else{
	$noteectsglobal="60";
	$pdf->SetFillColor(255);
}
$pdf->SetXY($xcoor0+7,$ycoor0+17);
$pdf->MultiCell(3,3,'',1,'L',1);
$pdf->SetXY($xcoor0+11,$ycoor0+16);
$pdf->MultiCell(20,5,'Ajourné(e)',0,'L',0);

if ($ismappects == 1) {
	$pdf->SetXY($xcoor0+45,$ycoor0+12);
	$pdf->MultiCell(50,5,"Total ECTS : $noteectsglobal",0,'L',0);
}

$pdf->SetXY($xcoor0+110,$ycoor0+4);
$date=dateDMY();
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(70,3," Fait à Paris, le $date",0,'L',0);




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
