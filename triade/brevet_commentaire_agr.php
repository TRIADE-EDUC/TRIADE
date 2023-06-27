<?php
session_start();
$idClasse=$_GET["idclasse"];
$idmatiereduprof=$_GET["idmatiereduprof"];

if (isset($_POST["idclasse"])) {
	$idClasse=$_POST["idclasse"];
	$idmatiereduprof=$_POST["idmatiereduprof"];
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

<form method="post" name="formulaire" action="brevet_commentaire_agr.php">
<!-- // debut form  -->
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
include_once("librairie_php/lib_brevet.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("profadmin");
$cnx=cnx();

$serie="STA";

$dateDebut=recupDateDebutAnnee();
$dateFin=recupDateFinAnnee();

$datap=config_param_visu("examenPREV_SANTE_ENV");
$examenPREV_SANTE_ENV=$datap[0][0];

$datap=config_param_visu("examenEPS");
$examenEPS=$datap[0][0];



if (isset($_POST["create3"])) {
	$nbcar=201;
	for($j=0;$j<$_POST["nbeleve"];$j++) {  
		// variable eleve
		$idEleve="idEleve$j";	$idEleve=$_POST[$idEleve];

		$notefran="notefrancais$j";	$notefran=$_POST[$notefran];
		$codefran="codefrancais$j";	$codefran=$_POST[$codefran];
		$notefran=trunchaine($notefran,$nbcar);
		if (isset($_POST["notefrancais$j"])) 	enrgCommBrevet($notefran,$codefran,$idEleve,$anneeScolaire);

		$notehistoirearts="notehistoirearts$j";	$notehistoirearts=$_POST[$notehistoirearts];
		$codehistoirearts="codehistoirearts$j";	$codehistoirearts=$_POST[$codehistoirearts];
		$notehistoirearts=trunchaine($notehistoirearts,$nbcar);
		if (isset($_POST["notehistoirearts$j"])) enrgCommBrevet($notehistoirearts,$codehistoirearts,$idEleve,$anneeScolaire);
	
		$notemath="noteMathematiques$j";$notemath=$_POST[$notemath];
		$codemath="codeMathematiques$j";$codemath=$_POST[$codemath];
		$notemath=trunchaine($notemath,$nbcar);
		if (isset($_POST["noteMathematiques$j"])) enrgCommBrevet($notemath,$codemath,$idEleve,$anneeScolaire);
	
		$notelv1="notelv1$j";	$notelv1=$_POST[$notelv1];
		$codelv1="codelv1$j";	$codelv1=$_POST[$codelv1];
		$notelv1=trunchaine($notelv1,$nbcar);
		if (isset($_POST["notelv1$j"])) enrgCommBrevet($notelv1,$codelv1,$idEleve,$anneeScolaire);
	
		$notephy="notesciencephysique$j"; $notephy=$_POST[$notephy];
		$codephy="codesciencephysique$j"; $codephy=$_POST[$codephy];
		$notephy=trunchaine($notephy,$nbcar);
		if (isset($_POST["notesciencephysique$j"])) enrgCommBrevet($notephy,$codephy,$idEleve,$anneeScolaire);
		
		$noteeps="noteeps$j";	$noteeps=$_POST[$noteeps];
		$codeeps="codeeps$j";	$codeeps=$_POST[$codeeps];
		$noteeps=trunchaine($noteeps,$nbcar);
		if (isset($_POST["noteeps$j"])) enrgCommBrevet($noteeps,$codeeps,$idEleve,$anneeScolaire);
		
		$noteart="notearts$j";	$noteart=$_POST[$noteart];
		$codeart="codearts$j";	$codeart=$_POST[$codeart];
		$noteart=trunchaine($noteart,$nbcar);
		if (isset($_POST["notearts$j"])) 	enrgCommBrevet($noteart,$codeart,$idEleve,$anneeScolaire);
		
		$noteprevsantenv="noteprevsantenv$j";	$noteprevsantenv=$_POST[$noteprevsantenv];
		$codeprevsantenv="codeprevsantenv$j";	$codeprevsantenv=$_POST[$codeprevsantenv];
		$noteprevsantenv=trunchaine($noteprevsantenv,$nbcar);
		if (isset($_POST["noteprevsantenv$j"])) 	enrgCommBrevet($noteprevsantenv,$codeprevsantenv,$idEleve,$anneeScolaire);

		$notebio="noteSciencesBio$j";		$notebio=$_POST[$notebio];
		$codebio="codeSciencesBio$j";		$codebio=$_POST[$codebio];
		$notebio=trunchaine($notebio,$nbcar);
		if (isset($_POST["noteSciencesBio$j"])) 	enrgCommBrevet($notebio,$codebio,$idEleve,$anneeScolaire);
		
		$notetech="noteTechnoAgricole$j";	$notetech=$_POST[$notetech];
		$codetech="codeTechnoAgricole$j";	$codetech=$_POST[$codetech];
		$notetech=trunchaine($notetech,$nbcar);
		if (isset($_POST["noteTechnoAgricole$j"])) 	enrgCommBrevet($notetech,$codetech,$idEleve,$anneeScolaire);
	
		$notelv2="noteEducationSocio$j";		$notelv2=$_POST[$notelv2];
		$codelv2="codeEducationSocio$j";		$codelv2=$_POST[$codelv2];
		$notelv2=trunchaine($notelv2,$nbcar);
		if (isset($_POST["noteEducationSocio$j"])) 	enrgCommBrevet($notelv2,$codelv2,$idEleve,$anneeScolaire);
		
		$notescol="noteviescolaire$j";	$notescol=$_POST[$notescol];
		$codescol="codeviescolaire$j";	$codescol=$_POST[$codescol];
		$notescol=trunchaine($notescol,$nbcar);
		if (isset($_POST["noteviescolaire$j"])) 	enrgCommBrevet($notescol,$codescol,$idEleve,$anneeScolaire);
		
		$noteA2="noteA2R$j";		$noteA2=$_POST[$noteA2];
		$codeA2="codeA2R$j";		$codeA2=$_POST[$codeA2];
		$noteA2=trunchaine($noteA2,$nbcar);
		if (isset($_POST["noteA2R$j"])) 	enrgCommBrevet($noteA2,$codeA2,$idEleve,$anneeScolaire);
	
		$notehist="notehistgeo$j";	$notehist=$_POST[$notehist];
		$codehist="codehistgeo$j";	$codehist=$_POST[$codehist];
		$notehist=trunchaine($notehist,$nbcar);
		if (isset($_POST["notehistgeo$j"])) 	enrgCommBrevet($notehist,$codehist,$idEleve,$anneeScolaire);
				
	}
}	

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
	$nbcar=200;
	// --------------------------------------------------------------------------------
	// HISTOIRE DES ARTS
	$tab=rechercheMatiereBrevet("histoire des arts",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		$note=$note*2;
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
	print "<tr><td valign='top'>Histoire des arts : <br />&nbsp;&nbsp; $note/40 </td><td><textarea $style onkeypress=\"compter(this,'$nbcar', this.form.CharRestantA_$j)\" name='notehistoirearts$j' cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantA_$j' size='2' disabled='disabled'></td></tr>";
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
	$tab=rechercheMatiereBrevet("Sciences Physiques",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"sciencephysique");
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
	print "<tr><td valign='top'>Sciences Physiques : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantE_$j)\" name='notesciencephysique$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantE_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codesciencephysique$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	
	// --------------------------------------------------------------------------------
	// Prevention sante 
	$tab=rechercheMatiereBrevet("Prévention Santé",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$codeEpreuve=recupCodeEpreuve($serie,"prevsantenv");
		if ($examenPREV_SANTE_ENV == "oui") {
	        	if ( "biologie" == strtolower(chercheMatiereNomBrevet($idMatiere))) {
	                	$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,"Brevet PREV. SANTE ENV.");
	                }else{
	                        $note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
	                }
	        }else{
	              $note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
	        }
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
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
	print "<tr><td valign='top'>Prévention Santé Environnement : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantF_$j)\" name='noteprevsantenv$j' cols=50 rows=4 $style  $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantF_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeprevsantenv$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	// --------------------------------------------------------------------------------
	//  
	$tab=rechercheMatiereBrevet("Education physique et sportive",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		if ($examenEPS == "oui") {
                	$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,"Brevet EPS");
                }else{
                        $note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
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
	$tab=rechercheMatiereBrevet("Education Socioculturelle",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"EducationSocio");
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
	print "<tr><td valign='top'>Education Socioculturelle : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantH_$j)\" name='noteEducationSocio$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantH_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeEducationSocio$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Sciences Biologiques 
	$tab=rechercheMatiereBrevet("Sciences Biologiques",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"SciencesBio");
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
	print "<tr><td valign='top'>Sciences Biologiques : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantI_$j)\" name='noteSciencesBio$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantI_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeSciencesBio$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	//

	// --------------------------------------------------------------------------------
	// Technologie 
	$tab=rechercheMatiereBrevet("Techno Secteur Agricoles",$idClasse);
	$nb=0;$noteT="";$note="";$okedit=0;
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($idMatiere == $idmatiereduprof) { $okedit=1; }
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") { $noteT=$noteT + $note; $nb++; }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"TechnoAgricole");
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
	print "<tr><td valign='top'>Technologique Secteur Agricole : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantJ_$j)\" name='noteTechnoAgricole$j' cols=50 rows=4 $style $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantJ_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codeTechnoAgricole$j' />";
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
	// Histoire - Géographie 
	$tab=rechercheMatiereBrevet("Histoire - Géographie - Civique",$idClasse);
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
	print "<tr><td valign='top'>Histoire - Géo - Education civique  : <br />&nbsp;&nbsp; $note/20 </td><td><textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestantR_$j)\" name='notehistgeo$j' $style cols=50 rows=4 $readonly >$commentaire</textarea>&nbsp;<input type='text' name='CharRestantR_$j' size='2' disabled='disabled'></td></tr>";
	print "<input type=hidden value='$codeEpreuve' name='codehistgeo$j' />";
	// ---------------------------------------------------------------------------------
	// ---------------------------------------------------------------------------------
	//
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

<input type=hidden name='nbeleve' value='<?php print count($eleveT) ?>' />
<input type=hidden name='serie' value="<?php print $serie ?>" />
<input type=hidden name='sMat' value="<?php print $idmatiereduprof  ?>" />
<input type=hidden name='sClasseGrp' value="<?php print $idClasse ?>" />
<input type=hidden name='idclasse' value="<?php print $idClasse ?>" />
<input type=hidden name='idmatiereduprof' value="<?php print $idmatiereduprof ?>" />
</form>
<br><br><center><i><font class=T1>AB:ABsent  - DI:DIspensé  - NN:Non Noté <br>VA: Niveau A2 de Langue régionale valide <br>NV: Niveau A2 de langue regionale non valide</font></i></center><br><br>

<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
?>

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY>
</HTML>
