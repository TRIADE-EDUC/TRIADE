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
<font class='T2'>
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
	$totalcredit=0;
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	$pdf->AddPage();
	$titre="<B>".ucwords($textTrimestre)."</B>";
	$nomEleve=strtoupper(trim($nomEleve));
	$nomEleve=ucfirst(sansaccent(strtolower($nomEleve)));
	$prenomEleve=trim($prenomEleve);
	$nomprenom="$nomEleve $prenomEleve";
	$xcoor0=0;  
	$ycoor0=0; 
	$police="Arial";
	$policeT=8;
	// Debut création PDF
	// mise en place du cadre 
	//
	$pdf->SetFillColor(255);
	$pdf->SetFont($police,'B',$policeT+6);
	$pdf->SetXY($xcoor0,$ycoor0+=10);
	$pdf->MultiCell(210,3,"$classe_nom",0,'C',0);	

	$pdf->SetFont($police,'B',$policeT+4);
	$pdf->SetXY($xcoor0,$ycoor0+=5);
	$Pdate="ANNEE ACADEMIQUE $anneeScolaire";
	$pdf->MultiCell(210,3,"$Pdate",0,'C',0);

	$pdf->SetFont($police,'B',$policeT+2);
	$pdf->SetXY($xcoor0,$ycoor0+=5);
	$textTrimestre=ucfirst($textTrimestre);
	$pdf->MultiCell(210,3,"$textTrimestre",0,'C',0);
	
	$pdf->SetFont($police,'B',$policeT+2);
	$pdf->SetXY($xcoor0,$ycoor0+=5);
	$pdf->MultiCell(210,3,"$nomprenom",0,'C',0);
		



	// cadre menu 
	// ----------
	$ycoor0+=8;
	$xcoor0=10;
	$largeurMat=80;
	$hauteurMaT=15;
	$pdf->SetFillColor(220);
	$pdf->SetFont($police,'B',$policeT+1);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell($largeurMat,$hauteurMaT,"COURSE",1,'C',1);
	$larg1=18;
	$pdf->SetXY($xcoor0+$largeurMat,$ycoor0); 
	$pdf->MultiCell($larg1,$hauteurMaT,"",1,'L',1);
	$pdf->SetXY($xcoor0+$largeurMat,$ycoor0); 
	$pdf->MultiCell($larg1,5,"Hours\nper\nsemester",1,'C',1);	
	$pdf->SetXY($xcoor0+$largeurMat+$larg1,$ycoor0); 
	$pdf->MultiCell($larg1,$hauteurMaT,"Credits",1,'C',1);
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+$larg1,$ycoor0); 
	$pdf->MultiCell($larg1,$hauteurMaT,"Grade",1,'C',1);
	$pdf->SetXY($xcoor0+$largeurMat+$larg1+$larg1+$larg1,$ycoor0); 
	$pdf->MultiCell($larg1,$hauteurMaT,"Grade US",1,'C',1);

	$Xmat=$xcoor0;
	$Ymat=$ycoor0+$hauteurMaT;


	
	// mise en place des matieres
	$largeurMatiere=$largeurMat;
	$hauteurMatiere=8;



	$Xmatcont=$Xmat+1;
	$Ymatcont=$Ymat+1;

	$pdf->SetFillColor(255);
	// Mise en place des matieres et nom de prof
	for($i=0;$i<count($ordre);$i++) {
		$pdf->SetFont($police,'',$policeT);
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		$heure=$ordre[$i][6];

		if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere


   		// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
    		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);



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
		$pdf->MultiCell($largeurMat,$hauteurMatiere,'',1,'L',1);
		$pdf->SetXY($Xmatcont,$Ymatcont);
		$pdf->WriteHTML(''.trunchaine(strtoupper(sansaccent(strtolower($matiere))),39).'');
		
		$Ymatcont=$Ymatcont + $hauteurMatiere;

		// Mise en place Hours per semestre
		$pdf->SetXY($Xmat+$largeurMat,$Ymat);
		$pdf->MultiCell($larg1,$hauteurMatiere,"$heure",1,'C',0);
		// --------------------------------------------------------------------------------------------------


		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
			$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
		}



		// Mise en place credits
		if ($noteaff > 10) {
			$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		}else{
			$coeffaff="0.00";
		}
		$pdf->SetXY($Xmat+$largeurMat+$larg1,$Ymat);
		$pdf->MultiCell($larg1,$hauteurMatiere,"$coeffaff",1,'C',0);
		$totalcredit+=$coeffaff;
		// --------------------------------------------------------------------------------------------------




		// mise en place Grade
		// ----------------------------------------------------------------------------------------------------
		$noteaff1=arrondi($noteaff);
		if ($noteaff1 != "") $noteaff1=number_format($noteaff1,2,',','');
		$pdf->SetXY($Xmat+$largeurMat+$larg1+$larg1,$Ymat);
		$pdf->MultiCell($larg1,$hauteurMatiere,"$noteaff1",1,'C',0);
		if ( $noteaff != "" ) {
		        $noteMoyEleGTempo = $noteaff1 * 1;
        	        $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
         	        $coefEleG=$coefEleG + 1;
		    //  $noteMoyEleGTempo = $noteaff1 * $coeffaff;
        	    //  $noteMoyEleG=$noteMoyEleG + $noteMoyEleGTempo;
         	    //  $coefEleG=$coefEleG + $coeffaff;
		}
		// ---------------------------------------------------------------------------------------------------


		// mise en place Grade US
		$noteaffUS="";
		if ($noteaff1 != "") $noteaffUS=recherche_note_en($noteaff1); 
		$pdf->SetXY($Xmat+$largeurMat+$larg1+$larg1+$larg1,$Ymat);
		$pdf->MultiCell($larg1,$hauteurMatiere,"$noteaffUS",1,'C',0);
		// ----------------------------------------------------------------------------------------------------		


		$Ymat=$Ymat + $hauteurMatiere;


	}
	// fin de la mise en place des matiere
	//
$pdf->SetFont($police,'B',$policeT+2);
$pdf->SetFillColor(220);
$pdf->SetXY($Xmat,$Ymat);
$pdf->MultiCell($largeurMat,8," TOTAL AVERAGE GRADE",1,'L',1);
$pdf->SetXY($Xmat+$largeurMat,$Ymat);
$pdf->MultiCell($larg1,8,"",1,'C',1);
$pdf->SetXY($Xmat+$largeurMat+$larg1,$Ymat);
if ($totalcredit != "") $totalcredit=number_format($totalcredit,2,',','');
$pdf->MultiCell($larg1,8,"$totalcredit",1,'C',1); // somme Credits
$pdf->SetXY($Xmat+$largeurMat+$larg1+$larg1,$Ymat);
$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
$pdf->MultiCell($larg1,8,"$moyenEleve",1,'C',1); // somme grade
$pdf->SetXY($Xmat+$largeurMat+$larg1+$larg1+$larg1,$Ymat);
if ($moyenEleve != "") { $moyenEleveUS=recherche_note_en($moyenEleve); }
$pdf->MultiCell($larg1,8,"$moyenEleveUS",1,'C',1); // somme grade US

$noteMoyEleG="";
$coefEleG="";


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
