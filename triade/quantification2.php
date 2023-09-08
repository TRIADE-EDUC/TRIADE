<?php
session_start();
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");

include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}

$cnx=cnx();
$datedebut=$_POST["saisie_date_debut"];
$datefin=$_POST["saisie_date_fin"];
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Quantification des heures entre $datedebut et $datefin " ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
     <!-- // fin  -->
<?php

$nature=$_POST["saisie_nature"];
$prestation=$_POST["prestation"];
$annule=$_POST["annule"];

if ($nature == "classe") {
	
	$data=listingQuantite($datedebut,$datefin,$prestation,$annule); // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule
	for($i=0;$i<count($data);$i++) {
		$idclasse=$data[$i][7];
		$duree=conv_en_seconde($data[$i][5]);
		if ($idclasse > 0) {
			$tabClasse[$idclasse]+=$duree;
		}
	}

	print "<table width=100% border='1'  style='border-collapse: collapse;'  >";
	print "<tr>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Classes&nbsp;</font></td>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Durée&nbsp;</font></td>";
	print "</tr>";
	foreach($tabClasse as $idclasse => $duree) {
		print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<td><font class=T2>&nbsp;".ucwords(chercheClasse_nom($idclasse))."</font></td>";
		print "<td><font class=T2>".calcul_hours2($duree)."</font><br>";
		print "<font class=T1> soit : ".convert_sec($duree)."</font></td>";
		print "</tr>";
	}
	print "</table>";
}

if ($nature == "enseignant") {
	$data=listingQuantite($datedebut,$datefin,$prestation,$annule); // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere
	for($i=0;$i<count($data);$i++) {
		$idprof=$data[$i][8];
		if  (verifSiProfActif($idprof)) { continue; }
		$duree=conv_en_seconde($data[$i][5]);
		if ($idprof > 0) {
			$tabClasse[$idprof]+=$duree;
		}
	}
	print "<table width=100% border='1' style='border-collapse: collapse;' >";
	print "<tr>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Enseignants&nbsp;</font></td>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Durée&nbsp;</font></td>";
	print "</tr>";
	
	foreach($tabClasse as $idprof => $duree) {
		print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<td>$n<font class=T2>&nbsp;".trunchaine(recherche_personne($idprof),50)."</font></td>";
		print "<td><font class=T2>".calcul_hours2($duree)."</font><br>";
		print "<font class=T1> soit : ".convert_sec($duree)."</font></td>";
		print "</tr>";
	}
	print "</table>";
}


if ($nature == "matiere") {
	
	$data=listingQuantite($datedebut,$datefin,$prestation,$annule); // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere
	for($i=0;$i<count($data);$i++) {
		$idMatiere=$data[$i][10];
		$duree=conv_en_seconde($data[$i][5]);
		if ($idMatiere > 0) {
			$tabClasse[$idMatiere]+=$duree;
		}
	}

	print "<table width=100% border='1' style='border-collapse: collapse;' >";
	print "<tr>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Matières&nbsp;</font></td>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Durée&nbsp;</font></td>";
	print "</tr>";
	foreach($tabClasse as $idMatiere => $duree) {
		print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >\n";
		$nomMatiere=ucwords(chercheMatiereNom($idMatiere));
		$nomMatiere=html_quotes($nomMatiere);
		print "<td><font class=T2>&nbsp;<a href='#' onMouseOver='AffBulle(\"$nomMatiere\");'  onMouseOut='HideBulle()' >".trunchaine($nomMatiere,50)."</a></font></td>\n";
		print "<td><font class=T2>".calcul_hours2($duree)."</font><br>\n";
		print "<font class=T1> soit : ".convert_sec($duree)."</font></td>\n";
		print "</tr>\n";
	}
	print "</table>";



}


if ($nature == "prestation") {
	$data=listingQuantite($datedebut,$datefin,$prestation,$annule); // id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere
	for($i=0;$i<count($data);$i++) {
		$idpresta=$data[$i][9];
		$duree=conv_en_seconde($data[$i][5]);
		if ($idpresta > 0) {
			$tabClasse[$idpresta]+=$duree;
		}
	}
	print "<table width=100% border='1' style='border-collapse: collapse;' >";
	print "<tr>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Prestations&nbsp;</font></td>";
	print "<td bgcolor='yellow'><font class=T2>&nbsp;Durée&nbsp;</font></td>";
	print "</tr>";
	foreach($tabClasse as $idpresta => $duree) {
		print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		$nomPresta=affEvalHoraireMotif($idpresta);
		$nomPresta=html_quotes($nomPresta);
		print "<td><font class=T2>&nbsp;<a href='#' onMouseOver='AffBulle(\"".$nomPresta[0][1]."\");'  onMouseOut='HideBulle()' >".trunchaine($nomPresta[0][1],50)."</a></font></td>";
		print "<td><font class=T2>".calcul_hours2($duree)."</font><br>";
		print "<font class=T1> soit : ".convert_sec($duree)."</font></td>";
		print "</tr>";
	}
	print "</table>";
}

// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF('L');  // declaration du constructeur

$pdf->SetTitle("Quantification");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Quantification"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
$X=3;
$Y=3;
$pdf->SetFont('Arial','',9);
$Largeur=28;
$Hauteur=10;

$deb=0;
$fin=9;
$deb2=0;
$fin2=16;

$nbProf=count(affPers("ENS"));
$nbClasse=count(affClasse());
$nbMatiere=count(affToutesLesMatieres());

while(true) {


	if ($nature == "matiere") {
		$tabV=affClasseLimit($deb2,$fin2); // code_class,libelle
		$tabH=affMatiereLimit($deb,$fin); // code_mat,libelle,sous_matiere
	}

	if ($nature == "classe") {
		$tabV=affClasseLimit($deb2,$fin2); // code_class,libelle
		$tabH=affPersDebutFin("ENS",$deb,$fin);  // pers_id, civ, nom, prenom,
	}

	if ($nature == "enseignant") {
		$tabV=affPersDebutFin("ENS",$deb2,$fin2);  // pers_id, civ, nom, prenom,
		$tabH=affMatiereLimit($deb,$fin); // code_mat,libelle,sous_matiere
	}

	if (count($tabH) == 0) { 
		//
	}else{ 
		$idV="";
		$pdf->addPage(); 
		$X=3;
		$Y=3; 
	} 


	for($i=0;$i<=count($tabV);$i++) { // code_class,libelle
		$pdf->SetXY($X+=$Largeur,$Y);
		for($j=0;$j<count($tabH);$j++) { 	//code_mat,libelle,sous_matiere
			$pdf->SetXY($X,$Y);
			$idH=$tabH[$j][0];
			if ($idV == "") {
				if ($nature == "matiere") {
					$ssmatiere=$tabH[$j][2];
					if ($ssmatiere == "0") { $ssmatiere=""; }
					$libelle=trunchaine($tabH[$j][1]." ".$ssmatiere,35);
				}
				if ($nature == "classe") {
					$libelle=trunchaine(ucwords($tabH[$j][2])." ".ucfirst($tabH[$j][3]),35);
				}
				if ($nature == "enseignant") {
					$ssmatiere=$tabH[$j][2];
					if ($ssmatiere == "0") { $ssmatiere=""; }
					$libelle=trunchaine($tabH[$j][1]." ".$ssmatiere,35);
				}
			}else{
				if ($nature == "matiere") {
					$idMatiere=$idH;
					$idClasse=$idV;
					$data=listingQuantiteMatiereClasse($datedebut,$datefin,$idMatiere,$idClasse,$prestation,$annule);
				}
				if ($nature == "classe") {
					$idPers=$idH;
					$idClasse=$idV;			
					$data=listingQuantiteClasseEnseignant($datedebut,$datefin,$idPers,$idClasse,$prestation,$annule);
				}
				if ($nature == "enseignant") {
					$idMatiere=$idH;
					$idPers=$idV;
					$data=listingQuantiteMatiereEnseignant($datedebut,$datefin,$idMatiere,$idPers,$prestation,$annule);
				}
				//id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule
				$duree="";$seconde="";
				for($a=0;$a<count($data);$a++) { 
					$idpresta=$data[$a][9];
					$duree+=conv_en_seconde($data[$a][5]);
					if ($idpresta > 0) { 
						$tabClasse[$idpresta]+=$duree;
					}
				}
				if ($duree > 0) {
					$duree=calcul_hours2($duree);
				}
				$libelle="$duree ";
			}
			$pdf->MultiCell($Largeur,$Hauteur,'',1,'L',0);
			$pdf->SetXY($X,$Y+1);
			$pdf->MultiCell($Largeur,3,$libelle,0,'L',0);
	
			$X+=$Largeur;
			$libelle="";
		}
		$X=3;
		$idV=$tabV[$i][0];
		$Y+=$Hauteur;
		$pdf->SetXY($X,$Y);
		if ($nature == "enseignant") {
			$libelle=trunchaine(ucwords($tabV[$i][2])." ".ucfirst($tabV[$i][3]),35);
		}else{
			$libelle=ucwords($tabV[$i][1]);
		}
		if (trim($libelle) != "") {
			$pdf->MultiCell($Largeur,$Hauteur,'',1,'L',0);
			$pdf->SetXY($X+1,$Y+1);
			$pdf->MultiCell($Largeur,3,$libelle,0,'L',0);
		}
		$libelle="";
	}

	

	if ($nature == "enseignant") {
		if ($deb < $nbMatiere) {
			$deb=$deb+9;
		}else{
			if ($deb2 < $nbProf) {
				$deb2=$deb2+16;
				//$deb=0;	
			}else{
				break;
			}
		}
	}else{
		if ($deb < $nbMatiere) {
			$deb=$deb+9;	
		}else{
			if ($deb2 < $nbClasse) {
				$deb2=$deb2+16;
				//$deb=0;	
			}else{
				break;
			}	
		}
	}
	$X=3;$Y=3;
}
	
if (!is_dir("./data/pdf_quantification")) { mkdir("./data/pdf_quantification"); htaccess("./data/pdf_quantification"); }
$fichier="./data/pdf_quantification/quantif_".$_SESSION["id_pers"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();

?>
<br>
<table align='center'>
<tr>
<td><script>buttonMagicPrecedent2()</script></td>
<td>
<?php if ($nature != "prestation") { ?>
<!-- <input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print "Détail" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"> -->
<?php } ?>
</td></tr></table>
<br>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
</BODY></HTML>
