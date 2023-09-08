<?php
session_start();
if (isset($_POST["saisie_classe"])) {
	$idClasse=$_POST["saisie_classe"];
	$serie=$_POST["serie"];
}

if (isset($_POST["sClasseGrp"])) {
	$idClasse=$_POST["sClasseGrp"];
	$idmatiereduprof=$_POST["sMat"];
	$serie=$_POST["serie"];

	if (preg_match('/:/',$idClasse)) {
		$listTmp=explode(":",$idClasse);
		$idClasse=$listTmp[0];
		$idgroupe=$listTmp[1];
	}
}

if ($serie == "STA") {
	header("Location:brevet_commentaire_agr.php?idclasse=$idClasse&idmatiereduprof=$idmatiereduprof");
	exit;
}

$anneeScolaire=$_COOKIE["anneeScolaire"];

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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Commentaire du brevet des collèges" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >

<form method="post" name="formulaire" action="brevet_commentaire.php">
<!-- // debut form  -->
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
include_once("librairie_php/lib_brevet.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("profadmin");
$cnx=cnx();

$datap=config_param_visu("epsviaexamen");
$epsviaexamen=$datap[0][0];

$dateDebut=recupDateDebutAnnee();
$dateFin=recupDateFinAnnee();

if (isset($_POST["create3"])) {


	for($j=0;$j<$_POST["nbeleve"];$j++) {  
		// variable eleve
		$idEleve="idEleve$j";	
		$idEleve=$_POST[$idEleve];

		$notefran="notefrancais$j";	$notefran=$_POST[$notefran];
		$codefran="codefrancais$j";	$codefran=$_POST[$codefran];
		if (isset($_POST["notefrancais$j"])) enrgCommBrevet($notefran,$codefran,$idEleve,$anneeScolaire);

		$notemath="notehistoirearts$j";	$notemath=$_POST[$notemath];
		$codemath="codehistoirearts$j";	$codemath=$_POST[$codemath];
		if (isset($_POST["notehistoirearts$j"])) enrgCommBrevet($notemath,$codemath,$idEleve,$anneeScolaire);
	
		$notemath="noteMathematiques$j";$notemath=$_POST[$notemath];
		$codemath="codeMathematiques$j";$codemath=$_POST[$codemath];
		if (isset($_POST["noteMathematiques$j"])) enrgCommBrevet($notemath,$codemath,$idEleve,$anneeScolaire);
	
		$notelv1="notelv1$j";		$notelv1=$_POST[$notelv1];
		$codelv1="codelv1$j";		$codelv1=$_POST[$codelv1];
		if (isset($_POST["notelv1$j"])) enrgCommBrevet($notelv1,$codelv1,$idEleve,$anneeScolaire);
	
		$notesvt="noteSVT$j";		$notesvt=$_POST[$notesvt];
		$codesvt="codeSVT$j";		$codesvt=$_POST[$codesvt];
		if (isset($_POST["noteSVT$j"])) enrgCommBrevet($notesvt,$codesvt,$idEleve,$anneeScolaire);
		
		$notephy="notephysChimi$j";	$notephy=$_POST[$notephy];
		$codephy="codephysChimi$j";	$codephy=$_POST[$codephy];
		if (isset($_POST["notephysChimi$j"])) enrgCommBrevet($notephy,$codephy,$idEleve,$anneeScolaire);
		
		$noteeps="noteeps$j";		$noteeps=$_POST[$noteeps];
		$codeeps="codeeps$j";		$codeeps=$_POST[$codeeps];
		if (isset($_POST["noteeps$j"])) enrgCommBrevet($noteeps,$codeeps,$idEleve,$anneeScolaire);
		
		$noteart="notearts$j";		$noteart=$_POST[$noteart];
		$codeart="codearts$j";		$codeart=$_POST[$codeart];
		if (isset($_POST["notearts$j"])) enrgCommBrevet($noteart,$codeart,$idEleve,$anneeScolaire);
		
		$notemuc="notemusic$j";		$notemuc=$_POST[$notemuc];
		$codemuc="codemusic$j";		$codemuc=$_POST[$codemuc];
		if (isset($_POST["notemusic$j"])) enrgCommBrevet($notemuc,$codemuc,$idEleve,$anneeScolaire);
		
		$notetech="notetechno$j";	$notetech=$_POST[$notetech];
		$codetech="codetechno$j";	$codetech=$_POST[$codetech];
		if (isset($_POST["notetechno$j"])) enrgCommBrevet($notetech,$codetech,$idEleve,$anneeScolaire);
	
		$notelv2="noteLV2$j";		$notelv2=$_POST[$notelv2];
		$codelv2="codeLV2$j";		$codelv2=$_POST[$codelv2];
		if (isset($_POST["noteLV2$j"])) enrgCommBrevet($notelv2,$codelv2,$idEleve,$anneeScolaire);
		
		$notedp6="noteDP6h$j";		$notedp6=$_POST[$notedp6];
		$codedp6="codeDP6h$j";		$codedp6=$_POST[$codedp6];
		if (isset($_POST["noteDP6h$j"])) enrgCommBrevet($notedp6,$codedp6,$idEleve,$anneeScolaire);
	
		$notescol="noteviescolaire$j";	$notescol=$_POST[$notescol];
		$codescol="codeviescolaire$j";	$codescol=$_POST[$codescol];
		if (isset($_POST["noteviescolaire$j"])) enrgCommBrevet($notescol,$codescol,$idEleve,$anneeScolaire);
		
		$noteopt="noteOPT$j";		$noteopt=$_POST[$noteopt];
		$codeopt="codeOPT$j";		$codeopt=$_POST[$codeopt];
		if (isset($_POST["noteOPT$j"])) enrgCommBrevet($noteopt,$codeopt,$idEleve,$anneeScolaire);
	
		$noteA2="noteA2R$j";		$noteA2=$_POST[$noteA2];
		$codeA2="codeA2R$j";		$codeA2=$_POST[$codeA2];
		if (isset($_POST["noteA2R$j"])) enrgCommBrevet($noteA2,$codeA2,$idEleve,$anneeScolaire);
	
		$notehist="notehistgeo$j";	$notehist=$_POST[$notehist];
		$codehist="codehistgeo$j";	$codehist=$_POST[$codehist];
		if (isset($_POST["notehistgeo$j"])) enrgCommBrevet($notehist,$codehist,$idEleve,$anneeScolaire);
		
		$noteeduc="noteeduciv$j";	$noteeduc=$_POST[$noteeduc];
		$codeeduc="codeeduciv$j";	$codeeduc=$_POST[$codeeduc];
		if (isset($_POST["noteeduciv$j"])) enrgCommBrevet($noteeduc,$codeeduc,$idEleve,$anneeScolaire);
	}
		

	print "<br><center><font class='T2'>Commentaires enregistrés</font></center><br>";
	print "<table align='center'><tr><td><script language=JavaScript>buttonMagicRetour('brevet_commentaire_admin.php','_parent')</script></td></tr></table><br>";
	

}else{	

	$eleveT=recupEleve($idClasse); // nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone, numero_eleve
	$nbEleveT=count($eleveT);
	print "<table border='0' >";
	for($j=0;$j<$nbEleveT;$j++) {  
		// variable eleve
		$noteGlobal="";
		$nomEleve=ucwords($eleveT[$j][0]);
		$prenomEleve=ucfirst($eleveT[$j][1]);
		$lv1Eleve=$eleveT[$j][2];
		$lv2Eleve=$eleveT[$j][3];
		$idEleve=$eleveT[$j][4];
		$nomEleve=strtoupper(trim($nomEleve));
		$prenomEleve=trim($prenomEleve);
	
		print "<tr><td colspan='3'><font class=T2>Elève : <b>$nomEleve $prenomEleve</b>  </td></tr>";
		print "<input type=hidden name='nomEleve$j' value=\"$nomEleve\" />";
		print "<input type=hidden name='prenomEleve$j' value=\"$prenomEleve\" />";
		print "<input type=hidden name='idEleve$j' value=\"$idEleve\" />";
		$nbcar=100;
		// --------------------------------------------------------------------------------
		// HISTOIRE DES ARTS
		$tab=rechercheMatiereBrevet("histoire des arts",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"histoire des arts");

	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; }
		$noteGlobal=$noteGlobal+$note;
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='imgA0$j' align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}
	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Histoire des arts : <br />&nbsp;&nbsp; $note/20 </td><td><textarea $style onkeypress=\"compter(this,'$nbcar', this.form.CharRestantA_$j)\" name='notehistoirearts$j' cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantA_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codehistoirearts$j' />";
	// ---------------------------------------------------------------------------------



	// --------------------------------------------------------------------------------
	// FRANCAIS
	$tab=rechercheMatiereBrevet("Français",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Français");

	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; }
		$noteGlobal=$noteGlobal+$note;
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img0$j' align=center/>";
		}
		if ($note > 0) {	
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}

	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1;
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Français : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantB_$j)\" name='notefrancais$j' cols=50 rows=4 $readonly $style >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantB_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codefrancais$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// MATHEMATIQUES
	$tab=rechercheMatiereBrevet("Mathématiques",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Mathematiques");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif'  id='img1$j' align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1;
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Mathématiques : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantC_$j)\" name='noteMathematiques$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantC_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeMathematiques$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------
	// Langue vivante 1
	$tab=rechercheMatiereBrevet("Langue vivante 1",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		if (!verifMatiereLangue($idEleve,$idMatiere,'LV1',$idClasse)) { continue; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"lv1");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img2$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);	
	if ($_SESSION["membre"] == "menuadmin") $okedit=1;
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Langue vivante 1 : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantD_$j)\" name='notelv1$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantD_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codelv1$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// SVT
	$tab=rechercheMatiereBrevet("SVT",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"SVT");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img3$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>S.V.T. : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantE_$j)\" name='noteSVT$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantE_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeSVT$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	
	// --------------------------------------------------------------------------------
	// Physique 
	$tab=rechercheMatiereBrevet("Physique - Chimie",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"physChimi");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img4$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Physique - Chimie : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantF_$j)\" name='notephysChimi$j' cols=50 rows=4 $style  $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantF_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codephysChimi$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------

	
	// --------------------------------------------------------------------------------
	// EPS 
	$tab=rechercheMatiereBrevet("Education physique et sportive",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		if ($epsviaexamen != "1") {
                        $note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
                }else{
               		$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,'Brevet EPS');
                }
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"eps");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img5$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Education physique et sportive : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantG_$j)\" name='noteeps$j' $style cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantG_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeeps$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Arts 
	$tab=rechercheMatiereBrevet("Arts plastiques",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Arts plastiques");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img6$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Arts plastiques : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantH_$j)\" name='notearts$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantH_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codearts$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Music 
	$tab=rechercheMatiereBrevet("Education musicale",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Education musicale");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img7$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Education musicale : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantI_$j)\" name='notemusic$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantI_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codemusic$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	//

	// --------------------------------------------------------------------------------
	// Technologie 
	$tab=rechercheMatiereBrevet("Technologique",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Technologique");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img8$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Technologique : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantJ_$j)\" name='notetechno$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantJ_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codetechno$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// 2eme Langue
if ($serie ==  "LV2") {
	$tab=rechercheMatiereBrevet("langue vivante 2",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		if (!verifMatiereLangue($idEleve,$idMatiere,'LV2',$idClasse)) { continue; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"LV2");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img9$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}
	}	
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Langue vivante 2 : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantK_$j)\" name='noteLV2$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantK_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeLV2$j' />";
	// ---------------------------------------------------------------------------------
}
	// ---------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// DP6H
if ($serie ==  "DP6") {
	$tab=rechercheMatiereBrevet("Découverte professionnelle 6h",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"DP6h");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		if ($note <= 0) { 
			$note=""; 
		}else{
			if ($note != "") { 
				$note=arrondiAuDemi($note); 
				$note=number_format($note,2,'.','');
				if ($note < 10) { $note="0".$note; } 
				$noteGlobal=$noteGlobal+$note;	
			}
		}
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img10$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Découverte professionnelle 6h : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantM_$j)\" name='noteDP6h$j' $style cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantM_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeDP6h$j' />";
	// ---------------------------------------------------------------------------------
}
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Vie Scolaire
	$note=calculNoteVieScolaireBrevet($idEleve,$idClasse);
	$codeEpreuve=recupCodeEpreuve($serie,"viescolaire");
	$okedit=0;
	if ($note != "") { 
		$note=arrondiAuDemi($note);
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note; 
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img11$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Vie Scolaire : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantN_$j)\" name='noteviescolaire$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantN_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeviescolaire$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// OPTION 
if ($serie ==  "LV2") {
	$tab=rechercheMatiereBrevet("Latin ou grec ou Découverte professionnelle 3h (option facultative)",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		if (!verifMatiereLangue($idEleve,$idMatiere,'OPT',$idClasse)) { continue; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"OPT");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if (trim($note) != "") { 
		if ($note <= 0) { 
			$note=""; 
		}else{
			if ($note != "") { 
				$note=arrondiAuDemi($note); 
				$note=number_format($note,2,'.','');
				if ($note < 10) { $note="0".$note; } 
				if ($note > 10) { $noteGlobal=$noteGlobal+$note-10; }
			}

		}
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img12$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			if ($note > 10) { $noteGlobal=$noteGlobal+$note-10; }
			$img="";	
		}

	}	
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Latin ou grec ou Découverte prof. 3h : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantO_$j)\" name='noteOPT$j' $style cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantO_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeOPT$j' />";
	// ---------------------------------------------------------------------------------
}
	// ---------------------------------------------------------------------------------

if ($serie ==  "DP6") {
	$tab=rechercheMatiereBrevet("Latin ou grec ou langue vivante 2 (option facultative)",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"OPT");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=$note-10;
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		if ($note > 10) { $noteGlobal=$noteGlobal+$note-10; }
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img13$j'  align=center/>";
		}
		if ($note > 0) {
			$note=$note-10;
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			if ($note > 10) { $noteGlobal=$noteGlobal+$note-10; }
			$img="";	
		}
	}	
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Latin ou grec ou Langue vivante 2 : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantP_$j)\" name='noteOPT$j' $style cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantP_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeOPT$j' />";
	// ---------------------------------------------------------------------------------
}
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// A2R
	$note=rechercheB2IEleve($idEleve,$idClasse,"A2R");
	$okedit=0;
	if ($note != "") {
		$img="";	
	}else{
		$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='imgR15$j'  align=center/>";
	}
	$codeEpreuve=recupCodeEpreuve($serie,"A2");
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Socle Niveau A2 : <br />&nbsp;&nbsp; $note </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantQ_$j)\" name='noteA2R$j' cols=50 $style rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantQ_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeA2R$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Histoire - Géographie 
	$tab=rechercheMatiereBrevet("Histoire - Géographie",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"histoireGeo");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img16$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$img="";	
		}

	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Histoire - Géographie : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantR_$j)\" name='notehistgeo$j' $style cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantR_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codehistgeo$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	//

	// --------------------------------------------------------------------------------
	// Education civique 
	$tab=rechercheMatiereBrevet("Education civique",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"educationcivique");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img17$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$img="";	
		}

		
	}
	$commentaire="";
	$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
	if ($_SESSION["membre"] == "menuadmin") $okedit=1; 
	if ($okedit == 0)  { $readonly="disabled='disabled'"; $style="style=\"font-size: 12px; \""; }else{ $readonly=""; $style="style=\"color: red; background-color: #CCCCCC ; font-size: 12px; \""; }
	print "<tr><td valign='top'>Education civique : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantS_$j)\" name='noteeduciv$j' $style cols=50 rows=4  $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantS_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeeduciv$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------


	//
	//TOTAL
	$noteGlobal=number_format($noteGlobal,2,'.','');
	if ($noteGlobal < 100) {$noteGlobal="0".$noteGlobal; }
	print "<tr><td><b>TOTAL<b> </td><td><input type=text name='total$j' value='$noteGlobal' size=4  onchange='cacheTelechargement()'  /> ";
	print "<input type=hidden name='tot$j' value='TOT' />";

	print "<tr><td colspan=3><hr></td></tr>";


	}	
	print "</table>";
	config_param_ajout($_POST["controle"],"contnotanet");
	PgClose();
	?>

	<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR." les commentaires" ?>","create3"); //text,nomInput</script>
	<input type=hidden name='nbeleve'    value='<?php print count($eleveT) ?>' />
	<input type=hidden name='serie'      value="<?php print $serie ?>" />
	<input type=hidden name='sMat'       value="<?php print $idmatiereduprof  ?>" />
	<input type=hidden name='sClasseGrp' value="<?php print $idClasse ?>" />
	</form>
	<br><br><center><i><font class=T1>AB:ABsent  - DI:DIspensé  - NN:Non Noté <br>VA: Niveau A2 de Langue régionale valide <br>NV: Niveau A2 de langue regionale non valide</font></i></center><br><br>

	<?php
	include_once("./librairie_php/lib_conexpersistant.php"); 
	connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
}
	?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY>
</HTML>
