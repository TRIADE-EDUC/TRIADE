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
      <?php print LANGBULL27?> : <?php print ucwords($textTrimestre)?><br> <br>
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



// recherche des dates de debut et fin
//$dateRecup=recupDateTrim("trimestre1");
$dateRecup=recupDateTrimByIdclasse("trimestre1",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT1=dateForm($dateDebut);
$dateFinT1=dateForm($dateFin);

$dateRecup=recupDateTrimByIdclasse("trimestre2",$_POST["saisie_classe"]);
//$dateRecup=recupDateTrim("trimestre2");
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT2=dateForm($dateDebut);
$dateFinT2=dateForm($dateFin);

//$dateRecup=recupDateTrim("trimestre3");
$dateRecup=recupDateTrimByIdclasse("trimestre3",$_POST["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT3=dateForm($dateDebut);
$dateFinT3=dateForm($dateFin);


$idClasse=$_POST["saisie_classe"];
$ordre=ordre_matiere_visubull($_POST["saisie_classe"]); // recup ordre matiere
// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
//$pdf=new PDF('P','mm','Legal');

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve
$effectif=count($eleveT);

$hautclassant=0;
$profptab=rechercheprofpMulti($_POST["saisie_classe"]); // idprof,idclasse
for($j=0;$j<count($profptab);$j++) {
	$profp1=recherche_personne2($profptab[$j][0]);
	$profp1=trunchaine($profp1,14);
	$profp[$j]=$profp1;
}
$profp=implode(', ',$profp);



if (count($profptab) > 1) { $hautclassant=1.5; }

for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	// variable eleve
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];
	$nbrtdnj=0;
	$nbabsdnj=0;
	//-------------------------------------//
	// recherche le nombre de retard et abs
	$nbretard=0;
	$nbabs=0;
	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebutT1),dateFormBase($dateFinT1)); // ideleve,debutdate,findate
	$nbretardT1=count($nbretard);
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebutT1),dateFormBase($dateFinT1)); // ideleve,debutdate,findate
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$duree_heureT1=0;
	$nbabsT1=0;
	for($o=0;$o<count($nbabs);$o++) {
		if (preg_match('/inconnu/i',$nbabs[$o][6])) { continue; }
		if ($nbabs[$o][4] > 0) {
			if (preg_match('/\.5$/',$nbabs[$o][4])) { 
				$nbabs[$o][4] = $nbabs[$o][4] - 0.5 ;  
				$nbabs[$o][4] = $nbabs[$o][4] * 2 ; 
				$nbabs[$o][4] = $nbabs[$o][4] + 1;   
			}else{
				$nbabs[$o][4] = $nbabs[$o][4] * 2 ;
			}
			$nbabsT1+=$nbabs[$o][4];
		}else{
			$duree_heureT1+=$nbabs[$o][7];
		}
	}
	if ($duree_heureT1 >= 3) { $duree_heureT1 = $duree_heureT1 / 3 ;  $duree_heureT1=preg_replace('/\..*$/','',$duree_heureT1); $nbabsT1 += $duree_heureT1 ; }

	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebutT2),dateFormBase($dateFinT2)); // ideleve,debutdate,findate
	$nbretardT2=count($nbretard);
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebutT2),dateFormBase($dateFinT2)); // ideleve,debutdate,findate
	$duree_heureT2=0;
	$nbabsT2=0;
	for($o=0;$o<count($nbabs);$o++) {
		if (preg_match('/inconnu/i',$nbabs[$o][6])) { continue; }
		if ($nbabs[$o][4] > 0) {
			if (preg_match('/\.5$/',$nbabs[$o][4])) { 
				$nbabs[$o][4] = $nbabs[$o][4] - 0.5 ;  
				$nbabs[$o][4] = $nbabs[$o][4] * 2 ; 
				$nbabs[$o][4] = $nbabs[$o][4] + 1;   
			}else{
				$nbabs[$o][4] = $nbabs[$o][4] * 2 ;
			}
			$nbabsT2+=$nbabs[$o][4];
		}else{
			$duree_heureT2+=$nbabs[$o][7];
		}
	}
	if ($duree_heureT2 >= 3) { $duree_heureT2 = $duree_heureT2 / 3 ;  $duree_heureT2=preg_replace('/\..*$/','',$duree_heureT2);  $nbabsT2 += $duree_heureT2 ; }


	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebutT3),dateFormBase($dateFinT3)); // ideleve,debutdate,findate
	$nbretardT3=count($nbretard);
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebutT3),dateFormBase($dateFinT3)); // ideleve,debutdate,findate
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$duree_heureT3=0;
	$nbabsT3=0;
	for($o=0;$o<count($nbabs);$o++) {
		if (preg_match('/inconnu/i',$nbabs[$o][6])) { continue; }
		if ($nbabs[$o][4] > 0) {
			if (preg_match('/\.5$/',$nbabs[$o][4])) { 
				$nbabs[$o][4] = $nbabs[$o][4] - 0.5 ;  
				$nbabs[$o][4] = $nbabs[$o][4] * 2 ; 
				$nbabs[$o][4] = $nbabs[$o][4] + 1 ;   
			}else{
				$nbabs[$o][4] = $nbabs[$o][4] * 2 ;
			}
			$nbabsT3+=$nbabs[$o][4];
		}else{
			$duree_heureT3+=$nbabs[$o][7];
		}
	}
	if ($duree_heureT3 >= 3) { $duree_heureT3 = $duree_heureT3 / 3 ;  $duree_heureT3=preg_replace('/\..*$/','',$duree_heureT3); $nbabsT3 += $duree_heureT3 ; }


	// recherche le nombre d'absence non justifier
	$nbabsdnj=nombre_absnj($idEleve,dateFormBase($dateDebutT1),dateFormBase($dateFinT1)); // ideleve,debutdate,findate
	$duree_heureTT1=0;
	$nbabsTT1=0;
	$nbabsdnjT1=0;
	for($o=0;$o<count($nbabsdnj);$o++) {
		if ($nbabsdnj[$o][4] > 0) {
			if (preg_match('/\.5$/',$nbabsdnj[$o][4])) { 
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] - 0.5 ;  
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] * 2 ; 
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] + 1;   
			}else{
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] * 2 ;
			}
			$nbabsTT1+=$nbabsdnj[$o][4];
		}else{
		         $duree_heureTT1+=$nbabsdnj[$o][7];
		}
	}
	$nbabsdnjT1=$nbabsTT1;
	if ($duree_heureTT1 >= 3) { $duree_heureTT1 = $duree_heureTT1 / 3 ;  $duree_heureTT1=preg_replace('/\..*$/','',$duree_heureTT1); $nbabsdnjT1 += $duree_heureTT1 ; }

	$nbabsdnj=nombre_absnj($idEleve,dateFormBase($dateDebutT2),dateFormBase($dateFinT2)); // ideleve,debutdate,findate
	$duree_heureTT2=0;
	$nbabsTT2=0;
	$nbabsdnjT2=0;
	for($o=0;$o<count($nbabsdnj);$o++) {
		if ($nbabsdnj[$o][4] > 0) {
			if (preg_match('/\.5$/',$nbabsdnj[$o][4])) { 
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] - 0.5 ;  
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] * 2 ; 
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] + 1;   
			}else{
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] * 2 ;
			}
	         	$nbabsTT2+=$nbabsdnj[$o][4];
		}else{
	         	$duree_heureTT2+=$nbabsdnj[$o][7];
		}
	}
	$nbabsdnjT2=$nbabsTT2;
	if ($duree_heureTT2 >= 3) { $duree_heureTT2 = $duree_heureTT2 / 3 ;  $duree_heureTT2=preg_replace('/\..*$/','',$duree_heureTT2); $nbabsdnjT2 += $duree_heureTT2 ; }


	$nbabsdnj=nombre_absnj($idEleve,dateFormBase($dateDebutT3),dateFormBase($dateFinT3)); // ideleve,debutdate,findate
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure 
	$duree_heureTT3=0;
	$nbabsTT3=0;
	$nbabsdnjT3=0;
	for($o=0;$o<count($nbabs);$o++) {
		if ($nbabsdnj[$o][4] > 0) {
			if (preg_match('/\.5$/',$nbabsdnj[$o][4])) { 
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] - 0.5 ;  
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] * 2 ; 
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] + 1;   
			}else{
				$nbabsdnj[$o][4] = $nbabsdnj[$o][4] * 2 ;
			}
	        	$nbabsTT3+=$nbabsdnj[$o][4];
		}else{
	         	$duree_heureTT3+=$nbabsdnj[$o][7];
		}
	}
	$nbabsdnjT3=$nbabsTT3;
	if ($duree_heureTT3 >= 3) { $duree_heureTT3 = $duree_heureTT3 / 3 ; $duree_heureTT3=preg_replace('/\..*$/','',$duree_heureTT3); $nbabsdnjT3 += $duree_heureTT3 ; }


	$policeT=12;

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

	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);
	$nomprenom=trunchaine("<b>$nomEleve</b> $prenomEleve",40);

	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=trim($classe_nom);

	$xtitre=80;  // sans logo
	$xcoor0=3;   // sans logo
	$ycoor0=3;   // sans logo

	// mise en place du logo
	$photo=recup_photo_bulletin();
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$xlogo=25;
			$ylogo=25;
			$xcoor0=30;
			$ycoor0=3;
			$xtitre=90; // avec logo
			$pdf->Image($logo,3,3,$xlogo,$ylogo);
		}
	}
	// fin du logo

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




	// cadre du haut
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(220);
	$pdf->SetXY(80,3); // placement du cadre du nom de l eleve
	$pdf->MultiCell(127,25,'',1,'L',1);

	$photoeleve=image_bulletin($idEleve);
	$photo=$photoeleve;

	$xphoto=80+3;
	$yphoto=5;
	$photowidth=10.8;
	$photoheight=16.3;
	$Xv1=80+3;
	$Xv11=111;
	if (!empty($photo)) {
		$photo=$photoeleve;
		$pdf->Image($photo,$xphoto,$yphoto,$photowidth,$photoheight);
		$Xv1=80+18+3;
		$Xv11=110;
	}

	$bis=verifGroupBis($idEleve);
	if ($bis == 1) {
		$bis=" - BIS";
	}else{
		$bis="";
	}
	$pdf->SetXY($Xv1,5); // placement du nom de l'eleve
	$pdf->WriteHTML($infoeleve);
	$pdf->SetXY($Xv1+52,10);
	$pdf->WriteHTML($infoeleve2);
	$pdf->SetXY($Xv1+66,10);
	$pdf->WriteHTML($infoeleveclasse.$bis);
	$pdf->SetXY($Xv1+52,15);
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell('53','3.5',"Titulaire : $profp",0,'L',0);
	$pdf->SetFont('Arial','',10);



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
		$YN1=10;
		$pdf->SetXY($Xv1,$YN1); 
		$pdf->SetFont('Arial','',8);
		$pdf->WriteHTML("N°: $numero_eleve ");
		$pdf->SetXY($Xv1,$YN1+=4);
		$pdf->WriteHTML("Né(e) le $datenaissance");
		$pdf->SetXY($Xv1,$YN1+=4); 
		$pdf->WriteHTML("Regime: $regime ");
		$pdf->SetXY($Xv1,$YN1+=4); 
		$pdf->WriteHTML("Année scolaire : $anneeScolaire ");
		$pdf->SetXY($Xv1+52,20+$hautclassant);
		$class_ant=trunchaine($class_ant,40);
		$pdf->WriteHTML("Classe ant.: $class_ant ");
	}

	$xcoor0=1;
	$ycoor0=25;
	
	$pdf->SetXY($xcoor0+1,$ycoor0+1);

	$HT1=25;
	$LR0=90;
	$LR1=10;


	$pdf->SetFont($police,'',$policeT);
	$pdf->SetXY($xcoor0+1,$ycoor0+7);
	$pdf->SetFillColor(220);


	$pdf->MultiCell($LR0,$HT1,'',1,'C',1);
	$pdf->SetXY($xcoor0+4,$ycoor0+13);
	$pdf->WriteHTML("<b>Compétences disciplinaires</b>");
	$pdf->SetXY($xcoor0+14,$ycoor0+13+10);
	$pdf->WriteHTML("<b>de $classe_nom</b>");


	$pdf->SetFont($police,'',$policeT-2);

	$pdf->SetXY($xcoor0+=$LR0+1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1=$xcoor0+6;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"1er Période","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=9;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"Examens de ","U");
	$pdf->TextWithDirection($xsujet1+4,$ysujet1,"décembre ","U");

	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=11;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"2eme période ","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=10;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"3eme période ","U");

	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=8;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"Examens de ","U");
	$pdf->TextWithDirection($xsujet1+4,$ysujet1,"juin ","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=11;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"Total ","U");
	$pdf->TextWithDirection($xsujet1+4,$ysujet1,"année ","U");


	$pdf->SetXY($xcoor0+=$LR1,$ycoor0+7);
	$pdf->MultiCell($LR1,$HT1,'',1,'C',0);
	$xsujet1+=9;
	$ysujet1=$ycoor0+13+18;
	$pdf->TextWithDirection($xsujet1,$ysujet1,"Max. ","U");
	$pdf->TextWithDirection($xsujet1+4,$ysujet1,"année ","U");


	$LR2=15;

	$pdf->SetXY($xcoor0+$LR1,$ycoor0+7);
	$pdf->MultiCell(45,$HT1/2,'',1,'C',0);

	$pdf->SetXY($xcoor0+17,$ycoor0+7);
	$pdf->WriteHTML("<b>C</b>omportement ");
	$pdf->SetXY($xcoor0+14,$ycoor0+14);
	$pdf->WriteHTML("social et personnel");

	$pdf->SetFont($police,'',$policeT-4);
	$pdf->SetXY($xcoor0+$LR1,$ycoor0+7+($HT1/2));
	$pdf->MultiCell($LR2,$HT1/2,'',1,'C',0);
	$pdf->SetXY($xcoor0+$LR1,$ycoor0+7+($HT1/2));
	$pdf->MultiCell(12,5,'1er période',0,'C',0);


	$pdf->SetXY($xcoor0+$LR1+$LR2,$ycoor0+7+($HT1/2));
	$pdf->MultiCell($LR2,$HT1/2,'',1,'C',0);
	$pdf->SetXY($xcoor0+$LR1+$LR2,$ycoor0+7+($HT1/2));
	$pdf->MultiCell(12,5,'2eme période',0,'C',0);


	$pdf->SetXY($xcoor0+$LR1+$LR2+$LR2,$ycoor0+7+($HT1/2));
	$pdf->MultiCell($LR2,$HT1/2,'',1,'C',0);
	$pdf->SetXY($xcoor0+$LR1+$LR2+$LR2,$ycoor0+7+($HT1/2));
	$pdf->MultiCell(12,5,'3eme période',0,'C',0);

	$pdf->SetFont($police,'',$policeT-2);

	$xcoor0=1;
	$ycoor0=$ycoor0+$HT1+7;
	$LR3=($LR0/3) * 2 ;
	$LR4=$LR0/3;
	$LR5=5;

	$pdf->SetXY($xcoor0+1,$ycoor0);
	$pdf->MultiCell($LR3,$LR5,'Disciplines',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3,$ycoor0);
	$pdf->MultiCell($LR4,$LR5,'Professeurs',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3+$LR4,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /20',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /20',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /20',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /20',1,'C',0);


	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1+$LR1,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /20',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /...',1,'C',0);

	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1,$ycoor0);
	$pdf->MultiCell($LR1,$LR5,'   /...',1,'C',0);


	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1,$ycoor0);
	$pdf->MultiCell($LR2,$LR5,'    /10',1,'C',0);
	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2,$ycoor0);
	$pdf->MultiCell($LR2,$LR5,'    /10',1,'C',0);
	$pdf->SetXY($xcoor0+1+$LR3+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2+$LR2,$ycoor0);
	$pdf->MultiCell($LR2,$LR5,'    /10',1,'C',0);


	// cadre menu matiere
	// ------------------
	$largeurMat=$LR3;
	$hauteurMaT=$hauteurMatiere=8;
	$Xmat=2;
	$Ymat=$ycoor0+$LR5;;
	
	$NoteNb1p="";
	$NoteNb2p="";
	$NoteNb3p="";
	$NoteNbJui="";
	$NoteNbDec="";
	$NoteNbCom1="";
	$NoteNbCom2="";
	$NoteNbCom3="";
	$NoteNbTotal="";
	$NoteNbMax="";

	$NoteMoyen1p="";
	$NoteMoyen2p="";
	$NoteMoyen3p="";
	$NoteMoyenJui="";
	$NoteMoyenDec="";
	$NoteMoyenCom1="";
	$NoteMoyenCom2="";
	$NoteMoyenCom3="";
	$NoteMoyenTotal="";
	$NoteMoyenMax="";

	$NoteCentTotal="";
	$NbCent="";
	// Mise en place des matieres et nom de prof
	for($i=0;$i<count($ordre);$i++) {

	//	if ($i == 10) {  break; }

		$totalAnnee="";
		$totalMax="";
	//	$pdf->SetTextColor(255,0,0);  // red
		$pdf->SetTextColor(0,0,0);   // noir

		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$nomprof=recherche_personne2($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere
   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);

		$Xmatcont=$Xmat+1;
		$Ymatcont=$Ymat+1;


		// gestion pour les sous matiere
		// -----------------------------
		// cod_mat,sous_matiere,libelle
		$datasousmatiere=verifsousmatierebull($idMatiere);
		// print $datasousmatiere;
		if ($datasousmatiere != "0") {
			$nomMatierePrincipale=$datasousmatiere[0][2];
			$nomSousMatiere=$datasousmatiere[0][1];
		}

	
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$pdf->SetFont($police,'',$policeT-2);
		$pdf->WriteHTML(''.trunchaine(strtoupper(sansaccent(strtolower($matiere))),22).'');
		
		// Nom du prof
		$pdf->SetFont($police,'',$policeT-5);
                $pdf->SetXY($Xmat+$largeurMat,$Ymat);
		$profAff=strtoupper(sansaccent(strtolower($nomprof)));
		$pdf->MultiCell($LR4,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+1,$Ymat+2);
		$pdf->WriteHTML(trunchaine($profAff,15));
		$pdf->SetFont($police,'',$policeT-3);

		// 1er periode
		$pdf->SetXY($Xmat+$largeurMat+$LR4,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT1,$dateFinT1,$idgroupe,$idprof);
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR4,$Ymat+2);
		if ($noteaff != "") {	$totalAnnee+=$noteaff; }
		if ($noteaff != "") { $totalMax += 20 ; $NoteNb1p++; $nbMatiere++; }
		//$noteaff=preg_replace("/\...$/",'',$noteaff);
		if ($noteaff < 10) {
			if ($noteaff != "" ) { $noteaff="0".$noteaff; }
			$pdf->WriteHTML("<font color=red>$noteaff</font>");
		}else{
			$pdf->WriteHTML($noteaff);
		}
		if ($noteaff != "") { $NoteCentTotal+=$noteaff; $NoteMoyen1p+=$noteaff;  }
		  

		//exam de decembre
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExam($idEleve,$ordre[$i][0],"décembre",$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupeExam($idEleve,$ordre[$i][0],"décembre",$idgroupe,$idprof);
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1,$Ymat+2);
		if ($noteaff != "") {	$totalAnnee+=$noteaff; }
		if ($noteaff != "") { $totalMax += 20 ;$NoteNbDec++; }
		//$noteaff=preg_replace("/\...$/",'',$noteaff);
		if ($noteaff < 10) {
			if ($noteaff != "" ) $noteaff="0".$noteaff;
			$pdf->WriteHTML("<font color=red>$noteaff</font>");
		}else{
			$pdf->WriteHTML($noteaff);
		}
		if ($noteaff != "") {
			$NoteCentTotal+=$noteaff;
			$NoteMoyenDec+=$noteaff;
		}

		// 2 ieme periode
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT2,$dateFinT2,$idgroupe,$idprof);
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1,$Ymat+2);
		if ($noteaff != "") {	$totalAnnee+=$noteaff; }
		if ($noteaff != "") { $totalMax += 20 ; $NoteNb2p++; }
	//	$noteaff=preg_replace("/\...$/",'',$noteaff);
		if ($noteaff < 10) {
			if ($noteaff != "" ) $noteaff="0".$noteaff;
			$pdf->WriteHTML("<font color=red>$noteaff</font>");
		}else{
			$pdf->WriteHTML($noteaff);
		}
		if ($noteaff != "") {
			$NoteCentTotal+=$noteaff;
			$NoteMoyen2p+=$noteaff;
		}


		// 3 ieme periode
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereSansExam($idEleve,$ordre[$i][0],$dateDebutT3,$dateFinT3,$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupeSansExam($idEleve,$ordre[$i][0],$dateDebutT3,$dateFinT3,$idgroupe,$idprof);
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1,$Ymat+2);
		if ($noteaff != "") {	$totalAnnee+=$noteaff; }
		if ($noteaff != "") { $totalMax += 20 ;$NoteNb3p++; }
	//	$noteaff=preg_replace("/\...$/",'',$noteaff);
		if ($noteaff < 10) {
			if ($noteaff != "" ) $noteaff="0".$noteaff;
			$pdf->WriteHTML("<font color=red>$noteaff</font>");
		}else{
			$pdf->WriteHTML($noteaff);
		}
		if ($noteaff != "") {
			$NoteCentTotal+=$noteaff;
			$NoteMoyen3p+=$noteaff;
		}

		//exam de juin
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$noteaff="";
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExam($idEleve,$ordre[$i][0],"juin",$idprof);
		}else{
			$noteaff=moyenneEleveMatiereGroupeExam($idEleve,$ordre[$i][0],"juin",$idgroupe,$idprof);
		}
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1,$Ymat+2);
		if ($noteaff != "") {	$totalAnnee+=$noteaff; }
		if ($noteaff != "") { $totalMax += 20 ; $NoteNbJui++; }
		if ($noteaff != "") { $NoteMoyenJui+=$noteaff; }
	//	$noteaff=preg_replace("/\...$/",'',$noteaff);
		if ($noteaff < 10) {
			if ($noteaff != "" ) $noteaff="0".$noteaff;
			$pdf->WriteHTML("<font color=red>$noteaff</font>");
		}else{
			$pdf->WriteHTML($noteaff);
		}
		if ($noteaff != "") { $NoteCentTotal+=$noteaff; }


		//Total à l'année
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat+2);
	
		$mi=$totalMax / 2 ;
		if ($totalAnnee < $mi ) {
			if ($totalAnnee < 100) {  $totalAnnee=number_format($totalAnnee,2,'.',''); }
			if ($totalAnnee == "0.00") { $totalAnnee=""; }
		        $pdf->WriteHTML("<font color=red>$totalAnnee</font>");
		}else{
			if ($totalAnnee < 100) { $totalAnnee=number_format($totalAnnee,2,'.','');  }
			if ($totalAnnee == "0.00") { $totalAnnee=""; }
			$pdf->WriteHTML($totalAnnee);
		}
		$NoteMoyenTotal+=$totalAnnee; 
		if ($totalAnnee != "") {$NoteNbTotal++; }


		// total max
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+1,$Ymat+2);
		if ($totalMax < 100) {
			$totalMax=$totalMax;
		}
		if ($totalMax == 0) { $totalMax=""; }
		$pdf->WriteHTML($totalMax);
		$NoteMoyenMax+=$totalMax;
		if ($totalMax != "") {$NoteNbMax++; }

		if ($totalMax > 0) { 
			//$NoteCent=($totalAnnee+$totalMax) / 100;
			//$NoteCent=($totalAnnee/$totalMax) * 100 ; 
			//print $NoteCent."  ($totalAnnee/$totalMax * 100) ";
			$NbCent+=$totalMax;
		}

	

		// Comportement 1er periode
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR2,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+2,$Ymat+2);
		$noteScolaire=cherche_note_scolaire_eleve($idEleve,$idMatiere,$idClasse,"trimestre1",$idprof,$idgroupe);
		if ($noteScolaire != "") {
			$noteScolaire=number_format($noteScolaire,2,'.','');
			if ($noteScolaire != "") { $NoteMoyenCom1+=$noteScolaire; $NoteNbCom1++ ;  }
			if (($noteScolaire != "" ) && ($noteScolaire < 10))  $noteScolaire="0".$noteScolaire;
			if ($noteScolaire < 5) {
				$pdf->WriteHTML("<font color=red>$noteScolaire</font>");
			}else{
				$pdf->WriteHTML($noteScolaire);	
			}
		}

		// COmportement 2iem periode
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2,$Ymat);
		$pdf->MultiCell($LR2,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2+2,$Ymat+2);
		$noteScolaire=cherche_note_scolaire_eleve($idEleve,$idMatiere,$idClasse,"trimestre2",$idprof,$idgroupe);
		if ($noteScolaire != "") {
			$noteScolaire=number_format($noteScolaire,2,'.','');
			$NoteNbCom2++ ; 
			if ($noteScolaire != "") { $NoteMoyenCom2+=$noteScolaire; }
			if (($noteScolaire != "" ) && ($noteScolaire < 10))  $noteScolaire="0".$noteScolaire;
			if ($noteScolaire < 5) {
				$pdf->WriteHTML("<font color=red>$noteScolaire</font>");
			}else{
				$pdf->WriteHTML($noteScolaire);	
			}
		}



		// COmportement 3iem periode
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2+$LR2,$Ymat);
		$noteScolaire=cherche_note_scolaire_eleve($idEleve,$idMatiere,$idClasse,"trimestre3",$idprof,$idgroupe);
		if ($noteScolaire != "") {
			if ($noteScolaire != "") { $NoteMoyenCom3+=$noteScolaire; }
			$NoteNbCom3++ ; 
			$noteScolaire=number_format($noteScolaire,2,'.','');
			if (($noteScolaire != "" ) && ($noteScolaire < 10))  $noteScolaire="0".$noteScolaire;
			if ($noteScolaire < 5) {
				$pdf->SetTextColor(255,0,0);
			}else{
				$pdf->SetTextColor(0,0,0);
			}
		}
		$pdf->MultiCell($LR2,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2+$LR2+1,$Ymat+0.5);
		$pdf->MultiCell($LR2,$hauteurMatiere,"  $noteScolaire",0,'L',0);
		$pdf->SetTextColor(0,0,0);


		// fin de la ligne
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;   
           
	}
	// fin de la mise en place des matiere


	// moyenne general
	$xcoor0=2;
	$ycoor0=$Ymat;


	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell($largeurMat+$LR4,$hauteurMatiere,'',1,'L',1);
	$pdf->SetXY($xcoor0+50,$ycoor0+2);
	$pdf->WriteHTML("<b>Moyenne Générale (%) </b>");

	$pdf->SetXY($xcoor0=$xcoor0+$largeurMat+$LR4,$ycoor0);

	if (trim($NoteMoyen1p) == "") {
		$NoteMoyen1p="";
	}else{
		$NoteMoyen1p=$NoteMoyen1p/$NoteNb1p;
		$NoteMoyen1p=number_format($NoteMoyen1p,2,'.','');
		if ($NoteMoyen1p < 10) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyen1p=$NoteMoyen1p/20 * 100 ;
	}
	$pdf->MultiCell($LR1,$hauteurMatiere,"$NoteMoyen1p",1,'L',1);  // MOYE  1er P
//----------------------------
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	if (trim($NoteMoyenDec) == "") {
		$NoteMoyenDec="";
	}else{
		$NoteMoyenDec=$NoteMoyenDec/$NoteNbDec;
		$NoteMoyenDec=number_format($NoteMoyenDec,2,'.','');
		if ($NoteMoyenDec < 10) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyenDec=$NoteMoyenDec/20 * 100 ;
	}
	$pdf->MultiCell($LR1,$hauteurMatiere,"$NoteMoyenDec",1,'L',1);  // MOYE  Ex Dec 
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
//----------------------------
	if (trim($NoteMoyen2p) == "") {
		$NoteMoyen2p="";
	}else{
		$NoteMoyen2p=$NoteMoyen2p/$NoteNb2p;
		$NoteMoyen2p=number_format($NoteMoyen2p,2,'.','');
		if ($NoteMoyen2p < 10) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyen2p=$NoteMoyen2p/20 * 100 ;
	}
	$pdf->MultiCell($LR1,$hauteurMatiere,"$NoteMoyen2p",1,'L',1);  // MOYE  2em P2em P 
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
//----------------------------
	
	if (trim($NoteMoyen3p) == "") {
		$NoteMoyen3p="";
	}else{
		$NoteMoyen3p=$NoteMoyen3p/$NoteNb3p;
		$NoteMoyen3p=number_format($NoteMoyen3p,2,'.','');
		if ($NoteMoyen3p < 10) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyen3p=$NoteMoyen3p/20 * 100 ;
	}
	$pdf->MultiCell($LR1,$hauteurMatiere,"$NoteMoyen3p",1,'L',1);  // MOYE  3em P2em P 
//----------------------------
	
	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	if (trim($NoteMoyenJui) == "") {
		$NoteMoyenJui="";
	}else{
		$NoteMoyenJui=$NoteMoyenJui/$NoteNbJui;
		$NoteMoyenJui=number_format($NoteMoyenJui,2,'.','');
		if ($NoteMoyenJui < 10) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyenJui=$NoteMoyenJui/20 * 100 ;
	}
	$pdf->MultiCell($LR1,$hauteurMatiere,"$NoteMoyenJui",1,'L',1);  // MOYE  Ex Juin 
	$pdf->SetTextColor(0,0,0);
//----------------------------

	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	//$NoteMoyenTotal=$NoteMoyenTotal/$NoteNbTotal;
	//$NoteMoyenTotal=number_format($NoteMoyenTotal,2,'.','');
	$pdf->MultiCell($LR1,$hauteurMatiere,"",1,'L',1);  // MOYE   Annee
	$pdf->SetXY($xcoor0,$ycoor0+1.3);
	if ($NbCent > 0) {
		// print $NoteCentTotal."<br>";
		$NoteCentTotal=($NoteCentTotal/$NbCent) * 100;
	}
	$NoteCentTotal=number_format($NoteCentTotal,2,'.','');
	$pdf->WriteHTML("<b>$NoteCentTotal</b>");




	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	$NoteMoyenMax=$NoteMoyenMax/$NoteNbMax;
	$NoteMoyenMax=number_format($NoteMoyenMax,2,'.','');
	//$pdf->MultiCell($LR1,$hauteurMatiere,"$NoteMoyenMax",1,'L',0);  // MOYE   Max
	$pdf->MultiCell($LR1,$hauteurMatiere,"",1,'L',0);  // MOYE   Max



	$pdf->SetXY($xcoor0+=$LR1,$ycoor0);
	if (trim($NoteMoyenCom1) == "") {
		$NoteMoyenCom1="";
	}else{
		$NoteMoyenCom1=$NoteMoyenCom1/$NoteNbCom1;
		$NoteMoyenCom1=number_format($NoteMoyenCom1,2,'.','');
		if ($NoteMoyenCom1 < 5) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyenCom1=$NoteMoyenCom1/10 * 100 ;
	}	
	$pdf->MultiCell($LR2,$hauteurMatiere,"  $NoteMoyenCom1",1,'L',1);  // MOYE   Max


	$pdf->SetXY($xcoor0+=$LR2,$ycoor0);
	if (trim($NoteMoyenCom2) == "") {
		$NoteMoyenCom2="";
	}else{
		$NoteMoyenCom2=$NoteMoyenCom2/$NoteNbCom2;
		$NoteMoyenCom2=number_format($NoteMoyenCom2,2,'.','');
		if ($NoteMoyenCom2 < 5) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyenCom2=$NoteMoyenCom2/10 * 100 ;
	}
	$pdf->MultiCell($LR2,$hauteurMatiere,"  $NoteMoyenCom2",1,'L',1);  // MOYE   Max

	$pdf->SetXY($xcoor0+=$LR2,$ycoor0);
	if (trim($NoteMoyenCom3) == "") {
		$NoteMoyenCom3="";
	}else{
		$NoteMoyenCom3=$NoteMoyenCom3/$NoteNbCom3;
		$NoteMoyenCom3=number_format($NoteMoyenCom3,2,'.','');
		if ($NoteMoyenCom3 < 5) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
		$NoteMoyenCom3=$NoteMoyenCom3/10 * 100 ;
	}	
	$pdf->MultiCell($LR2,$hauteurMatiere,"  $NoteMoyenCom3",1,'L',1);  // MOYE   Max


	$pdf->SetTextColor(0,0,0);

	$Ymat+=$hauteurMatiere;	
	$Xmat=2;
	/*
	for($i=0;$i<=1;$i++) {
		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat,$Ymat);
		$pdf->MultiCell($LR4,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR1,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1,$Ymat);
		$pdf->MultiCell($LR2,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2,$Ymat);
		$pdf->MultiCell($LR2,$hauteurMatiere,'',1,'L',0);
		$pdf->SetXY($Xmat+$largeurMat+$LR4+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR1+$LR2+$LR2,$Ymat);
		$pdf->MultiCell($LR2,$hauteurMatiere,'',1,'L',0);

		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere; 

	}
*/
	$LR6=80;
	$LR8=80;
	$HT3=10;
	$pdf->SetFont($police,'',$policeT);
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell($LR6,$HT3,'',1,'L',0);
	$pdf->SetXY($Xmat+3,$Ymat+2);
	$pdf->WriteHTML("<b>Comportement social et personnel </b>");
	$pdf->SetXY($Xmat+$LR6,$Ymat);
	$pdf->MultiCell($LR8,$HT3,'',1,'L',0);
	$pdf->SetXY($Xmat+$LR6+3,$Ymat+2);
	$pdf->WriteHTML("<b>N</b>ote globale des éducateurs     /50 ");

	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($Xmat+=$LR8+$LR6,$Ymat);
	$pdf->MultiCell($LR2,$HT3,'',1,'L',0);
	$pdf->SetXY($Xmat+2,$Ymat+2);
	$noteScolaire=cherche_note_scolaire_eleve_cpe($idEleve,"-10",$idClasse,"trimestre1","0");
	if ($noteScolaire != "") {
		$noteScolaire=number_format($noteScolaire,2,'.','');
		if ($noteScolaire < 25) {
			$pdf->WriteHTML("<font color=red>$noteScolaire</font>");
		}else{
			$pdf->WriteHTML("$noteScolaire");	
		}
	}
	
	$pdf->SetXY($Xmat+=$LR2,$Ymat);
	$pdf->MultiCell($LR2,$HT3,'',1,'L',0);
	$pdf->SetXY($Xmat+2,$Ymat+2);
	$noteScolaire=cherche_note_scolaire_eleve_cpe($idEleve,"-10",$idClasse,"trimestre2","0");
	if ($noteScolaire != "") {
		$noteScolaire=number_format($noteScolaire,2,'.','');
		if ($noteScolaire < 25) {
			$pdf->WriteHTML("<font color=red>$noteScolaire</font>");
		}else{
			$pdf->WriteHTML("$noteScolaire");	
		}
	}

	$pdf->SetXY($Xmat+=$LR2,$Ymat);
	$pdf->MultiCell($LR2,$HT3,'',1,'L',0);
	$noteScolaire=cherche_note_scolaire_eleve_cpe($idEleve,"-10",$idClasse,"trimestre3","0");
	if ($noteScolaire != "") {
		$noteScolaire=number_format($noteScolaire,2,'.','');
		if ($noteScolaire < 25) {
			$pdf->SetTextColor(255,0,0);
		}else{
			$pdf->SetTextColor(0,0,0);
		}
	}
	$pdf->SetXY($Xmat,$Ymat-0.5);
	$pdf->MultiCell($LR2,$HT3,"  $noteScolaire",0,'L',0);
	$pdf->SetFont($police,'',$policeT);
	$pdf->SetTextColor(0,0,0);

	$Xmat=2;
	$Ymat+=$HT3;
	$HT3=8;
	$pdf->SetXY($Xmat,$Ymat);
	$don1=recup_comport_namur("perio_1_namur");
	$pdf->MultiCell(205,$HT3,"  1er période : ",1,'L',0);
	$pdf->SetXY($Xmat+30,$Ymat+0.5);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(170,3.8,"$don1",0,'L',0);	
	$pdf->SetFont($police,'',$policeT);


	$pdf->SetXY($Xmat,$Ymat+=$HT3);
	$don2=recup_comport_namur("perio_2_namur");
	$pdf->MultiCell(205,$HT3,"  2eme période : ",1,'L',0);
	$pdf->SetXY($Xmat+35,$Ymat+0.5);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(170,3.8,"$don2",0,'L',0);	
	$pdf->SetFont($police,'',$policeT);


	$pdf->SetXY($Xmat,$Ymat+=$HT3);
	$don3=recup_comport_namur("perio_3_namur");
	$pdf->MultiCell(205,$HT3,"  3eme période : ",1,'L',0);
	$pdf->SetXY($Xmat+35,$Ymat+0.5);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(170,3.8,"$don3",0,'L',0);	
	$pdf->SetFont($police,'',$policeT);



	$HT3=24;
	$pdf->SetXY($Xmat,$Ymat+=8);
	$pdf->MultiCell($LR6,$HT3,'ASSIDUITE',1,'C',0);
	$pdf->SetXY($Xmat+$LR6,$Ymat);
	$pdf->MultiCell($LR8,$HT3,'',1,'C',0);

	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($Xmat+83,$Ymat+2);
	$pdf->WriteHTML("<b>A</b>bsences justifiées (demi-journées) .................. ");
	$pdf->SetXY($Xmat+83,$Ymat+10);
	$pdf->WriteHTML("<b>A</b>bsences non justifiées (demi-journées) ........... ");
	$pdf->SetXY($Xmat+83,$Ymat+17);
	$pdf->WriteHTML("<b>R</b>etard ................................................................. ");
	$pdf->SetFont($police,'',$policeT);




	// Case abs,rtd
	$pdf->SetXY($Xmat+$LR8+$LR6,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6,$Ymat+1);
	$pdf->WriteHTML("$nbabsT1");

	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2,$Ymat+1);
	$pdf->WriteHTML("$nbabsT2");

	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2+$LR2,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2+$LR2,$Ymat+1);
	$pdf->WriteHTML("$nbabsT3");

		// abs non justifié
	$pdf->SetXY($Xmat+$LR8+$LR6,$Ymat+=8);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6,$Ymat+1);
	$pdf->WriteHTML("$nbabsdnjT1");

	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2,$Ymat+1);
	$pdf->WriteHTML("$nbabsdnjT2");

	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2+$LR2,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2+$LR2,$Ymat+1);
	$pdf->WriteHTML("$nbabsdnjT3");

		// retard
	$pdf->SetXY($Xmat+$LR8+$LR6,$Ymat+=8);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6,$Ymat+1);
	$pdf->WriteHTML("$nbretardT1");

	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2,$Ymat+1);
	$pdf->WriteHTML("$nbretardT2");
	
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2+$LR2,$Ymat);
	$pdf->MultiCell($LR2,8,'',1,'C',0);
	$pdf->SetXY($Xmat+$LR8+$LR6+$LR2+$LR2,$Ymat+1);
	$pdf->WriteHTML("$nbretardT3");

	// signature
	$Xmat=2;
	$LR9=50;
	$HT9=8;
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->SetXY($Xmat,$Ymat+=8);
	$pdf->MultiCell($LR9+5,$HT9,'',1,'C',0);
	$pdf->SetXY($Xmat+=$LR9+5,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);
	$pdf->SetXY(4,$Ymat);
	$pdf->WriteHTML("Signature ");
	$pdf->SetXY(4,$Ymat+3);
	$pdf->WriteHTML("du chef d'établissement ");
	$pdf->SetXY($Xmat+=$LR9,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);
	$pdf->SetXY($Xmat+=$LR9,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);

	$Xmat=2;
	$pdf->SetXY($Xmat,$Ymat+=$HT9);
	$pdf->MultiCell($LR9+5,$HT9,'',1,'C',0);
	$pdf->SetXY(4,$Ymat);
	$pdf->WriteHTML("Signature ");
	$pdf->SetXY(4,$Ymat+3);
	$pdf->WriteHTML("des parents ");
	$pdf->SetXY($Xmat+=$LR9+5,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);
	$pdf->SetXY($Xmat+=$LR9,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);
	$pdf->SetXY($Xmat+=$LR9,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);

	$Xmat=2;
	$pdf->SetXY($Xmat,$Ymat+=$HT9);
	$pdf->MultiCell($LR9+5,$HT9,'',1,'C',0);
	$pdf->SetXY(4,$Ymat);
	$pdf->WriteHTML("Signature ");
	$pdf->SetXY(4,$Ymat+3);
	$pdf->WriteHTML("de l'élève ");
	$pdf->SetXY($Xmat+=$LR9+5,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);
	$pdf->SetXY($Xmat+=$LR9,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);
	$pdf->SetXY($Xmat+=$LR9,$Ymat);
	$pdf->MultiCell($LR9,$HT9,'',1,'C',0);

	// deuxieme page
	$pdf->AddPage();

	$pdf->SetFont($police,'',$policeT);
	$Xmat=2;
	$Ymat=2;
	$pdf->SetXY($Xmat,$Ymat);
	$pdf->MultiCell(205,30,'',1,'C',1);
	$pdf->SetXY($Xmat+85,$Ymat+6);
	$pdf->WriteHTML("<b>Commentaires</b>");
	$pdf->SetXY($Xmat+60,$Ymat+14);
	$pdf->WriteHTML("<b>Compétences disciplinaires et transversales</b>");

	$pdf->SetXY($Xmat,$Ymat+=30);
	$pdf->MultiCell(205,40,'',1,'L',0);
	$pdf->SetXY($Xmat+2,$Ymat+3);
	$pdf->WriteHTML("1er période");
	$commentaireprofp=recherche_com_profP($idEleve,"trimestre1");
	$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
	$pdf->SetXY($Xmat+2,$Ymat+8);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(195,5,"$commentaireprofp",0,'L',0);
	$pdf->SetFont($police,'',$policeT);

	$pdf->SetXY($Xmat,$Ymat+=40);
	$pdf->MultiCell(205,40,'',1,'C',0);
	$pdf->SetXY($Xmat+2,$Ymat+3);
	$pdf->WriteHTML("Examens de décembre");
	$commentaireprofp=recherche_com_profP($idEleve,"exam_dec");
	$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
	$pdf->SetXY($Xmat+2,$Ymat+8);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(195,5,"$commentaireprofp",0,'L',0);
	$pdf->SetFont($police,'',$policeT);


	$pdf->SetXY($Xmat,$Ymat+=40);
	$pdf->MultiCell(205,40,'',1,'C',0);
	$pdf->SetXY($Xmat+2,$Ymat+3);
	$pdf->WriteHTML("2eme période");
	$commentaireprofp=recherche_com_profP($idEleve,"trimestre2");
	$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
	$pdf->SetXY($Xmat+2,$Ymat+8);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(195,5,"$commentaireprofp",0,'L',0);
	$pdf->SetFont($police,'',$policeT);

	$pdf->SetXY($Xmat,$Ymat+=40);
	$pdf->MultiCell(205,40,'',1,'C',0);
	$pdf->SetXY($Xmat+2,$Ymat+3);
	$pdf->WriteHTML("3eme période");
	$commentaireprofp=recherche_com_profP($idEleve,"trimestre3");
	$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
	$pdf->SetXY($Xmat+2,$Ymat+8);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(195,5,"$commentaireprofp",0,'L',0);
	$pdf->SetFont($police,'',$policeT);

	$pdf->SetXY($Xmat,$Ymat+=40);
	$pdf->MultiCell(205,40,'',1,'C',0);
	$pdf->SetXY($Xmat+2,$Ymat+3);
	$pdf->WriteHTML("Examens de juin");
	$commentaireprofp=recherche_com_profP($idEleve,"exam_juin");
	$commentaireprofp=preg_replace('/\n/'," ",$commentaireprofp);
	$pdf->SetXY($Xmat+2,$Ymat+8);
	$pdf->SetFont($police,'',$policeT-3);
	$pdf->MultiCell(195,5,"$commentaireprofp",0,'L',0);
	$pdf->SetFont($police,'',$policeT);

	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($Xmat,$Ymat+=40);
	$pdf->MultiCell(205,30,'',1,'C',1);
	$pdf->SetXY($Xmat+2,$Ymat+3);
	$pdf->WriteHTML("<b>Compétence</b> : Aptitude à mettre en oeuvre un ensemble organisé de savoirs, de savoir faire et d'aptitudes permettant d'accomplir un certain nombre de tâches.");
	$pdf->SetXY($Xmat+2,$Ymat+12);
	$pdf->WriteHTML("<b>Compétence disciplinaires </b> : Compétences à acquérir et à mettre en oeuvre dans une discipline scolaire.");
	$pdf->SetXY($Xmat+2,$Ymat+20);
	$pdf->WriteHTML("<b>Compétence transversales </b> : Compétences communes aux différentes disciplines à acquérir et à mettre en oeuvre au cours de l'élaboration des différents savoirs et savoir-faire.");
	$pdf->SetFont($police,'',$policeT);



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
}
Pgclose();
?>

<?php
}else {
?>
<br />
<center>
<font class="T2">
<?php print LANGMESS14?> <br>
<br><br>
<?php print LANGMESS15?><br>
<br>
<?php print LANGMESS16?><br>
</font>
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
