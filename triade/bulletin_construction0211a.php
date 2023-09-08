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

config_param_ajout($_POST["hauteurmatiere"],"hauteurMatBull209");
config_param_ajout($_POST["abssconet"],"abssconet");
$abssconet=$_POST["abssconet"];

$debut=deb_prog();
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
if (count($valeur)) {

	if ($_POST["typetrisem"] == "trimestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL22; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL23; $triabsconet="T2";}
	if ($_POST["saisie_trimestre"] == "trimestre3" ) { $textTrimestre=LANGBULL24; $triabsconet="T3";}
}

if ($_POST["typetrisem"] == "semestre") {
	if ($_POST["saisie_trimestre"] == "trimestre1" ) { $textTrimestre=LANGBULL25; $triabsconet="T1"; }
	if ($_POST["saisie_trimestre"] == "trimestre2" ) { $textTrimestre=LANGBULL26; $triabsconet="T2"; }
}

// recupe du nom de la classe
$data=chercheClasse($_POST["saisie_classe"]);
$classe_nom=$data[0][1];

$afficheRemplacant=$_POST["afficheRemplacant"];
config_param_ajout($afficheRemplacant,"afficheRemplacant");

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


for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
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

	if ($nbretard == "") { $nbretard=0; }
	if ($nbabs == "") { $nbabs=0; }
	if ($nbabsnonjustifier == "") { $nbabsnonjustifier=0; }

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


	$titre="<B><U>".LANGBULL30."</U> <U>".ucwords($textTrimestre)."</u></B>";

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",45);


	$infoeleve=LANGBULL31." : $nomprenom";
	$infoclasse=trim($classe_nom);


	$titrenote1=LANGBULL32;
	$titrenote2=LANGBULL31;
	$titrenote3=LANGBULL33;
	$titrenote4="Appréciations";
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
//	$logo="./image/banniere/banniere-pigier-aix.jpg";
	if (file_exists($logo)) {
		$xlogo=25;
		$ylogo=25;
		$xcoor0=27;
		$ycoor0=3;
		$xtitre=90; // avec logo
		$pdf->Image($logo,3,3,$xlogo,$ylogo);
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
	$pdf->SetXY($xtitre,20);
	$pdf->SetFont('Courier','',18);
	$pdf->WriteHTML($titre);
	// fin titre

	$pdf->SetFont('Arial','B',10);
	$pdf->SetXY(15,38); // placement du la classe
	$pdf->MultiCell(180,5,"$infoclasse",0,'L',0);



	// cadre du haut
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(220);
	$pdf->SetXY(15,45); // placement du cadre du nom de l eleve
	$pdf->MultiCell(192,28,'',1,'L',1);

	// $photoeleve=image_bulletin($idEleve);

	$photo=$photoeleve;
	$xphoto=17;
	$yphoto=46;
	//$photowidth=18;
	//$photoheight=18;
	$photowidth=10.8;
	$photoheight=16.3;
	$Xv1=15;
	$Xv11=111;

	$pdf->SetXY($Xv1,46); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
//	$pdf->SetXY($Xv1+64,46);
//	$pdf->WriteHTML($infoeleve2);
//	$pdf->SetXY($Xv1+77,46);
//	$pdf->WriteHTML($infoeleveclasse);

	$pdf->SetFillColor(255);
	$pdf->RoundedRect($Xv11+5, 47, 80, 24, 3.5, 'DF');

	$pdf->SetFillColor(220);


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
		if ($datenaissance == "00/00/0000") { $datenaissance=""; }
		$regime=$dataadresse[$ik][12];
		$class_ant=trim(trunchaine($dataadresse[$ik][10],20));

		$pdf->SetXY($Xv1,51); 
		$pdf->SetFont('Arial','',8);
		$pdf->WriteHTML("N°: $numero_eleve ");
		$pdf->SetXY($Xv1,56);
		$pdf->WriteHTML("Né(e) le $datenaissance");
	//	$pdf->SetXY($Xv1,51); 
	//	$pdf->WriteHTML("Régime: $regime ");
	//	$pdf->SetXY($Xv1+80,44);
	//	$class_ant=trunchaine($class_ant,40);
	//	$pdf->WriteHTML("Classe ant.: $class_ant ");

		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($Xv11,46);
		
		$chaine=trim(strtoupper($nomtuteur))." ".trim(ucwords(strtolower($prenomtuteur)));
		$pdf->SetXY($Xv11+6,49);
		$chaine=trunchaine($chaine,50);
		$pdf->WriteHTML($chaine);
		$pdf->SetXY($Xv11+6,54);
		$chaine=trim($num_adr1)." ".ucfirst(trim(strtolower($adr1)));
		$chaine=trunchaine($chaine,50);
		$pdf->WriteHTML($chaine);
		$pdf->SetXY($Xv11+6,60);
		$chaine=trunchaine($chaine,50);
		$chaine=trim($code_post_adr1)." ".ucfirst(trim(strtolower($commune_adr1)));
		$pdf->WriteHTML($chaine);
	}

	// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(220);
	$pdf->SetXY(15,74); //  placement  cadre titre
	$pdf->MultiCell(192,8,'',1,'C',1);
	$pdf->SetXY(25,75); // placement contenu titre
	$pdf->WriteHTML($titrenote1);
	$pdf->SetX(67);
	$pdf->WriteHTML($titrenote2);
	$pdf->SetX(90+15);
	$pdf->WriteHTML($titrenote3);
	$pdf->SetX(125+15);
	$pdf->WriteHTML($titrenote4);
	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(82,78);
	$pdf->WriteHTML("Examen");
	$pdf->SetX(82+15);
	$pdf->WriteHTML($soustitre6);
	$pdf->SetX(92+15);
	$pdf->WriteHTML($soustitre7);
	$pdf->SetX(102+15);
	$pdf->WriteHTML($soustitre8);
	// fin des sous-titres

	$nbs=0;


	// Mise en place des matieres et nom de prof
	$Xmat=15;
	$Ymat=82;
	$Xmatcont=16;
	$Ymatcont=82;

	$Xprof=55;
	$Yprof=$Ymat;
	$Xcoeff=55;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 30;
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
	$iiii=0;

	$posiNoteSous=0;
	$effbordure=1;

	$TT=1;
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
		$largeurMat=50;
		$hauteurMatiere=$_POST["hauteurmatiere"]; // taille du cadre matiere
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
		$iiii++;
		if ($iiii == 25) {
			$pdf->AddPage();
			$Xmat=15;
			$Ymat=20;
			$Xmatcont=16;
			$Ymatcont=20;

			$Xprof=55;
			$Yprof=$Ymat;
			$Xcoeff=55;
			$Ycoeff=$Ymat;
			$Xmoyeleve=$Xcoeff + 10;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve + 30;
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
			$iiii=0;
		}

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
				if ( trim(strtolower($libelleMatiere)) == trim(strtolower($matiereSuivante))) {
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

	//	print "--<br>";
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

		if ($deja >= 1) {
			$libelleMatiere="";
			$effbordure2=0;
		}else{
			$effbordure2=1;
		}
	
	//	print ">$effbordure<";
		if ($effbordure == 0 ){
			$pdf->SetXY($Xmat,$Ymat);
			$H=$hauteurMatiere*$nbs;
			$posiNoteSous=$nbs;	
			$pdf->MultiCell($largeurMat,$H,'',1,'L',0);
			$deja++;
			$effbordure2=0;	
		}

	//	print $effbordure2." $libelleMatiere <br>";

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',$effbordure2,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$libelleMatiere=preg_replace('/0$/','',$libelleMatiere);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($libelleMatiere))),24).'</B>');

		if ($sousmatiere != "") {
			$pdf->SetXY($Xmat+($largeurMat/2)+3+10,$Ymat+2.5);
			$pdf->SetFont('Arial','',7);
			$sousmatiere=preg_replace('/0$/','',$sousmatiere);
			//$pdf->WriteHTML('<I>'.trunchaine(strtolower($sousmatiere),10).'</I>');
		}
		$pdf->SetFont('Arial','',8);
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff,$Ycoeff);
	//	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
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

		// mise en place de l'examen
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyeleve+15,$Ymoyeleve);
		if ($effbordure == 0) {
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(15,$H,'',1,'L',0);
		}else{
			if($nbs == 0) {
				$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',0);
			}
		}
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
	
	
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
		$pdf->MultiCell(32,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;


		if ($effbordure == 0 ){
			$pdf->SetXY($Xnote,$Ynote);
			$H=$hauteurMatiere*$nbs;
			$pdf->MultiCell(80,$H,'',1,'L',0);
		}

		// mise en place du cadre commentaire
		$pdf->SetXY($Xnote,$Ynote);
		$pdf->MultiCell(80,$hauteurMatiere,'',$effbordure2,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
		unset($noteaff);	
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		}


		if (($sousmatiere != "0") && ($sousmatiere != "")){
			$pdf->SetFont('Arial','',6);
			$pdf->SetXY($XnotVal-2.5,$YnotVal);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			$pdf->WriteHTML($noteaff1);
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
			if ( trim(strtolower($matiereEnCours)) != trim(strtolower($matiereSuivante))) {
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
				}elseif($posiNoteSous == 1){
					$ajus=0;  //OK
				}else{
					$ajus=$posiNoteSous*2;
				}
				$pdf->SetXY($XnotVal,$YnotVal-$ajus);

				$pdf->WriteHTML($noteaff1);
				unset($noteaff1);
				unset($notesoustotal1);
				unset($coefsoustotal1);
				unset($posiNoteSous);
			}
		}else{
			$pdf->SetFont('Arial','',12);
			$pdf->SetXY($XnotVal,$YnotVal);
			$noteaff1=$noteaff;
			if (($noteaff1 < 10) && ($noteaff1!="")) { $noteaff1="0".$noteaff1; }
			$pdf->WriteHTML($noteaff1);
			unset($noteaff1);
		}

		// mise en place note examen
		$examaff=recupExamPigierAix($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($XnotVal+15,$YnotVal);
		if (($examaff < 10) && ($examaff != "")) { $examaff="0".$examaff; }
		$pdf->WriteHTML($examaff);
		 
		$YnotVal=$YnotVal + $hauteurMatiere;

		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XcoeffVal,$YcoeffVal);
		//$pdf->WriteHTML($coeffaff);
		$YcoeffVal=$YcoeffVal + $hauteurMatiere;

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
           		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
    		}



		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($XmoyMatGVal+15,$YmoyMatGVal);
		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff!="")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		$pdf->WriteHTML($moyeMatGenaff);


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


	// mise en place du min
	$XmoyMatGenMinVal=$XmoyMatGVal + 11 + 15;
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff!="")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21 + 15 ;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff!="")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy

	$Xcom=$XmoyMatGenMaxVal + 10 ;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(80,$confPolice[1],$commentaireeleve,'','','L',0);
	
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',6);
	$pdf->SetXY($XprofVal-5,$YprofVal);
	if ($afficheRemplacant == "oui") { $profAff=recupIdSuppleant($profAff); }
	$profAff=recherche_personne2($profAff);
	$pdf->WriteHTML(trunchaine("$sousmatiere / $profAff",45));
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
	$pdf->WriteHTML('<B>'.'Vie Scolaire'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place de la colonne coeff
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff,$Ycoeff);
	//$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	$Ycoeff=$Ycoeff + $hauteurMatiere;
	// mise en place moyenne eleve
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
	$pdf->SetFillColor(240);  // couleur du cadre de l'eleve
	$pdf->MultiCell(15,$hauteurMatiere,'',1,'L',1);
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
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$_POST["saisie_trimestre"]);
	$pdf->SetFont('Arial','',12);
	$pdf->SetXY($XnotVal,$YnotVal);
	$pdf->WriteHTML($noteaff);


	$YnotVal=$YnotVal + $hauteurMatiere;
	// mise en place des coeff
	$coeffaff=$coefBull;
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XcoeffVal,$YcoeffVal);
	//$pdf->WriteHTML($coeffaff);
	$YcoeffVal=$YcoeffVal + $hauteurMatiere;

	// mise en place des moyennes de classe
        $moyeMatGen1=moyeMatGenVieScolaire($_POST["saisie_trimestre"],$idClasse); 
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
	$moyeMatGenaff=$moyeMatGen1;
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
	$pdf->SetXY($XmoyMatGenMinVal,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	
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

}

// fin notes
// --------






// cadre moyenne generale
if (count($ordre) >= 18) {
	$YmoyenneGeneral=$Ymoyclasse + 2;
}else{
	$YmoyenneGeneral=$Ymoyclasse + 5;
}
if ($YmoyenneGeneral > 228) {
	$pdf->AddPage();
	$YmoyenneGeneral=20;
}


$LargeurMG=$largeurMat;
$YmoyenneGeneralT=$YmoyenneGeneral + 2;
$XMoyGE= 15 + $LargeurMG;
$YMoyGE=$YmoyenneGeneral;
$XMoyCL=$XMoyGE + 15;

$XmoyClasseGValue=$XMoyGE + 10 + 6;
$YmoyClasseGValue=$YmoyenneGeneralT;
$XmoyClasseMinValue=$XmoyClasseGValue + 10;
$YmoyClasseMinValue=$YmoyenneGeneralT;
$XmoyClasseMaxValue=$XmoyClasseMinValue + 10 ;
$YmoyClasseMaxValue=$YmoyenneGeneralT;


$pdf->SetFont('Arial','',9);
$pdf->SetXY(15,$YmoyenneGeneral);
$pdf->MultiCell($LargeurMG,10,'',1,'L',0);
$pdf->SetXY(17,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>MOYENNE GENERALE</B>");
$pdf->SetXY($XMoyGE,$YMoyGE);
$pdf->SetFillColor(220);
$pdf->MultiCell(15,10,'',1,'L',1);
$pdf->SetXY($XMoyCL+15,$YMoyGE);
$pdf->MultiCell(32,10,'',1,'L',0);

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
$XmoyElValue=$LargeurMG + 17;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$pdf->SetFont('Arial','',12);
$pdf->SetXY($XmoyElValue,$YmoyElGenValue);

$moyenEleveaff=$moyenEleve;

$pdf->WriteHTML("<B>".$moyenEleveaff."</B>");
$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
// fin affichage moy eleve


//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
$pdf->SetFont('Arial','',10);
$pdf->SetXY($XmoyClasseGValue+15,$YmoyClasseGValue);

$moyenClasseGenaff=$moyenClasseGen;
if ($moyenClasseGenaff < 10) { $moyenClasseGenaff="0".$moyenClasseGenaff; }
$pdf->WriteHTML($moyenClasseGenaff);

$moyenClasseMinaff=$moyenClasseMin;
$pdf->SetXY($XmoyClasseMinValue+15,$YmoyClasseMinValue);
if ($moyenClasseMinaff < 10) { $moyenClasseMinaff="0".$moyenClasseMinaff; }
$pdf->WriteHTML($moyenClasseMinaff);

$moyenClasseMaxaff=$moyenClasseMax;
$pdf->SetXY($XmoyClasseMaxValue+15,$YmoyClasseMaxValue);
if ($moyenClasseMaxaff < 10) { $moyenClasseMaxaff="0".$moyenClasseMaxaff; }
$pdf->WriteHTML($moyenClasseMaxaff);
// fin de la calcul de min et max

// fin affichage


// cadre appréciation
$Ycom=$YMoyGE + 15;
$EpaisCom=30;
$YcomP1=$Ycom + 1;
$YcomP2=$YcomP1 + 10;
$YcomP3=$YcomP2 + 5;
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(220);
$pdf->SetXY(15,$Ycom);
$pdf->MultiCell(184,$EpaisCom,'',1,'C',0);
$pdf->SetXY(16,$YcomP1);
$pdf->WriteHTML($appreciation);
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML($appreciationbis);

// commentaire direction
$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
$pdf->SetXY(17,$YcomP1+5);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(170,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)

$pdf->SetFont('Arial','',10);
$pdf->SetXY(16,$YcomP2);
$pdf->WriteHTML($barre);
$pdf->SetXY(16,$YcomP3);
$pdf->WriteHTML($appreciation2);
$pdf->SetXY(16+74,$YcomP3);
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML(" ( Professeur Principal : ". $profp ." )" );
$pdf->SetFont('Arial','',9);

// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetXY(17,$YcomP1+20);
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(170,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


//duplicata et signature
$YduplicaSign=$Ycom + 1 + $EpaisCom;
$pdf->SetFont('Arial','',5);
$pdf->SetXY(16,$YduplicaSign);
$pdf->WriteHTML("<I>".$duplicata."</I>");
$pdf->SetFont('Arial','',8);
$pdf->SetXY(120,$YduplicaSign);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',5);
$pdf->SetXY(16,$YduplicaSign+3);
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
