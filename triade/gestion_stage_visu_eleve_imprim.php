<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body bgcolor="#FFFFFF" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
$cnx=cnx();
$idnumstage=$_POST["idstage"];
$idclasse=$_POST["saisie_classe"];

$numstage=rechercheNumStage($idnumstage);
$classe=chercheClasse_nom($idclasse);
$periode=recherchedatestage2($idnumstage,$idclasse);
?>

<table border=1 width=100% style='border-collapse: collapse;' >
<tr><td colspan='9' align=center bgcolor="#CCCCCC"  ><b>Stage <?php print $numstage ?>  de la classe <?php print $classe ?> du <?php print $periode ?></b></td></tr>
<tr bordercolor="#000000">
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Nom&nbsp;Prénom</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Nom&nbsp;de&nbsp;l'entreprise&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Nom&nbsp;du&nbsp;Responsable&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Lieu du stage&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Ville&nbsp;du&nbsp;stage&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Tuteur&nbsp;de&nbsp;stage&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Tél.&nbsp;Tuteur&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Enseignant&nbsp;Visiteur&nbsp;1&nbsp;</b></td>
	<td bgcolor="#CCCCCC" align=center width=5% ><b>&nbsp;Enseignant&nbsp;Visiteur&nbsp;2&nbsp;</b></td>
</tr>
<?php
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
for($i=0;$i<count($data);$i++) {
	$ideleve=$data[$i][1];
	$data2=rechercheEntreStageElecomplet($ideleve,$idnumstage) ;
	// id_eleve,id_entreprise,num_stage,lieu_stage,ville_stage,tuteur_stage,id_prof_visite,date_visite_prof,tel,date_visite_prof2,id_prof_visite2,compte_tuteur_stage,info_plus,dateDebutAlternance,dateFinAlternance
	for($j=0;$j<count($data2);$j++) {

		$entreprise="&nbsp;";
		$lieu="&nbsp;";
		$ville="&nbsp;";
		$responsable="&nbsp;";
		$prof="&nbsp;";
		$dateprof="&nbsp;";
		$dateDebutS=dateForm($data2[$j][13]);
		$dateFinS=dateForm($data2[$j][14]);

		//id_eleve,id_entreprise,num_stage,lieu_stage,ville_stage,tuteur_stage,id_prof_visite,date_visite_prof,tel,date_visite_prof2,id_prof_visite2,compte_tuteur_stage
		$prenom=recherche_eleve_prenom($ideleve);
		$nom=recherche_eleve_nom($ideleve);		
		$entreprise=recherche_entr_nom_via_id($data2[$j][1]);
		$tuteur=recherche_personne_nom($data2[$j][11])." ".recherche_personne_prenom($data2[$j][11]);

		$lieu=$data2[$j][3];
		$ville=strtolower($data2[$j][4]);
		$info_plus=$data2[$j][12];
		$responsable=$data2[$j][5];
		if ($data2[$j][6] == "") {
			$prof="&nbsp;";
		}else {
			$prof=recherche_personne($data2[$j][6]);
		}
		if ($data2[$j][9] == "") {
			$prof2="&nbsp;";
		}else {
			$prof2=recherche_personne($data2[$j][10]);
		}
		$tel=$data2[$j][8];
		$dateprof=$dateprof2="&nbsp;";
		if ($data2[$j][7]) { $dateprof=dateForm($data2[$j][7]); }
		if ($data2[$j][9]) { $dateprof2=dateForm($data2[$j][9]); }
		if (trim($dateprof) == "00/00/0000") {$dateprof="&nbsp;";}
		if (trim($dateprof2) == "00/00/0000") {$dateprof2="&nbsp;";}
		if (trim($prof) == "M.") { $prof="&nbsp;";}
		if (trim($prof2) == "M.") { $prof2="&nbsp;";}
		$prof=preg_replace("/ /","&nbsp;",$prof);
		$prof2=preg_replace("/ /","&nbsp;",$prof2);
		$responsable=preg_replace("/ /","&nbsp;",$responsable);
		$nom=strtoupper($nom);
		$nom=preg_replace("/ /","&nbsp;",$nom);
		$tel=preg_replace("/\//",".",$tel);
		$entreprise=preg_replace("/ /","&nbsp;",$entreprise);

		$T1="";$T2="";
		if ($prof != "") {
			$T1="&nbsp;$prof<br>&nbsp;le&nbsp;$dateprof";
		}
		if ($prof2 != "") {
			$T2="&nbsp;$prof2<br>&nbsp;le&nbsp;$dateprof2";
		}

?>
		<tr bordercolor="#000000">
		<td>&nbsp;<?php print $nom ?>&nbsp;<?php print trunchaine(ucwords($prenom),10) ?></td>
		<td>&nbsp;<?php print $entreprise ?></td>
		<td>&nbsp;<?php print $responsable ?></td>
		<td>&nbsp;<?php print $lieu ?></td>
		<td>&nbsp;<?php print $ville ?></td>
		<td>&nbsp;<?php print $tuteur ?></td>
		<td>&nbsp;<?php print $tel ?></td>
		<td><?php print $T1 ?></td>
		<td><?php print $T2 ?></td>
		</tr>
		<?php
		$infocompterendu=listingContreRenduStage($ideleve,$data2[$j][1]);
		//id,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,saisiele
		//
?>
		<tr bordercolor="#000000"  ><td valign='top' colspan='9' >
		<?php 
		if (strtolower($idnumstage) == "alternance") {
			print "En alternance du $dateDebutS au $dateFinS <br/>";
		}
		?>
		Rapport de visite du <?php print dateForm($infocompterendu[0][2])." à ".timeForm($infocompterendu[0][3])." : ".$infocompterendu[0][5] ?><br /><i>Information : <?php print $info_plus ?></i></td></tr>


		
<?php
	}
}
?>

</table>
<?php
// deconnexion en fin de fichier 
Pgclose();
?>
</BODY>
</HTML>
