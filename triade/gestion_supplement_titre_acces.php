<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET
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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTMESS507 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php 
print "<form method='post' action='gestion_supplement_titre_acces.php'   name='formulaire' onsubmit=\"return valide_supp_choix('sClasseGrp','".LANGCLASSE."')\" >";
?>
<blockquote>
<BR>
<font class="T2"><?php print LANGPER25 ?> : </font><select name="sClasseGrp">
<option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_classe(); // creation des options
?>
</select> 
<br><br>
<?php
$data=recupListeSupplementAuTitre();
//libelle,fichier
print "<font class='T2'>".LANGTMESS510."</font>";
print "<select name='doc' >";
print "<option STYLE='color:#000066;background-color:#FCE4BA'>".LANGCHOIX."</option>";
for($i=0;$i<count($data);$i++) {
	$libelle=$data[$i][0];
	$fichier="./data/parametrage/".$data[$i][1];
	print "<option value=\"$fichier\" >$libelle</font>";
}
print "</select>";
?>
<br><br>
<ul><UL><UL>
<table><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","creatett"); //text,nomInput</script></td><td><script language=JavaScript>buttonMagic("<?php print LANGSTAGE73 ?>","gestion_examen.php","_parent","",""); </script></td></tr></table>
</UL></UL></UL><br><br>
</blockquote>
</form>


<?php 
if (isset($_POST["creatett"])) {

	$ficRTF=$_POST['doc'];
	$idClasse=$_POST['sClasseGrp'];

	$idsite=chercheIdSite($idClasse);
	$data=visu_paramViaIdSite($idsite);
	// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement
	$nom_etablissement=trim($data[0][0]);
	$nom_directeur=trim($data[0][6]);

	print "<hr><br />";

	$nbEtudiant=nbEleveTotal();

	$nom_classe_long=chercheClasse_nomLong($idClasse);
	$classeNom=chercheClasse_nom($idClasse);

	if (!is_dir("./data/pdf_certif/supplement_titre/")) { mkdir("./data/pdf_certif/supplement_titre/"); }
	mkdir("./data/pdf_certif/supplement_titre/".$classeNom);

	$datedujour=dateDMY();

	$eleveT=recupEleve($idClasse); // recup liste eleve
	for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
		// variable eleve
		$nomEleve=ucwords($eleveT[$j][0]);
		$prenomEleve=ucfirst($eleveT[$j][1]);
		$idEleve=$eleveT[$j][4];

		$TempFilename="$ficRTF";
		$fichier=fopen($TempFilename,"r");
		$texte=fread($fichier,filesize($TempFilename));
		fclose($fichier);	

		$dataadresse=chercheadresse($idEleve);
		$INEEleve=$dataadresse[0][9];
		$dateNaissanceEleve=dateForm($dataadresse[0][11]);


		$historyEtudiant="";
		$dataHisto=recherche_stage_historique($idEleve); //e.nom,s.nomprenomeleve,s.classeeleve,s.periodestage,s.trimestre,s.langue
		//$historyEtudiant="School / Ecole - Company / Société - Date - Service";
		$historyEtudiant="(School / Ecole - Company / Société - Date) \\par ";
		for($a=0;$a<count($dataHisto);$a++){
			$nomEt=$dataHisto[$a][0];
			$periode=$dataHisto[$a][3];
			$semestre=$dataHisto[$a][4];
			if ($semestre == 0) $semestre="";
			$Lang=$dataHisto[$a][5];
			if ($Lang == "") $Lang="French / English";
			$periode=preg_replace('/au/','-',$periode);
			//$historyEtudiant.="$nom_etablissment  - $nomEt - $periode - $nom_service";
			$historyEtudiant.="$nom_etablissement  - $nomEt - $periode \\par ";
		}

		$specification=chercherSpecificationClasse($idClasse);

		$texte=preg_replace('/NBETUDIANTS/',"$nbEtudiant",$texte); 			// => Nombre d'étudiants
		$texte=preg_replace('/HISTOETUDIANT/',"$historyEtudiant",$texte); 		// => Parcours de l'étudiant
		$texte=preg_replace('/NOMETUDIANT/',"$nomEleve",$texte); 			// => Nom de l'étudiant
		$texte=preg_replace('/PREETUDIANT/',"$prenomEleve",$texte); 			// => Prénom de l'étudiant
		$texte=preg_replace('/DATENAISETUDIANT/',"$dateNaissanceEleve",$texte); 	// => Date de naissance de l'étudiant
		$texte=preg_replace('/IDENTETUDIANT/',"$INEEleve",$texte); 			// => Code d'identification de l'étudiant
		$texte=preg_replace('/NOMETABLISSEMENT/',"$nom_etablissement",$texte); 		// => Nom de l'établissement de l'étudiant
		$texte=preg_replace('/DATEDUJOUR/',"$datedujour",$texte);	 		// => date du jour
		$texte=preg_replace('/SPECIALISATION/',"$specification",$texte); 		// => spécification de la classe
		$texte=preg_replace('/NOMDIRECTEUR/',"$nom_directeur",$texte); 			// => Nom du directeur de l'établissement
		$texte=preg_replace('/NOMCLASSELONG/',"$nom_classe_long",$texte); 		// => Nom de la classe format long 


		$langueclasse=recupLangueClasse($idClasse); 
		$NBRETUDIANTA2=nbEtudiantNiveau("A2"); 
		$NBRETUDIANTA4=nbEtudiantNiveau("A4"); 
		$NBRETUDIANTPREPA=nbEtudiantNiveau("PREPA"); 

		$NBRETUDIANTPREPAA4=$NBRETUDIANTPREPA+$NBRETUDIANTA4;

		$texte=preg_replace('/LANGUEETUDIANT/',"$langueclasse",$texte); 
		$texte=preg_replace('/NBRETUDIANTPA2/',"$NBRETUDIANTA2",$texte); 
		$texte=preg_replace('/NBRETUDIANTPA1/',"$NBRETUDIANTPREPAA4",$texte); 
		$texte=preg_replace('/NBRETUDIANTM4/',"$NBRETUDIANTA4",$texte); 
		$texte=preg_replace('/NBRETUDIANTPREPA/',"$NBRETUDIANTPREPA",$texte);

		$nomEleve=preg_replace("/'/","",$nomEleve);
		$prenomEleve=preg_replace("/'/","",$prenomEleve);
		$nomEleve=preg_replace("/ /","_",$nomEleve);
		$prenomEleve=preg_replace("/ /","_",$prenomEleve);
		$nomfic="Supplement_titre_${nomEleve}_${prenomEleve}.rtf";
		$fic="./data/pdf_certif/supplement_titre/$classeNom/$nomfic";
		$fichier=fopen("$fic","a+");
		fwrite($fichier,$texte);
		fclose($fichier);
		unset($texte);

	}

	include_once('./librairie_php/pclzip.lib.php');
	@unlink('./data/pdf_certif/supplement_titre/'.$classeNom.'.zip');
	$archive = new PclZip('./data/pdf_certif/supplement_titre/'.$classeNom.'.zip');
	$archive->create('./data/pdf_certif/supplement_titre/'.$classeNom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_certif/supplement_titre/');
	$fichier='./data/pdf_certif/supplement_titre/'.$classeNom.'.zip';
	//$bttexte="Récupérer le fichier ZIP Suppléments Titre";
	$bttexte=LANGTMESS511;
	@nettoyage_repertoire('./data/pdf_certif/supplement_titre/'.$classeNom);
	@rmdir('./data/pdf_certif/supplement_titre/'.$classeNom);


?>
	<center><input type=button onclick="open('telecharger.php?fichier=<?php print $fichier?>&fichiername=<?php print $fichier ?>','_blank','');" value="<?php print $bttexte ?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" /></center>
<?php
}
?>

<br>
<!-- // fin  -->
</td></tr></table>
<?php

Pgclose();

       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
     print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
     print "</SCRIPT>";

       endif ;
     ?>
</BODY></HTML>
