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
if ($id != 1) {	set_time_limit(900); }

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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
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
validerequete("menuadmin");
$valeur=visu_affectation_detail($_POST["saisie_classe"]);
include_once("librairie_php/lib_brevet.php");
$serie=$_POST["type_colonne"];

if ($_POST["arrondi"] == 1) {
	$arrondi=1;
}else{
	$arrondi=0;
}

$option=$_POST["option"];
config_param_ajout($option,"optionname");

$viescolaire=$_POST["viescolaire"];
config_param_ajout($viescolaire,"viescolaire");

$examenEPS=$_POST["examenEPS"];
config_param_ajout($examenEPS,"examenEPS");

$examenPREV_SANTE_ENV=$_POST["examenPREV_SANTE_ENV"];
config_param_ajout($examenPREV_SANTE_ENV,"examenPREV_SANTE_ENV");

$recupNoteNotanet=$_POST["recupNoteNotanet"];
config_param_ajout($recupNoteNotanet,"recupNoteNotanet");


if (count($valeur)) {

	// recupe du nom de la classe
	$data=chercheClasse($_POST["saisie_classe"]);
	$idClasse=$data[0][0];
	$classe_nom=$data[0][1];

	$typeColonne=$_POST["type_colonne"];

	// recup année scolaire
	$anneeScolaire=date("Y");
	?>
	<ul>
	<font class="T2">
	      <?php print "Examen" ?> : <?php print "Brevet série collège" ?><br> <br>
	      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
	      <?php print LANGBULL29?> : <?php print date("Y") ?><br /><br />
	</font>
	</ul>

	<?php
	include_once('librairie_php/recupnoteperiode.php');

	// recuperation des coordonnées
	// de l etablissement
	$data=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement
	for($i=0;$i<count($data);$i++) {
	       $nom_etablissement=trim(TextNoAccent($data[$i][0]));
	       $adresse=trim($data[$i][1]);
	       $postal=trim($data[$i][2]);
	       $ville=ucfirst(trim($data[$i][3]));
	       $tel=trim($data[$i][4]);
	       $mail=trim($data[$i][5]);
	       $directeur=trim($data[$i][6]);
	       $accademie=trim($data[$i][8]);
	       $urlsite=trim($data[$i][7]);
	       $pays=trim($data[$i][9]);
	       $departement=trim($data[$i][10]);
	}
	// fin de la recup

	$dateDebut=recupDateDebutAnneeByClasse($idClasse);
	$dateFin=recupDateFinAnneeByClasse($idClasse);

	// creation PDF
	//
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');

	$pdf=new PDF();  // declaration du constructeur

	include_once('./librairie_pdf/fpdf_merge.php');
	$merge=new FPDF_Merge();

	$eleveT=recupEleve($idClasse); // recup liste eleve
	$nbEleveT=count($eleveT);
	for($j=0;$j<$nbEleveT;$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$nomEleve=ucwords($eleveT[$j][0]);
		$prenomEleve=ucfirst($eleveT[$j][1]);
		$lv1Eleve=$eleveT[$j][2];
		$lv2Eleve=$eleveT[$j][3];
		$idEleve=$eleveT[$j][4];
	
		$pdf->AddPage();
		$pdf->SetTitle("Brevet - $nomEleve $prenomEleve");
		$pdf->SetCreator("T.R.I.A.D.E.");
		$pdf->SetSubject("Brevet série collège"); 
		$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 


		// declaration variable
		$nomEleve=strtoupper(trim($nomEleve));
		$prenomEleve=ucfirst(trim($prenomEleve));
	
		$X=3;
		$Y=3;


		// Debut création PDF
		// Académie

		$policeSize=9;
		
		if ($option == "agricole") $option="Agricole";

		if (trim($hauteurlogo) == "") {
		        $hauteurlogo=25;
		        $largeurlogo=25;
		}


		$XX=20;
		$photo=recup_photo_bulletin();
		if (count($photo) > 0) {
			$logo="./data/image_pers/".$photo[0][0];
			if (file_exists($logo)) {
				$xlogo=$largeurlogo;
				$ylogo=$hauteurlogo;
				$X=35;$XX=0;
				$pdf->Image($logo,3,3,$xlogo,$ylogo);
			}
		}


		$pdf->SetFont('Arial','',$policeSize);	
		$pdf->SetXY($X,$Y);
		$pdf->WriteHTML("Académie de $accademie ");
		$pdf->SetXY($X,$Y+=5);
		$pdf->WriteHTML("Département : $departement");
		$pdf->SetXY($X,$Y+=5);
		$pdf->WriteHTML("FICHE SCOLAIRE-RELEVE ET PROCES VERBAL DE NOTES");
		$pdf->SetXY($X,$Y+=5);
		$pdf->WriteHTML("Série : PROFESSIONNELLE");
		$pdf->SetXY($X,$Y+=5);
		$pdf->WriteHTML("Option : $option");

		
		//fin coordonnees



		$pdf->SetXY($X+100+$XX,$Y=8);
		$pdf->SetFont('Arial','B',$policeSize+4);
		$pdf->MultiCell(70,3,"FICHE SCOLAIRE BREVET",0,'C',0);		
		$pdf->SetXY($X+100+$XX,$Y+5);
		$pdf->SetFont('Arial','B',$policeSize+2);
		$pdf->MultiCell(70,3,"- session $anneeScolaire -",0,'C',0);		
		$pdf->SetXY($X+65,$Y+=7+10);
		$pdf->SetFont('Arial','B',$policeSize+3);


		$pdf->SetXY($X=4,$Y);
		
		$pdf->SetFillColor(230,230,255);
		$pdf->RoundedRect($X, $Y+5, 90, 33, 3.5, 'DF');
		$pdf->SetFillColor(255);

		$pdf->SetXY($X+2,$Y+5+2);

		$pdf->SetFont('Arial','',$policeSize);
//		$pdf->setTextColor(51,153,102);
		$pdf->MultiCell(100,5,"Etablissement : $nom_etablissement \n$adresse \n$ville -  $postal \n$tel $mail  ",0,'L',0);
		$pdf->setTextColor(0);

		// adresse de l'élève
		// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance
		$dataadresse=chercheadresse($idEleve);
		$nomtuteur=$dataadresse[0][1];
		$prenomtuteur=$dataadresse[0][2];	
		$adr1=$dataadresse[0][3];
		$code_post_adr1=$dataadresse[0][4];
		$commune_adr1=$dataadresse[0][5];	
		$numero_eleve=$dataadresse[0][9];
		$datenaissance=$dataadresse[0][11];
		if ($datenaissance != "") {  $datenaissance=dateForm($datenaissance); }
		$regime=$dataadresse[0][12];
		$class_ant=trim(trunchaine($dataadresse[0][10],20));
		$lieu_naissance=ucfirst($dataadresse[0][19]);
		$INE=$dataadresse[0][9];


		$pdf->SetFillColor(230,230,255);
		$pdf->RoundedRect($X+105, $Y+=5, 95, 33, 3.5, 'DF');
		$pdf->SetFillColor(255);


		$pdf->SetFont('Arial','',$policeSize);
		$pdf->SetXY($X+113,$Y+=2);
		$pdf->MultiCell(65,5,"NOM : $nomEleve ",0,'L',0);
		$pdf->SetXY($X+113,$Y+=5);
		$pdf->MultiCell(65,5,"Prénom : $prenomEleve ",0,'L',0);
		$pdf->SetXY($X+113,$Y+=5);
		$pdf->MultiCell(65,5,"INE : $INE ",0,'L',0);
		$pdf->SetXY($X+113,$Y+=5);
		$pdf->MultiCell(65,5,"né(e) le $datenaissance ",0,'L',0);
		$pdf->SetXY($X+113,$Y+=5);
		$pdf->MultiCell(65,5,"à $lieu_naissance",0,'L',0);
		$pdf->SetXY($X+113,$Y+=5);
		$pdf->MultiCell(65,5,"redoublant(e) :       oui            non ",0,'L',0);
		$pdf->SetXY($X+137,$Y+1.5);
		$pdf->MultiCell(2,2,"",1,'C',0);  // case redoublant
		$pdf->SetXY($X+152,$Y+1.5);
		$pdf->MultiCell(2,2,"",1,'C',0);  // case non redoublant


		// ---------------------------------------------------

		$Y+=15;
		$X=3;
/*LARGEUR MAT*/ $largeurMatiere=60;
		$largeurMoy=15;
		$largeurAppreciation=95;

		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',$policeSize);
		$pdf->MultiCell($largeurMatiere,18,"DISCIPLINES",1,'C',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','',$policeSize-1);
		$pdf->MultiCell($largeurMoy,18,"",1,'C',0);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell($largeurMoy,3,"\nmoyenne\nde la classe",0,'C',0);
		$pdf->SetFont('Arial','',$policeSize-1);
		$pdf->SetXY($X+=$largeurMoy,$Y);
		$pdf->MultiCell($largeurMoy,18,"",1,'C',0);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell($largeurMoy,3,"\nnote\nl'élève sur 20",0,'C',0);

		$pdf->SetXY($X+=$largeurMoy,$Y);
		$pdf->MultiCell($largeurAppreciation,18,"Appréciations des professeurs",1,'C',0);

		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,18,"",1,'C',0);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell($largeurMoy,3,"\nnote de CC\navec coefficient",0,'C',0);


		// ---------------------------------------------------

		$Y+=18;
		$X=3;
/*Hauteur Mat*/	$hauteurMatiere=11.5;
		$policeSizeMatiere=$policeSize-1;


		// FRANCAIS //
		// -------- //
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"FRANCAIS",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Français",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Français",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Français");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}


		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") {  $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);

		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //


		// MATHEMATIQUES //
		// ------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"MATHEMATIQUES",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);	

		$tab=rechercheMatiereBrevet("Mathématiques",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Mathématiques",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Mathematiques");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}
		
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ; 
			if ($arrondi) { $note=arrondiAuDemi($note); }      
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //



		

		// LANGUE VIVANTE 1 //
		// --------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"LANGUE VIVANTE 1 : ANGLAIS",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Langue vivante 1",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if (!verifMatiereLangue($idEleve,$idMatiere,'LV1',$idClasse)) { continue; } 
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Langue vivante 1",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"lv1");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
		

		// PREVENTION SANTE ENVIRONNEMENT //
		// ------------------------------ //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"PREVENTION SANTE ENVIRONNEMENT",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Prévention Santé",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if ($examenPREV_SANTE_ENV == "oui") {
				if ( "biologie" == strtolower(chercheMatiereNomBrevet($idMatiere))) {
					$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,"Brevet PREV. SANTE ENV.");
				}else{
					$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
				}
			}else{
				$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			}
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Prévention Santé",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0 ) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"preventionsante");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") {
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if ($examenPREV_SANTE_ENV == "oui") {
				if ( "biologie" == strtolower(chercheMatiereNomBrevet($idMatiere))) {
					$note=moyeMatGenBrevetExamen($idMatiere,$dateDebut,$dateFin,$idClasse,"Brevet PREV. SANTE ENV.");
				}else{
					$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
				}
			}else{
				$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			}
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$codeEpreuve=recupCodeEpreuve($serie,"prevsantenv");
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
		

		// PREVENTION SANTE ET ENVIRONNEMENT //
		// --------------------------------- //
/*		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"ECONOMIE FAMILIALE ET SOCIALE",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Prevention Sante Environnement",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Prevention Sante Environnement",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		$note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"prevsantenv");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ; 
			if ($arrondi) { $note=arrondiAuDemi($note); }       
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> *///	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> *///	$pdf->SetXY($X+=$largeurMoy,$Y);

/*
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
 */		

		// EDUCATION PHYSIQUE ET SPORTIVE //
		// ------------------------------ //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"EDUCATION PHYSIQUE ET SPORTIVE",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Education physique et sportive",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if ($examenEPS == "oui") {
				$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,"Brevet EPS");
			}else{
				$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			}
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Education physique et sportive",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"eps");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if ($examenEPS == "oui") {
				$note=moyeMatGenBrevetExamen($idMatiere,$dateDebut,$dateFin,$idClasse,"Brevet EPS");
			}else{
				$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			}
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
		

		// Education SOCIOCULTURELLE //
		// ------------------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"EDUCATION SOCIOCULTURELLE",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Education Socioculturelle",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Education Socioculturelle",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"EducationSocio");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
				
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);


		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
		

		// Sciences biologiques //
		// ------------------- //
/*
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"SCIENCES BIOLOGIQUES",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Sciences Biologiques",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Sciences Biologiques",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		$note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"SciencesBio");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);
	
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
 */	
		// TECHNOLOGIQUE   //
		// -------------  //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"",1,'L',0);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell($largeurMatiere,3,"TECHNOLOGIE SCIENCES ET DECOUVERTE DE LA VIE PROFESSIONNELLE",0,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Techno Secteur Agricoles",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Techno Secteur Agricoles",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$note=$note*3;
		$codeEpreuve=recupCodeEpreuve($serie,"TechnoAgricole");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;  
			$note = $note * 3 ;
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 60",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);
	
/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 60",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 60",1,'R',0);
		// -------- //

		// Vie scolaire  //
		// ------------- //
if ($viescolaire == 1) {
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"VIE SCOLAIRE",1,'L',0);
		
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		$moyenMatiere="";
		$note=calculNoteVieScolaireBrevet($idEleve,$idClasse);

		$codeEpreuve=recupCodeEpreuve($serie,"viescolaire");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 	
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		$note=moyeMatGenVieScolaireBrevet($idClasse);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
		
		if ($noteT != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }       
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		// -------- //
}

		// OPTION  //
		// ------- //
/*
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere-1);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"OPTION FACULTATIVE Langue Régionale",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);		
	
		$tab=rechercheMatiereBrevet("",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if (!verifMatiereLangue($idEleve,$idMatiere,'OPT',$idClasse)) { continue; } 
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		$note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
			$noteGlobal=$noteGlobal+$moyenMatiere;  
		}

		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note ",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);
	
/* --> */	//$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve ",1,'R',0);
/* --> */	//$pdf->SetXY($X+=$largeurMoy,$Y);
/*
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere ",1,'R',0);
 */


		// -------- //
		// HISTOIRE GEOGRAPHIE //
		// ------------------- //

		$X=3;$Y+=5;
		$pdf->SetFillColor(230,230,255);
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,5,"Pour information : ",1,'L',1);
		$Y+=5;
		$pdf->SetFillColor(255);


		$X=3;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"HIST. GEOGRAPHIE / EDUC. CIVIQUE",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);	

		$tab=rechercheMatiereBrevet("Histoire - Géographie - Civique",$idClasse);
		$nb=0;$noteT="";$note="";$moyenMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { 
                                $coef=recupCoefBrevet($idClasse,"Histoire - Géographie - Civique",$idMatiere);
                                $noteT=$noteT + ($note*$coef);
                                $nb+=$coef;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"histoireGeo");
		if ($recupNoteNotanet == 1) $moyenMatiere=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			$note=arrondiaudixieme($note);
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			
			$moyenMatiere=$note;
			if ($arrondi) { $moyenMatiere=arrondiAuDemi($note); }
	                $moyenMatiere=number_format($moyenMatiere,2,'.','');
			if ($moyenMatiere < 10) { $moyenMatiere="0".$moyenMatiere; }
		//	$noteGlobal=$noteGlobal+$moyenMatiere;  
		}
		
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$noteEleve=$note;
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($noteT != "") { 
			$note = $noteT / $nb ; 
			if ($arrondi) { $note=arrondiAuDemi($note); }      
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=$largeurMoy,$Y);

/* --> */	$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$noteEleve / 20",1,'R',0);
/* --> */	$pdf->SetXY($X+=$largeurMoy,$Y);

		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X+=$largeurAppreciation,$Y);
		//$pdf->MultiCell($largeurMoy,$hauteurMatiere,"$moyenMatiere / 20",1,'R',0);
		$pdf->MultiCell($largeurMoy,$hauteurMatiere,"",1,'R',0);
		// -------- //

		
	
		$Y+=10;
		$YAVIS=$Y;
		
		$pdf->SetFont('Arial','',$policeSizeMatiere);
	
		$pdf->SetXY($X-$largeurAppreciation-5,$Y+=3);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"Total : ",0,'R',0);

		$pdf->SetFillColor(230,230,255);
		$pdf->SetXY($X-5,$Y);
		$totalP=200;
		if ($viescolaire != 1) $totalP-=20;
		$pdf->MultiCell(20,10," $noteGlobal / $totalP",1,'R',1); 
		$noteGlobal="";

		$X=3;
		$pdf->SetXY($X,$Y+=12);
		$pdf->MultiCell(200,10," ",1,'R',0); 

		$pdf->SetXY($X+50,$Y+2);
		$pdf->MultiCell(5,5,'',1,'R',0); 
		$pdf->SetXY($X+110,$Y+2);
		$pdf->MultiCell(5,5,'',1,'R',0); 



		$pdf->SetXY($X+3,$Y+3);
		$pdf->MultiCell(200,3,"Avis du conseil de classe :                         Doit faire ses preuves                                         Avis favorable",0,'L',0); 

		$X=3;
		$pdf->SetXY($X,$Y+=15);
		$pdf->MultiCell(200,23," ",1,'R',0); 
		$pdf->SetXY($X+3,$Y+3);
		$pdf->MultiCell(200,3,"Le chef de l'établissement certifie l'exactitude de résultats de la classe de $classe_nom portés ci-dessus. \n\nSignature :",0,'L',0); 


		// ---------------------------------------------------
		// fin duplicata
		// ----------------------------------------------------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$nomEleve=TextNoCarac($nomEleve);
$nomEleve=TextNoAccent($nomEleve);
$prenomEleve=TextNoCarac($prenomEleve);
$prenomEleve=TextNoAccent($prenomEleve);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$classe_nom=preg_replace('/ /',"_",$classe_nom);
$nomEleve=preg_replace('/ /',"_",$nomEleve);
$nomEleve=preg_replace('/\//',"_",$nomEleve);
$prenomEleve=preg_replace('/ /',"_",$prenomEleve);
$prenomEleve=preg_replace('/\//',"_",$prenomEleve);
if (!is_dir("./data/pdf_bull/brevet_$classe_nom")) { mkdir("./data/pdf_bull/brevet_$classe_nom"); }
$fichier=urlencode($fichier);
$fichier="./data/pdf_bull/brevet_$classe_nom/brevet_college_".$nomEleve."_".$prenomEleve."_".date("Y").".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage("Brevet Collège",$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
$merge->add("$fichier");
$pdf=new PDF();
} // fin du for on passe à l'eleve suivant
$merge->output("./data/pdf_bull/brevet_$classe_nom/liste_complete.pdf");
include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/brevet_'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/brevet_'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/brevet_'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/brevet_'.$classe_nom.'.zip';
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
	history_cmd($_SESSION["nom"],"CREATION BREVET COLLEGE","Classe : $classe_nom");
	Pgclose();
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
Pgclose();
?>
