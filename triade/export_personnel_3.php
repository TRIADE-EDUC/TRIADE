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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des données élèves" ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<br />
<form method="post" action="export_eleve_3.php" >
<font class="T2">
<?php
$nosauvegarde=1;
if (isset($_GET["libelle"])) {
	$cnx=cnx();
	$ok=1;
	$libelle="##struct##".$_GET["libelle"];
	$data=aff_enr_parametrage($libelle);
	$colonne=unserialize($data[0][1]);
}


if (isset($_POST['create'])) {
	$nosauvegarde=0;
	$ok=1;
	$cnx=cnx();
	$nbcolname=$_POST['nbcolname'];
	$tablist=explode('%##%',$_POST['liste']);

	$i=0;
	foreach($_POST['ordre'] as $key=>$value) {
		if (is_numeric($tablist[$i])) {
			$o=$tablist[$i];
			$colonne[$value]=stripslashes($nbcolname[$o]);
		}else{
			$colonne[$value]=$tablist[$i];
		}
		$i++;
	}

	for($i=1;$i<=count($colonne);$i++) {
		$ordre=$colonne[$i].";";
	}
	$ordre=preg_replace('/:$/',"",$ordre);
}



if ($ok == 1) {

	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

	$fichier="./data/fichier_ASCII/export_".$_SESSION["id_pers"].".xls";
	@unlink($fichier);
//	$fname = tempnam("/tmp", "$fichier");
	
	$workbook = new writeexcel_workbook($fichier);

	$worksheet1 = $workbook->addworksheet('Listing');
//	$worksheet1->freeze_panes(1, 0); # 0 row
	
	$header = $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');

	$center = $workbook->addformat();
	$center->set_align('left');

	#
	# Sheet 1
	#

//	$worksheet1->set_column('A:I', 16);
//	$worksheet1->set_row(0, 20);
	$worksheet1->set_selection('A0');

	$j=0;
	for($i=1;$i<=count($colonne);$i++) {
		$titre=$colonne[$i];
		$worksheet1->write(0, $j, "$titre", $header);
		$j++;
	}

	$saisie_type=$_POST["saisie_type"];
	$datalisting=listingPersonnel($saisie_type); 
	/* 
	 * pers_id 		0
	 * nom   		1
	 * prenom  		2
	 * prenom2  		3
	 * mdp  		4
	 * type_pers  		5
	 * civ  		6
	 * photo  		7
	 * email  		8
	 * valid_forward_mail  	9
	 * adr  		10
	 * code_post  		11
	 * commune  		12
	 * tel  		13
	 * tel_port  		14
	 * identifiant  	15
	 * lieudenseigement  	16
	 * offline  		17
	 * id_societe_tuteur  	18
	 * pays  		19
	 * indice_salaire  	20
	 * qualite  		21
	 */
	for ($i=1;$i<count($datalisting);$i++) {
		$a=1;
		for ($j=0;$j<count($colonne);$j++) {
			$donnee="";
			if ($colonne[$a] == "nom") { 		$choix=1;     	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "prenom") { 	$choix=2;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "civ_1") { 		$choix=6;  	$donnee=civ($datalisting[$i][$choix]); 	      }
			if ($colonne[$a] == "adr1") { 		$choix=10;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "code_post_adr1") { $choix=11;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "commune_adr1") { 	$choix=12;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "tel_port_1") { 	$choix=14;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "telephone") { 	$choix=23;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "email") { 		$choix=8;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "identifiant") { 	$choix=15;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "indice_salaire") { $choix=20;  	$donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "code_barre") { 	
				$choix=0;  	
				$membre=renvoiTypePersonneMembre($datalisting[$i][5]);
				$donnee=recupIdCodeBar($datalisting[$i][$choix],"$membre"); 
			}


			$worksheet1->write($i, $j, "$donnee", $center);
			$a++;
	    	}
	}

	$workbook->close();


}
?>
</font>
</form>
<center>
<input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2">
<br /></center>
<br><br>
<?php 
if ($nosauvegarde == 0) { ?>
<hr>
<center>
<font class=T1>Si vous souhaitez sauvegarder la structure de l'exporation, récupérez d'abord votre 
fichier excel, puis cliquez sur le bouton "Sauvegarder la structure"</font>
<form method=post action="export.php" name="formulaire">
<?php 
$colonne=serialize($colonne);
$colonne=preg_replace("/'/","&#146;",$colonne);
?>
<input type=hidden name="structure" value='<?php print $colonne ?>' />
Nom de la structure : <input type=text name="nom_structure"  maxlength='200' />
<br><br>
<input type=submit  value="<?php print "Sauvegarder la structure" ?>"  class="bouton2" name="savestructure" >
</form>
<center>
<br><br>
<?php } ?>

<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
