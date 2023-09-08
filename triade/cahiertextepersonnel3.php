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

if (isset($_GET["saisie_classe"])) { 
	$idclasse=$_GET["saisie_classe"]; 
}

if (isset($_GET["idmat"])) {
	$idmatiere=$_GET["idmat"];
	$nommatiere=chercheMatiereNom($idmatiere);
}


if (isset($_POST["idmat"])) {
	$idmatiere=$_POST["idmat"];
	$nommatiere=chercheMatiereNom($idmatiere);
}

if (isset($_GET["id"])) {
	$idclasse=$_GET["id"];
}

if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
}


if (isset($_GET["sClasseGrp"])) {
	$sClasseGrp=$_GET["sClasseGrp"];
	$idmatiere=$_GET["sMat"];
}

if (isset($_POST["sClasseGrp"])) {
	$sClasseGrp=$_POST["sClasseGrp"];
	$idmatiere=$_POST["sMat"];
}

$nommatiere=chercheMatiereNom($idmatiere);
$listTmp=explode(":",$sClasseGrp);
$HPV[cid]=$listTmp[0];
$HPV[gid]=$listTmp[1];
$list2=$listTmp[1];
$list1=$listTmp[0];
unset($listTmp);
if ($idclasse == "") $idclasse=$list1; 
$nomclasse=chercheClasse($idclasse);

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
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_visadirec.js"></script>
<script type="text/javascript" src="./FCKeditor/fckeditor.js"></script>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="100%">
	<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><font class='T2'><?php print LANGPROF37 ?> - </b><?php print ucwords($nomclasse[0][1])?> - <?php print $nommatiere ?> <span id='plagedate'></span></font></td></tr>
<tr >
<td valign='top'>
<!-- // fin  -->
<?php
$date=dateDMY();
if (isset($_GET["iddate"])) {
	$date=dateForm($_GET["iddate"]);
}
if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
}
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
?>
<table width='100%' border='0' >
<ul>
<tr><td colspan=2>
<form method=post name="formulaire" action="cahiertextepersonnel3.php">
<table border=0>
<tr><td>
<?php print "Du" ?> <input type=text value="<?php print $date ?>" name=saisie_date size=10 class=bouton2>
<?php
include_once("librairie_php/calendar.php");
calendar('id1','document.formulaire.saisie_date',$_SESSION["langue"],"0");
?>
</td><td>
<td>
<?php print au ?> <input type=text value="<?php print $datefin ?>" name=saisie_date_fin size=10 class=bouton2>
<?php
calendar('id2','document.formulaire.saisie_date_fin',$_SESSION["langue"],"0");
?>
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print "Visualiser" ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicImprimer(); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
<input type="hidden" name="saisie_classe" value="<?php print $idclasse?>" />
<input type="hidden" name="idmat" value="<?php print $idmatiere?>" />
</form>
</ul>

<?php 
$hauteur=240;
if ($_SESSION["navigateur"] == "NONIE") { $hauteur=340; }
?>

<?php
$nb=4; // nombre de jour à afficher
if (isset($_POST["saisie_date_fin"])) {
	include_once("librairie_php/timezone.php");
	$date_fin=dateFormBase($_POST["saisie_date_fin"]);
	$date_debut=dateFormBase($_POST["saisie_date"]);
	$nb=nbjours_entre_2_date($date_fin,$date_debut);
}

print "<table border=1  width=100%   align='center' height='100%' bordercolor='black' cellspanding=0 cellspacing=0 >";

print "<tr >";
print "<td width='5%'  align='center'><font class=T2><b>Date</b></font></td>";
print "<td align='center'><font class=T2><b>Contenu des cours</b></font></td>";
print "<td align='center'><font class=T2><b>Objectifs des cours</b></font></td>";
print "<td align='center'><font class=T2><b>Devoir à faire</b></font></td>";
print "</tr>";

$dateDebut=dateform($date);
for($i=0;$i<=$nb;$i++) {
	$date2=dateplusn($date,$i);
	print "<tr><td valign='top' >&nbsp;&nbsp;<font class=T2>".dateform($date2)."</font>&nbsp;</td>";
	$date2=dateplusn($date,$i);
	$date2=dateForm($date2);
	$dateFin=dateform($date2);
	$data=affcontenuScolaireParent($idclasse,$date2,"date_contenu");
	// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, contenu, classorgrp, id, number, fichier,idprof,
	$devoirvisu=0;

	print "<td valign=top width=33%>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		if (($data[$j][1] != "$idmatiere") && ($idmatiere != "tous")) { continue; }
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
		$id=$data[$j][7];
		if (trim($number) != "" ) {
			$fichier=$data[$j][9];	
			$lienFichier="<br><img src='image/stockage/defaut.gif' align='center'> Fichier : <a href='telecharger.php?fichier=data/DevoirScolaire/${number}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,10)."</a>";
		}
		print "<div style=\"width:100%; overflow:auto; border:solid 1px black;background-color:$bgcolor \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			if ($devoirvisu == "0") $verifedit=verifEditeContenu($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "1") $verifedit=verifEditeObjectif($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "2") $verifedit=verifEditeDevoir($id,$_SESSION["id_pers"]);
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
	        print "<br><font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".$contenu;
		print "$lienFichier";
		print "</div>";
	}
	print "</td>";


	$data=affobjectifScolaireParent($idclasse,$date2,"date_contenu");
	// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, objectif, classorgrp, id, number, fichier,idprof
	$devoirvisu=2;
	print "<td valign=top  width=33%>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		if (($data[$j][1] != "$idmatiere") && ($idmatiere != "tous")) { continue; }
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
		$id=$data[$j][7];
		if (trim($number) != "" ) {
			$fichier=$data[$j][9];	
			$lienFichier="<br><img src='image/stockage/defaut.gif' align='center'> Fichier : <a href='telecharger.php?fichier=data/DevoirScolaire/${number}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,10)."</a>";
		}
		print "<div style=\" overflow:auto; border:solid 1px black;background-color:$bgcolor \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			if ($devoirvisu == "0") $verifedit=verifEditeContenu($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "1") $verifedit=verifEditeObjectif($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "2") $verifedit=verifEditeDevoir($id,$_SESSION["id_pers"]);
		
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
	        print "<br><font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".$contenu;
		print "$lienFichier";
		print "</div>";
	}
	print "</td>";


	$data=affdevoirScolaireParent($idclasse,$date2,"date_devoir");
	// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, fichier,idprof,tempsestimedevoir
	$devoirvisu=1;

	
	print "<td valign=top  width=33%>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		if (($data[$j][1] != "$idmatiere") && ($idmatiere != "tous")) { continue; }	
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
		$id=$data[$j][7];
		if (trim($number) != "" ) {
			$fichier=$data[$j][9];	
			$lienFichier="<br><img src='image/stockage/defaut.gif' align='center'> Fichier : <a href='telecharger.php?fichier=data/DevoirScolaire/${number}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,10)."</a>";
		}
		print "<div style=\" overflow:auto; border:solid 1px black;background-color:$bgcolor \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			if ($devoirvisu == "0") $verifedit=verifEditeContenu($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "1") $verifedit=verifEditeObjectif($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "2") $verifedit=verifEditeDevoir($id,$_SESSION["id_pers"]);
			
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
	        print "<br><font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".$contenu;
		print "$lienFichier";
		print "</div>";
	}
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
&nbsp;&nbsp;<input type=button value="<-- <?php print LANGPROF35 ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="open('cahiertextepersonnel3.php?iddate=<?php print $dateP ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>&idmat=<?php print $idmatiere ?>','_self','')" >
</td>
<td align=right>
&nbsp;&nbsp;
<input type=button value="<?php print LANGPROF36 ?> --> "  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="open('cahiertextepersonnel3.php?iddate=<?php print $dateS ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>&idmat=<?php print $idmatiere ?>','_self','')" >
</td></tr>
</table>
</td></tr></table>
<script>
document.getElementById("plagedate").innerHTML=" du <?php print $dateDebut ?> au <?php print $dateFin ?>";
document.formulaire.saisie_date_fin.value="<?php print $dateFin ?>";
</script>
</BODY>
</HTML>
<?php @Pgclose() ?>
