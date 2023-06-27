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
$anneeScolaire=$_POST["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+3600*24*30);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type='text/javascript' src='./librairie_js/ajax-moyenne.js'></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFB1 ?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();

$tri=$_POST["choix_trimestre"];
$idclasse=$_POST["sClasseGrp"];

$listTmp=explode(":",$idclasse);
unset($HPV[cgrp]);
$idclasse=$listTmp[0];
$HPV[gid]=$listTmp[1];
unset($listTmp);

if (isset($_POST["valide"])) {
	$saisie_text=$_POST["saisie_text"];
	$saisie_matiere=$_POST["saisie_matiere"];
	$tri=$_POST["saisie_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$nb=$_POST["nb"];

	$listTmp=explode(":",$idclasse);
	unset($HPV[cgrp]);
	$idclasse=$listTmp[0];
	$HPV[gid]=$listTmp[1];
	unset($listTmp);

	for($i=0;$i<$nb;$i++) {
		$value=$_POST["saisie_text_$i"];
		$saisie_matiere=$_POST["saisie_matiere_$i"];
		enr_commentaire_classe($value,$saisie_matiere,$tri,$idclasse,$anneeScolaire);
	}
	$message="<br><center><font class='T2' id='color2' >".LANGABS28."</font></center>";
}

/***************************************************************************/


// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
       	 $dateFin=$dateRecup[$j][1];
}
$dateDebutT1=dateForm($dateDebut);
$dateFinT1=dateForm($dateFin);
//-----/
$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
       	 $dateDebut=$dateRecup[$j][0];
       	$dateFin=$dateRecup[$j][1];	
}
$dateDebutT2=dateForm($dateDebut);
$dateFinT2=dateForm($dateFin);
//-----/
$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
       	$dateDebut=$dateRecup[$j][0];
        $dateFin=$dateRecup[$j][1];
}
$dateDebutT3=dateForm($dateDebut);
$dateFinT3=dateForm($dateFin);
//-----/

?>
<?php
print $message 
?>
<br>
<ul><font class='T2'>Trimestre / Semestre : <?php print preg_replace('/trimestre/','',$tri); ?>
<?php print " - ".LANGBULL3." : $anneeScolaire" ?>
</font>
</ul>
<br>
<table border=0>
<td valign="top"><font class=T2>Moy. classe : </font></td>
<td><font class=T2><b><div id="m1"></div></b> (Premier Trimestre)</font></td>
<td><font class=T2><b><div id="m2"></div></b> (Deuxième Trimestre)</font></td>
<td><font class=T2><b><div id="m3"></div></b> (Troisième Trimestre)</font></td>
</tr></table>

<table>
<tr><td valign=top  ><br>
<form method=post name="form" >
<table border='1' >
<?php

include_once('librairie_php/recupnoteperiode.php');



$ordre=ordre_matiere_visubull($idclasse,$anneeScolaire);
$idEleve=$ideleve;
$idClasse=$idclasse;

for($i=0;$i<count($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$nomprof=recherche_personne($ordre[$i][1]);
	$idMatiere=$ordre[$i][0];
	// mise en place du nom du prof
        $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
        $profAff=recherche_personne($ordre[$i][1]);

	if (verifsousmatierebull($idMatiere)) { continue; } // verif pour l'eleve de l'affichage de la matiere

	// mise en place des matieres
	print "<tr>";
	print "<td bordercolor='#cccccc' valign=top ><font size=2><input type=text readonly value='".trunchaine(strtoupper($matiere),50)."' size=40 title=\"$matiere\"></font>";
	print "<br><i><font size=1> ".trunchaine(trim($profAff),50)." </font></i></td>";	

	$commentaire=cherche_com_classe_matiere($idMatiere,$tri,$idclasse,$anneeScolaire);
	$commentaire=preg_replace('/"/',"&rdquo;",$commentaire);

	print "<td align=left bgcolor='#FFFFFF'>";
	print "<input type=hidden name='saisie_matiere_$i' value='$idMatiere' >";
	
	if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
	if ($typecom > 0) { $nbcar=150; }
	print "<input type='text' name='CharRestant_$i' size='2' disabled='disabled'> ($nbcar caractères maximum)<br>";
	$disabled="";
	if ($idprof != $_SESSION["id_pers"]) $disabled="disabled='disabled'";
	print "<textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" cols='48' rows='5' name='saisie_text_$i' $disabled >$commentaire</textarea></td>";
	print "</tr>";
	
}

?>
</table>
<br><br>
<!-- 
<input type='hidden' name="saisie_classe" value="<?php print $idclasse?>" />
<input type='hidden' name="anneeScolaire" value="<?php print $anneeScolaire?>" />
<input type='hidden' name="saisie_trimestre" value="<?php print $tri?>" />
<input type='hidden' name="nb" value="<?php print count($ordre) ?>" />
-->
<table><tr><td>&nbsp;&nbsp;<!-- <input type=submit value="Enregistrer" class="bouton2" name="valide" onclick="this.value='Veuillez patientez'"> -->
</td><td><script  language="JavaScript" >buttonMagicRetour('editer_bulletin.php','_self')</script></td></tr></table>

</form>
</tr></td>

</td></tr></table>
<img src="image/commun/indicator.gif" style="visibility:hidden" />
<?php Pgclose(); ?>
<script>RecupMoyenne('<?php print "trimestre1" ?>','<?php print $idclasse?>','m1','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre2"?>','<?php print $idclasse?>','m2','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre3"?>','<?php print $idclasse?>','m3','<?php print $anneeScolaire ?>')</script>
<?php 
if ($okenr == 1) {
	alertJs(LANGDONENR);
}
?>
<br><br>
     <!-- // fin  -->
     </td></tr></table>
</BODY>
</HTML>
