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
?>
<HTML>
<HEAD>
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
validerequete("menuadmin");
$cnx=cnx();

if ((isset($_POST["modif"])) &&  ($_POST["saisie_carnet"] > 0)) {
	$idcarnet=$_POST["saisie_carnet"];
	$nom_carnet=chercheNomCarnet($idcarnet);
}else{
	print "<script>location.href='carnet_admin_modif.php?erreur'</script>";
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
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
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Carnet de compétence");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Carnet de compétence"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

//
//$pdf->WriteHTML($nom_carnet);

$x=3;
$y=3;

$sizePolice="12";
$fontPolice="Arial";


/* 1er cadre */

$data=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $urlsite=trim($data[$i][7]);
       $academie=trim($data[$i][8]);
       $pays=strtoupper(trim($data[$i][9]));
}

$pdf->SetFont($fontPolice,'B',$sizePolice);
$pdf->SetXY($x,$y);
$pdf->MultiCell(204,33,"",1,'C',0);
$pdf->SetXY($x,$y+1);
$pdf->MultiCell(204,5,"$nom_carnet",0,'C',0);
$pdf->SetXY($x,$y);
$pdf->SetFont($fontPolice,'',$sizePolice);
$pdf->MultiCell(204,5,"\n\n${nom_etablissement}\n${pays}",0,'C',0);
$pdf->SetXY($x,$y+25);
$pdf->WriteHTML("Classe : ................  Enseignant(s) : .......................................");

/* 2eme cadre (Eleve) */
$x=3;
$y+=33; 

$pdf->SetXY($x,$y+3);
$pdf->SetFont($fontPolice,'B',$sizePolice);
$pdf->MultiCell(20,5,"ÉLÈVE",1,'L',0);
$pdf->SetXY($x+20,$y+3);
$pdf->SetFont($fontPolice,'',$sizePolice-2);
$pdf->MultiCell(184,5,"Nom : ........................................... Prénom : ...........................................  Né(e) le .........................",1,'L',0);


/* 3eme cadre (Style de note) */
$x=3;
$y+=8; 
$pdf->SetXY($x+=50,$y+3);
$pdf->MultiCell(25,10,"acquis",1,'C',0);
$pdf->SetXY($x+=25,$y+3);
$pdf->MultiCell(25,10,"à confirmer",1,'C',0);
$pdf->SetXY($x+=25,$y+3);
$pdf->MultiCell(25,5,"en cours dacquisition",1,'C',0);
$pdf->SetXY($x+=25,$y+3);
$pdf->MultiCell(25,10,"non acquis",1,'C',0);

$x=3;
$y+=3;
$pdf->SetXY($x,$y+10);
$pdf->SetFont($fontPolice,'',$sizePolice-5);
$pdf->MultiCell(25,5,"Codes dappréciation pouvant être choisis par lenseignant\n\n\n",1,'C',0);
$pdf->SetXY($x+=25,$y+10);
$pdf->SetFont($fontPolice,'B',$sizePolice-2);
$data=chercheTypeNotation($idcarnet); // code_lettre,code_chiffre,code_couleur,code_note
$chiffre=($data[0][1] == 1) ? "Chiffres -> \n" : "\n";
$code_lettre=($data[0][0] == 1) ? "Lettres -> \n" : "\n";
$code_couleur=($data[0][2] == 1) ? "Couleurs -> \n" : "\n";
$pdf->MultiCell(25,5,"$chiffre $code_lettre $code_couleur\n",1,'C',0);

$chiffre=($data[0][1] == 1) ? "1\n" : "\n";
$code_lettre=($data[0][0] == 1) ? "A\n" : "\n";
$code_couleur=($data[0][2] == 1) ? "Vert\n" : "\n";
$pdf->SetXY($x+=25,$y+10);
$pdf->MultiCell(25,5,"$chiffre $code_lettre $code_couleur\n",1,'C',0);

$chiffre=($data[0][1] == 1) ? "2\n" : "\n";
$code_lettre=($data[0][0] == 1) ? "B\n" : "\n";
$code_couleur=($data[0][2] == 1) ? "Bleu\n" : "\n";
$pdf->SetXY($x+=25,$y+10);
$pdf->MultiCell(25,5,"$chiffre $code_lettre $code_couleur\n",1,'C',0);

$chiffre=($data[0][1] == 1) ? "3\n" : "\n";
$code_lettre=($data[0][0] == 1) ? "C\n" : "\n";
$code_couleur=($data[0][2] == 1) ? "Orange\n" : "\n";
$pdf->SetXY($x+=25,$y+10);
$pdf->MultiCell(25,5,"$chiffre $code_lettre $code_couleur\n",1,'C',0);


$chiffre=($data[0][1] == 1) ? "4\n" : "\n";
$code_lettre=($data[0][0] == 1) ? "D\n" : "\n";
$code_couleur=($data[0][2] == 1) ? "Rouge\n" : "\n";
$pdf->SetXY($x+=25,$y+10);
$pdf->MultiCell(25,5,"$chiffre $code_lettre $code_couleur\n",1,'C',0);

$pdf->SetFont($fontPolice,'',$sizePolice-5);
$pdf->SetXY($x+=25,$y+10);
$pdf->MultiCell(25,5,"X\nCompétence travaillée mais non évaluée\n",1,'C',0);

if ($data[0][2] == 1) {
	$code_note="Notes";
	$texte_note="0 à 10 ou 0 à 20";
}else{
	$code_note="";
	$texte_note="";
}

$x=3;
$y+=10;
$pdf->SetXY($x+25,$y+20);
$pdf->SetFont($fontPolice,'B',$sizePolice-2);
$pdf->MultiCell(25,5,"$code_note\n\n",1,'C',0);
$pdf->SetXY($x+50,$y+20);
$pdf->MultiCell(125,5,"$texte_note\n\n",1,'C',0);


/* Definition des périodes */

$nbPeriode=nb_periode($idcarnet);


$y+=35;

if ($nbPeriode == 1) { $largeurCom=60; }
if ($nbPeriode == 2) { $largeurCom=40; }
if ($nbPeriode == 3) { $largeurCom=30; }
if ($nbPeriode == 4) { $largeurCom=20; }
if ($nbPeriode == 5) { $largeurCom=15; }


for($i=1;$i<=$nbPeriode;$i++) {

$x=3;

$pdf->SetFont($fontPolice,'B',$sizePolice-2);
$pdf->SetXY($x,$y);
$pdf->MultiCell(110,5,"Période $i",1,'C',0);
$pdf->SetXY($x+110,$y);
$pdf->MultiCell(94,5,"SIGNATURES",1,'C',0);
$pdf->SetFont($fontPolice,'',$sizePolice-2);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(110,5,"Observation de lenseignant/des enseignants ",1,'C',0);
$pdf->SetXY($x+110,$y);
$pdf->MultiCell(31,5,"Enseignant(s)",1,'C',0);
$pdf->SetXY($x+110+31,$y);
$pdf->MultiCell(31,5,"Direction",1,'C',0);
$pdf->SetXY($x+110+31+31,$y);
$pdf->MultiCell(32,5,"Parents",1,'C',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(110,$largeurCom,"",1,'C',0);
$pdf->SetXY($x+110,$y);
$pdf->MultiCell(31,$largeurCom,"",1,'C',0);
$pdf->SetXY($x+110+31,$y);
$pdf->MultiCell(31,$largeurCom,"",1,'C',0);
$pdf->SetXY($x+110+31+31,$y);
$pdf->MultiCell(32,$largeurCom,"",1,'C',0);

$y+=$largeurCom+5;

}

$pdf->SetFont($fontPolice,'',$sizePolice-2);
$pdf->SetXY($x,$y);
$pdf->MultiCell(110,5,"Décision de léquipe pédagogique",1,'C',0);
$pdf->SetXY($x+110,$y);
$pdf->MultiCell(94,5,"",1,'C',0);
$pdf->SetFont($fontPolice,'B',$sizePolice-2);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(55,5,"Admis(e)",1,'C',0);
$pdf->SetXY($x+55,$y);
$pdf->MultiCell(55,5,"Maintenu(e)",1,'C',0);
$pdf->SetXY($x+110,$y);
$pdf->SetFont($fontPolice,'',$sizePolice-2);
$pdf->MultiCell(31,5,"Enseignant(s)",1,'C',0);
$pdf->SetXY($x+110+31,$y);
$pdf->MultiCell(31,5,"Direction",1,'C',0);
$pdf->SetXY($x+110+31+31,$y);
$pdf->MultiCell(32,5,"Parents",1,'C',0);
$pdf->SetXY($x,$y+=5);
$pdf->MultiCell(110,$largeurCom,"",1,'C',0);
$pdf->SetXY($x+110,$y);
$pdf->MultiCell(31,$largeurCom,"",1,'C',0);
$pdf->SetXY($x+110+31,$y);
$pdf->MultiCell(31,$largeurCom,"",1,'C',0);
$pdf->SetXY($x+110+31+31,$y);
$pdf->MultiCell(32,$largeurCom,"",1,'C',0);
// ---------------------------------------------------------------------------------------



// ---------------------------------------------------------------------------------------
$pdf->AddPage();

$tabCompetence=listeCompetence($idcarnet); // id,idcarnet,libelle,ordre
$tabSection=chercheSectionCarnet($idcarnet);


$x=3;
$y=3;

$largeurSection=$nbPeriode*12;
//print $largeurSection."<br>";
$nbSection=count($tabSection);
//print $nbSection."<br>";
$largeurSectionTotal=$nbSection*$largeurSection;
//print $largeurSectionTotal."<br>";
$largeurCommentaire=204-$largeurSectionTotal;
//print $largeurCommentaire."<br>";

for ($i=0;$i<count($tabCompetence);$i++) {
	$x=3;
	$idcompetence=$tabCompetence[$i][0];
	$tabDescriptif=rechercheDescriptif($idcompetence,$idcarnet); // id,libelle,bold,ordre
	$libelle=$tabCompetence[$i][2];
	$pdf->SetFont($fontPolice,'B',$sizePolice);
	$pdf->SetXY($x,$y+2);
	$pdf->MultiCell(204,5,"$libelle",0,'C',0);
	$y+=10;
	$x+=$largeurCommentaire;
	$pdf->SetFont($fontPolice,'B',$sizePolice-3);
	foreach($tabSection as $key=>$value) {
		$value=chercheNomSection($value);
		$pdf->SetXY($x,$y);
		$pdf->MultiCell($largeurSection,5,"$value",1,'C',0);
		$x+=$largeurSection;
	}
	$y+=5;
	$x=3+$largeurCommentaire;
	for($jj=0;$jj<$nbSection;$jj++) {
		for($j=1;$j<=$nbPeriode;$j++) {
			$pdf->SetXY($x,$y);
			$pdf->MultiCell(12,5,"T$j",1,'C',0);
			$x+=12;
		}
	}
	$y+=5;
	for($jjj=0;$jjj<count($tabDescriptif);$jjj++) {
		$libelle=$tabDescriptif[$jjj][1];
		$bold=$tabDescriptif[$jjj][2];
		$x=3;
		
		$pdf->SetXY($x,$y);
		if ($bold) { 
			$pdf->SetFillColor(210);
			$hauteurCommentaire=5;
			$center='C'; 
			$B='B';
		}else { 
			$pdf->SetFillColor(255);
			$hauteurCommentaire=10;
			$center='L'; 
			$B='';
		}
		$pdf->SetFont($fontPolice,$B,$sizePolice-3);
		$pdf->MultiCell($largeurCommentaire,$hauteurCommentaire,"",1,'',1);
		$pdf->SetXY($x,$y);
		$pdf->MultiCell($largeurCommentaire,5,"$libelle",0,$center,0);
		$x+=$largeurCommentaire;
		for($jj=0;$jj<$nbSection;$jj++) {
			for($j=1;$j<=$nbPeriode;$j++) {
				$pdf->SetXY($x,$y);
				$pdf->MultiCell(12,$hauteurCommentaire,"",1,'C',1);  // --> VALEUR A REMPLIR
				$x+=12;
			}
		}
		$pdf->SetFillColor(255);

		$y+=$hauteurCommentaire;
		if ($y > 260) {
			$pdf->AddPage();
			$y=3;
		}	
	}

	if ($y > 260) {
		$pdf->AddPage();
		$y=3;
	}

}
// ---------------------------------------------------------------


// ---------------------------------------------------------------
$nom_carnet=preg_replace('/\//','_',$nom_carnet);
$nom_carnet=preg_replace('/ /','_',$nom_carnet);
$fichier="./data/parametrage/${nom_carnet}.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
//----------------------------------------------------------------
//
?>
<br />

<font class="T2">&nbsp;&nbsp;<?php print LANGCARNET57 ?> :</font> 
	<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print CLICKICI ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<br><br>
<script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<br><br>


<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
