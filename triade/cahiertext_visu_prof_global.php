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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);


// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

if (isset($_GET["saisie_idprof"])) { 
	$idprof=$_GET["saisie_idprof"]; 
}


if (isset($_GET["id"])) {
	$idprof=$_GET["id"];
}

if (isset($_POST["saisie_idprof"])) {
	$idprof=$_POST["saisie_idprof"];
}

$nomprof=recherche_personne_nom($idprof,'ENS');
$prenomprof=recherche_personne_prenom($idprof,'ENS');

?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="100%">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print LANGPROF37 ?> - </b><font id="color2"><?php print strtoupper($nomprof)." ".ucwords($prenomprof) ?></font></td></tr>
<tr >
<td valign='top'>
<!-- // fin  -->
<table width='100%' border='0' >
<ul>
<?php
$date=dateDMY();
if (isset($_GET["iddate"])) {
	$date=dateForm($_GET["iddate"]);
}
if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
}
?>
<tr><td colspan=2>
<form method=post name="formulaire" action="cahiertext_visu_prof_global.php">
<table border=0>
<tr><td>
<?php print LANGPROFN ?> <input type=text value="<?php print $date ?>" name=saisie_date size=10 class=bouton2>
<?php
include_once("librairie_php/calendar.php");
calendar('id1','document.formulaire.saisie_date',$_SESSION["langue"],"0");
?>
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print "Devoir à faire" ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicSubmit("<?php print "Contenu des cours" ?>","contenu"); //text,nomInput</script>
<script language=JavaScript>buttonMagicSubmit("<?php print "Objecif des cours" ?>","objectif"); //text,nomInput</script>
<script language=JavaScript>buttonMagicImprimer(); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
<input type="hidden" name="saisie_idprof" value="<?php print $idprof?>" />
</form>
</ul>

<?php
$nb=4; // nombre de jour à afficher
print "<table border=0    align='center' height='100%' >";
print "<tr width=200 >";
for($i=0;$i<=$nb;$i++) {
	$date2=dateplusn($date,$i);
	print "<td>&nbsp;&nbsp;&nbsp; ".dateform($date2)."</td>";
}
print "</tr><tr>";
$devoir=0;

if (isset($_POST["contenu"])) {
	$devoirvisu=0;	
}
if (isset($_POST["objectif"])) {
	$devoirvisu=2;	
}
if (isset($_POST["create"])) {
	$devoirvisu=1;	
}
if (isset($_GET["devoirvisu"])) {
	$devoirvisu=$_GET["devoirvisu"];
}

$idprof2=verif_si_suppleant($idprof);
for($i=0;$i<=$nb;$i++) {
	$date2=dateplusn($date,$i);
	$date2=dateForm($date2);
	if ($devoirvisu == 0) {
		$data=affcontenuScolaireProf($idprof2,$date2,"date_contenu");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, contenu, classorgrp, id, number, fichier,idprof,
		$sujet="Contenu des cours";
		$devoirvisu=0;
	}else if ($devoirvisu == 2) {
		$data=affobjectifScolaireProf($idprof2,$date2,"date_contenu");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, objectif, classorgrp, id, number, fichier,idprof
		$sujet="Objectif des cours";
		$devoirvisu=2;
	}else{
		$data=affdevoirScolaireProf($idprof2,$date2,"date_devoir");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, fichier,idprof,tempsestimedevoir
		$sujet="Devoir à faire";
		$devoirvisu=1;
		$devoir=1;
	}
	print "<td valign=top width=5><br>";
	print "<div style=\"width:190; height:30;  border:solid 0px black;\">";
	print "<img src='image/commun/on1.gif' align=center width=8 height=8> <b><u>$sujet</u> :</b><br>";
	print "</div>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		$tempsestime=$data[$j][11];
		$cumultempsestime+=conv_en_seconde($data[$j][11]);
		if (($tempsestime != "00:00:00") && ($devoirvisu==1) && (trim($tempsestime) != "") ) {
			$tempsestime="<br /><font class='T1'>Temps de travail estimé à ".timeForm($tempsestime)."</font>";
		}
		if (isset($_POST["contenu"])) {
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}else{
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}

		$number=$data[$j][8];
		if ($bgcolor == "#CCCCCC") {
			$bgcolor="#F1CFCF";
		}else{
			$bgcolor="#CCCCCC";
		}

		$lienFichier="";
		if (trim($number)  != "" ) {
			$fichier=$data[$j][9];	
			$lienFichier="<img src='image/stockage/defaut.gif' align='center'> Fichier : <a href='telecharger.php?fichier=data/DevoirScolaire/${number}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,10)."</a>";
		}
		print "<div style=\"width:190; height:150; overflow:auto; border:solid 1px black;background-color:$bgcolor \"> ";
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		print "$tempsestime";
	        print "<br><font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".$contenu;
		print "$lienFichier";
		print "</div>";
	}
	if ($devoir == 1)
	print "<br><div style='width:190; height:30;overflow:auto; border:solid 1px black;' id='coulBar0' ><font class='T1'  id='menumodule1' >&nbsp;Temps de travail total estimé : <br>&nbsp;".timeForm(calcul_hours($cumultempsestime))."</font></div>";
	print "</td>";
}
print "</tr>";
?>
</table>
<br>
<?php
$nb=$nb + 1;
$dateS=datesuivante_nb($date,$nb);
$dateP=dateprecedent_nb($date,$nb);
?>
<table border='0' width='100%' align='center' >
<tr><td align=left>
&nbsp;&nbsp;<input type=button value="<-- <?php print LANGPROF35 ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="open('cahiertext_visu_prof_global.php?iddate=<?php print $dateP ?>&id=<?php print $idprof?>&devoirvisu=<?php print $devoirvisu?>','devoir','')" >
</td>
<td align=right>
&nbsp;&nbsp;
<input type=button value="<?php print LANGPROF36 ?> --> "  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="open('cahiertext_visu_prof_global.php?iddate=<?php print $dateS ?>&id=<?php print $idprof?>&devoirvisu=<?php print $devoirvisu?>','devoir','')" >
</td></tr>
</table>
</td></tr></table>
</BODY>
</HTML>
<?php @Pgclose() ?>
