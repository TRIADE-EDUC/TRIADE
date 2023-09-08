<?php
session_start();
//error_reporting(0);
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
$valeur=visu_affectation_detail($_POST["saisie_classe"],$_COOKIE["anneeScolaire"]);


$matsvt=$_POST["matsvt"];
$matphychi=$_POST["matphychi"];
$matmusic=$_POST["matmusic"];
$viescolaire=$_POST["viescolaire"];
$recupNoteNotanet=$_POST["recupNoteNotanet"];
if ($cadredubas == "") $cadredubas=0;
config_param_ajout($matsvt,"matsvt");
config_param_ajout($matphychi,"matphychi");
config_param_ajout($matmusic,"matmusic");
config_param_ajout($viescolaire,"viescolaire");
config_param_ajout($recupNoteNotanet,"recupNoteNotanet");

$datap=config_param_visu("matsvt");
$matsvt=$datap[0][0];
$datap=config_param_visu("matphychi");
$matphychi=$datap[0][0];
$datap=config_param_visu("matmusic");
$matmusic=$datap[0][0];
$datap=config_param_visu("viescolaire");
$viescolaire=$datap[0][0];

if ($_POST["arrondi"] == 1) {
	$arrondi=1;
}else{
	$arrondi=0;
}

if ($_POST["noteformat"] == 1) {
	$notesur20="oui";
}else{
	$notesur20="non";
}

include_once("librairie_php/lib_brevet.php");
$serie=$_POST["type_colonne"];
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
	      <?php print LANGBULL29?> : <?php print $_COOKIE["anneeScolaire"] ?><br /><br />
	</font>
	</ul>

<?php
	include_once('librairie_php/recupnoteperiode.php');
	// recuperation des coordonnées
	// de l etablissement
	$data=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement
	for($i=0;$i<count($data);$i++) {
	       $nom_etablissement=trim(TextNoAccent($data[$i][0]));
	       if (PRODUCTID == "e9fcc898aa89f8a6aac1cd7492abdf33") { $nom_etablissement="Lycée VAUBAN"; }
	       $adresse=trim($data[$i][1]);
	       $postal=trim($data[$i][2]);
	       $ville=trim($data[$i][3]);
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

	include_once('./librairie_pdf/fpdf_merge.php');
	$merge=new FPDF_Merge();

	$pdf=new PDF();  // declaration du constructeur

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
		$prenomEleve=trim($prenomEleve);
	
		$X=3;
		$Y=3;


		// Debut création PDF
		// Académie

		$policeSize=9;

		$pdf->SetFont('Arial','',$policeSize);	

		$pdf->SetXY($X,$Y);
		$pdf->WriteHTML("Académie de $accademie ");
		$pdf->SetXY($X,$Y+5);
		$pdf->WriteHTML("Département : $departement");

		$pdf->SetXY($X,$Y+10+1);
		$pdf->MultiCell(60,22.5,'',1,'L',0);
		$pdf->SetXY($X,$Y+10+2);
		$pdf->SetFont('Arial','',$policeSize-3);
		$pdf->MultiCell(50,3,"Etablissement : $nom_etablissement \n $adresse \n $ville - $postal \n $tel $mail  ",0,'L',0);
		
		//fin coordonnees


		$pdf->SetXY($X+65,$Y+7);
		$pdf->SetFont('Arial','B',$policeSize+4);
		$pdf->MultiCell(70,3,"FICHE SCOLAIRE BREVET",0,'C',0);		
		$pdf->SetXY($X+65,$Y+7+5);
		$pdf->SetFont('Arial','B',$policeSize+2);
		$pdf->MultiCell(70,3,"- session $anneeScolaire -",0,'C',0);		
		$pdf->SetXY($X+65,$Y+7+10);
		$pdf->SetFont('Arial','B',$policeSize+3);
		$pdf->MultiCell(70,3,"série Pro.",0,'C',0);		
	
		// adresse de l'élève
		// elev_id, nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, 
		// commune_adr2, numeroEleve, class_ant, date_naissance, regime, civ_1, civ_2
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
		$INE=$dataadresse[0][9];	

		$pdf->SetXY($X+140,$Y);
		$pdf->SetFont('Arial','',$policeSize);
		$pdf->MultiCell(65,5,"NOM : $nomEleve ",1,'L',0);
		$pdf->SetXY($X+140,$Y+=5);
		$pdf->MultiCell(65,5,"Prénom : $prenomEleve ",1,'L',0);
		$pdf->SetXY($X+140,$Y+=5);
		$pdf->MultiCell(65,5,"INE : $INE ",1,'L',0);
		$pdf->SetXY($X+140,$Y+=5);
		$pdf->MultiCell(65,5,"né(e) le $datenaissance ",1,'L',0);
		$pdf->SetXY($X+140,$Y+=5);
		$pdf->MultiCell(65,5,"à ",1,'L',0);
		$pdf->SetXY($X+140,$Y+=5);
		$pdf->MultiCell(65,5,"redoublant(e) :  oui          non         (*)",1,'L',0);
		$pdf->SetXY($X+170,$Y+1.5);
		$pdf->MultiCell(2,2,"",1,'C',0);  // case redoublant
		$pdf->SetXY($X+185,$Y+1.5);
		$pdf->MultiCell(2,2,"",1,'C',0);  // case non redoublant


		// ---------------------------------------------------

		$Y+=10;
/*LARGEUR MAT*/ $largeurMatiere=60;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',$policeSize);
		$pdf->MultiCell($largeurMatiere,20,"DISCIPLINES",1,'C',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','',$policeSize-2);
		$pdf->MultiCell(30,8,"",1,'C',0);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell(30,3,"NOTE MOYENNE\naffectée du coefficient",0,'C',0);

		$pdf->SetXY($X,$Y+8);
		$pdf->SetFont('Arial','B',$policeSize-2);
		$pdf->MultiCell(30,5,"3ème à option",1,'C',0);

		$pdf->SetXY($X,$Y+13);
		$pdf->SetFont('Arial','',$policeSize);
		$pdf->MultiCell(30,7,"DP6h",1,'C',0);

		$pdf->SetFont('Arial','',$policeSize-1);
		$pdf->SetXY($X+=30,$Y);
		$pdf->MultiCell(20,20,"",1,'C',0);
		$pdf->SetXY($X,$Y+1);
		$pdf->MultiCell(20,3,"\nNote\nmoyenne\nde la\nclasse",0,'C',0);

		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell(95,20,"Appréciations des professeurs",1,'C',0);

		// ---------------------------------------------------

		$Y+=20;
		$X=3;
		$largeurAppreciation=95;
/*Hauteur Mat*/	$hauteurMatiere=9.2;
		$policeSizeMatiere=$policeSize+1;


		// FRANCAIS //
		// -------- //
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Français",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Français",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Français");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);

		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}

//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") {  $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		
		// -------- //

		// MATHEMATIQUES //
		// ------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Mathématiques",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);	

		$tab=rechercheMatiereBrevet("Mathématiques",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Mathematiques");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ; 
			if ($arrondi) { $note=arrondiAuDemi($note); }      
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //

		// LANGUE VIVANTE 1 //
		// ---------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Langue vivante 1 : ",1,'L',0);

		
		$tab=rechercheMatiereBrevet("Langue vivante 1",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if (!verifMatiereLangue($idEleve,$idMatiere,'LV1',$idClasse)) { continue; } 
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			
			if ($note != "") { 
				$noteT=$noteT + $note; $nb++; 
				$nomMatiere=chercheMatiereNomBrevet($idMatiere);
				$pdf->SetXY($X+30,$Y);
				$pdf->MultiCell(30,$hauteurMatiere,"$nomMatiere",0,'L',0);
				$tabIdMatiereGlobalLV1[]=$idMatiere;
			}
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"lv1");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") {
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
		$pdf->SetXY($X+=$largeurMatiere,$Y);
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";$okMat=0;
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			foreach($tabIdMatiereGlobalLV1 as $key=>$value) {
				if ($idMatiere == $value) {
					$okMat=1;	
				}
			}
			if ($okMat == 1) {
				$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
				if ($note != "") { 
					$noteT=$noteT + $note; $nb++; 
				}
			}
		}

		$tabIdMatiereGlobalLV1="";
		if ($note != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }		      
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //

		// SVT //
		// --- //
if ($matsvt == 1) {
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Sciences de la vie et de la terre",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("SVT",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"SVT");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
}

		// Physique-chimie //
		// --------------- //
if ($matphychi == 1) {
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Physique-chimie",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Physique - Chimie",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"physChimi");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") {
			if ($arrondi) { $note=arrondiAuDemi($note); }	
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
}
		// -------- //
		

		// Education physique et sportive //
		// ------------------------------ //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Education physique et sportive",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Education physique et sportive",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"eps");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ; 
			if ($arrondi) { $note=arrondiAuDemi($note); }       
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
		

		// Arts platisque //
		// -------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Arts plastiques",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Arts plastiques",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Arts plastiques");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
		

		// Education musicale //
		// ------------------ //
if ($matmusic == 1) {
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Education musicale",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Education musicale",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Education musicale");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note; 
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
}
		// -------- //
		

		// Technologie //
		// ----------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Technologie",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("Technologique",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if (($notesur20 == "oui")&&($note != "")) { $note=$note * 2; }
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"Technologique");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 40",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if (($notesur20 == "oui")&&($note != "")) { $note=$note * 2; }
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 40",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
		
		// Histoire des arts //
		// ----------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Histoire des arts",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		
		$tab=rechercheMatiereBrevet("histoire des arts",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if (($notesur20 == "oui")&&($note != "")) { $note=$note * 2; }
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($nb > 0) $note = $noteT / $nb ;
		$codeEpreuve=recupCodeEpreuve($serie,"histoire des arts");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note;  
		}	
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 40",1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if (($notesur20 == "oui")&&($note != "")) { $note=$note * 2; }
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');	
			if ($note < 10) { $note="0".$note; } 
		}

		$pdf->MultiCell(20,$hauteurMatiere,"$note / 40",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
		

		// LANGUE VIVANTE 2 //
		// ---------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Langue vivante 2 :",1,'L',0);

		
		$tab=rechercheMatiereBrevet("langue vivante 2",$idClasse);
		$nb=0;$noteT="";$note="";$nomMatiere="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if (!verifMatiereLangue($idEleve,$idMatiere,'LV2',$idClasse)) { continue; } 
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		
			if ($note != "") { 
				$noteT=$noteT + $note; $nb++; 
				if ($nomMatiere == "") {
					$nomMatiere=chercheMatiereNomBrevet($idMatiere);
					$pdf->SetXY($X+30,$Y);
					$nomMatiere=trunchaine($nomMatiere,13);
					$pdf->MultiCell(30,$hauteurMatiere,"$nomMatiere",0,'L',0);
				}
				$tabIdMatiereGlobalLV2[]=$idMatiere;
				
			}

		}



		if ($nb != 0) { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}	
		$codeEpreuve=recupCodeEpreuve($serie,"LV2");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);

		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteGlobal=$noteGlobal+$note; 
			$noteDP6H="";
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->SetFillColor(220);
		$pdf->MultiCell(30,$hauteurMatiere,"     ",1,'L',1);
		$pdf->SetFillColor(255);
		$pdf->SetXY($X+=30,$Y);
		
		$nb=0;$noteT="";$note="";$okMat=0;
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			foreach($tabIdMatiereGlobalLV2 as $key=>$value) {
				if ($idMatiere == $value) {
					$okMat=1;	
				}
			}
			if ($okMat == 1) {
				$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
				if ($note != "") { $noteT=$noteT + $note; $nb++; }
			}
		}
		if ($note != "") { 
			$note = $noteT / $nb ;  
			if ($arrondi) { $note=arrondiAuDemi($note); } 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}
		$tabIdMatiereGlobalLV2="";
		if ($typeColonne == "LV2") {
			$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		}
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
		

		// Vie scolaire  //
		// ------------- //
if ($viescolaire == 1) {
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Vie scolaire",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		$note=calculNoteVieScolaireBrevet($idEleve,$idClasse);
		$codeEpreuve=recupCodeEpreuve($serie,"viescolaire");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if ($note != "") { 
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
			$noteGlobal=$noteGlobal+$note; 
		}
		if ($typeColonne == "LV2") {
			$noteLV2=$note;
			$noteDP6H="";		
		}else{
			$noteDP6H=$note;
			$noteLV2="";
		}
//		$pdf->MultiCell(15,$hauteurMatiere,"$noteLV2 / 20",1,'R',0);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$noteDP6H / 20",1,'C',0);

		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenVieScolaireBrevet($idClasse);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") { 
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }       
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}
	
		$pdf->SetXY($X+=30,$Y);
		$pdf->MultiCell(20,$hauteurMatiere,"$note / 20",1,'R',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);
		// -------- //
}



		// Découverte professionnelle 6 heures //
		// ----------------------------------- //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"Découverte professionnelle 6 heures",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$note="";
	if ($typeColonne != "LV2") {
		$tab=rechercheMatiereBrevet("Découverte professionnelle 6h",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if (($notesur20 == "oui")&&($note != "")) { $note=$note * 3; }
			if ($note != "") {
				if (PRODUCTID == "e9fcc898aa89f8a6aac1cd7492abdf33") {
					if ($note > $noteT) {
						$noteT=$note;
						$nb=1;
					}
				}else{
					$noteT=$noteT + $note;
					$nb++;
				}
			}
		}
		if ($nb != 0) {	$note = $noteT / $nb ; }
		if ($note != "") {
			if ($arrondi) { $note=arrondiAuDemi($note); }
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}
	}
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		
		if ($typeColonne != "LV2") {
			$noteGlobal=$noteGlobal+$note; 
		}
	
		$pdf->SetFillColor(220);
//		$pdf->MultiCell(15,$hauteurMatiere,"",1,'R',1);
		$pdf->SetFillColor(255);
//		$pdf->SetXY($X+=15,$Y);
		$pdf->MultiCell(30,$hauteurMatiere,"$note / 60",1,'C',0);

	if ($typeColonne != "LV2") {
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyeMatGenBrevet($idMatiere,$dateDebut,$dateFin,$idClasse);
			if (($notesur20 == "oui")&&($note != "")) { $note=$note * 3; }
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		if ($note != "") {
			$note = $noteT / $nb ;
			if ($arrondi) { $note=arrondiAuDemi($note); }       
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}
	}
		$codeEpreuve=recupCodeEpreuve($serie,"DP6h");
		if ($recupNoteNotanet == 1) $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);

		$pdf->SetXY($X+=30,$Y);
		if ($typeColonne != "LV2") {
			$pdf->MultiCell(20,$hauteurMatiere,"$note / 60",1,'R',0);
		}
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		$commentaire="";
		$commentaire=preg_replace('/\n/'," ",recupCommBrevet($codeEpreuve,$idEleve));
		$pdf->SetXY($X+1,$Y+1);
		$pdf->MultiCell($largeurAppreciation,3,"$commentaire",0,'L',0);

		// -------- //


		// A titre indicatif : 
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','B',$policeSizeMatiere);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"A titre indicatif :",1,'L',0);
		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','B',$policeSizeMatiere-2);
		$pdf->MultiCell(30,$hauteurMatiere,"TOTAL DES POINTS" ,1,'C',0);
		$pdf->SetXY($X+=30,$Y);
		$pdf->MultiCell(20,$hauteurMatiere,"     ",1,'C',0);
		$pdf->SetXY($X+=20,$Y);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"     ",1,'L',0);
		

		// Histoire-géographie //
		// ------------------ //
		$X=3;
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$tab=rechercheMatiereBrevet("Histoire - Géographie",$idClasse);
		$nb=0;$noteT="";$note="";
		for($i=0;$i<count($tab);$i++) {
			$idMatiere=$tab[$i][0];
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($note != "") { $noteT=$noteT + $note; $nb++; }
		}
		$note = $noteT / $nb ;
		if ($note != "") {
			if ($arrondi) { $note=arrondiAuDemi($note); }
			//$noteGlobal=$noteGlobal+$note; 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; } 
		}	

		$pdf->MultiCell($largeurMatiere,$hauteurMatiere*2,"",1,'L',0);
		$pdf->SetXY($X,$Y+5);
		$pdf->MultiCell($largeurMatiere,3,"Histoire-géographie : ",0,'L',0);
		$pdf->SetXY($X,$Y+9);
		$pdf->MultiCell($largeurMatiere,3,"et éducation civique  ",0,'L',0);
		$pdf->SetXY($X+40,$Y+5);
		$pdf->SetFont('Arial','',$policeSizeMatiere-2);
		$pdf->MultiCell($largeurMatiere,$hauteurMatiere,"$note/20",0,'L',0);


		if ($typeColonne == "LV2") {
			$noteGlobalLV2=$noteGlobal;
			$noteGlobalDP6H="";
		}else{
			$noteGlobalDP6H=$noteGlobal;
			$noteGlobalLV2="";
		}
		$noteGlobal="";

		$pdf->SetXY($X+=$largeurMatiere,$Y);
		$pdf->SetFont('Arial','B',$policeSizeMatiere);

		$pdf->MultiCell(30,$hauteurMatiere*2,"$noteGlobalDP6H",1,'C',0);  // DP6h
		$pdf->SetXY($X+2,$Y+3.5);
		$pdf->SetFont('Arial','B',$policeSizeMatiere-2);

		$totalP=240;
		if ($viescolaire != 1) {
			$totalP-=20;
		}

		$pdf->MultiCell(30,$hauteurMatiere*2,"   /$totalP",0,'C',0);

		$pdf->SetFont('Arial','B',$policeSizeMatiere);
		$pdf->SetXY($X+=30,$Y);
		$pdf->MultiCell(20,$hauteurMatiere*2,"",1,'C',0);  // rien 
		$pdf->SetXY($X+=20,$Y);
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere*2,"  ",1,'L',0);   // commentaire histoire géo
		$pdf->SetXY($X,$Y+=$hauteurMatiere);
//		$pdf->MultiCell($largeurAppreciation,$hauteurMatiere,"  ",1,'L',0);   // commentaire Education civique
		// -------- //
		
		$X=3;
		$Y+=$hauteurMatiere;
		$YAVIS=$Y;
		$pdf->SetFont('Arial','',$policeSizeMatiere);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(205,8," ",1,'R',0);
		$pdf->SetXY($X,$Y+1);		
		$pdf->MultiCell(205,3,"Le chef d'établissement atteste : - Attestation de maîtrise des connaissances et compétences : ",0,'L',0);
		$pdf->SetXY($X+51.5,$Y+4.5);		

		$pdf->SetXY($X+150,$Y+1);		
		$pdf->MultiCell(2,2,"",1,'R',0);
		$pdf->SetXY($X+150+3,$Y+0.5);		
		$pdf->MultiCell(10,3,"oui",0,'L',0);
		$pdf->SetXY($X+150+10,$Y+1);		
		$pdf->MultiCell(2,2,"",1,'R',0);
		$pdf->SetXY($X+150+13,$Y+0.5);		
		$pdf->MultiCell(10,3,"non",0,'L',0);

		$pdf->SetXY($X,$Y+=8);		
		$pdf->MultiCell(205,43," ",1,'R',0); 
		$pdf->SetXY($X+3,$Y+=2);		
		$pdf->SetFont('Arial','',$policeSizeMatiere+2);
		$pdf->MultiCell(205,3,"Le chef d'établissement ",0,'L',0);
		
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
// ---------------------------------------------------------------------------------------------------------------------------------------------------
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
