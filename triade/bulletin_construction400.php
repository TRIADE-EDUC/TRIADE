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

$debut=deb_prog();
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
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
      <?php print LANGBULL27?> : <?php print $textTrimestre?><br> <br>
      <?php print LANGBULL28?> : <?php print ucfirst($classe_nom)?><br> <br>
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

$typeexamen=$_POST["examen"];
$typecommentaire="default";
// Modifier 
if ($typeexamen == "bull401") { $typeexamen="Examen B.A.C. Blanc"; $typecommentaire="bacblanc";    $examen="BAC Blanc"; }  // BAC Blanc
if ($typeexamen == "bull402") { $typeexamen="Examen B.T.S. Blanc";$typecommentaire="btsblanc";     $examen="BTS Blanc"; }  // BTS Blanc
if ($typeexamen == "bull403") { $typeexamen="Examen Brevet Blanc";$typecommentaire="brevetblanc";  $examen="Brevet Blanc"; }  // Brevet Blanc
if ($typeexamen == "bull404") { $typeexamen="Examen C.A.P. Blanc";$typecommentaire="capblanc";     $examen="CAP Blanc"; }  // CAP Blanc
if ($typeexamen == "bull405") { $typeexamen="Examen B.E.P. Blanc";$typecommentaire="bepblanc";     $examen="BEP Blanc"; }  // BEP Blanc
if ($typeexamen == "bull406") { $typeexamen="Examen Partiel Blanc";$typecommentaire="partielblanc";$examen="Partiel Blanc"; }  // Partiel Blanc
if ($typeexamen == "bull409") { $typeexamen="Brevet Professionnel Blanc";$typecommentaire="professionnelblanc";$examen="Brevet Professionnel Blanc"; }  // Partiel Blanc




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
$ordre=ordre_matiere_visubull($_POST["saisie_classe"],$anneeScolaire); // recup ordre matiere

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
$moyenClasseGen=calculMoyenClasseBlanc($idClasse,$eleveT,$dateDebut,$dateFin,$ordre,$examen);
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

			$noteaff=moyenneEleveMatiereExamen($idEleveMoyen,$idMatiere,$dateDebut,$dateFin,$idprof,$examen);
			$coeffaff=recup_coef_bulletin($_POST["examen"],$idClasse,$idMatiere,$ordre[$t][2]);
		
			if (( $noteaff != "" ) && ($coeffaff > 0)) {
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
	
if (isset($_POST["plageEleve"])) {
	$plageEleve=$_POST["plageEleve"];
	if ($plageEleve == "tous") { $dep=0; $nbEleveT=count($eleveT); }
	if ($plageEleve == "10") { $dep=0; $nbEleveT=9; }
	if ($plageEleve == "20") { $dep=9; $nbEleveT=19; }
	if ($plageEleve == "30") { $dep=19; $nbEleveT=29; }
	if ($plageEleve == "40") { $dep=29; $nbEleveT=39; }
	if ($plageEleve == "50") { $dep=39; $nbEleveT=49; }
	if ($plageEleve == "60") { $dep=49; $nbEleveT=59; }
	if ($nbEleveT > count($eleveT)) { $nbEleveT=count($eleveT); }
}else{
	$dep=0;
	$nbEleveT=count($eleveT);
}
for($j=$dep;$j<$nbEleveT;$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	$pdf->AddPage();
	$pdf->SetTitle("Examen BLANC- $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Examen BLANC $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="Téléphone : ".$tel;
	$coordonne4="E-mail : ".$mail;

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",30);


	$infoeleve=LANGBULL31." : $nomprenom";

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


	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place des coordonnées
	$xcoor0=10;
	$ycoor0=10;

	$pdf->MultiCell(190,0.2,'',1,'L',1);

	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($xcoor0,$ycoor0+=2);
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
	$pdf->SetXY($xcoor0,$ycoor0+25);
	$pdf->MultiCell(190,0.2,'',1,'L',1);
	//fin coordonnees

	// Titre
	$pdf->SetXY($xcoor0+100,$ycoor0);
	$pdf->SetFont('Courier','',14);
	$pdf->WriteHTML(trim($typeexamen));
	// fin titre

	// insertion de la Classe + Annee SCOLAIRE
	$Pdate=$anneeScolaire;
	$pdf->SetFont('Courier','',10);
	$pdf->SetXY($xcoor0+100,$ycoor0+5);
	$pdf->WriteHTML(trim(trunchaine($classe_nom,20))." ($Pdate)");
	// fin d'insertion
	
	
	$pdf->SetXY($xcoor0+100,$ycoor0+10);
	$pdf->WriteHTML(ucwords(trim($textTrimestre)));


	$pdf->SetXY($xcoor0,$ycoor0+=30); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);

	$Xv11=108;
	$Yv11=53;
	$pdf->SetFillColor(255);
	$pdf->RoundedRect($Xv11+5, $Yv11, 85, 20, 3.5, 'DF');
	$pdf->SetFillColor(220);

	// adresse de l'élève
	//elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance
	$dataadresse=chercheadresse($idEleve);
	//for($ik=0;$ik<=count($dataadresse);$ik++) {
	$ik=0;
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
		$civ=civ($dataadresse[$ik][13]);
		$nomtuteur=trim(strtoupper($nomtuteur));
		$prenomtuteur=trim(ucwords(strtolower($prenomtuteur)));
		$nomprenomtuteur="$civ $nomtuteur $prenomtuteur";

		$Xv1=15;

		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($Xv11+6,$Yv11+1);
		$chaine=trunchaine($nomprenomtuteur,40);
		$pdf->WriteHTML($chaine);
		$pdf->SetXY($Xv11+6,$Yv11+6);
		$chaine=trim($num_adr1." ").ucfirst(trim($adr1));
		$chaine=trunchaine($chaine,50);
		trim($chaine);
		$pdf->WriteHTML($chaine);
		$pdf->SetXY($Xv11+6,$Yv11+12);
		$chaine=trunchaine($chaine,40);
		$chaine=trim($code_post_adr1)." ".ucfirst(trim($commune_adr1));
		$pdf->WriteHTML($chaine);
		
	//}

	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$YY=$Yv11+20+3;
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(220);
	$pdf->SetXY(10,$YY); //  placement  cadre titre
	$pdf->MultiCell(189,8,'',1,'C',1);
	$pdf->SetXY(25,$YY+0.5); // placement contenu titre
	$pdf->WriteHTML($titrenote1);
	$pdf->SetX(67);
	$pdf->WriteHTML("Note");
	$pdf->SetX(90);
	$pdf->WriteHTML("Note");
	$pdf->SetX(125);
	if (!preg_match('/epicom.triade-educ.com/',$_SERVER["HTTP_REFERER"])) {  $pdf->WriteHTML("Appréciations des professeurs"); }
	// fin des titres

	$pdf->SetXY(80,$YY+4); // placement contenu sous-titre
	$pdf->SetFont('Arial','',7);
	$pdf->WriteHTML("Coef");
	$pdf->SetX(88);
	$pdf->WriteHTML("avec Coef");
	$pdf->SetX(102);
	$pdf->WriteHTML("Moy.Class");
	$pdf->SetFont('Arial','',9);

	// Mise en place des matieres et nom de prof
	$Xmat=10;
	$Ymat=$YY+8;
	$Xmatcont=11;
	$Ymatcont=$YY+8;

	$Xprof=55;
	$Yprof=$Ymat;
	$Xcoeff=55;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15;
	$Ymoyclasse=$Ymat;


	$XnomProfcont=56;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=$Xcoeff + 12;
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




	for($i=0;$i<count($ordre);$i++) {

		

		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];

		$coeff=recup_coef_bulletin($_POST["examen"],$idClasse,$idMatiere,$ordre[$i][2]);
		if ($coeff == "0.00") { continue; } 

		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);


		// mise en place des matieres
		$largeurMat=55;
		$hauteurMatiere=8; // taille du cadre matiere
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
		$ii++;
		if ($ii == 25) {
			$pdf->AddPage();
			$Xmat=10;
			$Ymat=20;
			$Xmatcont=11;
			$Ymatcont=20;

			$Xprof=55;
			$Yprof=$Ymat;
			$Xcoeff=55;
			$Ycoeff=$Ymat;
			$Xmoyeleve=$Xcoeff + 10;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve + 15;
			$Ymoyclasse=$Ymat;

			$XnomProfcont=56;
			$YnomProfcont=$Ymatcont;
			$Xnote=$Xmoyclasse + 32;
			$Ynote=$Ymat;
			$XnotVal=$Xcoeff + 12;
			$YnotVal=$Ycoeff + 3;
			$XcoeffVal=$Xcoeff + 1;
			$YcoeffVal=$Ymat + 3;
			$XprofVal=20; // x en nom prof
			$YprofVal=$Ymat + 4; // y en nom du prof
			$XmoyMatGVal=$Xcoeff + 26 ;
			$YmoyMatGVal=$Ycoeff + 3 ;
			$ii=0;
		}

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),30).'</B>');
		// $pdf->WriteHTML('<B>'.trunchaine(sansaccentmajuscule(strtoupper($matiere)),20).'</B>');
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		// mise en place moyenne eleve
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
		$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
		$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;

		// mise en place du cadre note
		$pdf->SetXY($Xnote,$Ynote);
		$pdf->MultiCell(87,$hauteurMatiere,'',1,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
	
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$examen);
		}else{
			$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
		}
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($XnotVal-1,$YnotVal);
		$noteaff1=$noteaff;
		if (($noteaff1 < 10) && ($noteaff1 != "")) { $noteaff1="0".$noteaff1; }
		$pdf->WriteHTML($noteaff1);


		$YnotVal=$YnotVal + $hauteurMatiere;
		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=$coeff;
//		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		if ($coeffaff > 0) { $moyenCoeffGenaff+=$coeffaff; }

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyeMatGenExamen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof,$examen);
    		}else {
           		$moyeMatGen=moyeMatGenGroupeExamen($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$examen);
    		}

		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);

		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		// $moyeMatGenaff
		$pdf->WriteHTML($coeffaff);
/*
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
 */
	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
	//$moyeMatGenMinaff=$moyeMatGenMin;
	//if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$noteaveccoef="";
	if ($noteaff1 != "") {$noteaveccoef=$coeffaff*$noteaff1; }
	$pdf->WriteHTML($noteaveccoef);
	$moyenNoteCoeffGenaff+=$noteaveccoef;
	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
//	$moyeMatGenMaxaff=$moyeMatGenMax;
//	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenaff);
	$Ycom=$YmoyMatGVal - 3;
	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;
	// mise en place des commentaires
//	$commentaireeleve=cherche_com_eleve_examen($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe,$examen);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy
	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
//	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal,$YprofVal);
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine($profAff,20));
	$YprofVal=$YprofVal + $hauteurMatiere ;

	// pour le calcul de la moyenne general de l'eleve
	if (($noteaff != "" ) && ($coeffaff > 0)) {
	        $noteMoyEleGTempo = $noteaff * $coeffaff;
                $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
                $coefEleG=$coefEleG + $coeffaff;
	}

}
// fin de la mise en place des matiere


// fin notes
// --------


// cadre moyenne generale
$YmoyenneGeneral=$Ymoyclasse;
if ($YmoyenneGeneral > 230) {
	$pdf->AddPage();
	$YmoyenneGeneral=20;
}


$LargeurMG=$largeurMat;
$YmoyenneGeneralT=$YmoyenneGeneral+2;
$XMoyGE= 10 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 10 + 6;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;


$pdf->SetFont('Arial','',9);
$pdf->SetXY(10,$YmoyenneGeneral);
$pdf->MultiCell($LargeurMG,10,'',1,'L',0);
$pdf->SetXY(12,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>MOYENNE </B>");
$pdf->SetXY($XMoyGE,$YMoyGE);
$pdf->SetFillColor(220);
$pdf->MultiCell(15,10,'',1,'L',1);
$pdf->SetXY($XMoyCL,$YMoyGE);
$pdf->MultiCell(32,10,'',1,'L',0);


$pdf->SetXY($XMoyCL+2,$YMoyGE+2);
$pdf->WriteHTML($moyenCoeffGenaff);
$moyenCoeffGenaff="";

$pdf->SetXY($XMoyCL+11,$YMoyGE+2);
$pdf->WriteHTML($moyenNoteCoeffGenaff);
$moyenNoteCoeffGenaff="";

$moyenClasseGenaff=$moyenClasseGen;
if (($moyenClasseGenaff < 10) && ($moyenClasseGenaff != "")) { $moyenClasseGenaff="0".$moyenClasseGenaff; }
$pdf->SetXY($XMoyCL+22,$YMoyGE+2);
$pdf->WriteHTML($moyenClasseGenaff);





if ((file_exists("./data/image_pers/logo_signature.jpg")) && ($_POST["ajsignature"] == "oui")){
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(120,$YmoyenneGeneralT+6.5);
	$pdf->WriteHTML("[ <I>Signature du directeur</I> ]");
	$taille = getimagesize("./data/image_pers/logo_signature.jpg");
	$logox=$taille[0]/25;
	$logoy=$taille[1]/25;
	$pdf->Image("./data/image_pers/logo_signature.jpg","150",$YmoyenneGeneralT-6,$logox,$logoy);
}

// fin du cadre moyenne generale

// affichage de la moyenne generale eleve
$XmoyElValue=$LargeurMG + 10;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$pdf->SetFont('Arial','',12);
$pdf->SetXY($XmoyElValue+1,$YmoyElGenValue);

$moyenEleveaff=$moyenEleve;

$pdf->WriteHTML("<B>".$moyenEleveaff."</B>");
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general



// fin affichage moy eleve

$datap=config_param_visu($_POST["examen"]."Mdmis");
$moyenadmis=$datap[0][0];
if (($moyenadmis != 0) && (trim($moyenadmis) != "")) {
	$moyenEleve=preg_replace('/,/','.',$moyenEleve);
	if ($moyenEleve >=  $moyenadmis) {
		$admis="ADMIS";
	}else{
		$admis="REFUSE";
	}
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XmoyElValue+80,$YmoyElGenValue);
	$pdf->WriteHTML("<FONT COLOR=red><B>".$admis."</B></FONT>");
}

// fin affichage


// cadre appréciation
$Ycom=$YMoyGE+10;
$EpaisCom=30;
$YcomP1=$Ycom + 1;
$YcomP2=$YcomP1 + 10;
$YcomP3=$YcomP2 + 5;
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(220);
$pdf->SetXY(10,$Ycom);
$pdf->MultiCell(189,$EpaisCom,'',1,'C',0);

// commentaire direction
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"$typecommentaire");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(17,$YcomP1+7);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
//$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->SetFont('Arial','',11);
//$pdf->MultiCell(170,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)
$pdf->MultiCell(170,4,$commentairedirection,'','','L',0); // commentaire de la direction (visa)

$pdf->SetFont('Arial','',10);
$pdf->SetXY(11,$YcomP1);
$pdf->WriteHTML($appreciation2);
$pdf->SetXY(11+74,$YcomP1);
$pdf->SetFont('Arial','',8);
if (!preg_match('/epicom.triade-educ.com/',$_SERVER["HTTP_REFERER"])) { $pdf->WriteHTML(" ( Professeur Principal : ". $profp ." )" ); }
$pdf->SetFont('Arial','',9);

/*
// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetXY(12,$YcomP1+20);
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(170,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)
*/

//duplicata et signature
$YduplicaSign=$Ycom + 1 + $EpaisCom;
$pdf->SetFont('Arial','',5);
$pdf->SetXY(11,$YduplicaSign);
$pdf->WriteHTML("<I>".$duplicata."</I>");
$pdf->SetFont('Arial','',8);
$pdf->SetXY(120,$YduplicaSign);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',5);
$pdf->SetXY(11,$YduplicaSign+3);
$pdf->WriteHTML($signature2);

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
$fichier="./data/pdf_bull/$classe_nom/bulletin_${typecommentaire}_".$nomEleve."_".$prenomEleve."_".$_POST["saisie_trimestre"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage("$examen ".$_POST["saisie_trimestre"],$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
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
