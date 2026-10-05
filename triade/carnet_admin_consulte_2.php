<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(3000);
}
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
 *   Site                 : http://www.triade-educ.org
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
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
validerequete("3");
$cnx=cnx();

if ($_SESSION['membre'] == "menuprof") { 
	$idcarnet=$_POST['saisie_carnet']; 
	$nom_carnet=chercheNomCarnet($idcarnet);
}

$idClasse=$_POST['saisie_classe'];
$nom_classe=chercheClasse_nom($idClasse);

if ($_SESSION['membre'] == "menuadmin") {
	if ((isset($_POST["modif"])) &&  ($_POST["saisie_carnet"] > 0)) {
		$idcarnet=$_POST["saisie_carnet"];
		$nom_carnet=chercheNomCarnet($idcarnet);
	}else{
		if ($_SESSION['membre'] == "menuadmin") {
		print "<script>location.href='carnet_admin_modif.php?erreur'</script>";
		}
	}
}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Consultation du Carnet de Suivi : <font id='color2'> $nom_carnet </font>" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br />
<?php
//----------------------------------------------------------------
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/lib.php');
//include_once('./librairie_pdf/html2pdf.php');

$pdf=new RPDF('P','mm','A4');

$eleveT=recupEleve($idClasse); // nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve,tel_fixe_eleve

for($jT=0;$jT<countTriade($eleveT);$jT++) {

        $nomEleve=ucwords($eleveT[$jT][0]);
        $prenomEleve=ucfirst($eleveT[$jT][1]);
        $lv1Eleve=$eleveT[$jT][2];
        $lv2Eleve=$eleveT[$jT][3];
        $idEleve=$eleveT[$jT][4];
        $datenaissance=dateForm($eleveT[$jT][5]);


//$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Carnet de suivi");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Carnet de suivi"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.org"); 

//
//$pdf->WriteHTML($nom_carnet);

$x=3;
$y=3;

$sizePolice="12";
$fontPolice="Arial";


/* 1er cadre */

$data=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays
for($i=0;$i<countTriade($data);$i++) {
       $nom_etablissement=strtoupper(trim($data[$i][0]));
       $adresse=strtoupper(trim($data[$i][1]));
       $postal=strtoupper(trim($data[$i][2]));
       $ville=strtoupper(trim($data[$i][3]));
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
       $academie=strtoupper(trim($data[$i][8]));
       $pays=strtoupper(trim($data[$i][9]));
}

$x=3;
$y=3;
$pdf->Image("./image/commun/logo-educnational.jpg",$x,$y);
$x=28;

$pdf->SetXY($x,$y);
$pdf->SetFont($fontPolice,'',$sizePolice-2);
$pdf->MultiCell(30,3,"Académie",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(30,3,"Département",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(30,3,"Circonscription",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(30,3,"Ecole",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(30,3,"Adresse",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(30,3,"Téléphone",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(30,3,"Courriel",0,'L',0);
// Reponse 
$x+=32;
$y=3;
$pdf->SetXY($x,$y);
$pdf->MultiCell(80,3,"$academie",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"$postal",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"$nom_etablissement",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"$adresse",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"$tel",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"$mail",0,'L',0);
$y=3;

$anneeScolaire=anneeScolaireViaIdClasse($idClasse);
$pdf->SetXY(130,$y);
$pdf->SetFont($fontPolice,'B',$sizePolice);
$pdf->MultiCell(80,3,"Année scolaire $anneeScolaire",0,'L',0);
$pdf->SetFont($fontPolice,'',$sizePolice-2);
$y+=5;
$x+=53;

if ($datenaissance == "") $datenaissance=".......................";
if ($nomEleve == "") $nomEleve=".......................";
$classe_nom=chercheClasse_nom(chercheClasseEleve($idEleve));
if ($classe_nom == "") $classe_nom=".......................";

$pdf->SetXY($x,$y);
$pdf->MultiCell(80,3,"Né le $datenaissance",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"Elève $nomEleve $prenomEleve",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"Cycle / Niveau ....................",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"Classe de $classe_nom",0,'L',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(80,3,"Enseignant(e)(s) .......................",0,'L',0);

/* 1er cadre (Style de note) */
$x=3;
$y+=12; 
$pdf->SetFont($fontPolice,'B',$sizePolice);
$pdf->SetFillColor(210);
$pdf->SetXY($x,$y);
$pdf->MultiCell(205,10,"Bilan des acquis scolaires de l'élève",1,'C',1);
$pdf->SetXY($x,$y+=15);
$pdf->MultiCell(205,10,"Suivi des acquis scolaires de l'élève",1,'C',1);
$pdf->SetFillColor(255);

$y+=15;

$pdf->SetFont($fontPolice,'',$sizePolice);
$pdf->SetXY($x,$y);
$pdf->MultiCell(109,25,"",1,'C',0);
$pdf->SetXY($x,$y+3);
$pdf->MultiCell(109,5,"Éléments du programme travaillés durant la période (connaissances/compétences)",0,'C',0);

$pdf->SetXY($x+=109,$y);
$pdf->MultiCell(54,25,"",1,'C',1);
$pdf->SetXY($x,$y+3);
$pdf->SetFont($fontPolice,'',$sizePolice);
$pdf->MultiCell(54,5,"Acquisitions, progrès et difficultés éventuelles",0,'C',0);

$pdf->SetFont($fontPolice,'',$sizePolice-4);

$pdf->SetXY($x+=54,$y);
$pdf->MultiCell(41,10,"",1,'C',1);
$pdf->SetXY($x,$y+1);
$pdf->MultiCell(41,3,"Positionnement Objectifs d'apprentissage",0,'C',0);
$pdf->SetXY($x,$y+=10);
$pdf->MultiCell(10,15,"",1,'C',1);
$pdf->TextWithRotation($x+3,$y+10,"Non",'90');
$pdf->TextWithRotation($x+6,$y+12,"atteints",'90');
$pdf->SetXY($x+=10,$y);
$pdf->MultiCell(10,15,"",1,'C',1);
$pdf->SetFont($fontPolice,'',$sizePolice-5);
$pdf->TextWithRotation($x+3,$y+14,"Partielleme",'90');
$pdf->TextWithRotation($x+6,$y+14,"nts atteints",'90');

$pdf->SetFont($fontPolice,'',$sizePolice-4);
$pdf->SetXY($x+=10,$y);
$pdf->MultiCell(10,15,"",1,'C',1);
$pdf->SetFont($fontPolice,'',$sizePolice-5);
$pdf->TextWithRotation($x+5,$y+12,"Atteints",'90');

$pdf->SetXY($x+=10,$y);
$pdf->MultiCell(11,15,"",1,'C',1);
$pdf->SetFont($fontPolice,'',$sizePolice-5);
$pdf->TextWithRotation($x+5,$y+12,utf8_decode("Dépassés"),'90');

$y+=10;

// ---------------------------------------------------------------------------------------

$tabCompetence=listeCompetence($idcarnet); //  id,idcarnet,libelle,ordre
$tabSection=chercheSectionCarnet($idcarnet);


for ($i=0;$i<countTriade($tabCompetence);$i++) {
	$x=3;
	$idcompetence=$tabCompetence[$i][0];
	$tabDescriptif=rechercheDescriptif($idcompetence,$idcarnet); // id,libelle,bold,ordre
	$matiere=stripslashes($tabCompetence[$i][2]);

	$pdf->SetFont($fontPolice,'',$sizePolice-4);
	$pdf->SetXY($x,$y+2);
	$y+=5;
	$hauteurZ=10;
	for($jjj=0;$jjj<countTriade($tabDescriptif);$jjj++) {
		$iddescriptif=$tabDescriptif[$jjj][0];
		$libelle=stripslashes(stripslashes($tabDescriptif[$jjj][1]));
		$bold=$tabDescriptif[$jjj][2];
		if ($bold == 1) {	
			$x=3;
			$pdf->SetFont($fontPolice,'',$sizePolice);
			$pdf->SetFillColor(210);
			$pdf->SetXY($x,$y);
			$pdf->MultiCell(204,10,"",1,'',1);
			$pdf->SetXY($x,$y);
			if (strlen("$matiere / $libelle") >= 90) $pdf->SetFont($fontPolice,'',$sizePolice-2);
			if (strlen("$matiere / $libelle") >= 130) $libelle=trunchaine($libelle,70); 
			$pdf->MultiCell(204,10,"$matiere / $libelle",0,"C",0);
			$x+=50;
			$y+=10;
		}else{
			$pdf->SetFont($fontPolice,'',$sizePolice-3);
			$pdf->SetFillColor(255);
			$len=strlen($libelle);
			$hauteurZ=($len/40)*3.5;
			$hauteurZ=number_format($hauteurZ,0,'','');
			if ($hauteurZ < 10) $hauteurZ=8;

			if ($y+$hauteurZ > 260) {
                        	$pdf->AddPage();
	                        $y=3;
        	                $x=3;
                	}



			$pdf->SetXY(3,$y);
			$pdf->MultiCell(109,$hauteurZ,"",1,'',1);
			$pdf->SetXY(3,$y+1);
			$libelle=preg_replace("/\n/","\n- ",$libelle);
			$pdf->MultiCell(109,3.5,"- $libelle",0,"L",0);

			$pdf->SetFillColor(210);

			$pdf->SetXY(112,$y);
			$pdf->MultiCell(54,$hauteurZ,"",1,'',0);
	
			unset($note);	
			$note=recupNoteCarnetSuivi($idEleve,$idcompetence,$idcarnet,$iddescriptif,"educnational",$idClasse);

			$pdf->SetXY(112+54,$y);
			if ($note == "1") { $etat="1"; $signe="X";  }else{ $etat="0"; $signe=""; }
			$pdf->MultiCell(10,$hauteurZ,"$signe",1,'C',$etat);
	
	
			$pdf->SetXY(112+54+10,$y);
			if ($note == "2") { $etat="1"; $signe="X"; }else{ $etat="0"; $signe=""; }
			$pdf->MultiCell(10,$hauteurZ,"$signe",1,'C',$etat);

			$pdf->SetXY(112+54+10+10,$y);
			if ($note == '3') { $etat="1"; $signe="X"; }else{ $etat="0"; $signe=""; }
			$pdf->MultiCell(10,$hauteurZ,"$signe",1,'C',$etat);

			$pdf->SetXY(112+54+10+10+10,$y);
			if ($note == '4') { $etat="1"; $signe="X"; }else{ $etat="0"; $signe=""; }
			$pdf->MultiCell(11,$hauteurZ,"$signe",1,'C',$etat);

			$pdf->SetFillColor(255);

			$y+=$hauteurZ;
		}

		if ($y > 260) {
			$pdf->AddPage();
			$y=3;
			$x=3;
		}	
	}

	if ($y > 260) {
		$pdf->AddPage();
		$y=3;
		$x=3;
	}

}
	$y+=10;	

	$pdf->SetXY(3,$y);
	$pdf->SetFillColor(210);
	$pdf->SetFont($fontPolice,'',$sizePolice);
	$pdf->MultiCell(205,10,"Bilan de l'acquisition des connaissances et compétences",1,'C',1);

	$pdf->SetXY(3,$y+=10);
	$pdf->MultiCell(205,60,"",1,'L',0);
	$pdf->SetXY(4,$y+1);
	$pdf->SetFont($fontPolice,'B',$sizePolice-2);
	$pdf->MultiCell(203,10,"Appréciation générale sur la progression de l'élève",0,'L',1);
	$pdf->SetFont($fontPolice,'',$sizePolice-2);
	$pdf->SetXY(3,$y+12);
	$pdf->MultiCell(203,10,"Appréciation personnelle de l’enseignant(e) / des enseignant(e)s",0,'L',0);
	$pdf->SetXY(100,$y+22);
	$date=dateDMY();
	$pdf->MultiCell(205,10,"Le $date",0,'L',0);
	$pdf->SetXY(100,$y+27);
	$pdf->MultiCell(205,10,"Signature de l'enseignant(e) / des enseignant(e)s",0,'L',0);

	$y+=60;	
	
	$y+=10;
	$pdf->SetXY(3,$y);
	$pdf->SetFillColor(210);
        $pdf->SetFont($fontPolice,'',$sizePolice);
        $pdf->MultiCell(205,10,"Communication avec les familles",1,'C',1);
	$pdf->SetXY(3,$y+=10);
        $pdf->MultiCell(205,60,"",1,'L',0);
        $pdf->SetXY(4,$y+1);
        $pdf->SetFont($fontPolice,'B',$sizePolice-2);
        $pdf->MultiCell(203,10,"Visa des parents ou du responsable légal",0,'L',1);
	$pdf->SetFont($fontPolice,'',$sizePolice-2);
        $pdf->SetXY(100,$y+22);
        $date=dateDMY();
        $pdf->MultiCell(205,10,"Pris connaissance le :",0,'L',0);
        $pdf->SetXY(100,$y+27);
        $pdf->MultiCell(205,10,"Signatures :",0,'L',0);
	
	$pdf->SetXY(3,$y+61);
	$text="Conformément aux articles 39 et suivants de la loi n° 78-17 du 6 janvier 1978 modifiée en 2004 relative à l’informatique, aux fichiers et aux libertés, toute personne peut obtenir communication et, le cas échéant, rectification ou suppression des informations la concernant, en s’adressant à son établissement scolaire.";
        $pdf->SetFont($fontPolice,'I',$sizePolice-4);
	$pdf->MultiCell(205,3,"$text",0,'L',0);

}

// ---------------------------------------------------------------


// ---------------------------------------------------------------
if (!is_dir('./data/pdf_carnet')) mkdir('./data/pdf_carnet'); 
$nom_carnet=preg_replace('/\//','_',$nom_carnet);
$nom_carnet=preg_replace('/ /','_',$nom_carnet);
$fichier="./data/pdf_carnet/${nom_classe}.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
//----------------------------------------------------------------
//
?>
<br />

<font class="T2">&nbsp;&nbsp;<?php print LANGCARNET57 ?> :</font> 
<?php if ($_SESSION['membre'] == "menuadmin") { ?>
	<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print CLICKICI ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } 
if ($_SESSION['membre'] == "menuprof") { ?>
	<input type=button onclick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');" value="<?php print CLICKICI ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } ?>


<br><br>
<?php if ($_SESSION['membre'] == "menuadmin") { ?>
<script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<?php } ?>
<?php if ($_SESSION['membre'] == "menuprof") { ?>
<script language=JavaScript>buttonMagicRetour2("profp2.php","_parent","<?php print LANGCIRCU14?>");</script>
<?php } ?>
<br><br>


<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
