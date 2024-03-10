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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importation d'un fichier Excel" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);
$cnx=cnx();

$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];
$anneeScolaire=$_POST["anneeScolaire"];

$ok=0;

$taille=2000000;
$taille2="2Mo";

if ($id != 1) {
	set_time_limit(600);
	$taille=8000000;
	$taille2="8Mo";
}

$fic_xls="data/fichier_gep/traitement-ipac.xls";
@unlink($fic_xls);
if ( (!empty($fichier)) && (($type == "application/octet-stream" ) || ($type == "application/vnd.ms-excel" ))) {
	move_uploaded_file($tmp_name,$fic_xls);
	include_once('./librairie_php/reader2.php');
	$data = new Spreadsheet_Excel_Reader();
	//$data->setOutputEncoding('CP1250');
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
	/*
		1) ordre_affichage
		2) Nom_fr
		3) Nom_us
		4) semestre
		5) ects	
		6) coef	
		7) UE_fr
		8) UE_us
		9) code matiere	
		10) classe
	        11) Ordre de l'UE
	        12) Spécif. Etude de cas  
	        13) Info. Semestre (1 à 10)  
	        14) Coef certification
	        15) Note planché  
	 */ 

	
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
		$ordre_affichage=trim(addslashes($data->sheets[0]['cells'][$i][1]));
	 	$libelle_matiere_fr=trim(addslashes($data->sheets[0]['cells'][$i][2]));
       	 	$libelle_matiere_en=trim(addslashes($data->sheets[0]['cells'][$i][3]));
	 	$numero_semestre=trim(addslashes($data->sheets[0]['cells'][$i][4]));
		$ects=trim(addslashes($data->sheets[0]['cells'][$i][5]));
       	 	$coef=trim(addslashes($data->sheets[0]['cells'][$i][6]));
		$UE_libelle_fr=trim(addslashes($data->sheets[0]['cells'][$i][7]));
	 	$UE_libelle_en=trim(addslashes($data->sheets[0]['cells'][$i][8]));
       	 	$code_matiere=trim(addslashes($data->sheets[0]['cells'][$i][9]));		
		$libelle_classe=trim(addslashes($data->sheets[0]['cells'][$i][10]));		
		$ordre_UE=trim(addslashes($data->sheets[0]['cells'][$i][11]));
		$specif_etudeDeCas=trim(addslashes($data->sheets[0]['cells'][$i][12]));
		$info_semestre=trim(addslashes($data->sheets[0]['cells'][$i][13]));
		$coef_certif=trim(addslashes($data->sheets[0]['cells'][$i][14]));
		$note_planche=trim(addslashes($data->sheets[0]['cells'][$i][15]));


		// enregistrer la classe ----------------------------------
		$idClasse=chercheIdClasse($libelle_classe);
		if ($idClasse <= 0) {
			import_classe_ipac($libelle_classe);
			$idClasse=chercheIdClasse($libelle_classe);
		}
		
		// --------------------------------------------------------
		// enregistrer la matiere
		$idMatiere=chercheIdMatiere2($libelle_matiere_fr);
		if ($idMatiere <= 0 ) {
			import_matiere_ipac($libelle_matiere_fr,$libelle_matiere_en,$code_matiere);
			$idMatiere=chercheIdMatiere2($libelle_matiere_fr);
		}else{
			update_matiere_ipac($idMatiere,$libelle_matiere_fr,$libelle_matiere_en,$code_matiere);
		}
		
		// --------------------------------------------------------
		// enregistrer l'Unite Enseignement
		$sem=$numero_semestre;
		if ($numero_semestre == "1.2") $sem=0 ; 
		$idUE=verifUEenCours($idClasse,$sem,$ordre_UE,$UE_libelle_fr,$UE_libelle_en,$anneeScolaire);
		if ($idUE <= 0 ) {
			import_UE_IPAC($idClasse,$sem,$ordre_UE,$UE_libelle_fr,$UE_libelle_en,$anneeScolaire);
			$idUE=verifUEenCours($idClasse,$sem,$ordre_UE,$UE_libelle_fr,$UE_libelle_en,$anneeScolaire);
		}

		// --------------------------------------------------------
		// enregistrement Detail UE
		$idDetailUE=verifDetailUEenCours($idMatiere,$idUE);
		if ($idDetailUE <= 0 ) {
			import_Detail_UE_IPAC($idMatiere,$idUE);
			$idDetailUE=verifDetailUEenCours($idMatiere,$idUE);
		}

		// enregistrer l'affectation

		if ($numero_semestre == "1.2") {
			$trim="tous";
		}else{
			$trim="trimestre$numero_semestre";
		}


		$nb=verifSiAffectationEnCoursAvecUE($idMatiere,$idClasse,$coef,$trim,$ects,$idDetailUE,$anneeScolaire);
		if ($nb == 0)  { 
			$nb=verifSiAffectationEnCoursSansUE($idMatiere,$idClasse,$coef,$trim,$ects,$idDetailUE,$anneeScolaire);
			if ($nb == 0) {
				import_affectation_IPAC($idMatiere,$idClasse,$coef,$trim,$ects,$idDetailUE,$ordre_affichage,$specif_etudeDeCas,$anneeScolaire,$info_semestre,$coef_certif,$note_planche);
			}else{ 		
				import_affectation_IPAC_UPDATE($idMatiere,$idClasse,$coef,$trim,$ects,$idDetailUE,$ordre_affichage,$specif_etudeDeCas,$anneeScolaire,$info_semestre,$coef_certif,$note_planche);
			}
		}
	}

}else {
	$ok=1;
}

@unlink("$fic_xls");

if ($ok == 0) {
?>
	<br />
	<center>
	<font class=T2>Import terminé</font>
	</center>
<?php
}else{
	print "<center><font class=T2>Fichier non forme, le fichier doit être au format xls</font></center>";
}
Pgclose();
?>

<br><br />
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
