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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Tableau de statistique" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br><br>
<!-- // fin  -->
<?php
$dateDebut=$_POST["saisie_date_debut"];
$dateFin=$_POST["saisie_date_fin"];
$choix=$_POST["saisie_nature"];

	require_once "./librairie_php/class.writeexcel_workbook.inc.php";
	require_once "./librairie_php/class.writeexcel_worksheet.inc.php";

	$fichier="./data/fichier_ASCII/statistique.xls";
	@unlink($fichier);
	
	$workbook = new writeexcel_workbook($fichier);

	
	$header = $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('blue');

	$center = $workbook->addformat();
	$center->set_align('left');

	$worksheet1 = $workbook->addworksheet('Matière');
	$worksheet1->write(0, 0, "Matière", $header);
//	$worksheet1->write(0, 1, "Nb heure programmé", $header);
	$worksheet1->write(0, 1, "Nb heure réalisé", $header);
//	$worksheet1->write(0, 3, "Coût programmé", $header);
	$worksheet1->write(0, 2, "Coût réalisé", $header);

	$worksheet2 = $workbook->addworksheet('Classe');
	$worksheet2->write(0, 0, "Classe", $header);
//	$worksheet2->write(0, 1, "Nb heure programmé", $header);
	$worksheet2->write(0, 1, "Nb heure réalisé", $header);
//	$worksheet2->write(0, 3, "Coût programmé", $header);
	$worksheet2->write(0, 2, "Coût réalisé", $header);

	$worksheet3 = $workbook->addworksheet('Enseignant');
	$worksheet3->write(0, 0, "Enseignant", $header);
//	$worksheet3->write(0, 1, "Nb heure programmé", $header);
	$worksheet3->write(0, 1, "Nb heure réalisé", $header);
//	$worksheet3->write(0, 3, "Coût programmé", $header);
	$worksheet3->write(0, 2, "Coût réalisé", $header);

	$data=affMatiere();
	$j=1;
	for($i=0;$i<count($data);$i++) {
		$matiere=$data[$i][1]; //code_mat,libelle,sous_matiere,offline
		$sousmatiere=$data[$i][2]; //code_mat,libelle,sous_matiere,offline
		$idmatiere=$data[$i][0];
		$Nb_heure_programme=nbHeureProgrammeParMatiere($idmatiere);
		if ($Nb_heure_programme == "") { $Nb_heure_programme=0; }
		$Nb_heure_realise=nbHeureVacationParIdMatiere($idmatiere,$dateDebut,$dateFin);
		$tab_type_prestation=recupTypePrestationViaIdMatiere($idmatiere);
		$Cout_programme=0;
		for($h=0;$h<count($tab_type_prestation);$h++) {
			$taux=affTauxViaId($tab_type_prestation[$h][0]); //taux
			$nbheure=recupNbHeureCommandeViaIdMatiereAndTypePrestation($tab_type_prestation[$h][0],$idmatiere);
			if ($nbheure > 0) { $Cout_programme+=$taux * $nbheure; }
		}

		if (empty($Nb_heure_realise)) continue; 

		$tabtmp=recupNbHeureEffectueViaIdMatiere($idmatiere,$dateDebut,$dateFin); //duree,prestation
		$Cout_realise=0;
		for($k=0;$k<count($tabtmp);$k++) {
			$taux=affTauxViaId($tabtmp[$k][1]); 
			list($nbheure,$nbmin,$nbsec)=preg_split('/:/',$tabtmp[$k][0]);
			$Cout_realise+=$taux * preg_replace('/^0/','',$nbheure);
		}
		$sousmatiere=preg_replace('/0$/','',$sousmatiere);
		$worksheet1->write($j, 0, "$matiere $sousmatiere", $center);
		$worksheet1->write($j, 1, "$Nb_heure_realise", $center);
		$worksheet1->write($j, 2, "$Cout_realise", $center);
/*		$worksheet1->write($j, 1, "$Nb_heure_programme", $center);
		$worksheet1->write($j, 2, "$Nb_heure_realise", $center);
		$worksheet1->write($j, 3, "$Cout_programme", $center);
		$worksheet1->write($j, 4, "$Cout_realise", $center);
*/
		$j++;
	}

	unset($Nb_heure_realise);
	$data=affClasse(); //code_class,libelle,desclong
	$j=1;
	for($i=0;$i<count($data);$i++) {
		$classe=$data[$i][1];
		$idclasse=$data[$i][0];
		$Nb_heure_programme=nbHeureProgrammeParClasse($idclasse);
		$Nb_heure_realise=nbHeureVacationParIdClasse($idclasse,$dateDebut,$dateFin);
		$tab_type_prestation=recupTypePrestationViaIdClasse($idclasse);
		$Cout_programme=0;
		for($h=0;$h<count($tab_type_prestation);$h++) {
			$taux=affTauxViaId($tab_type_prestation[$h][0]); //taux
			$nbheure=recupNbHeureCommandeViaIdClasseAndTypePrestation($tab_type_prestation[$h][0],$idclasse);
			if ($nbheure > 0) { $Cout_programme+=$taux * $nbheure; }
		}
		$tabtmp=recupNbHeureEffectueViaIdClasse($idclasse,$dateDebut,$dateFin); //duree,prestation
		$Cout_realise=0;
		for($k=0;$k<count($tabtmp);$k++) {
			$taux=affTauxViaId($tabtmp[$k][1]); 
			list($nbheure,$nbmin,$nbsec)=preg_split('/:/',$tabtmp[$k][0]);
			$Cout_realise+=$taux * preg_replace('/^0/','',$nbheure);
		}

		if (($idclasse > 0) && ($Nb_heure_realise != '')) {
			$worksheet2->write($j, 0, "$classe", $center);
//			$worksheet2->write($j, 1, "$Nb_heure_programme", $center);
			$worksheet2->write($j, 1, "$Nb_heure_realise", $center);
//			$worksheet2->write($j, 3, "$Cout_programme", $center);
			$worksheet2->write($j, 2, "$Cout_realise",$center);
			$j++;
		}
	}


	unset($Nb_heure_realise);
	$data=affPersActif('ENS'); //pers_id, civ, nom, prenom, identifiant, offline, email
	$j=1;
	for($i=0;$i<count($data);$i++) {
		$nomprenom=ucwords($data[$i][2])." ".ucwords($data[$i][3]);
		$idpers=$data[$i][0];
		$Nb_heure_programme=nbHeureProgrammeParIdPers($idpers);
		$Nb_heure_realise=nbHeureVacationParIdPers($idpers,$dateDebut,$dateFin);
		$tab_type_prestation=recupTypePrestationViaIdPers($idpers);
		$Cout_programme=0;
		for($h=0;$h<count($tab_type_prestation);$h++) {
			$taux=affTauxViaId($tab_type_prestation[$h][0]); //taux
			$nbheure=recupNbHeureCommandeViaIdPersAndTypePrestation($tab_type_prestation[$h][0],$idpers);
			if ($nbheure > 0) { $Cout_programme+=$taux * $nbheure; }
		}
		$tabtmp=recupNbHeureEffectueViaIdPers($idpers,$dateDebut,$dateFin); //duree,prestation
		$Cout_realise=0;
		for($k=0;$k<count($tabtmp);$k++) {
			$taux=affTauxViaId($tabtmp[$k][1]); 
			list($nbheure,$nbmin,$nbsec)=preg_split('/:/',$tabtmp[$k][0]);
			$Cout_realise+=$taux * preg_replace('/^0/','',$nbheure);
		}


		if (($idpers > 0) && ($Nb_heure_realise != ''))  {
			$worksheet3->write($j, 0, "$nomprenom", $center);
//			$worksheet3->write($j, 1, "$Nb_heure_programme ", $center);
			$worksheet3->write($j, 1, "$Nb_heure_realise", $center);
//			$worksheet3->write($j, 3, "$Cout_programme", $center);
			$worksheet3->write($j, 2, "$Cout_realise",$center);
			$j++;
		}
	}
	$workbook->close();
?>
<br>
<center>
<input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "R&eacute;cup&eacute;ration des statistiques" ?>"  class="bouton2">
<br /></center><br><br>
<table align='center'><tr><td><script>buttonMagicRetour2('gestion_statistique.php','_self','Retour')</script></td></tr></table>
<br><br>

     <!-- // fin  -->
     </td></tr></table>
     <?php
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
