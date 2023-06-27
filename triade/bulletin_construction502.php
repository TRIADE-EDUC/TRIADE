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
 *   Modifi              : par E. TAESCH 
 *   version              : bonif-17 du 26/01/08
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
include_once("./librairie_php/lib_tunisie.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(9000);
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade, 2001">
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
$classe_nom=trim($data[0][1]);
// recup anne scolaire
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

// recuperation des coordonn&eacute;es
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


$idliste=aff_grp_bull_bonifacio("français");
$liste_matiere=preg_replace('/\{/',"",$idliste[0][1]);
$liste_matiere=preg_replace('/\}/',"",$liste_matiere);
$liste_francais="";
if ($liste_matiere != "") {
	$sql="SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
		for($i=0;$i<count($data);$i++) {
			$liste_francais.=ucwords($data[$i][1]).",";
		}	
}

$idliste=aff_grp_bull_bonifacio("scientifique");
$liste_matiere=preg_replace('/\{/',"",$idliste[0][1]);
$liste_matiere=preg_replace('/\}/',"",$liste_matiere);
$liste_scientifique="";
if ($liste_matiere != "") {
	$sql="SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle";
      	$res=execSql($sql);
   	$data=chargeMat($res);
       	for($i=0;$i<count($data);$i++) {
               $liste_scientifique.=ucwords($data[$i][1]).",";
        }
}

$idliste=aff_grp_bull_bonifacio("technique");
$liste_matiere=preg_replace('/\{/',"",$idliste[0][1]);
$liste_matiere=preg_replace('/\}/',"",$liste_matiere);
$liste_technique="";
if ($liste_matiere != "") {
	$sql="SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle";
      	$res=execSql($sql);
   	$data=chargeMat($res);
       	for($i=0;$i<count($data);$i++) {
               $liste_technique.=ucwords($data[$i][1]).",";
        }
}

$idliste=aff_grp_bull_bonifacio("arabe");
$liste_matiere=preg_replace('/\{/',"",$idliste[0][1]);
$liste_matiere=preg_replace('/\}/',"",$liste_matiere);
$liste_arabe="";
if ($liste_matiere != "") {
	$sql="SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
		for($i=0;$i<count($data);$i++) {
			$liste_arabe.=ucwords($data[$i][1]).",";
		}	
}
$idliste=aff_grp_bull_bonifacio("social");
$liste_matiere=preg_replace('/\{/',"",$idliste[0][1]);
$liste_matiere=preg_replace('/\}/',"",$liste_matiere);
$liste_social="";
if ($liste_matiere != "") {
	$sql="SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle";
	$res=execSql($sql);
	$data=chargeMat($res);
		for($i=0;$i<count($data);$i++) {
			$liste_social.=ucwords($data[$i][1]).",";
		}	
}



$data=aff_grp_bull_bonifacio("français");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabfrancais=explode(",",$idliste);

$data=aff_grp_bull_bonifacio("technique");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabtechnique=explode(",",$idliste);

$data=aff_grp_bull_bonifacio("arabe");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabarabe=explode(",",$idliste);

$data=aff_grp_bull_bonifacio("social");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabsocial=explode(",",$idliste);

$data=aff_grp_bull_bonifacio("scientifique");
$idliste=$data[0][1];
$idliste=preg_replace('/[\{\}]/','',$idliste);
$tabscientifique=explode(",",$idliste);


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
		$classement[$g]=$moyenEleve2;

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

	$moyenFrancais=0;
        $nbnoteFrancais=0;
	$moyenScientifique=0;
       	$nbnotescientifique=0;
	$moyenArabe=0;
        $nbnoteArabe=0;
	$moyenSocial=0;
        $nbnoteSocial=0;
	$moyenTechnique=0;
       	$nbnoteTechnique=0;

	$pdf->AddPage();
	$pdf->SetTitle("Bulletin - $nomEleve $prenomEleve");
	$pdf->SetCreator("T.R.I.A.D.E.");
	$pdf->SetSubject("Bulletin de notes $textTrimestre "); 
	$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


	// declaration variable
	$coordonne0=strtoupper($nom_etablissement);
	$coordonne1=$adresse;
	$coordonne2=$postal." - ".ucwords($ville);
	$coordonne3="T&eacute;l&eacute;phone : ".$tel;
	$coordonne4="E-mail : ".$mail;

    //Medali  BULLETIN Premier Trimestre
	$titre="<b>Bulletin scolaire du ".$textTrimestre."</b>";    
	$duplicata2="<b>Republique Tunisienne: Ministere De L'Enseignement Et De La Formation  </b>";
	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",35);


	$infoeleve=LANGBULL31." : $nomprenom"; //Eleve
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=trim($classe_nom);
	$titrenote1=LANGBULL32; //Matieres
	$titrenote2=LANGBULL31; //Eleve
	$titrenote3=LANGBULL33; //Classe
	$titrenote4="Appréciations et"; //Appréciations, progres, conseils pour progresser
	$soustitre5=LANGBULL35; //Coef
	$soustitre6=LANGBULL36;//Moy
	$soustitre7=LANGBULL37; //Mini
	$soustitre8=LANGBULL38; //Maxi
	$titrenote9="conseils pour progresser.";
	$titrenote10="Eval 1.";
	$titrenote11="Eval 2.";
	$titrenote12="Control.";
	$titrenote13="Synth.";
	$titrenote14="Oral.";


	$appreciation=LANGBULL39;


$idprofp=rechercheprofp($_POST["saisie_classe"]);
$profp=recherche_personne($idprofp);
$com_visa_scolaire=recherche_com_scolaire($idEleve,$_POST["saisie_trimestre"]);
$com_visa_scolaire=trunchaine($com_visa_scolaire,135);

//$nbretard="___";
//$nbabs="___";
//$nbheureabs="___";

$appreciation="Bilan Assiduit: $nbretard  retard(s) & $nbabs  demi-journe(s) d'absence(s) (dont ___ non justifie(s)) et $nbheureabs heure(s) d'absence.                                  ";
$appreciation2="<br>    Observations et appréciations du conseil de classe:    Satisfactions [  ]    Encouragements [  ]  Félicitations [  ] ";
$barre="____________________________________________________________________________________________________________________";
$duplicata="<b>ATTENTION:Ce bulletin est l'original, il doit etre conservé par la famille <b> - suivi d'un éleve sur http://www.laghmani.ens.tn";
$signature="Visa et Signature de la Direction:";
// FIN variables

	$xtitre=80;  // sans logo
	$xcoor0=15;   // sans logo
	$ycoor0=3;   // sans logo

	// mise en place du logo
	$photo=recup_photo_bulletin();
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xlogo=$largeurlogo;
			$ylogo=$hauteurlogo;
			$xcoor0=15;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$pdf->Image($logo,15,3,$xlogo,$ylogo);
		}
	}
	// fin du logo

	//

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


// Debut cration PDF
	// mise en place des coordonnes
	$pdf->SetFont('Arial','',8);
    $pdf->SetXY(60,5);
	$pdf->WriteHTML("<I>".$duplicata2."</I>");
	$pdf->SetFont('Arial','',16);
	$pdf->SetXY(75,15);
	$pdf->WriteHTML("<I>".$coordonne0."</I>");	
    $pdf->SetFont('Arial','',12);
    $pdf->SetXY(70,25);
	$pdf->WriteHTML("<I><b>D'Enseignement Privé 1er &amp; 2eme Cycle </b></I>");
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(15,30);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY(15,35);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY(15,40);
	$pdf->WriteHTML($coordonne3);
	$pdf->SetXY(15,45);
	$pdf->WriteHTML($coordonne4);
	//fin coordonnees


	// insertion de la Annee SCOLAIRE
	//$Pdate=LANGBULL43." ".$anneeScolaire;
	//$pdf->SetFont('Courier','',11);
	//$pdf->SetXY(130,10);
	//$pdf->WriteHTML($Pdate);
	// fin d'insertion

	// Titre
	//$pdf->SetXY(50,45);
	//$pdf->SetFont('Courier','',20);
	//$pdf->WriteHTML($titre);
	// fin titre

	

// cadre du haut
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(230,230,255);
$pdf->SetXY(59,33); // placement du cadre du Annee de l eleve
$pdf->MultiCell(140,8,'',1,'L',1);
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(59,41); // placement du cadre du nom de l eleve
$pdf->MultiCell(140,8,'',1,'L',1);

	//$photoeleve=image_bulletin($idEleve);
	//$photo=$photoeleve;

	$xphoto=17;
	$yphoto=61;
	$photowidth=18;
	$photoheight=18;
	$Xv1=20;
	$Xv11=111;
	$pdf->SetXY(54+18+3,45); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	$pdf->SetXY(54+18+3,41); // placement du prenom de l'eleve
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetX(69+18+3,41);
    $pdf->SetFont('Arial','',12);
	$pdf->WriteHTML($infoeleveclasse);


	// adresse de l'éleve
	// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
	$dataadresse=chercheadresse($idEleve);
	for($ik=0;$ik<=count($dataadresse);$ik++) {
		$nomtuteur=$dataadresse[$ik][1];
		if (trim($nomtuteur) != "") {
			$civ=civ($dataadresse[$ik][13]);
		}else{
			$civ="";
		}
		$prenomtuteur=ucfirst($dataadresse[$ik][2]);
		$adr1=$dataadresse[$ik][3];
		$code_post_adr1=$dataadresse[$ik][4];
		$commune_adr1=$dataadresse[$ik][5];
		$numero_eleve=$dataadresse[$ik][9];
		$datenaissance=$dataadresse[$ik][11];
		if ($datenaissance != "") { $datenaissance=dateForm($datenaissance); }
		$regime=$dataadresse[$ik][12];
		$class_ant=trunchaine($dataadresse[$ik][10],20);

		$pdf->SetXY($Xv1,65); 
		$pdf->SetFont('Arial','',8);
		//$pdf->WriteHTML("N°: $numero_eleve ");
		$pdf->SetXY(100 + 33,45);
		$pdf->WriteHTML("Né(e) le:   $datenaissance");
		$pdf->SetXY(100 + 33,41); 
		$pdf->SetFont('Arial','',8);
		$pdf->WriteHTML("Regime: $regime ");
		$pdf->SetXY($Xv1+ 43,69);
		//$pdf->WriteHTML("Classe ant.: $class_ant ");

		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($Xv11,61);
            	$nomtuteur=$civ." ".strtoupper($nomtuteur);
		$chaine=trunchaine("$nomtuteur $prenomtuteur",35);
		//$pdf->WriteHTML(trunchaine($chaine,35));
		$pdf->SetXY($Xv11,67);
		$chaine=trim($num_adr1)." ".trim($adr1);
		//$pdf->WriteHTML(trunchaine($chaine,50));;
		$pdf->SetXY($Xv11,73);
		$commune_adr1=strtoupper($commune_adr1);
            	$chaine=trim($code_post_adr1)." ".trim($commune_adr1);
		//$pdf->WriteHTML(trunchaine($chaine,30));

	}

// Titre
$Pdate="Ann&eacute;e Scolaire ".$anneeScolaire;
$periode=$titre." - ".$Pdate;
$pdf->SetXY(63,35);
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->WriteHTML($periode);
// fin titre

/* insertion de la Annee SCOLAIRE
$Pdate="Ann&eacute;e Universitaire ".$anneeScolaire;
$pdf->SetFont('Arial','',10);
$pdf->SetXY(150,40);
$pdf->SetTextColor(255,255,255);
$pdf->WriteHTML($Pdate);
// fin d'insertion */



// fin cadre du haut

	// cadre des notes
	// ---------------
	// Barre des titres
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(230,230,255);
	$pdf->SetXY(15,55); //  placement  cadre titre
	$pdf->MultiCell(184,11,'',1,'C',1);
	$pdf->SetXY(25,57); // placement contenu titre
	$pdf->WriteHTML($titrenote1);
	$pdf->SetX(85);
	$pdf->WriteHTML($titrenote2);
	$pdf->SetX(122);
	$pdf->WriteHTML($titrenote3);
	$pdf->SetX(160);
	$pdf->WriteHTML($titrenote4);
	$pdf->SetXY(155,61);
	$pdf->WriteHTML($titrenote9);
	// fin des titres

	// possition des sous-titres
	$pdf->SetFont('Arial','',7);
	$pdf->SetXY(56,61);
	$pdf->WriteHTML($soustitre5);
	//$pdf->SetX(117);
	//$pdf->WriteHTML($soustitre6);
	$pdf->SetX(117);
	$pdf->WriteHTML($soustitre7);
	$pdf->SetX(127);
	$pdf->WriteHTML($soustitre8);
	$pdf->SetX(76);
	$pdf->WriteHTML($titrenote10);
	$pdf->SetX(86);
	$pdf->WriteHTML($titrenote11);
	$pdf->SetX(96);
	$pdf->WriteHTML($titrenote12);
	$pdf->SetX(106);
	$pdf->WriteHTML($titrenote13);
	$pdf->SetX(66);
	$pdf->WriteHTML($titrenote14);
	
	// fin des sous-titres



	// Mise en place des matieres et nom de prof
	$Xmat=15;
	$Ymat=41+25;
	$Xmatcont=16;
	$Ymatcont=41+25;

	$Xprof=55;
	$Yprof=$Ymat;
	$Xcoeff=55;
	$Ycoeff=$Ymat;
	$Xmoyeleve=$Xcoeff + 10;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve + 15;
	$Ymoyclasse=$Ymat;
	$XSynth=$Xcoeff + 10;
	$YSynth=$Ymat;
	$XCont=$Xcoeff + 50;
	$YCont=$Ymat;
	$XEval2=$Xcoeff + 40;
	$YEval2=$Ymat;
	$XEval1=$Xcoeff + 30;
	$YEval1=$Ymat;
	$XOral=$Xcoeff + 20;
	$YOral=$Ymat;
    $XOralVal=$XOral;


	$XnomProfcont=56;
	$YnomProfcont=$Ymatcont;
	$Xnote=$Xmoyclasse + 32;
	$Ynote=$Ymat;
	$XnotVal=$Xcoeff + 12;
	$YnotVal=$Ycoeff + 3+1;
	$XcoeffVal=$Xcoeff + 1;
	$YcoeffVal=$Ymat + 1; // pour aligner les valeures medali
	$XprofVal=20; // x en nom prof
	$YprofVal=$Ymat + 2; // y en nom du prof
	$XmoyMatGVal=$Xcoeff + 26 ;
	$YmoyMatGVal=$Ycoeff + 1 ; //pour aligner les valeures medali

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


		// mise en place des matieres
		$largeurMat=40;
		$hauteurMatiere=6; // taille du cadre matiere
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
		//if (($ii == 17) || (($ii == 16)&& (MODNAMUR0 == "oui") )) {
		
		if ($ii == 26) {
		//	$pdf->AddPage();
			$Xmat=15;
			$Ymat=15;
			$Xmatcont=16;
			$Ymatcont=15;

			$Xprof=55;
			$Yprof=$Ymat;
			$Xcoeff=55;
			$Ycoeff=$Ymat;
			$Xmoyeleve=$Xcoeff + 10;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve + 15;
			$Ymoyclasse=$Ymat;
			$XSynth=$Xcoeff + 10;
			$YSynth=$Ymat;
			$XCont=$Xcoeff + 50;
			$YCont=$Ymat;
			$XEval2=$Xcoeff + 40;
	        $YEval2=$Ymat;
	        $XEval1=$Xcoeff + 30;
	        $YEval1=$Ymat;
			$XOral=$Xcoeff + 20;
	        $YOral=$Ymat;

			$XnomProfcont=56;
			$YnomProfcont=$Ymatcont;
			$Xnote=$Xmoyclasse + 32;
			$Ynote=$Ymat;
			$XnotVal=$Xcoeff + 12;
			$YnotVal=$Ycoeff + 6;
			$XcoeffVal=$Xcoeff + 1;
			$YcoeffVal=$Ymat + 3;
			$XprofVal=20; // x en nom prof
			$YprofVal=$Ymat + 4; // y en nom du prof
			$XmoyMatGVal=$Xcoeff + 26 ;
			$YmoyMatGVal=$Ycoeff + 3 ;
			$ii=0;
		}

		$pdf->SetFont('Arial','',6);
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),27).'</B>');
		// $pdf->WriteHTML('<B>'.trunchaine(sansaccentmajuscule(strtoupper($matiere)),20).'</B>');
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;
		// mise en place de la colonne coeff
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff,$Ycoeff);
		$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
		$Ycoeff=$Ycoeff + $hauteurMatiere;
		// mise en place de la colonne Synth
	    $pdf->SetFont('Arial','',8);
	    $pdf->SetXY($XSynth,$YSynth);
	    $pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)//
	    $pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	    $YSynth=$YSynth + $hauteurMatiere;
		// mise en place de la colonne Cont
	    $pdf->SetFont('Arial','',8);
	    $pdf->SetXY($Xcoeff+50,$YCont);
	    $pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)//
	    $pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	    $YCont=$YCont + $hauteurMatiere;
		// mise en place de la colonne Eval2
	    $pdf->SetFont('Arial','',8);
	    $pdf->SetXY($Xcoeff+40,$YEval2);
	    $pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)//
	    $pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	    $YEval2=$YEval2 + $hauteurMatiere;
		// mise en place de la colonne Eval1
	    $pdf->SetFont('Arial','',8);
	    $pdf->SetXY($Xcoeff+30,$YEval1);
	    $pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)//
	    $pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	    $YEval1=$YEval1 + $hauteurMatiere;
		// mise en place de la colonne Oral
	    $pdf->SetFont('Arial','',8);
	    $pdf->SetXY($Xcoeff+20,$YOral);
	    $pdf->SetFillColor(255,255,255);  // couleur du cadre de l'eleve (240)//
	    $pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	    $YOral=$YOral + $hauteurMatiere;
		
		// mise en place moyenne eleve
		//$pdf->SetFont('Arial','',8);
		//$pdf->SetXY($Xcoeff+60,$Ymoyeleve);
		//$pdf->SetFillColor(230,230,255);  // couleur du cadre de l'eleve
		//$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',1);
		//$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;
		
		// mise en place moyenne classe
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($Xcoeff+60,$Ymoyclasse);
		$pdf->SetFillColor(230,230,255);  
		$pdf->MultiCell(22,$hauteurMatiere,'',1,'L',1);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;

		// mise en place du cadre note
		$pdf->SetXY($Xcoeff+82,$Ynote);
		$pdf->MultiCell(62,$hauteurMatiere,'',1,'',0);
		$Ynote=$Ynote + $hauteurMatiere;
		
		// mise en place des Oral
	    $oralaff=recupOral($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	    $pdf->SetFont('Arial','',8);
	    $pdf->SetXY($Xcoeff+10,$YOral-5);
	    $pdf->WriteHTML($oralaff);	

		
		// mise en place des Eval1
	$eval1aff=recupEval1($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+20,$YEval1-5);
	$pdf->WriteHTML($eval1aff);
	$YcoeffVal=$YcoeffVal-2 + $hauteurMatiere;
	
	// mise en place des Eval2
	$eval2aff=recupEval2($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+30,$YEval2-5);
	$pdf->WriteHTML($eval2aff);
	$YcoeffVal=$YcoeffVal-2 + $hauteurMatiere;
	
	// mise en place des Cont
	$contaff=recupCont($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+40,$YCont-5);
	$pdf->WriteHTML($contaff);
	$YcoeffVal=$YcoeffVal-2 + $hauteurMatiere;
	
	// mise en place des Synth
	$synthaff=recupSynth($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff+50,$YSynth-5);
	$pdf->WriteHTML($synthaff);
	$YcoeffVal=$YcoeffVal-2 + $hauteurMatiere;



		// mise en place des notes
		//$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
	//$pdf->SetFont('Arial','',8);
	//$pdf->SetXY($Xcoeff+60,$YSynth-5);
	//$pdf->WriteHTML("<B>".$noteaff."<B>");//AB
	
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		}
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($Xcoeff+60,$YSynth-5);
		$noteaff1=$noteaff;
		if (($noteaff1 < 10) && ($noteaff1 != "")) { $noteaff1="0".$noteaff1; }
		//$pdf->WriteHTML($noteaff1);


		$YnotVal=$YnotVal + $hauteurMatiere-1; //aligner les notes
		// mise en place des coeff
		//$coefftab=coeffMatiere($ordre[$i][0],$idClasse);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($XcoeffVal,$Ymat-5);
		$pdf->WriteHTML($coeffaff);
		$YcoeffVal=$YcoeffVal + $hauteurMatiere;

		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
           		$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
    		}else {
           		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
    		}

		//$pdf->SetFont('Arial','',9);
		//$pdf->SetXY($XmoyMatGVal+34.5,$YmoyMatGVal);

		$moyeMatGenaff=$moyeMatGen;
		if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
		//$pdf->WriteHTML($moyeMatGenaff);


		// -----------------------
		foreach($tabfrancais as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenFrancais+= $noteaff1 * $coeffaff;
					$nbnoteFrancais+=$coeffaff;
				}
			}
		}

		foreach($tabscientifique as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenScientifique+= $noteaff1 * $coeffaff;
					$nbnotescientifique+=$coeffaff;
				}
			}
		}
		
		foreach($tabtechnique as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenTechnique+= $noteaff1 * $coeffaff;
					$nbnoteTechnique+=$coeffaff;
				}
			}
		}
		
		foreach($tabarabe as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenArabe+= $noteaff1 * $coeffaff;
					$nbnoteArabe+=$coeffaff;
				}
			}
		}
		foreach($tabsocial as $value) {
			// print "Note : $noteaff1 - Idmatiere : $idMatiere - Coef : $coeffaff - Tabscient : $value <br>";
			if ($idMatiere == $value) {
				if ($noteaff1 != "") {
					$moyenSocial+= $noteaff1 * $coeffaff;
					$nbnoteSocial+=$coeffaff;
				}
			}
		}
		
		// ------------------------
		
		// calcul du min et du max
		if ($idgroupe == "0") {   // non matiere affecte  un groupe
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
	$XmoyMatGenMinVal=$XmoyMatGVal + 11;
	$pdf->SetXY($XmoyMatGenMinVal+24,$YmoyMatGVal);
	$moyeMatGenMinaff=$moyeMatGenMin;
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal+24.5,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
	$commentaireeleve=preg_replace('/\n/'," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> policy ; $confPolice[1] -> cadre

	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom+27,$Ycom+3);
	$pdf->MultiCell(87,$confPolice[1],$commentaireeleve,'','','L',0);
	//$pdf->WriteHTML($commentaireeleve);
	
	// mise en place du nom du prof
	$profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$coeffaff=recupCoeff($ordre[$i][0],$idClasse,$ordre[$i][2]);
	$pdf->SetFont('Arial','',6);
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
	//$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
	$pdf->SetXY($Xmatcont,$Ymatcont);
	//$pdf->WriteHTML('<B>'.'NOTE DE VIE SCOLAIRE'.'</B>');
	$Ymat=$Ymat + $hauteurMatiere;
	$Ymatcont=$Ymatcont + $hauteurMatiere;
	// mise en place de la colonne coeff
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($Xcoeff,$Ycoeff);
	$pdf->MultiCell(10,$hauteurMatiere,'',1,'L',0);
	$Ycoeff=$Ycoeff + $hauteurMatiere;
	
	
	// mise en place des coeff
	$coeffaff=$coefBull;
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($XcoeffVal,$YcoeffVal);
	$pdf->WriteHTML($coeffaff);
	$YcoeffVal=$YcoeffVal + $hauteurMatiere;

	// mise en place des moyennes de classe
        //$moyeMatGen1=moyeMatGenVieScolaire($_POST["saisie_trimestre"],$idClasse); 
	//$pdf->SetFont('Arial','',9);
	//$pdf->SetXY($XmoyMatGVal,$YmoyMatGVal);
	//$moyeMatGenaff=$moyeMatGen1;
	//if (($moyeMatGenaff < 10) && ($moyeMatGenaff != "")) { $moyeMatGenaff="0".$moyeMatGenaff; }
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
	if (($moyeMatGenMinaff < 10) && ($moyeMatGenMinaff != "")) { $moyeMatGenMinaff="0".$moyeMatGenMinaff; }
	$pdf->WriteHTML($moyeMatGenMinaff);

	// mise en place du max
	$XmoyMatGenMaxVal=$XmoyMatGVal + 21;
	$pdf->SetXY($XmoyMatGenMaxVal,$YmoyMatGVal);
	$moyeMatGenMaxaff=$moyeMatGenMax;
	if (($moyeMatGenMaxaff < 10) && ($moyeMatGenMaxaff != "")) { $moyeMatGenMaxaff="0".$moyeMatGenMaxaff; }
	$pdf->WriteHTML($moyeMatGenMaxaff);

	$Ycom=$YmoyMatGVal - 3;

	$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

	// mise en place des commentaires
	$commentaireeleve=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$_POST["saisie_trimestre"],"");
	$commentaireeleve=preg_replace('/\n/'," ",$commentaireeleve);
	$confPolice=confPolice($commentaireeleve);  // $confPolice[0] -> Cadre ; $confPolice[1] -> Policy


	$Xcom=$XmoyMatGenMaxVal + 10;
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->SetXY($Xcom,$Ycom);
	$pdf->MultiCell(85,$confPolice[1],$commentaireeleve,'','','L',0);
	
	// mise en place du nom du prof
	//$profAff=$persVieScolaire;
	//$pdf->SetFont('Arial','',6);
	//$pdf->SetXY($XprofVal-3,$YprofVal);
	//$pdf->WriteHTML('Equipe pdagogique & ducative');
	//$YprofVal=$YprofVal + $hauteurMatiere ;

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
if ($moyenFrancais != ""){
	$noteFrancais = $moyenFrancais / $nbnoteFrancais;
	$noteFrancais=number_format($noteFrancais,2,'.','');
	$noteFrancais=preg_replace('/\./',',',$noteFrancais);
}
if ($moyenScientifique != ""){
	$noteScientifique = $moyenScientifique / $nbnotescientifique;
	$noteScientifique=number_format($noteScientifique,2,'.','');
	$noteScientifique=preg_replace('/\./',',',$noteScientifique);
}
if ($moyenTechnique != ""){
	$noteTechnique = $moyenTechnique / $nbnoteTechnique;
	$noteTechnique=number_format($noteTechnique,2,'.','');
	$noteTechnique=preg_replace('/\./',',',$noteTechnique);
}
if ($moyenArabe != ""){
	$noteArabe = $moyenArabe / $nbnoteArabe;
	$noteArabe=number_format($noteArabe,2,'.','');
	$noteArabe=preg_replace('/\./',',',$noteArabe);
}
if ($moyenSocial != ""){
	$noteSocial = $moyenSocial / $nbnoteSocial;
	$noteSocial=number_format($noteSocial,2,'.','');
	$noteSocial=preg_replace('/\./',',',$noteSocial);
}


// cadre moyenne generale
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$YmoyenneGeneral=$Ymoyclasse + 1;
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

$pdf->SetFont('Arial','',9);
$pdf->SetXY(15,$YmoyenneGeneral);
$pdf->MultiCell($LargeurMG-17,10,'',1,'L',0);
$pdf->SetXY(17,$YmoyenneGeneralT);
$pdf->WriteHTML("<B>MOYENNES</B>");

$pdf->SetXY($LargeurMG,$YMoyGE);
$pdf->MultiCell(52,10,'',1,'L',0);  // cadre generale
$pdf->SetXY($LargeurMG+2,$YMoyGE+2);  // placement du mot 
$pdf->SetFont('Arial','',9);
$pdf->WriteHTML("<b>GENERALE</b>");
$pdf->SetXY($LargeurMG+25,$YMoyGE+20); // cadre note generale

//$pdf->MultiCell(15,10,'',1,'L',0);
//$pdf->SetXY($XMoyCL+5+17+2,$YMoyGE+20);  // placement du mot 
//$pdf->SetFont('Arial','',11);
//$pdf->WriteHTML("<b>$moyenEleve</b>");

//$pdf->SetY($YmoyenneGeneral+29);
//$pdf->MultiCell($largeurMat,3,"Moyenne et total du 1er : " ,0,'R',0);
//$pdf->SetXY($largeurMat+13,$YmoyenneGeneral+15);
//$pdf->MultiCell(10,3,"$moyenClasseMax" ,0,'L',0);


//$pdf->SetXY($XMoyCL+29,$YMoyGE+20);
//$tt=$moyenClasseMax*$moyenCoeffGenaff;
//$pdf->MultiCell(10,3,"$tt" ,1,'L',0);

rsort($classement);
foreach ($classement as $key => $val) {	
	if ($val == $moyenEleve) { $val1 = $key; }
	
}

$val1++;
if ($val1 == 1) { $rang="1 er"; }else{ $rang="$val1 eme"; }
$pdf->SetXY($XMoyCL+14,$YMoyGE+6);
//$pdf->WriteHTML("$rang");
//$pdf->MultiCell(30,3,"$rang",1,'L',0);
//$pdf->SetXY($XMoyCL+17,$YMoyGE+8);
//$pdf->MultiCell(30,3,"sur",0,'L',0);
//$pdf->WriteHTML("sur");
//$pdf->SetXY($largeurMat+15+32+13,$YmoyenneGeneral+10);
//$pdf->MultiCell(30,3,count($classement). " élèves en classe",0,'L',0);



//$pdf->SetXY($XMoyCL+14,$YMoyGE+8);
//$pdf->MultiCell(33,7,'',1,'L',0);  // cadre Classement
//$pdf->SetXY($XMoyCL+14,$YMoyGE+8);  // placement du mot 
//$pdf->SetFont('Arial','',8);
//$pdf->WriteHTML("<b>Classement General:</b>");

// fin du cadre moyenne generale

//$pdf->SetXY($XMoyCL+29,$YMoyGE+8);
//$tt=$moyenClasseMax*$moyenCoeffGenaff;
//$pdf->MultiCell(10,3,"$tt" ,1,'L',0);

$pdf->SetXY($XMoyCL+50,$YMoyGE+6);
$pdf->MultiCell(33,5,'',1,'L',0);  // cadre Arabe
$pdf->SetXY($XMoyCL+50,$YMoyGE+7);  // placement du mot 
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("<b>Arabe</b>");

$pdf->SetXY($XMoyCL+55+15+34,$YMoyGE+6); // cadre note Arabe
//$pdf->MultiCell(10,5,'',1,'L',0);
$pdf->SetXY($XMoyCL+70,$YMoyGE+7);  // placement du mot 
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML("<b>$noteArabe</b>");

$pdf->SetXY($XMoyCL+14,$YMoyGE);
$pdf->MultiCell(33,5,'',1,'L',0);  // cadre Français
$pdf->SetXY($XMoyCL+14,$YMoyGE+1);  // placement du mot 
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("<b>Français: </b>");

$pdf->SetXY($XMoyCL+17+32,$YMoyGE); // cadre note Français
//$pdf->MultiCell(10,5,'',1,'L',0);
$pdf->SetXY($XMoyCL+29,$YMoyGE+1);  // placement du mot 
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML("<b>$noteFrancais</b>");

$pdf->SetXY($XMoyCL+50,$YMoyGE); // cadre scientifique
$pdf->MultiCell(33,5,'',1,'L',0);  
$pdf->SetXY($XMoyCL+50,$YMoyGE+1); // placement du mot
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("<b>Scientifique:</b>");


$pdf->SetXY($XMoyCL+55+15+34,$YMoyGE); // cadre note scientifique
//$pdf->MultiCell(10,5,'',1,'L',0);
$pdf->SetXY($XMoyCL+70,$YMoyGE+1); // placement du mot
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML("<b>$noteScientifique</b>");



$pdf->SetXY($XMoyCL+86,$YMoyGE); // cadre social
$pdf->MultiCell(33,5,'',1,'L',0);  
$pdf->SetXY($XMoyCL+86,$YMoyGE+1); // placement du mot
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("<b>Sociale:</b>");


$pdf->SetXY($XMoyCL+55+15+32,$YMoyGE); // cadre note social
//$pdf->MultiCell(10,5,'',1,'L',0);
$pdf->SetXY($XMoyCL+100,$YMoyGE+1); // placement du mot
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML("<b>$noteSocial</b>");

$pdf->SetXY($XMoyCL+86,$YMoyGE+6); // cadre Technique et Physique
$pdf->MultiCell(33,5,'',1,'L',0);  
$pdf->SetXY($XMoyCL+86,$YMoyGE+7); // placement du mot
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML("<b>Tech & Phys:</b>");


$pdf->SetXY($XMoyCL+55+15+32,$YMoyGE+6); // cadre note Technique et Physique
//$pdf->MultiCell(10,5,'',1,'L',0);
$pdf->SetXY($XMoyCL+105,$YMoyGE+7); // placement du mot
$pdf->SetFont('Arial','',10);
$pdf->WriteHTML("<b>$noteTechnique</b>");

// affichage de la moyenne generale eleve
$XmoyElValue=$LargeurMG + 27;
$YmoyElGenValue=$YmoyenneGeneral  + 2 ;
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$pdf->SetFont('Arial','',11);
$pdf->SetXY($XmoyElValue,$YmoyElGenValue);

$moyenEleveaff=$moyenEleve;

$pdf->SetFont('Arial','',11);
//$pdf->WriteHTML("<b>$moyenEleveaff<b>");
$oralMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
// fin affichage moy eleve


//affichage  du min et du max et moyenne general
if ($moyenClasseMin == 1000) {$moyenClasseMin="";}
if ($moyenClasseGen == 0) {$moyenClasseGen="";}
$moyenClasseGen=preg_replace('/\./',',',$moyenClasseGen);
$pdf->SetFont('Arial','',5);
$pdf->SetXY($XmoyClasseGValue+1,$YmoyClasseGValue-2);
$pdf->WriteHTML("classe");
$pdf->SetFont('Arial','',9);
$pdf->SetXY($XmoyClasseGValue-1,$YmoyClasseGValue+1);
$moyenClasseGenaff=$moyenClasseGen;
//$pdf->WriteHTML($moyenClasseGenaff);

$moyenClasseMinaff=$moyenClasseMin;
//$pdf->SetXY($XmoyClasseMinValue,$YmoyClasseMinValue);
//$pdf->WriteHTML($moyenClasseMinaff);

$moyenClasseMaxaff=$moyenClasseMax;
//$pdf->SetXY($XmoyClasseMaxValue,$YmoyClasseMaxValue);
//$pdf->WriteHTML($moyenClasseMaxaff);
// fin de la calcul de min et max



// fin affichage


// cadre apprciation

$Ycom=$YMoyGE + 21;//10
$EpaisCom=40;//30
$YcomP1=$Ycom + 1;
$YcomP2=$YcomP1 + 6;//10
$YcomP3=$YcomP2 + 5;
$YcomP4=$YcomP3 + 5;//lisaa
$pdf->SetFont('Arial','',8);

//$pdf->SetFillColor(220);/ pas de couleur de fond FM
$pdf->SetXY(10,$Ycom-5);
$pdf->MultiCell(190,$EpaisCom-6,'',1,'C',0);
$pdf->SetXY(13,$YcomP1-5);
$pdf->WriteHTML("<B>".$appreciation."</B>"."$com_visa_scolaire");
$pdf->WriteHTML("<B>".$appreciation2."</B>");
$pdf->SetXY(13,$YcomP2-5);
$pdf->WriteHTML($barre);
$pdf->SetXY(13,$YcomP3-5);
$pdf->SetXY(110,$YcomP4-5);
$pdf->MultiCell(120,3,$commentairegen,'','','L',0);

// commentaire direction
// ---------------------

$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
$commentairedirection=preg_replace('/\n/'," ",$commentairedirection);
$pdf->SetXY(13,$YcomP4-10);
$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(170,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)


// commentaire prof principal
$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
$pdf->SetXY(13,$YcomP4-10);
$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
$pdf->SetFont('Arial','',$confPolice[0]);
$pdf->MultiCell(140,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


//duplicata et signature
$YduplicaSign=$Ycom -5 + $EpaisCom;//+1
$pdf->SetFont('Arial','',7);
$pdf->SetXY(16,$YduplicaSign +5);//+0
//$pdf->SetFont('Arial','',6);
//$pdf->WriteHTML("* moy.des matires suivantes: ".$liste_litteraire." / ** moy.des matires suivantes: ".$liste_scientifique);
$pdf->SetFont('Arial','',8);
$pdf->SetXY(16,$YduplicaSign -6);//+0
$pdf->WriteHTML("<I>".$duplicata."</I>");
$pdf->SetFont('Arial','',8);
$pdf->SetXY(20,$YduplicaSign-20);
$pdf->WriteHTML($signature);
$pdf->SetFont('Arial','',7);
//$pdf->SetXY(16,$YduplicaSign );//+15
//$pdf->WriteHTML($signature2);

// fin duplicata




//FIN apprciation
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
        // alertJs("Bulletin cr -- Service Triade");
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
