<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
        set_time_limit(900);
	ini_set('memory_limit','512M'); 
}
$anneeScolaire=$_POST["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
setcookie("dateDebut_export_cahierTexte",$_POST["saisie_date_debut"],time()+36000*24*30);
setcookie("dateFin_export_cahierTexte",$_POST["saisie_date_fin"],time()+36000*24*30);
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="TriadeÂ©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
        $cnx=cnx();
        if (!verifDroit($_SESSION["id_pers"],"cahiertextes")) {    
	        accesNonReserveFen();
                exit();
        }
        Pgclose();
}else{
	validerequete("menuadmin");	
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF37 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign='top'>
<br>
<!-- // debut form  -->
<?php
$cnx=cnx();

$dateDebut=$_POST["saisie_date_debut"];
$dateFin=$_POST["saisie_date_fin"];

/* ordre de classement : 
 * CLASSE > ENSEIGNANT > MATIERE > SOUS MATIERE > entr�es clases par dates 
 *
 * fichier 2 : ordre de classement : 
 * ENSEIGNANT >CLASSE > MATIERE > SOUS MATIERE > entrées clases par dates 
 *
 */

include_once('./PHPWord.php');

// New Word Document
$PHPWord = new PHPWord();

$dataclasse=affClasse();

$PHPWord->addParagraphStyle('pStyle', array('spacing'=>100));


//code_class,libelle,desclong,offline
for($i=0;$i<count($dataclasse);$i++) {
	$idclasse=$dataclasse[$i][0];
	$libelle=$dataclasse[$i][1];
	$libellelong=$dataclasse[$i][2];

	$section = $PHPWord->createSection();
	
	$section->addText(utf8_decode("Classe : $libelle"), array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>16));
	$section->addText(utf8_decode("         $libellelong"), array('italic'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>14));

	

	$dataens=visu_affectation_detail_cahier_texte($idclasse,$anneeScolaire);
	//ordre_affichage,code_matiere,code_prof,code_classe,coef,g.libelle,a.langue,a.avec_sous_matiere,a.visubull,a.nb_heure
	for($j=0;$j<count($dataens);$j++) {
		$idprof=$dataens[$j][2];
		$idmatiere=$dataens[$j][1];
		$nomprof=recherche_personne($idprof);
		$nommatiere=chercheMatiereNom($idmatiere);

		if (trim($nommatiere) == "") continue;
		$nommatiere=preg_replace('//','',$nommatiere);
	

		$textrun = $section->createTextRun('pStyle');
		$textrun->addText("Enseignant : ",array('size'=>12));
		$textrun->addText(utf8_decode("$nomprof  "),array('bold'=>true,'size'=>12));
		$textrun->addText(utf8_decode("Matière :"),array('size'=>12));
		$textrun->addText(utf8_decode("$nommatiere "),array('bold'=>true,'size'=>12));
	//	$section->addTextBreak();
		$data=exportPDF_contobj_cahiertext(dateFormBase($dateDebut),dateFormBase($dateFin),"date_contenu",$idprof,$idmatiere,$idclasse);

		for($o=0;$o<count($data);$o++) {
			$saisiele=dateForm($data[$o][0]);
			$pourle=dateForm($data[$o][2]);
			$contenu=strip_tags(html_vers_text($data[$o][3]));
			$objectif=strip_tags(html_vers_text($data[$o][4]));
			$contenu=preg_replace('/\n/',' ',$contenu);
			$objectif=preg_replace('/\n/',' ',$objectif);
	
			$section->addText("Saisie le $saisiele pour le $pourle ", array('italic'=>true,'name'=>'Verdana', 'color'=>'F79646', 'size'=>10));
			$textrun = $section->createTextRun('pStyle');
			$textrun->addText("Contenu : ", array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>10));
			$textrun->addText(utf8_decode("$contenu"), array('name'=>'Arial', 'color'=>'000000', 'size'=>10));
			$textrun = $section->createTextRun('pStyle');
			$textrun->addText("Objectif : ", array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>10));
			$textrun->addText(utf8_decode("$objectif"), array('name'=>'Arial', 'color'=>'000000', 'size'=>10));
		
		}
		$data=exportPDF_devoir_cahiertext(dateFormBase($dateDebut),dateFormBase($dateFin),"date_devoir",$idprof,$idmatiere,$idclasse);
		if (count($data)) {
			$section->addText(utf8_decode("Devoir à faire en $nommatiere "), array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>10));
		}
		for($o=0;$o<count($data);$o++) {
			$saisiele=dateForm($data[$o][0]);
			$pourle=dateForm($data[$o][2]);
			$devoir=strip_tags(html_vers_text($data[$o][3]));
			$devoir=preg_replace('/\n/',' ',$devoir);
			if (trim($devoir) == "") continue; 
			$section->addText("Saisie le $saisiele pour le $pourle ", array('name'=>'Verdana', 'color'=>'F79646', 'size'=>10));
			$section->addText("$devoir ", array('name'=>'Arial', 'color'=>'000000', 'size'=>10));
		}
		$section->addTextBreak();
	}
	 
}

// Save File
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
if (!is_dir("./data/pdf_cahierdetexte")) { mkdir("./data/pdf_cahierdetexte"); htaccess("./data/pdf_cahierdetexte");  }
$fichier1="./data/pdf_cahierdetexte/export_cahier_texte_1.docx";
@unlink($fichier1); // destruction avant creation
$objWriter->save($fichier1);


// ------------------------------------------------------------------------------------------------------------------- /
// ------------------------------------------------------------------------------------------------------------------- /
// ------------------------------------------------------------------------------------------------------------------- /

$PHPWord = new PHPWord();

$dataprof=affPersActif('ENS');

// pers_id, civ, nom, prenom, identifiant, offline, email
for($i=0;$i<count($dataprof);$i++) {
	$idpers=$dataprof[$i][0];
	$nom=$dataprof[$i][2];
	$prenom=$dataprof[$i][3];
	$civ=civ($dataprof[$i][1]);

	$section = $PHPWord->createSection();

	$section->addText(utf8_decode("Enseignant : $civ $nom $prenom"), array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>16));

	$dataClasse=recupClasseProf($idpers,$anneeScolaire);

	for($P=0;$P<count($dataClasse);$P++) {
		$idclasse=$dataClasse[$P][0];		
		$libellelong=chercheClasse_description($idclasse);
		$nomclasse=chercheClasse_nom($idclasse);
		if ($libellelong != "") $libellelong="(".$libellelong.")";
		
		$textrun = $section->createTextRun('pStyle');
                $textrun->addText("Classe : ",array('name'=>'Verdana', 'color'=>'000000', 'size'=>12));
		$textrun->addText(utf8_decode("$nomclasse $libellelong"),array('bold'=>true,'name'=>'Verdana', 'color'=>'000000', 'size'=>12));

		$dataens=visu_affectation_detail_cahier_texte_ens($idclasse,$anneeScolaire,$idpers);
		//ordre_affichage,code_matiere,code_prof,code_classe,coef,g.libelle,a.langue,a.avec_sous_matiere,a.visubull,a.nb_heure
		for($j=0;$j<count($dataens);$j++) {
			$idprof=$dataens[$j][2];
			if ($idprof != $idpers) continue; 
			$idmatiere=$dataens[$j][1];
			$textrun = $section->createTextRun('pStyle');
			$nommatiere=chercheMatiereNom($idmatiere);
			$textrun->addText(utf8_decode("Matière :"),array('name'=>'Verdana', 'color'=>'000000', 'size'=>12));
			$textrun->addText(utf8_decode("$nommatiere "), array('bold'=>true,'name'=>'Verdana', 'color'=>'000000', 'size'=>12));
			$data=exportPDF_contobj_cahiertext(dateFormBase($dateDebut),dateFormBase($dateFin),"date_contenu",$idprof,$idmatiere,$idclasse);
			// date_saisie, heure_saisie, date_contenu, contenu, objectif	
			for($o=0;$o<count($data);$o++) {
				$saisiele=dateForm($data[$o][0]);
				$pourle=dateForm($data[$o][2]);
				$contenu=$data[$o][3];
				$objectif=$data[$o][4];
				$contenu=strip_tags(html_vers_text($data[$o][3]));
				$objectif=strip_tags(html_vers_text($data[$o][4]));
				$contenu=preg_replace('/\n/',' ',$contenu);
				$objectif=preg_replace('/\n/',' ',$objectif);
				$section->addText("Saisie le $saisiele pour le $pourle ", array('italic'=>true,'name'=>'Verdana', 'color'=>'F79646', 'size'=>10));
				$textrun = $section->createTextRun('pStyle');
       		                $textrun->addText("Contenu : ", array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>10));
	                        $textrun->addText(utf8_decode("$contenu"), array('name'=>'Arial', 'color'=>'000000', 'size'=>10));
	                        $textrun = $section->createTextRun('pStyle');
	                        $textrun->addText("Objectif : ", array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>10));
	                        $textrun->addText(utf8_decode("$objectif"), array('name'=>'Arial', 'color'=>'000000', 'size'=>10));

			}
			unset($saisiele);
			unset($pourle);
			unset($contenu);
			unset($objectif);
			$data=exportPDF_devoir_cahiertext(dateFormBase($dateDebut),dateFormBase($dateFin),"date_devoir",$idprof,$idmatiere,$idclasse);
			if (count($data)) {
				$section->addText(utf8_decode("Devoir Ã  faire en $nommatiere "), array('bold'=>true,'name'=>'Verdana', 'color'=>'006699', 'size'=>10));
			}
			for($o=0;$o<count($data);$o++) {
				$saisiele=dateForm($data[$o][0]);
				$pourle=dateForm($data[$o][2]);
				$devoir=strip_tags(html_vers_text($data[$o][3]));
				$devoir=preg_replace('/\n/',' ',$devoir);
				if (trim($devoir) == "") continue; 
				$section->addText("Saisie le $saisiele pour le $pourle ", array('name'=>'Verdana', 'color'=>'F79646', 'size'=>10));
	                        $section->addText(utf8_decode("$devoir "), array('name'=>'Arial', 'color'=>'000000', 'size'=>10));
			}
			unset($nommatiere);	
			unset($saisiele);
			unset($pourle);
			unset($devoir);
			$section->addTextBreak();
		}
	}
}
	

$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
if (!is_dir("./data/pdf_cahierdetexte")) { mkdir("./data/pdf_cahierdetexte"); htaccess("./data/pdf_cahierdetexte");  }
$fichier2="./data/pdf_cahierdetexte/export_cahier_texte_2.docx";
@unlink($fichier2); // destruction avant creation
$objWriter->save($fichier2);

Pgclose();
?>
<!-- // fin form -->
<font class=T2><b>Extraction</b> : </font><br><br>

&nbsp;&nbsp;<font class='T2'>CLASSE > ENSEIGNANT > MATIERE  : </font> <input type=button onclick="open('telecharger.php?fichier=<?php print $fichier1?>','_blank','');" value="<?php print "Fichier Export Word" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">

<br><br>

&nbsp;&nbsp;<font class='T2'>ENSEIGNANT >CLASSE > MATIERE > SOUS MATIERE  : </font> <input type=button onclick="open('telecharger.php?fichier=<?php print $fichier2?>','_blank','');" value="<?php print "Fichier Export Word" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">

<br><br>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
