<?php
session_start();
//error_reporting(0);
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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
include_once("./librairie_php/lib_attente.php");
validerequete("2");

$taille=2000000;
$taille2="2Mo";

include_once("librairie_php/lib_get_init.php");
include_once("common/config6.inc.php");

if (MAXUPLOAD == "oui") {
	$id=php_ini_get("safe_mode");
	if ($id != 1) {
		set_time_limit(0); // en secondes
		$taille=8000000;
		$taille2="8Mo";
	}
}

if (php_module_load("SimpleXML") != 1) {
	$erreurXML="<font class='T2' color='red'>". LANGEDT10bis." </font> ";
	$disabled="disabled='diseabled'";

}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGEDT2 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<form method="post" name="formulaire" ENCTYPE="multipart/form-data"  >
<ul>
<table>
<tr><td><font class="T2"> <?php print LANGCARNET64 ?> :</font></td>
    <td><input type=file name="fichier"  > <A href='#' onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<font face=Verdana size=1><B><font color=red><?php print LANGEDT1?></font></B><?php print LANGEDT1bis." <b>$taille2</b> . </font>" ?> '); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A> </td>
</tr>
<tr><td colspan="2"><br /><font class=T2><?php print LANGCARNET65 ?></font> <input type=checkbox name="supp" value="oui"> (<?php print LANGOUI ?>) <br /><br /></td></tr>
<tr><td colspan="2"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGAGENDA86 ?>","create","<?php print $disabled ?> onclick='AfficheAttente()' "); //text,nomInput</script></td></tr>
</table>
<br>
<?php print $erreurXML ?>

</ul>
</form>
<!-- // fin  -->

<?php
if (isset($_POST["create"])) {
	$fichier=$_FILES['fichier']['name'];
	$type=strtolower($_FILES['fichier']['type']);
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	//alertJs($type);

	if ( (!empty($fichier)) &&  ($size <= $taille) &&  ((trim($type) == "text/xml") || (preg_match('/zip/i',$type) ) )) {

		
		@unlink("data/fichier_ASCII/edt.xml");
		if (preg_match('/zip/i',$type)) {
			move_uploaded_file($tmp_name,"edt.zip");
			include_once('./librairie_php/pclzip.lib.php');
			$archive = new PclZip('edt.zip');
			if ($archive->extract(PCLZIP_OPT_PATH, 'data/patch/') == 0) {
			die(print "<a href='javascript:history.go(0)'><b>Cliquez ici pour réactualiser le patch</a></b>"); }

			if ($dh = @opendir("data/patch/")) {
       				while (($file = readdir($dh)) !== false) {
           				if (($file != '.') && ($file != '..')) {
                  				if (preg_match('/xml$/i',strtolower($file))) {
							copy("data/patch/$file","./data/fichier_ASCII/edt.xml");
							unlink("data/patch/$file");
							break;
						}
					}
				}
			}
			closedir($dh);  		
			@unlink("edt.zip");
		}else{
			move_uploaded_file($tmp_name,"data/fichier_ASCII/edt.xml");
		}


		// traitement du fichier XML

		/* Ouverture du fichier en lecture seule */
		$handle = fopen('data/fichier_ASCII/edt.xml', 'r');
		@unlink("data/fichier_ASCII/edtF.xml");
		$fd = fopen('data/fichier_ASCII/edtF.xml', 'w');
		/* Si on a réussi à ouvrir le fichier */
		if ($handle){	/* Tant que l'on est pas à la fin du fichier */	
			while (!feof($handle))	{		
				/* On lit la ligne courante */		
				$ligne = fgets($handle);		
				/* On l'affiche */		
				$ligne=preg_replace('/&pos;/',"'",$ligne);
				fwrite($fd,"$ligne");	
			}	/* On ferme le fichier */	
			fclose($handle);
		}	
		fclose($fd);

?>
		</td></tr></table>
		<br /><br />
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
		<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGEDT2 ?></font></b></td></tr>
		<tr id='cadreCentral0' >
		<td >
<?php
		$cnx=cnx();

		//&pos;


		$xml = simplexml_load_file("./data/fichier_ASCII/edtF.xml");
		
/*		 $result = $xml->xpath('/DATA_VISUAL_TIMETABLING/LES_PROFESSEURS/UN_PROFESSEUR/NOM'); 
		
		 foreach ($result as $valeur) {
			print $valeur."<br>"; 
		 }
 */		 

		foreach ($xml->LES_PROFESSEURS->UN_PROFESSEUR as $LES_PROFESSEURS) {
		/*	
			print $LES_PROFESSEURS->CODE;
			print ":";
			print $LES_PROFESSEURS->NOM;
			print ":";
			print $LES_PROFESSEURS->PRENOM;
			print "<br>";
		 */
	
			$idprof=chercheIdPersonneEDT($LES_PROFESSEURS->NOM,$LES_PROFESSEURS->PRENOM,'ENS');
			$idcode=$LES_PROFESSEURS->CODE;
			if ($idprof > 0) {
				$tabIdprof["$idcode"]="$idprof";
			}else{
				foreach($LES_PROFESSEURS->ADRESSE as $L_ADRESSE) {
					$num=$L_ADRESSE->NUMERO;
					$rue=$L_ADRESSE->RUE;
					$ccp=$L_ADRESSE->CODE_POSTAL;
					$ville=$L_ADRESSE->VILLE;
					$pays=$L_ADRESSE->PAYS;
					$tel=$L_ADRESSE->TELEPHONE;
					$tel2=$L_ADRESSE->TEPEPHONE2;
					$email=preg_replace("/'/","",$L_ADRESSE->EMAIL);
				}
				$adresse="$num $rue";
				create_personnel_prof(addslashes(strtolower($LES_PROFESSEURS->NOM)),addslashes(strtolower($LES_PROFESSEURS->PRENOM)),'Aucun1motDePasse','ENS','','',addslashes($adresse),"$ccp",$tel,addslashes($email),addslashes($ville),$tel2,'','');
				$idprof=chercheIdPersonneEDT($LES_PROFESSEURS->NOM,$LES_PROFESSEURS->PRENOM,'ENS');
				$tabIdprof["$idcode"]="$idprof";
			}
		}

		foreach ($xml->LES_GROUPES->UN_GROUPE as $UN_GROUPE) {
			if (trim($UN_GROUPE->NOM) == "") continue;
			$idclasse=chercheIdClasse(strtolower(addslashes($UN_GROUPE->NOM)));
			$idcode=$UN_GROUPE->CODE;
			if ($idclasse > 0) {
				$tabIdClasse["$idcode"]="$idclasse";
			}else{
				$classenom=$UN_GROUPE->NOM;
				$classenom=preg_replace("/'/",'',$classenom);
				$classenom=preg_replace('/"/','',$classenom);
				create_classe2(addslashes($classenom));
				$idclasse=chercheIdClasse(strtolower(addslashes($classenom)));
			}
			$tabIdClasse["$idcode"]="$idclasse";
		}


		// GESTION DES ETUDIANTS
		foreach ($xml->LES_ETUDIANTS->UN_ETUDIANT as $UN_ETUDIANT) {
			$code=$UN_ETUDIANT->CODE;
			$nom=$UN_ETUDIANT->NOM;
			$prenom=$UN_ETUDIANT->PRENOM;
			$naissance=$UN_ETUDIANT->NAISSANCE;
			$idnationnal=$UN_ETUDIANT->IDENTIFIANT;
			$boursier=$UN_ETUDIANT->BOURSIER;
			$email=$UN_ETUDIANT->EMAIL;
		
			$idEleve=chercheIdEleveEdt($nom,$prenom,$naissance);	
			if ($idEleve > 0) {
				$tabIdEleve["$idcode"]="$idEleve";
			}else{
				// 2013-5-6
				list($annee,$mois,$jour)=preg_split('/-/',$naissance);
				if (checkdate($mois,$jour,$annee)) {
					creation_eleve_via_edt($nom,$prenom,$naissance,$idnationnal,$boursier,$email);
					$idEleve=chercheIdEleveEdt($nom,$prenom,$naissance);	
					$tabIdEleve["$idcode"]="$idEleve";
				}
			}	
		}

		foreach ($xml->LES_GROUPES->UN_GROUPE as $UN_GROUPE) {
			$idclasse=chercheIdClasse(strtolower(addslashes($UN_GROUPE->NOM)));
			foreach($UN_GROUPE->LES_ETUDIANTS_DU_GROUPE as $LES_ETUDIANTS_DU_GROUPE) {
				$codeEleve=$LES_ETUDIANTS_DU_GROUPE->UN_CODE_ETUDIANT;
				$idEleve=$tabIdEleve["$codeEleve"];
				if ($idEleve > 0) { modifClasseEleveEdt($idEleve,$idclasse); }
			}
		}

		foreach ($xml->LES_MATIERES->UNE_MATIERE as $LES_MATIERES) {
			$idcode=$LES_MATIERES->CODE;
			$couleur=codeCouleurEDTVT($LES_MATIERES->COULEUR);
			$idmatiere=chercheIdMatiere(addslashes($LES_MATIERES->ALIAS));
			if ($idmatiere > 0) {
				mise_ajoutCouleurMatiere($idcode,$couleur);
			}else{
				create_matiereEDT(addslashes($LES_MATIERES->ALIAS),$couleur);
				$idmatiere=chercheIdMatiere(addslashes($LES_MATIERES->ALIAS));
			}
			$tabIdMatiere["$idcode"]="$idmatiere";
		}


		foreach ($xml->LES_SALLES->UNE_SALLE as $LES_SALLES) {
			/*
			print $LES_SALLES->CODE;
			print ":";
			print $LES_SALLES->NOM;
			print "<br>";
			 */
			$info=$LES_SALLES->COMMENTAIRE;
			if (trim($LES_SALLES->NOM) == "") continue;
			$idSalle=chercheIdMatos(strtolower(addslashes($LES_SALLES->NOM)));
			$idcode=$LES_SALLES->CODE;
			if ($idSalle > 0) {
				$tabIdSalle["$idcode"]="$idSalle";
			}else{
				$sallenom=$LES_SALLES->NOM;
				$sallenom=preg_replace("/'/",'',$sallenom);
				$sallenom=preg_replace('/"/','',$sallenom);
				create_salle(strtolower(addslashes($sallenom)),addslashes($info));
				$idSalle=chercheIdMatos(strtolower(addslashes($sallenom)));
				$tabIdSalle["$idcode"]="$idSalle";
			}
		}
		
		purgeEdtEnseignement();

		if ((isset($_POST["supp"])) && ($_POST["supp"] == "oui")) { purgeEdtSeance(); }

		foreach ($xml->LES_ENSEIGNEMENTS->UN_ENSEIGNEMENT as $LES_ENSEIGNEMENTS) {
			/*
			print $LES_ENSEIGNEMENTS->CODE;
			print ":";
			print $LES_ENSEIGNEMENTS->NOM;
			print ":";
			print $LES_ENSEIGNEMENTS->CODE_MATIERE;
			print ":";
			print $LES_ENSEIGNEMENTS->DUREE_TOTALE;
			print ":";
			print $LES_ENSEIGNEMENTS->DUREE_CHAQUE_SEANCE;
			print "<br>";
			 */
			$couleur=$LES_ENSEIGNEMENTS->COULEUR;
			$idcode=$LES_ENSEIGNEMENTS->CODE_MATIERE;
			$idmatiere=$tabIdMatiere["$idcode"];
			import_edt_enseignement($LES_ENSEIGNEMENTS->CODE,addslashes($LES_ENSEIGNEMENTS->IDENTIFIANT),$idmatiere,$LES_ENSEIGNEMENTS->DUREE_TOTALE,$LES_ENSEIGNEMENTS->DUREE_CHAQUE_SEANCE);
		}

		

		
		foreach ($xml->LES_SEANCES->UNE_SEANCE as $LES_SEANCES) {
			/*	
			print $LES_SEANCES->CODE;
			print ":";
			print $LES_SEANCES->ENSEIGNEMENT;
			print ":";
			print $LES_SEANCES->DATE;
			print ":";
			print $LES_SEANCES->HEURE;
			print ":";
			print $LES_SEANCES->DUREE;
			print "<br>";
			 */
			unset($tab);
			$code_enseignement=$LES_SEANCES->ENSEIGNEMENT;
			foreach($LES_SEANCES->LES_RESSOURCES->UNE_RESSOURCE as $LES_RESSOURCES) {
				$clef=$LES_RESSOURCES->TYPE;
				$value=$LES_RESSOURCES->CODE_RESSOURCE;
				if ($clef == "SALLE") { 
					$idSalle=$value; 
					foreach($tabIdSalle as $key=>$value) {
						if ($key == $idSalle) {
							$idSalle=$value;
							break;
						}
					}
				}
				if ($clef == "GROUPE") { 
					$idclasse=$value; 
					foreach($tabIdClasse as $key=>$value) {
						if ($key == $idclasse) {
							$idclasse=$value;
							break;
						}
					}
				}
				if ($clef == "PROF") {	
					$idprof=$value; 
					foreach($tabIdprof as $key=>$value) {
						if ($key == $idprof) {
							$idprof=$value;
							break;
						}
					}			    
				}

				/*
				foreach($tabIdMatiere as $key=>$value) {
					if ($code_matiere == $key) {
						$idmatiere=$value;
						break;
					}
				}*/

				$idmatiere=rechercheIdEdtEnseignement($code_enseignement);
				import_edt_seance($LES_SEANCES->CODE,$LES_SEANCES->ENSEIGNEMENT,$LES_SEANCES->DATE,$LES_SEANCES->HEURE,$LES_SEANCES->DUREE,$idclasse,$idprof,$idmatiere,$idSalle);
				unset($idmatiere);
				unset($idSalle);
				unset($idprof);
			}
		}


		/* --------------------
		 
		 * purge des tables
		 */
		deleteEleveViaEdt();
		purgeEdtEnseignement();
		// --------------------

		Pgclose();

	//	@unlink("data/fichier_ASCII/edtF.xml");
	//	@unlink("data/fichier_ASCII/edt.xml");
?>

	<center><font class="T2"><?php print LANGEDT3 ?></font><br /><br />

	<font class="T2"><?php print LANGEDT4 ?> : <input type=button class="bouton2" onclick="open('edt_visu.php','edt','width=1050,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');" value="Administrer" /></center>
	<script language=JavaScript>attente_close();</script>
	<?php } 
}
?>
<?php attente(); ?>

</td></tr></table>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
