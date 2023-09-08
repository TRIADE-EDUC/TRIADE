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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxNoteVisu.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script type="text/javascript" src="./librairie_js/ajax-menu-tab.js"></script>
<script type="text/javascript" src="./librairie_js/menu-tab.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
validerequete("7");
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<?php
$id_eleve=$_GET["eid"];
$saisie_classe=$_GET["idclasse"];
$_SESSION["pageretour"]="ficheeleve3.php?eid=$id_eleve&idclasse=$saisie_classe";

if (trim($saisie_classe) == "") {
	$saisie_classe=chercheIdClasseDunEleve($id_eleve);
}

$disabledSMS="disabled";

if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	if ((LAN == "oui") && (file_exists("./common/config-sms.php"))) {
		$disabledSMS="";
	}
}

$disabledprojo="1";
if (( ((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) && ($_SESSION["membre"] == "menuprof")) || ($_SESSION["membre"] == "menuadmin")||($_SESSION["membre"]=="menupersonnel")) { 
		$disabledprojo="0";
}
if ($saisie_classe == "") $saisie_classe=chercheIdClasseDunEleve($id_eleve);
?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  bgcolor="#0B3A0C"  height="830">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGPROF26 ?> <font id="color2" ><?php print recherche_eleve($id_eleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' valign='top' ><td>
<br>

<table width='100%' border=0>
<tr>
<td>
<form method="post" action="ficheeleve2.php" >
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS250 ?>","rien");</script>
<input type="hidden" name="sClasseGrp" value="<?php print $saisie_classe?>" />
<input type="hidden" name="anneeScolaire" value="<?php print anneeScolaireViaIdClasse($saisie_classe) ?>" />
</form>
</td><td>
<form method='get' action="sms-mess.php">
<script language=JavaScript> buttonMagicSubmit3("<?php print LANGMESS251 ?>","sms","<?php print $disabledSMS?>")</script>
<input type='hidden' name="eid" value="<?php print  $id_eleve ?>" />
</form>
</td><td>
<script language=JavaScript>buttonMagic2("<?php print LANGPROF38 ?>","profpprojo.php?fiche=1&idClasse=<?php print $saisie_classe?>","video","width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","<?php print $disabledprojo ?>");</script>
</td>
<?php if (($_SESSION["membre"] == "menuadmin")  ||  ((VIESCOLAIREMODIFETUDIANT == "oui") && ($_SESSION["membre"] == "menuscolaire")) ) { ?>
<td>
<form method='get' action="modif_eleve.php">
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGMESS252 ?>","fiche","")</script>
<input type='hidden' name="eid" value="<?php print  $id_eleve ?>" />
</form>
</td>
<?php } ?>
</tr>
<tr><td height='20'></td></tr>
<?php if ( (CARNETSUIVIPROF == "oui") && ($_SESSION["membre"] == "menuprof"))  { ?>
	<tr><td><script language=JavaScript>buttonMagic("<?php print LANGMESST392 ?>","carnet_editer.php?sClasseGrp=<?php print $saisie_classe?>","_parent","","");</script></td></tr>
<?php } ?>

<tr>
<?php 
if ($_SESSION["membre"] == "menuadmin") { 
	$brimg=$img2=$img="";
	if (isset($_GET["val"])) { inactifEleve($id_eleve,$_GET["val"]); }
	$inactif=getInactifEleve($id_eleve);
	if ($inactif == "1") {
		$bouton=LANGMESS255;
		$inactifval="0";
		$img="<font id='color3'><img src='image/commun/warning2.gif' align='center' /><b>".LANGMESST393."</b></font>";
	}else{
		$bouton=LANGMESS254;
		$inactifval="1";
	}

	if (isset($_GET["proba"])) { ProbatoireEleve($id_eleve,$_GET["proba"]); }
        $inactifProba=getProbaEleve($id_eleve);
        if ($inactifProba == "1") {
                $bouton2=LANGMESST395;
                $probaval="0";
                $img2="<font id='color3'><img src='image/commun/warning2.gif' align='center' /><b>".LANGMESST394."</b></font>";
        }else{
		$bouton2=LANGMESST396;
                $probaval="1";
        }


	if (($img != "") || ($img2 != "")) {
		$brimg="<br><br>";
	}
?>
<td colspan='4' align='left' >
<table><tr>
</td><td><script language=JavaScript>buttonMagic2('<?php print $bouton?>','ficheeleve3.php?eid=<?php print $id_eleve?>&val=<?php print $inactifval?>','_self','')</script></td>
<td><script language=JavaScript>buttonMagic2('<?php print $bouton2 ?>','ficheeleve3.php?eid=<?php print $id_eleve?>&proba=<?php print $probaval?>','_self','')</script></td>
<td><script language=JavaScript>buttonMagic2('<?php print "Affecter &agrave; un stage"?>','gestion_stage_affec_eleve_2.php?id=<?php print $id_eleve?>&idclasse=<?php print $saisie_classe ?>','_self','')</script>
<?php if (true)  { ?>
<td><script language=JavaScript>buttonMagic2('<?php print "Impression"?>','impressionficheeleve.php?id=<?php print $id_eleve?>&idclasse=<?php print $saisie_classe ?>','_self','')</script>
<?php } ?>
</tr>
<tr><td colspan='3' align='center' ><?php print $brimg.$img." ".$img2 ?></td></tr>
</table>
</td>
<?php } ?>
</tr>

</table>




<br /><br />

<div id="dhtmlgoodies_tabView1">

  <?php // Renseignements ?>
  <div class="dhtmlgoodies_aTab">
<?php
$sql=<<<EOF
SELECT
	elev_id, 
	nom,
	prenom,
	c.libelle,
	lv1,
	lv2,
	`option`,
	regime,
	date_naissance,
	lieu_naissance,
	nationalite,
	passwd,
	passwd_eleve,
	civ_1,
	nomtuteur,
	prenomtuteur,	
	adr1,
	code_post_adr1,
	commune_adr1,
	tel_port_1,
	civ_2,
	nom_resp_2,
	prenom_resp_2,
	adr2,
	code_post_adr2,
	commune_adr2,
	tel_port_2,
	telephone,
	profession_pere,
	tel_prof_pere,
	profession_mere,
	tel_prof_mere,
	nom_etablissement,
	numero_etablissement,
	code_postal_etablissement,
	commune_etablissement,
	numero_eleve,
	email,
	email_eleve,
	class_ant,
	annee_ant,
	tel_eleve,
	email_resp_2,
	sexe,
	code_compta,
	information,
	adr_eleve,
	commune_eleve,
	ccp_eleve,
	tel_fixe_eleve,
	boursier,
	montant_bourse,
	indemnite_stage,
	emailpro_eleve,
	rangement,
	cdi,
	bde,
	situation_familiale,
	annee_scolaire,
	serie_bac,
	annee_bac,
	departement_bac,
	departementnais
FROM
	${prefixe}eleves, ${prefixe}classes c
WHERE
	elev_id='$id_eleve'
AND	c.code_class=classe

EOF;
$res=execSql($sql);
$data=chargeMat($res);

$idEleve=$id_eleve;
$nom=$data[0][1];
$prenom=$data[0][2];
$classe=$data[0][3];
$boursier=($data[0][50] == 0) ? LANGNON : LANGOUI ;
$lv1=$data[0][4];
$lv2=$data[0][5];
$option=$data[0][6];
$regime=$data[0][7];
$date_naissance=$data[0][8];
$lieu_naissance=$data[0][9];
$nationalite=$data[0][10];
$numero_eleve=$data[0][36];

/*
13	civ_1,
14	nomtuteur,
15	prenomtuteur,	
16	adr1,
17	code_post_adr1,
18	commune_adr1,
19	tel_port_1,
20	civ_2,
21	nom_resp_2,
22	prenom_resp_2,
23	adr2,
24	code_post_adr2,
25	commune_adr2,
26	tel_port_2,
27	telephone,
28	profession_pere,
29	tel_prof_pere,
30	profession_mere,
31	tel_prof_mere,
 */
$civ_1=$data[0][13];
$nomtuteur=$data[0][14];
$prenomtuteur=$data[0][15];
$adr1=$data[0][16];
$code_post_adr1=$data[0][17];
$commune_adr1=$data[0][18];
$tel_port_1=$data[0][19];
$civ_2=$data[0][20];
$nom_resp_2=$data[0][21];
$prenom_resp_2=$data[0][22];
$adr2=$data[0][23];
$code_post_adr2=$data[0][24];
$commune_adr2=$data[0][25];
$tel_port_2=$data[0][26];
$telephone=$data[0][27];
$profession_pere=$data[0][28];
$tel_prof_pere=$data[0][29];
$profession_mere=$data[0][30];
$tel_prof_mere=$data[0][31];

/*
37	email,
38	email_eleve,
39	class_ant,
40	annee_ant,
41	tel_eleve,
42	email_resp_2,
43	sexe,
44	code_compta,
45	information,
46	adr_eleve,
47	commune_eleve,
48	ccp_eleve,
49	tel_fixe_eleve,
50	boursier,
51	montant_bourse,
52	indemnite_stage,
53	emailpro_eleve
54	rangement
55 	cdi,
56	bde,
57	situation_familiale
*/
$email=$data[0][37];
$email_eleve=$data[0][38];
$class_ant=$data[0][39];
$annee_ant=$data[0][40];
$tel_eleve=$data[0][41];
$email_resp_2=$data[0][42];
$sexe=$data[0][43];
$code_compta=$data[0][44];
$information=$data[0][45];
$adr_eleve=$data[0][46];
$commune_eleve=$data[0][47];
$ccp_eleve=$data[0][48];
$tel_fixe_eleve=$data[0][49];
$emailpro_eleve=$data[0][53];
$rangement=$data[0][54];
$cdi=($data[0][55] == 0) ? LANGNON : LANGOUI ;
$bde=($data[0][56] == 0) ? LANGNON : LANGOUI ;
$lv2=($lv2 == "NULL") ? "" : $lv2 ;
$lv1=($lv1 == "NULL") ? "" : $lv1 ;
$situation_familiale=$data[0][57];
$annee_scolaire_eleve=$data[0][58];
$serie_bac=$data[0][59];
$annee_bac=$data[0][60];
$departement_bac=$data[0][61];
$departementnais=$data[0][62];

?>

	<div style="position:relative;left:10px;top:15px;float:left;text-align:center;z-index:10000">
		<div style="position:absolute;top:-28;left:-24px;z-index:1000000" ><img src='image/commun/paperclip.png'></div>
		<img src="image_trombi.php?idE=<?php print $id_eleve ?>" border=0 ><br>[ <a href="#" class="bouton2"  onclick="open('photoajouteleve.php?ideleve=<?php print $idEleve?>','photo','width=450,height=280')" ><?php print LANGPER30 ?></a> ]</div>

	<div style="position:relative;left:20px;top:5px">
	<table border=0 width='65%'>
	<tr><td align='right' width='35%' ><font class='T2'><?php print LANGMESS270 ?></font></td><td><b><font class='T2'><?php print $nom ?></font></b></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS271 ?></font></td><td><font class='T2'><?php print $prenom ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS272 ?></font></td><td title="<?php print $classe?>" ><font class='T2'><?php print trunchaine($classe,35) ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print "Année Scolaire : " ?></font></td><td><?php print $annee_scolaire_eleve  ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS273 ?></font></td><td><font class='T2'><?php print dateForm($date_naissance) ?></font></td></tr>
	<tr><td align='right' width='5%' ><font class='T2'><?php print LANGMESS274 ?></font></td><td><font class='T2'><?php print $nationalite ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS275 ?></font></td><td><font class='T2'><?php print $lieu_naissance ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print "D&eacute;partement de naissance" ?></font>&nbsp;</td><td><font class='T2'><?php print $departementnais ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS276 ?></font></td><td><font class='T2'><?php print $boursier ?>&nbsp;/&nbsp;CDI&nbsp;:&nbsp;<?php print $cdi ?>&nbsp;/&nbsp;BDE&nbsp;:&nbsp;<?php print $bde ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS277 ?></font></td><td><font class='T2'><?php print $numero_eleve ?></font></td></tr>
	<tr><td align='right' width='5%' ><font class='T2'><?php print LANGMESS278 ?></font></td><td><font class='T2'><?php print $lv1 ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS279 ?></font></td><td><font class='T2'><?php print $lv2 ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS280 ?></font></td><td><font class='T2'><?php print $option ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS281 ?></font></td><td><font class='T2'><?php print $regime ?></font></td></tr>
	<tr><td align='right'><font class='T2'><?php print LANGMESS282 ?></font></td><td><font class='T2'><?php print $rangement ?></font></td></tr>
		<?php
		$texte=recupIdCodeBar($id_eleve,"menueleve"); 
		if ($texte != "") {
			$infoT=LANGASS39;
		   	print "<tr><td align='right' valign='top'  ><font class='T2'>$infoT :</font></td><td><font class='T2'><img src='./codebar/image.php?code=code39&text=$texte' /></font></td></tr>";
		}
		?>
	    	</table>
	</div>
<br><br>
	<hr>
<br>
<div id="dhtmlgoodies_tabView2">
		<div class="dhtmlgoodies_aTab">
		<table border=0 width='100%'>
		<tr><td align='right' width='20%' ><font class='T2'><?php print LANGMESS283 ?></font></td><td><b><font class='T2'><?php print civ($civ_1)." ".$nomtuteur." ".$prenomtuteur ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS284 ?></font></td><td><font class='T2'><?php print $situation_familiale ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS285 ?></font></td><td><font class='T2'><?php print $adr1 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS287 ?></font></td><td><font class='T2'><?php print $code_post_adr1 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS288 ?></font></td><td><font class='T2'><?php print $commune_adr1 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS289 ?></font></td><td><font class='T2'><?php print $email ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS290 ?></font></td><td><font class='T2'><?php print "$telephone / $tel_port_1" ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS291 ?></font></td><td><font class='T2'><?php print $profession_pere ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS292 ?></font></td><td><font class='T2'><?php print $tel_prof_pere ?></font></td></tr>
	    	</table>
		</div>
		<div class="dhtmlgoodies_aTab">
		<table border=0 width='100%'>
		<tr><td align='right' width='20%' ><font class='T2'><?php print LANGMESS283 ?></font></td><td><b><font class='T2'><?php print civ($civ_2)." ".$nom_resp_2." ".$prenom_resp_2 ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS284 ?></font></td><td><font class='T2'><?php print $situation_familiale ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS285 ?></font></td><td><font class='T2'><?php print $adr2 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS287 ?></font></td><td><font class='T2'><?php print $code_post_adr2 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS288 ?></font></td><td><font class='T2'><?php print $commune_adr2 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS289 ?></font></td><td><font class='T2'><?php print $email_resp_2 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS290 ?></font></td><td><font class='T2'><?php print "$tel_port_2" ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS291 ?></font></td><td><font class='T2'><?php print $profession_mere ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS292 ?></font></td><td><font class='T2'><?php print $tel_prof_mere ?></font></td></tr>
	    	</table>
		</div>
		<div class="dhtmlgoodies_aTab">
		<table border=0 width='100%'>
		<tr><td align='right' width='20%' ><font class='T2'><?php print LANGMESS283 ?></font></td><td><b><font class='T2'><?php print $nom." ".$prenom ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS285 ?></font></td><td><b><font class='T2'><?php print $adr_eleve ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS287 ?></font></td><td><font class='T2'><?php print $code_post_adr1 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS288 ?></font></td><td><font class='T2'><?php print $commune_adr1 ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS289 ?></font></td><td><font class='T2'><?php print "$email_eleve / $emailpro_eleve" ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS290 ?></font></td><td><font class='T2'><?php print "$tel_eleve / $tel_fixe_eleve" ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS293 ?></font></td><td><font class='T2'><?php print $sexe ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS294 ?></font></td><td><font class='T2'><?php print $class_ant ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print "S&eacute;rie du bac : " ?></font></td><td><font class='T2'><?php print $serie_bac ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print "Ann&eacute;e du bac : " ?></font></td><td><font class='T2'><?php print $annee_bac ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print "D&eacute;partement du bac : " ?></font></td><td><font class='T2'><?php print $departement_bac ?></font></td></tr>
	    	</table>
		</div>
		<div class="dhtmlgoodies_aTab">
                <table border=0 width='100%'>
		<?php
		$dataTuteur=recupInfoTuteurStage($id_eleve);
		// nom,prenom,civ,email,adr,code_post,commune,tel,tel_port,id_societe_tuteur 
		$nomTuteurStage=$dataTuteur[0][0];
		$prenomTuteurStage=$dataTuteur[0][1];
		$civTuteur=civ($dataTuteur[0][2]);
		$societeTuteurStage=recherche_entr_nom_via_id($dataTuteur[0][9]);
		$adr_TuteurStage=$dataTuteur[0][4];
		$ccp_TuteurStage=$dataTuteur[0][5];
		$commune_TuteurStage=$dataTuteur[0][6];
		$email_TuteurStage=$dataTuteur[0][3];
		$tel_TuteurStage=$dataTuteur[0][7];
		$telPort_TuteurStage=$dataTuteur[0][8];
		?>
		<tr><td align='right' width='20%' ><font class='T2'><?php print LANGMESS283 ?></font></td><td><b><font class='T2'><?php print $civTuteur." ".$nomTuteurStage." ".$prenomTuteurStage ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGAGENDA61 ?>&nbsp;:</font></td><td><b><font class='T2'><?php print $societeTuteurStage ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGAGENDA63 ?>&nbsp;:</font></td><td><b><font class='T2'><?php print $adr_TuteurStage ?></font></b></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS287 ?></font></td><td><font class='T2'><?php print $ccp_TuteurStage ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS288 ?></font></td><td><font class='T2'><?php print $commune_TuteurStage ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS289 ?></font></td><td><font class='T2'><?php print "$email_TuteurStage" ?></font></td></tr>
		<tr><td align='right'><font class='T2'><?php print LANGMESS290 ?></font></td><td><font class='T2'><?php print "$tel_TuteurStage / $telPort_TuteurStage" ?></font></td></tr>
                </table>
                </div>

		<div class="dhtmlgoodies_aTab">
		<table width=100% border=1 style='border-collapse: collapse;' >
		<tr>
		<td width=5 bgcolor='yellow' >&nbsp;Période&nbsp;</td>
		<td align=center  bgcolor='yellow'  >&nbsp;Classe&nbsp;</td>
		<td align=center  bgcolor='yellow'  >&nbsp;<?php print LANGSTAGE39?>&nbsp;</td>
		</tr>
		<?php
		$data=recherche_stage_historique($id_eleve); //e.nom,s.nomprenomeleve,s.classeeleve,s.periodestage
		for($i=0;$i<count($data);$i++) {
		        $nom_entreprise=$data[$i][0];
		        $periode=preg_replace('/ /','&nbsp;',$data[$i][3]);
		        $classe=$data[$i][2];
		        print "<tr bgcolor='#FFFFFF' >";
		        print "<td width=5 >&nbsp;$periode&nbsp;</td>";
		        print "<td >&nbsp;$classe&nbsp;</td>";
		        print "<td >&nbsp;<a href='gestion_stage_ent_visu_rech_nom.php?recherche=$nom_entreprise' title='Consulter' >$nom_entreprise</a>&nbsp;</td>";
		        print "</tr>";
		}
		print "</table><br><br>";
		?>


		</div>





		<div class="dhtmlgoodies_aTab">
		<?php
		$databull=recupArchiveBulletin($idEleve); //  ideleve,anneescolaire,trimestre,date,classe,file
		?>
		<table border=1 width='100%'  style="border-collapse: collapse;" >
		<tr>	<td bgcolor='yellow' id='bordure' ><font class='T1'><?php print LANGMESS295 ?></font></td>
			<td bgcolor='yellow' id='bordure' ><font class='T1'><?php print LANGELE4 ?></font></td>
			<td bgcolor='yellow' id='bordure' ><font class='T1'><?php print LANGMESS296 ?></font></td>
			<td bgcolor='yellow' id='bordure' ><font class='T1'><?php print LANGMESS297 ?></font></td>
			<td bgcolor='yellow' id='bordure' ><font class='T1'><?php print LANGMESS298 ?></font></td>
		</tr>
		<?php

		if (isset($_GET["supp"])) {
			if ($_SESSION["membre"] == "menuadmin") {
				if (file_exists($ficsupp)) @unlink($ficsupp);
			}
		}

		for($j=0;$j<count($databull);$j++) {
			$fichierarc=$databull[$j][5];
			$fichierarc=preg_replace('/\'/',"",$fichierarc);

			if (file_exists($fichierarc)) {	
				$lien="<a href='visu_document.php?fichier=$fichierarc' target='_blank' ><img src='image/commun/download.png' title='".LANGTELECHARGE."' border='0' /></a>";
				if ($_SESSION["membre"] == "menuadmin") $lien.="&nbsp;<a href='ficheeleve3.php?eid=$idEleve&idclasse=$saisie_classe&supp=$fichierarc' ><img src='image/commun/trash.png' title='".LANGBT50."' border='0' /></a>";
				print '<tr class="tabnormal" onmouseover="this.className=\'tabover\'" onmouseout="this.className=\'tabnormal\'" >';
				print '<td id=bordure   ><font class=T1>'.$databull[$j][1].'</font></td>';
				print '<td id=bordure   ><font class=T1>'.preg_replace('/_/','&nbsp;',$databull[$j][4]).'</font></td>';
				print '<td id=bordure   ><font class=T1>'.$databull[$j][2].'</font></td>';
				print '<td id=bordure   ><font class=T1>'.$lien.'</font></td>';
				print '<td id=bordure  ><font class=T1>'.dateForm($databull[$j][3]).'</font></td>';
				print '</tr>';
			}else{
				suppArchiveBulletinEleve($fichierarc);
			}
		}

		?>
	    	</table>
		</div>

		<?php // info medic ?>
		<div class="dhtmlgoodies_aTab">
		<?php
		if ( ((defined("INFOMEDIC")) && (INFOMEDIC == "oui")) || ($_SESSION["membre"] == "menuadmin" ) || ($_SESSION["membre"] == "menupersonnel") ) {
			print "<font class='T2'>".LANGPROF29." : </font><br><br>";
			$data=profPmedAff($idEleve);
			// id,date,ideleve,nomProf,commentaire
			print "<table width='100%' border='1'  style='border-collapse: collapse;'  >";
			print "<tr>
				<td id='bordure' bgcolor='yellow' width='5%'>&nbsp;".LANGTE7."&nbsp;</td>
				<td id='bordure' bgcolor='yellow' width='30%'>&nbsp;".LANGMESST397."&nbsp;</td>
				<td id='bordure' bgcolor='yellow'>&nbsp;".LANGSTAGE37."&nbsp;</td>
				</tr>";
			for($i=0;$i<count($data);$i++) { ?>
				<tr>
				<td valign='top' ><?php print dateForm($data[$i][1]) ?></td>
				<td valign='top' ><?php print $data[$i][3]?></td>
				<td valign='top' ><?php print $data[$i][4]?> &nbsp;&nbsp;</td>
				</tr>
			<?php
			}
			print "</table>";
		}else{ ?>
			<br><br><center><font class='T2'><?php print LANGMESS308 ?></font></center>
		<?php 
		} 
		?>
		</div>
		<div class="dhtmlgoodies_aTab">
		<?php 
		if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")){
			print "<br><script language=JavaScript>buttonMagic('".LANGMESS309."','profpcomplement.php?eid=$idEleve','_parent','','');</script>";
		}
		?>
		<br><br>
		<?php
			$data=profPinfoAff($idEleve);
		// id,date,idEleve,nomProf,commentaire
			print "<table width='100%' border='1'  style='border-collapse: collapse;'  >";
			print "<tr>
				<td id='bordure' bgcolor='yellow' width='5%'>&nbsp;".LANGTE7."&nbsp;</td>
				<td id='bordure' bgcolor='yellow' width='30%'>&nbsp;".LANGMESST397."&nbsp;</td>
				<td id='bordure' bgcolor='yellow'>&nbsp;".LANGSTAGE37."&nbsp;</td>
				</tr>";
			for($i=0;$i<count($data);$i++) {
			?>
				<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'" >
				<td valign='top' ><?php print dateForm($data[$i][1]) ?></td>
				<td valign='top' ><?php print $data[$i][5]?></td>
				<td valign='top' ><?php print $data[$i][4]?> &nbsp;&nbsp;</td>
				</tr>		
				
			<?php
			}
		?>
		</table>
		</div>
	</div>
  </div>


  <div class="dhtmlgoodies_aTab">
<?php 
	if (  (((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) && ($_SESSION["membre"] == "menuprof")) 
		|| ( ($_SESSION["membre"] == "menuprof") && (ENTRETIENPROF == "oui") )
		|| ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel") 
		|| (($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRENOTEENSEIGNANT == "oui"))
	   ) { ?>
		<div id='visunote'></div>
		<table  >
		<tr>
		<td>
		<form method="get" action="entretien2.php" >
		<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS310 ?>","rien");</script>
		<input type="hidden" name="idclasse" value="<?php print $saisie_classe?>" />
		<input type="hidden" name="eid" value="<?php print $id_eleve?>" />
		</form>
		</td>
		</tr></table>

		<script>ajaxVisuNote('<?php print $id_eleve ?>','<?php print $saisie_classe ?>','','')</script>
<?php	}else{ ?>
		<br><br><center><font class='T2'><?php print LANGMESS308 ?></font></center>
<?php   } ?>
  </div>


  <?php // vie scolaire ?>
  <div class="dhtmlgoodies_aTab" style="overflow:auto;" >
		<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ) {  ?>
			<br><table>
			<tr><td>
			<form method="post" action="gestion_abs_retard_planifier.php" >
			<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS311 ?>","rien");</script>
			<input type="hidden" name="saisie_nom_eleve" value="<?php print recherche_eleve_nom($id_eleve) ?>"  />
			</form>
			</td><td>
			<form method="post" action="gestion_abs_retard_modif_donne.php" >
			<td id='bordure'><script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS312 ?>","rien");</script>
			<input type="hidden" name="saisie_nom_eleve" value="<?php print recherche_eleve_nom($id_eleve) ?>"  />
			</form>
			</td><td>
			<form method="post" action="gestion_abs_retard_modif.php" >
			<td id='bordure'><script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS313 ?>","rien");</script>
			<input type="hidden" name="saisie_nom_eleve" value="<?php print recherche_eleve_nom($id_eleve) ?>"  />
			</form>
			</td></tr></table><br>
		<?php } 
			if ((ACCESPROFVISUABSRTD == "oui") ||  ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ||  ($_SESSION["membre"] == "menupersonnel")) { 
				$sql="SELECT c.libelle,e.nom,e.prenom,e.elev_id FROM ${prefixe}eleves e, ${prefixe}classes c WHERE e.elev_id='$idEleve' AND c.code_class=e.classe ORDER BY c.libelle, e.nom, e.prenom";
				$res=execSql($sql);
				$data=chargeMat($res);
				for($i=0;$i<count($data);$i++) { ?>
					<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;"  >
					<tr>
					<td bgcolor="#FFFFFF" width=55%><?php print LANGTP1 ?> : <B><?php print ucwords(trim($data[$i][1]))?></b></td>
					<td bgcolor="#FFFFFF"><?php print LANGCALEN7 ?> : <font color=red><?php print trim($data[$i][0])?></font>
					</td></tr>
					<tr>
					<td bgcolor="#FFFFFF"><?php print LANGTP2 ?> : <b><?php print ucwords(trim($data[$i][2]))?></b></td>
					<td bgcolor="#FFFFFF"> <?php print LANGABS62 ?></td>
					</tr>
					</table>
					<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;"  >
					<TR>
					<TD bgcolor='yellow' align=center width=15%><?php print LANGABS13 ?></td>
					<TD bgcolor='yellow' align=center width=20%><?php print LANGPARENT17 ?> </td>
					<TD bgcolor='yellow' align=center width=15%><?php print LANGABS60 ?> </td>
					<TD bgcolor='yellow' align=center width=20%><?php print LANGABS12 ?> </td>
					</TR>
					<?php
					$data_2=affRetard($data[$i][3]);
					// $data : tab bidim - soustab 3 champs
					// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux
					for($j=0;$j<count($data_2);$j++) {
						list($creneaux,$debcre,$fincre)=preg_split('/#/',$data_2[$j][10]);
						$matiere=chercheMatiereNom($data_2[$j][7]);
						if (($matiere == "") || ($matiere < 0)) { $matiere="";  } ?>
							<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
							<form name="formulaire_<?php print $i.$j?>" >
							<TD align=center valign=top><?php print date_jour(dateForm($data_2[$j][2])); ?>  <br>
							<?php  print dateForm($data_2[$j][2])?>
							</td>
							<TD  align=center valign=top><?php print timeForm($data_2[$j][1])." ".$creneaux ?> <br> <?php print "  ($debcre - $fincre)" ?> <br>(<?php print trunchaine($matiere,11) ?>) </td>
							<TD  align=center valign=top>
							<select name="saisie_duree_<?php print $i?>" >
							<option STYLE='color:#000066;background-color:#FCE4BA'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							<option STYLE='color:#000066;background-color:#CCCCFF'></option>
							</select>
							<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>" value="<?php print $data_2[$j][5]?>"  >
							<?php
							$yy=$data_2[$j][5];
							if ($data_2[$j][5] == 0) { $yy="???"; }
							?>
							<script langage=Javascript>
							chargement_pendant('<?php print trim($yy)?>','<?php print $i?>','<?php print $i.$j?>');
							</script>
							</td>
							<TD  valign=top>
							<?php
							$motiftext=$data_2[$j][6] ;
							if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
							$motiftext=preg_replace('/"/'," ",$motiftext);
							?>
							<input type=text name="saisie_modif_<?php print $i?>" value="<?php print $motiftext ?>" size=30 readonly >
							( <input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" disabled <?php if ($data_2[$j][8] == 1) { print "checked='checked'"; } ?> > Justifié)
							</td>
							</form>
							</TR>
						<?php
        					}
						?>
						</table>
						<BR>
						<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;"  >
						<TR>
						<TD bgcolor='yellow' align=center width=15%><?php print LANGPARENT8 ?> </td>
						<TD bgcolor='yellow' align=center width=15%><?php print LANGABS60 ?> </td>
						<TD bgcolor='yellow' align=center width=20%>&nbsp;<?php
						if ($_SESSION["membre"] == "menuprof") {
							print "Créneau&nbsp;";
						}else{
							print LANGGRP29bis."&nbsp";
						}
						?>
						</td>
						<TD bgcolor='yellow' align=center width=20%><?php print LANGABS12 ?> </td>
						</TR>
						<?php
						$data_3=affAbsence($data[$i][3]);
						//    elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif,  duree_heure, id_matiere, time, justifier, heure_saisie, heuredabsence, creneaux
						for($j=0;$j<count($data_3);$j++) {
							list($creneaux,$debcre,$fincre)=preg_split('/#/',$data_3[$j][13]);
						?>
						<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
						<form  name="formulaire_3_<?php print $i.$j?>" >
						<TD  align=center valign=top><?php print date_jour(dateForm($data_3[$j][1])); ?><br><?php print dateForm($data_3[$j][1])?></td>
						<TD  align=center valign=top>
						<select name="saisie_duree_<?php print $i?>"  >
						<option STYLE='color:#000066;background-color:#FCE4BA'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						<option STYLE='color:#000066;background-color:#CCCCFF'></option>
						</select>
						<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>" value="<?php print $data_3[$j][4]?>"  >
						<?php
						$yy=$data_3[$j][4]." J";
						if ($data_3[$j][4] == 0) { $yy="???"; }
						if ($data_3[$j][4] == -1) { $yy=$data_3[$j][7]."H"; }
						?>
						<script langage=Javascript>
						chargement_pendant_jour('<?php print trim($yy)?>','<?php print $i?>','<?php print $i.$j?>');
						</script>
						<TD align=center valign=top>
						<?php 
						if ($_SESSION["membre"] == "menuprof") {
							print "$creneaux ($debcre - $fincre)";
						}else{
							print dateForm($data_3[$j][2])?> <br> <?php if (($data_3[$j][11] != "") && ($data_3[$j][11] != "00:00:00") ){ print timeForm($data_3[$j][11]); 
						}
					}
					?>	       
					</td>
					<TD valign=top>
					<?php $motiftext=$data_3[$j][6];
      					if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
      					$motiftext=preg_replace('/"/'," ",$motiftext);
					?>
					<input type=text name="saisie_modif_<?php print $i?>" value="<?php print $motiftext ?>" size=30 readonly >
					( <input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" disabled <?php if ($data_3[$j][10] == 1) { print "checked='checked'"; } ?> > Justifié)
					</td>
					<input type=hidden name=saisie_eleve_id_2 value="<?php print $data[$i][3]?>">
					<input type=hidden name=saisie_date_ret_2 value="<?php print $data_3[$j][1]?>">
					<input type=hidden name=saisie_nom_eleve value="<?php print $data[$i][1]?>">
					<input type=hidden name=saisie_id_champ value="<?php print $i?>">
					<input type=hidden name=saisie_time value="<?php print $data_3[$j][9]?>">
					<input type=hidden name=saisie_matiere value="<?php print $data_3[$j][8]?>">
					</form>
					</td>
					</TR>
<?php 				} ?>
				</table>
		<?php } ?>
	<?php }else{ ?>
		<br><br><center><font class='T2'><?php print LANGMESS308 ?></font></center>
	<?php 
	     } 
	?>
  </div>

  <?php //  ?>
  <div class="dhtmlgoodies_aTab"  style="overflow:auto;"  >
	<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;"  >
	<TR><td colspan=4 bgcolor=#FFFFFF align=center> <?php print LANGPARENT15 ?>  </td></tr>
	<TR>

	<TD bgcolor='yellow' align=center width=5><?php print ucwords(LANGPROFK) ?></td>
	<TD bgcolor='yellow' align=center ><?php print LANGDISC57?> </td>

	</TR>
<?php

if (($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menupersonnel") ) {
	$data_2=affSanction_par_eleve($idEleve);
}


// id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire
// $data : tab bidim - soustab 3 champs

for($j=0;$j<count($data_2);$j++)
        {
		$raison=$data_2[$j][8];
		$raison=preg_replace('/\r\n/',"<br />",$raison);
		$raison=preg_replace('/\n/',"<br />",$raison);

?>
	<TR  class="tabnormal" onMouseOver="this.className='tabover'" onMouseOut="this.className='tabnormal'">
	
	<TD align=center valign="top" width=10% ><?php print dateForm($data_2[$j][4])?></td>
	<TD valign=top>
	&nbsp;<?php print ucwords(LANGDISC20) ?>: <font color=red><b><?php print rechercheCategory($data_2[$j][3])?></b></font> <br />
	&nbsp;<?php print ucwords(LANGPARENT15) ?>: <b><?php print $data_2[$j][2]?></b><br />
	&nbsp;<?php print LANGABS12 ?> : <?php print $data_2[$j][9] ?><br>
	&nbsp;<?php print LANGDISC9 ?> : <?php print trim($data_2[$j][7]) ?><br>
	&nbsp;<?php print LANGMESS98 ?> : <?php print $data_2[$j][8]?>
	</td>
	
	</TR>

<?php
	
        }
?>
</table>
<br /><br />
<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;"  >
<TR><td colspan=3 bgcolor=#FFFFFF align=center> <?php print LANGPARENT16 ?></td></tr>
<TR>
<TD bgcolor='yellow' align=center width=10%><?php print LANGPARENT16 ?></td>
<TD bgcolor='yellow' align=center ><?php print LANGDISP2 ?></td>
</TR>
<?php
if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menututeur") ) {
	$data_2= affRetenuTotal_par_eleve($_SESSION["id_pers"]);
}

if (($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") || ($_SESSION["membre"] == "menupersonnel") ) {
	$data_2= affRetenuTotal_par_eleve($_GET["eid"]);
}


// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data_2);$j++) {
?>
        <TR  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
        <form method=POST>
	<TD align=center valign=top><?php print dateForm($data_2[$j][1])?><br><?php print LANGPARENT17 ?><br><?php print $data_2[$j][2]?>
	<br> (<?php print timeForm($data_2[$j][10]) ?>) </td>
	<TD valign=top>
	&nbsp;<?php print ucwords(LANGDISC20) ?>: <font color=red><b><?php print rechercheCategory($data_2[$j][5])?></b></font> <br />
	&nbsp;<?php print ucwords(LANGPARENT15) ?>: <b><?php print $data_2[$j][7]?></b><br />
	&nbsp;<?php print LANGPARENT18 ?> :
		<?php
		if ($data_2[$j][6] != 1 ) {
			print "<b><font color=red>".ucwords(LANGNON)."</font></b>";
		}else {
			print ucwords(LANGOUI);
		}
		?>
	<br />&nbsp;<?php print LANGABS12 ?> : <?php print $data_2[$j][12] ?>
	<br />&nbsp;<?php print LANGMESS98 ?> : <?php print $data_2[$j][11]?>
	<br />&nbsp;<?php print LANGDISC9 ?> : <?php print ucwords($data_2[$j][8])?> - <?php print LANGTE12 ?> <?php print dateForm($data_2[$j][3]) ?>
	</td>
        </form>
        </TR>
<?php } ?>

</table>
</div>

<?php // savoir etre ?>
  <div class="dhtmlgoodies_aTab" style="overflow:auto;" >
<table width='100%' border="1" style="border-collapse: collapse;" >
<tr>
<td bgcolor="yellow"><font class='T2'><?php print "Date" ?></font></td>
<td bgcolor="yellow"><font class='T2'><?php print "Ponctualité" ?></font></td>
<td bgcolor="yellow"><font class='T2'><?php print "Motivation" ?></font></td>
<td bgcolor="yellow"><font class='T2 '><?php print "Dynamisme" ?></font></td>
</tr>
<?php
$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
$dataInfo=recupSavoirEtre($id_eleve,$saisie_classe,$anneeScolaire);
for($j=0;$j<count($dataInfo);$j++) { 
	$ponct=stripslashes($dataInfo[$j][0]);
	$motiv=stripslashes($dataInfo[$j][1]);
	$dynam=stripslashes($dataInfo[$j][2]);
	$id=$dataInfo[$j][3];
	$date=dateForm($dataInfo[$j][4]);
	$motiv=preg_replace('/"/',"&quot;",$motiv);
	$dynam=preg_replace('/"/',"&quot;",$dynam);
	$ponct=preg_replace('/"/',"&quot;",$ponct); 
	print "<tr bgcolor='#FFFFFF' >";
	print "<td width='10%' valign='top' ><font class='T1'>$date</font></td>";
	print "<td width='30%' valign='top' ><font class='T2'>$ponct</font></td>";
	print "<td width='30%' valign='top' ><font class='T2'>$motiv</font></td>";			
	print "<td width='30%' valign='top' ><font class='T2'>$dynam</font></td>";
}
?>
</table>

</div>


	<div class="dhtmlgoodies_aTab"  style="overflow:auto;"  >
  		<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")|| ($_SESSION["membre"] == "menupersonnel")) {  ?>
  			<table width=100% border="1" bgcolor='#FFFFFF'  style="border-collapse: collapse;"  >
  			<?php 
			print "<tr><td bgcolor='yellow' width='5%' >&nbsp;Année&nbsp;Scolaire</td><td bgcolor='yellow'>&nbsp;Classe</td></tr>";
			$data=listingHistoClasseEleve($idEleve); // annee, classe
			for ($i=0; $i<count($data);$i++) {
				print "<tr><td>&nbsp;".$data[$i][0]."&nbsp;</td><td>&nbsp;".$data[$i][1]."</td></tr>";
			}
   			?>
  			</table>
		<?php }else{ ?>
			<br><br><center><font class='T2'><?php print LANGMESS308 ?></font></center>
		<?php } ?>
	</div>	



  	<?php //  Opérations effectuées ?>
	<div class="dhtmlgoodies_aTab"  style="overflow:auto;"  >
  		<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")|| ($_SESSION["membre"] == "menupersonnel")) {  ?>
  			<table width=100% border="1" bgcolor='#FFFFFF'  style="border-collapse: collapse;"  >
  			<?php 
			print "<tr><td bgcolor='yellow' width='5%' >&nbsp;Date</td><td bgcolor='yellow'>&nbsp;Action</td><td bgcolor='yellow'>&nbsp;Information</td></tr>";
			$data=listingHistoEleve($idEleve); // date,action,info
			for ($i=0; $i<count($data);$i++) {
				print "<tr><td>&nbsp;".dateForm($data[$i][0])."&nbsp;</td><td>&nbsp;".$data[$i][1]."</td><td>".$data[$i][2]."</td></tr>";
			}
   			?>
  			</table>
		<?php }else{ ?>
			<br><br><center><font class='T2'><?php print LANGMESS308 ?></font></center>
		<?php } ?>
	</div>



</div>




<script type="text/javascript">
initTabs('dhtmlgoodies_tabView1',Array('<?php print LANGMESS259 ?>','<?php print LANGMESS260 ?>','<?php print LANGMESS261 ?>','<?php print LANGMESS262 ?>','Savoir & Etre','<?php print LANGTMESS505 ?>','<?php print LANGMESS263 ?>'),'0','100%',840,Array(false,false,false,false,false,false,false));
initTabs('dhtmlgoodies_tabView2',Array('<?php print LANGMESS264 ?>','<?php print LANGMESS265 ?>','<?php print LANGMESS266 ?>','<?php print LANGTMESS435 ?>','Historie Stage','<?php print LANGMESS267 ?>','<?php print LANGMESS268 ?>','<?php print LANGMESS269 ?>'),'0','100%',437,Array(false,false,false,false,false,false,false));
</script> 










	<br><br>
</td></tr>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
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
<?php @Pgclose() ?>
</BODY>
</HTML>
