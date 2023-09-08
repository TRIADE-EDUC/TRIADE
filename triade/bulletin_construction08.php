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

      <?php print LANGBULL27?> : <?php print ucwords($textTrimestre)?><br> <br>
      <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
      <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
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
//$pdf=new PDF('P','mm','Legal');

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();


$eleveT=recupEleve($_POST["saisie_classe"]); // recup liste eleve
$effectif=count($eleveT);





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
	$nbretard=nombre_retard($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbretard=count($nbretard);
	$nbabs=nombre_abs($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbabs=count($nbabs);
	//-------------------------------------//
	// recherche le nombre de discipline
	$nbdiscipline=0;
	$nbdiscipline=affSanction_par_eleve_trimestre($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
	$nbdiscipline=count($nbdiscipline);
	$nbretenenu=affRetenuTotal_par_eleve_trimestre($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin));
	$nbdiscipline+=count($nbretenenu);
	//-------------------------------------//

	// recherche le nombre d'absence non justif
	$nbabsdnj=nombre_absnj($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbabsdnj=count($nbabsdnj);
	// recherche le nombre de retard non justif
	$nbrtdnj=nombre_rtdnj($idEleve,dateFormBase($dateDebut),dateFormBase($dateFin)); // ideleve,debutdate,findate
	$nbrtdnj=count($nbrtdnj);



	$pdf->AddPage();

	




	$titre="<B><U>Année scolaire</U> <U>".ucwords($textTrimestre)."</u></B>";

	$nomEleve=strtoupper(trim($nomEleve));
	$nomEleve=strtoupper(sansaccent(strtolower($nomEleve)));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="<b>$nomEleve</b> $prenomEleve";


	$infoeleve=LANGBULL31." : $nomprenom";
	$infoeleve2=LANGELE4." : ";
	$infoeleveclasse=$classe_nom;

	$duplicata="  -   $coordonne4 - $coordonne5 ";
	$signature=LANGBULL42;
	$signature2="";



	$xtitre=80;  
	$xcoor0=3;  
	$ycoor0=3; 

	$police="Arial";
	$policeT=8;

	$idprofp=rechercheprofp($_POST["saisie_classe"]);
	$profp=recherche_personne2($idprofp);


	// Debut création PDF
	// mise en place du cadre 

	


	// mise en place du logo
	$logo="./image/banniere/banniere-chicago1.jpg";
	if (file_exists($logo)) {
		$taille=getimagesize($logo);
		$logox=$taille[0]/7;
		$logoy=$taille[1]/7;
		$pdf->Image($logo,7,7,$logox,$logoy);
	}
	// fin du logo
	//
	$pdf->SetFillColor(255);
	$long=60;
	
	$pdf->SetXY($xcoor0+40,$ycoor0+3);
	

	$pdf->SetFont($police,'',$policeT+2);

	$pdf->SetXY($xcoor0,$ycoor0);

	$coordonne0=strtoupper($nom_etablissement)." ".$adresse." ".$ville." ";
	$coordonne1="Telephone: $tel - Internet: $urlsite";

	$pdf->SetXY($xcoor0+61,$ycoor0+4);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetXY($xcoor0+61,$ycoor0+9);
	$pdf->WriteHTML($coordonne1);

	$pdf->SetXY($xcoor0+5,$ycoor0+25);
	$Pdate="ANN&Eacute;E SCOLAIRE"." ".$anneeScolaire;
	$pdf->WriteHTML($Pdate);

	$pdf->SetFont($police,'',$policeT+6);
	$pdf->SetXY($xcoor0+75,$ycoor0+25);
	$pdf->WriteHTML(strtoupper("Bulletin du ".ucwords($textTrimestre)));

		
/*
	$pdf->MultiCell($long,$hauteurentete,'',1,'L',1);
	$pdf->SetXY($xcoor0+1,$ycoor0+5);
	$pdf->WriteHTML($coordonne0);
	$pdf->SetXY($xcoor0+1,$ycoor0+10);
	$pdf->WriteHTML($coordonne1);
	$pdf->SetXY($xcoor0+1,$ycoor0+15);
	$pdf->WriteHTML($coordonne2);
	$pdf->SetXY($xcoor0+1,$ycoor0+20);
	$pdf->WriteHTML($coordonne3);

	// insertion de la Annee SCOLAIRE
	$Pdate="ANN&Eacute;E SCOLAIRE"." ".$anneeScolaire;
	$pdf->SetXY($xcoor0+$long,$ycoor0);
	$pdf->MultiCell(95,$hauteurentete,'',1,'L',1);
	$pdf->SetFont($police,'',$policeT+6);
	$pdf->SetXY($xcoor0+$long+5,$ycoor0+5);
	$pdf->WriteHTML($Pdate);
	$pdf->SetXY($xcoor0+$long+10,$ycoor0+11);
	$pdf->WriteHTML(ucwords($textTrimestre));

	// insertion de la classe, grade et effectif
	$pdf->SetFont($police,'',$policeT+4);
	$pdf->SetXY($xcoor0+$long+95,$ycoor0);
	$pdf->MultiCell(54,$hauteurentete,'',1,'L',1);
	$pdf->SetXY($xcoor0+$long+95+1,$ycoor0+5);
	$pdf->WriteHTML("Classe : $classe_nom");
	$pdf->SetXY($xcoor0+$long+95+1,$ycoor0+10);
	//$pdf->WriteHTML("Grade : ");
	$pdf->SetXY($xcoor0+$long+95+1,$ycoor0+15);
	$pdf->WriteHTML("Effectif : $effectif");
*/
	
	$hauteurentete=35;

	// cadre du nom eleve
	$pdf->SetFillColor(255);
	$pdf->SetXY($xcoor0,$ycoor0+$hauteurentete);
	$pdf->MultiCell(209,20,'',0,'L',0);
	$pdf->SetXY($xcoor0+1,$ycoor0+$hauteurentete+5); 
	$pdf->SetFont($police,'',$policeT+2);
	$pdf->WriteHTML("Name / Nom : $nomprenom");
	$pdf->SetXY($xcoor0+1,$ycoor0+$hauteurentete+9);
	$pdf->SetFont($police,'',$policeT);
	$pdf->WriteHTML("Né(e) : le ".recup_date_naissance_eleve($idEleve));
	$pdf->SetFont($police,'',$policeT+2);
	$pdf->SetXY($xcoor0+1,$ycoor0+$hauteurentete+14);
	$pdf->WriteHTML(strtoupper($classe_nom));


	// cadre menu matiere
	// ------------------
	$ycoor0=$hauteurentete+25;
	$largeurMat=50;
	$hauteurMaT=8;
	$pdf->SetFillColor(220);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell($largeurMat,$hauteurMaT,'',1,'L',1);
	$pdf->SetXY($xcoor0,$ycoor0);
       	$pdf->WriteHTML("MATI&Egrave;RES");
	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($xcoor0,$ycoor0+3);
       	$pdf->WriteHTML("Subjects");
	$pdf->SetFont($police,'',$policeT);

	// cadre menu note
	// ---------------
	$larg1=$largeurMat-30;
	$pdf->SetXY($xcoor0+$largeurMat,$ycoor0); 
	$pdf->MultiCell($larg1,$hauteurMaT,'',1,'L',1);
	$pdf->SetXY($xcoor0+$largeurMat+1,$ycoor0);
       	$pdf->WriteHTML("NOTE");
	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($xcoor0+$largeurMat+1,$ycoor0+3);
       	$pdf->WriteHTML("Term Grade");
	$pdf->SetFont($police,'',$policeT);

	$pdf->SetXY($xcoor0+$largeurMat+$larg1,$ycoor0); 
	$pdf->MultiCell($larg1,$hauteurMaT,'',1,'L',1);
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+1,$ycoor0);
       	$pdf->WriteHTML("MOYENNE");
	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+1,$ycoor0+3);
       	$pdf->WriteHTML("Average");
	$pdf->SetFont($police,'',$policeT);

	// $longCom=119;
	$longCom=115;
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+$larg1,$ycoor0); 
	$pdf->MultiCell($longCom,$hauteurMaT,'',1,'L',1);
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+$larg1+1,$ycoor0);
       	$pdf->WriteHTML("APPR&Eacute;CIATIONS");
	$pdf->SetFont($police,'',$policeT-2);
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+$larg1+1,$ycoor0+3);
       	$pdf->WriteHTML("Comments");
	$pdf->SetFont($police,'',$policeT+3);


	$Xmat=3;
	$Ymat=$ycoor0+$hauteurMaT;
	$Xmatcont=16;
	$Ymatcont=73;

	
	$XprofVal=10; 			// x en nom prof
	$YprofVal=$Ymat + 6; 		// y en nom du prof

	$Xmoyeleve=$largeurMat+3;
	$Ymoyeleve=$Ymat;
	$Xmoyclasse=$Xmoyeleve+20;
	$Ymoyclasse=$Ymoyeleve;
	
	$Xnote=$Xmoyclasse+20;
	$Ynote=$Ymoyclasse;


	$XnotVal=$Xmoyeleve + 5;
	$YnotVal=$Ymoyeleve + 3;

	$XmoyMatGVal=$Xmoyclasse + 5;
	$YmoyMatGVal=$Ymoyclasse + 3;

	$Xcom=$Xnote+2;
	$Ycom=$Ynote;
	$ii=0;
	
	// mise en place des matieres
	$largeurMatiere=40;
	$hauteurMatiere=13;
	
	// Mise en place des matieres et nom de prof
	for($i=0;$i<count($ordre);$i++) {
		$pdf->SetFont($police,'',$policeT+2);
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

		// fin de la gestion sous matiere
		// ------------------------------
		$ii++;
		if ($ii == 16) {
			$pdf->AddPage();

			$Xmat=3;
			$Ymat=41;
			$Xmatcont=$Xmat+1;
			$Ymatcont=$Ymat+1;

		
			$XprofVal=10; 			// x en nom prof
			$YprofVal=$Ymat + 6; 		// y en nom du prof

			$Xmoyeleve=$largeurMat+3;
			$Ymoyeleve=$Ymat;
			$Xmoyclasse=$Xmoyeleve+20;
			$Ymoyclasse=$Ymoyeleve;
	
			$Xnote=$Xmoyclasse+20;
			$Ynote=$Ymoyclasse;

			$Xnote=$Xmoyclasse+20;
			$Ynote=$Ymoyclasse;


			$XnotVal=$Xmoyeleve + 5;
			$YnotVal=$Ymoyeleve + 3;

			$XmoyMatGVal=$Xmoyclasse + 5;
			$YmoyMatGVal=$Ymoyclasse + 3;

			$Xcom=$Xnote+2;
			$Ycom=$Ynote;
			$ii=0;

		}

		$pdf->SetXY($Xmat,$Ymat);
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',1);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$pdf->WriteHTML('<B>'.trunchaine(strtoupper(sansaccent(strtolower($matiere))),19).'</B>');
		$Ymat=$Ymat + $hauteurMatiere;
		$Ymatcont=$Ymatcont + $hauteurMatiere;

		// mise en place du nom du prof
                if (strtolower(trim($matiere)) != "vie scolaire") {
                        $profAff=profAff($ordre[$i][0],$idClasse,$ordre[$i][2]);
                        $profAff=recherche_personne2($profAff);
                }else{
                        $profAff="";
                }
                $pdf->SetFont('Arial','',$policeT);
                $pdf->SetXY($XprofVal,$YprofVal);
		$profAff=strtoupper(sansaccent(strtolower($profAff)));
                $pdf->WriteHTML(trunchaine($profAff,20));
                $YprofVal=$YprofVal + $hauteurMatiere ;
                $pdf->SetFont($police,'',$policeT+2);
		

		// mise en place moyenne eleve
		$pdf->SetXY($Xmoyeleve,$Ymoyeleve);
		$pdf->MultiCell(20,$hauteurMatiere,'',1,'L',0);
		$Ymoyeleve=$Ymoyeleve + $hauteurMatiere;


		// mise en place moyenne classe
		$pdf->SetXY($Xmoyclasse,$Ymoyclasse);
		$pdf->MultiCell(20,$hauteurMatiere,'',1,'L',0);
		$Ymoyclasse=$Ymoyclasse + $hauteurMatiere;


		// mise en place du cadre appreciation
		$pdf->SetXY($Xnote,$Ynote);
		$pdf->MultiCell($longCom,$hauteurMatiere,'',1,'',0);
		$Ynote=$Ynote + $hauteurMatiere;

		// mise en place des notes
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
			$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
		}
		$pdf->SetXY($XnotVal-5,$YnotVal);
		$noteaff1=$noteaff;
		if (trim($notetype) == "en") { 
			if ($noteaff1 != "") {
				$noteaff1=number_format($noteaff1,0,'','')."% - ".recherche_note_en($noteaff); 
			}else{
				$noteaff1="";
			}
		}else{
			$noteaff1=arrondi($noteaff);
		}
		$pdf->SetFont('Arial','',$policeT+3);
		$pdf->WriteHTML($noteaff1);
		$YnotVal=$YnotVal + $hauteurMatiere;


		// mise en place des moyennes de classe
		if ($idgroupe == "0") {
           		// idMatiere,datedebut,dateFin,idclasse
			$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
			$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
    		}else {
			$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
    		}
		$pdf->SetXY($XmoyMatGVal-5,$YmoyMatGVal);
		$moyeMatGenaff=$moyeMatGen;
		if (trim($notetype) == "en") { 
			$moyeMatGenaff=number_format($moyeMatGen,0,'','')."% - ".recherche_note_en($moyeMatGen); 
		}else{
			$moyeMatGenaff=arrondi($moyeMatGen);
		}
		$pdf->SetFont('Arial','',$policeT+3);
		$pdf->WriteHTML($moyeMatGenaff);
		$YmoyMatGVal=$YmoyMatGVal + $hauteurMatiere;

		// mise en place des commentaires

		$commentaireeleve=cherche_com_eleve($idEleve,$ordre[$i][0],$idClasse,$_POST["saisie_trimestre"],$idprof,$idgroupe);
		$commentaireeleve=preg_replace("/\n/"," ",$commentaireeleve);

		$confPolice=confPoliceChicago($commentaireeleve);
		$pdf->SetFont('Arial','',$confPolice[0]);	

		$pdf->SetXY($Xcom,$Ycom+0.5);
		$pdf->MultiCell(110,$confPolice[1],$commentaireeleve,'','','L',0);
		$pdf->SetFont($police,'',$policeT+2);
		$Ycom=$YmoyMatGVal - 3;
	}
	// fin de la mise en place des matiere


	// fin notes
	// --------
	$XcomG=$Xmat;
	$YcomG=$Ynote;
	$hauteurComG=50;
	if ($YcomG > 230) {
		$pdf->AddPage();
		$YcomG=20;
	}

	// cadre appréciation
	$pdf->SetFont('Arial','',$policeT);
	$pdf->SetXY($XcomG,$YcomG);
	$pdf->MultiCell($largeurMat,$hauteurComG,'',1,'L',0);
	$pdf->SetXY($XcomG,$YcomG+4);
	$pdf->WriteHTML("Retards-Tardies: $nbretard - $nbrtdnj non justifiés ");
	$pdf->SetXY($XcomG,$YcomG+10);
//	$pdf->WriteHTML("Discipline: $nbdiscipline");
	$pdf->SetXY($XcomG,$YcomG+16);
	$pdf->WriteHTML("Absences justifiées: $nbabs - $nbabsdnj non justifiées ");
	


	$XcomG2=$XcomG+$largeurMat;
	$YcomG2=$YcomG;
	$pdf->SetXY($XcomG2,$YcomG2);
	$pdf->MultiCell(155,$hauteurComG,'',1,'L',0);
	$XcomG3=90;
	$pdf->SetXY($XcomG3,$YcomG2+1);
	$pdf->SetFont('Arial','',$policeT+2);
	$pdf->WriteHTML("OBSERVATIONS DU CHEF D'&Eacute;TABLISSEMENT");
	$pdf->SetXY($XcomG3+19,$YcomG2+5);
	$pdf->WriteHTML("Overall Observations");

	$commentairedirection=recherche_com($idEleve,$_POST["saisie_trimestre"],"default");
	$commentairedirection=preg_replace("/\n/"," ",$commentairedirection);
	$pdf->SetXY($XcomG2+2,$YcomG2+15);
	$confPolice=confPolice2($commentairedirection);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->MultiCell(140,$confPolice[1],$commentairedirection,'','','L',0); // commentaire de la direction (visa)

	// commentaire prof principal
	$commentaireprofp=recherche_com_profP($idEleve,$_POST["saisie_trimestre"]);
	$commentaireprofp=preg_replace("/\n/"," ",$commentaireprofp);
	$pdf->SetXY($XcomG2+2,$YcomG2+15+13);
	$confPolice=confPolice2($commentaireprofp);  // $confPolice[1] -> Cadre ; $confPolice[0] -> Policy
	$pdf->SetFont('Arial','',$confPolice[0]);
	$pdf->MultiCell(140,$confPolice[1],$commentaireprofp,'','','L',0); // commentaire de la prof P (visa)


	$pdf->SetFont('Arial','',$policeT+2);
	$pdf->SetXY($XcomG,$YcomG2+$hauteurComG);
	$pdf->WriteHTML($duplicata);
	$pdf->SetXY($XcomG+149,$YcomG2+$hauteurComG);
	$pdf->WriteHTML($signature2);

	// sortie dans le fichier
	//
	
	// mise en place du logo
	$photo=recup_photo_bulletin();
	if (count($photo) > 0) {
		$logo="./data/image_pers/".$photo[0][0];
		if (file_exists($logo)) {
			$taille = getimagesize($logo);
			$logox=$taille[0]/28;
			$logoy=$taille[1]/28;
			$pdf->Image($logo,120,5);
		}
	}
	// fin du logo
 
// fin du for on passe à l'eleve suivant



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
