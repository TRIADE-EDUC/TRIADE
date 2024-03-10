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

	$datalisting=listingEleve(); 
	/* elev_id,			0
	 * nom, 			1
	 * prenom,			2
	 * classe,			3
	 * lv1,				4
	 * lv2,				5
	 * option,			6
	 * regime,			7
	 * date_naissance,		8
	 * lieu_naissance,		9
	 * nationalite,			10
	 * passwd,			11
	 * passwd_eleve,		12
	 * civ_1,			13
	 * nomtuteur,			14
	 * prenomtuteur,		15
	 * adr1,			16
	 * code_post_adr1,		17
	 * commune_adr1,		18
	 * tel_port_1,			19
	 * civ_2,			20
	 * nom_resp_2,			21
	 * prenom_resp_2,		22
	 * adr2,			23
	 * code_post_adr2,		24
	 * commune_adr2,		25
	 * tel_port_2,			26
	 * telephone,			27
	 * profession_pere,		28
	 * tel_prof_pere,		29
	 * profession_mere,		30
	 * tel_prof_mere,		31
	 * nom_etablissement,		32
	 * numero_etablissement,	33
	 * code_postal_etablissement,	34
	 * commune_etablissement,	35
	 * numero_eleve,		36
	 * photo,			37
	 * email,			38
	 * email_eleve,			39
	 * email_resp_2,		40
	 * class_ant,			41
	 * annee_ant,			42
	 * numero_gep,			43
	 * valid_forward_mail_eleve,	44
	 * valid_forward_mail_parent,	45
	 * tel_eleve,			46
	 * code_compta,			47
	 * sexe 			48
	 * email_eleve,			49
	 * adr_eleve,			50
	 * ccp_eleve,			51
	 * commune_eleve,		52
	 * pays_eleve			53
	 * emailpro_eleve		54
	 * annee_scolaire		55
	 * information			56
	 * tel_fixe_eleve		57
	 */
	$ii=1;
	for ($i=0;$i<count($datalisting);$i++) {
		$a=1;
		for ($j=0;$j<count($colonne);$j++) {
			$donnee="";
			if ($colonne[$a] == "nom") { 	$choix=1;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "prenom") { $choix=2;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "classe") { $choix=3;  $donnee=chercheClasse_nom($datalisting[$i][$choix]);  }
			if ($colonne[$a] == "lv1") { 	$choix=4;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "lv2") { 	$choix=5;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "option") { $choix=6;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "regime") { $choix=7;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "date_naissance") { $choix=8;  $donnee=dateForm($datalisting[$i][$choix]); }
			if ($colonne[$a] == "lieu_naissance") { $choix=9;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "nationalite") { 	$choix=10;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "civ_1") { 		$choix=13;  $donnee=civ($datalisting[$i][$choix]); }
			if ($colonne[$a] == "nomtuteur") { 	$choix=14;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "prenomtuteur") { 	$choix=15;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "adr1") { 		$choix=16;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "code_post_adr1") { $choix=17;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "commune_adr1") { 	$choix=18;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "tel_port_1") { 	$choix=19;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "civ_2") { 		$choix=20;  $donnee=civ($datalisting[$i][$choix]); }
			if ($colonne[$a] == "nomtuteur_2") { 	$choix=21;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "prenomtuteur_2") { $choix=22;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "adr2") { 		$choix=23;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "code_post_adr2") { $choix=24;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "commune_adr2") { 	$choix=25;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "tel_port_2") { 	$choix=26;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "telephone") { 	$choix=27;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "profession_pere") { $choix=28;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "tel_prof_pere") { 	 $choix=29;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "profession_mere") { $choix=30;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "tel_prof_mere") { 	 $choix=31;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "nom_etablissement") { 	$choix=32;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "numero_etablissement") { 	$choix=33;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "code_postal_etablissement") { $choix=34;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "commune_etablissement") { 	$choix=35;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "numero_eleve") { 	$choix=36;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "photo") { 		$choix=37;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "email") { 		$choix=38;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "email_eleve") { 	$choix=39;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "email_resp_2") { 	$choix=40;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "class_ant") { 	$choix=41;  $donnee=$datalisting[$i][$choix]; } 
			if ($colonne[$a] == "annee_ant") { 	$choix=42;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "numero_gep") { 	$choix=44;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "valid_forward_mail_eleve") {   $choix=45;  $donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "tel_eleve") { 	$choix=46;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "valid_forward_mail_parent") {  $choix=47;  $donnee=strtolower($datalisting[$i][$choix]); }
			if ($colonne[$a] == "sexe") { 		$choix=48;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "code_barre") { 	$choix=0;   $donnee=recupIdCodeBar($datalisting[$i][$choix],"menueleve"); }
			if ($colonne[$a] == "email_eleve") { 	$choix=49;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "adresse_eleve") { 	$choix=50;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "ccp_eleve") { 	$choix=51;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "commune_eleve") { 	$choix=52;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "pays_eleve") { 	$choix=53;  $donnee=$datalisting[$i][$choix]; }
			if ($colonne[$a] == "email_eleve_pro") {$choix=54;  $donnee=$datalisting[$i][$choix]; }	
			if ($colonne[$a] == "annee_scolaire")  {$choix=55;  $donnee=$datalisting[$i][$choix]; }	
			if ($colonne[$a] == "information")     {$choix=56;  $donnee=$datalisting[$i][$choix]; }	
			if ($colonne[$a] == "tel_fixe_eleve")  {$choix=57;  $donnee=$datalisting[$i][$choix]; }	
			$worksheet1->write_string($ii, $j, "$donnee", $center);
			$a++;
	    	}
		$ii++;
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
