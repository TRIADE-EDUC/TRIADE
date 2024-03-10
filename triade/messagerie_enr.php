<?php
session_start();
include_once("librairie_php/securite.php");
if (isset($_POST["saisie_envoi"])) {
	$autorise=verifDroitEnvoiMessage($_SESSION["membre"],$_POST["saisie_type_personne_dest"],$_POST["saisie_envoi"]);
	if ($autorise == "0") {
		$aqui=$_POST["saisie_type_personne_dest"];
		header("Location:messagerie_envoi.php?autorise=non&aqui=$aqui");
	}
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS1?>  <?php print dateDMY()   ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (isset($_POST["idsuppbrouillon"])) { suppression_message_brouillon($_POST["idsuppbrouillon"]); }

$brouillon=$_POST["brouillon"];
$envoimessagecompletparmail=$_POST["envoimessagecompletparmail"];

if ($_POST["saisie_type_personne_dest"] == "ELE") { $membre_dest1="menueleve"; }
if ($_POST["saisie_type_personne_dest"] == "PAR") { $membre_dest1="menuparent"; }
if ($_POST["saisie_type_personne_dest"] == "ADM") { $membre_dest1="menuadmin"; }
if ($_POST["saisie_type_personne_dest"] == "MVS") { $membre_dest1="menuscolaire"; }
if ($_POST["saisie_type_personne_dest"] == "ENS") { $membre_dest1="menuprof"; }
if ($_POST["saisie_type_personne_dest"] == "TUT") { $membre_dest1="menututeur"; }
if ($_POST["saisie_type_personne_dest"] == "PER") { $membre_dest1="menupersonnel"; }
if ($_POST["saisie_type_personne_dest"] == "TUTEURSTAGE") { $membre_dest1="menututeur"; }

if (($_SESSION["membre"] == "menuparent") && (ACCESMESSENVOIPARENT == "non"))  { $valid=1; } 
if (($_SESSION["membre"] == "menueleve") && (ACCESMESSENVOIELEVE == "non")) { $valid=1; } 
if ($valid == 1) {
	if (verifdelegue($_SESSION["id_pers"],$_SESSION["membre"],chercheIdClasseDunEleve($_SESSION["id_pers"]))) {
		if ( (MESSDELEGUEELEVE == "oui") && ($_SESSION["membre"] == "menueleve")) {   $valid=0; }
		if ( (MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")) {   $valid=0; }
	}
}

if ($valid == 1) {
	print "<center><font color=red class='T2' >".LANGMESS37.".</font></center><br />";
}else{
	if ($_SESSION["membre"] == "menuadmin") {
		$type_personne="ADM";
		$emetteur=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ADM);}
	if ($_SESSION["membre"] == "menututeur") {
		$type_personne="TUT";
		$emetteur=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],TUT);}
	if ($_SESSION["membre"] == "menupersonnel") {
		$type_personne="PER";
		$emetteur=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],PER);}
	if ($_SESSION["membre"] == "menuprof") {
		$type_personne="ENS";
		$emetteur=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],ENS);}
	if ($_SESSION["membre"] == "menuscolaire") {
		$type_personne="MVS";
		$emetteur=chercheIdPersonne(strtolower($_SESSION["nom"]),$_SESSION["prenom"],MVS);}
	if ($_SESSION["membre"] == "menuparent") {
		$type_personne="PAR";
		$emetteur=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);}
	if ($_SESSION["membre"] == "menueleve") {
		$type_personne="ELE";
		$emetteur=chercheIdEleve(strtolower($_SESSION["nom"]),$_SESSION["prenom"]);}

	if ($_POST["saisie_envoi"] == "mailexterne") {
		$objet=$_POST["saisie_objet"];
		$text=$_POST["resultat"];
		$tabdestinataire=explode(",",$_POST["saisie_destinataire_value"]);
		$source=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"]); 
		foreach($tabdestinataire as $key=>$destinataire) {
			if (trim($destinataire) != "") {
				 $nomemetteur=$_SESSION["nom"];
	                	 $prenomemetteur=$_SESSION["prenom"];
		                 $idpiecejointe=$_POST["idpiecejoint"];
		                 $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
                		 for($jj=0;$jj<count($tabfichierjoint);$jj++) {
		        	         $nomfic=$tabfichierjoint[$jj][1];
                		         $fichierj=$tabfichierjoint[$jj][0];
		                         if (file_exists("./data/fichiersj/$fichierj")) {
               			         	$tabsuppfichier[]="./data/fichiersj/$fichierj";
		                                if (!is_dir("./data/tmp")) mkdir("./data/tmp");
                		                copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
                                		$fichierjoint.="./data/tmp/$nomfic,";
		                                $tabsuppfichier[]="./data/tmp/$nomfic";
                		         }
                 		}
		                 $fichierjoint=preg_replace('/,$/','',$fichierjoint);
		                 mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
			}
		}
	 	if (count($tabsuppfichier) > 0) {
                 	foreach($tabsuppfichier as $key=>$val) { if (file_exists($val)) @unlink($val); }
                        deleteRefPieceJointe($idpiecejointe);
                 }
	}	

	if ($envoimessagecompletparmail == '1') {
		$objet=$_POST["saisie_objet"];
		$text=$_POST["resultat"];

/*		if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
                if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
                if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
                if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
                if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
                if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
                if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
*/
                // -------------------------------------------------------------------------------------------------------------------------------------------------- //
		$tabdestinataire=explode(",",$_POST["saisie_destinataire_value"]);
		$source=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"]); 
		foreach($tabdestinataire as $key=>$destinataire) {
			if (trim($destinataire) != "") {
				$type_personne_dest=$_POST["saisie_type_personne_dest"];
                                if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
                                if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
                                if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
                                if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
                                if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
                                if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
                                if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
				if (($destinataire == "tousleseleves") || ($destinataire == "touslesparents") || ($destinataire == "tousleselevesdelegue") || ($destinataire == "tousleselevesecole") || ($destinataire == "touslesparentsecole") || ($destinataire == "touslesparentsdelegues") ) {
					$saisie_classe=$_POST["saisie_classe"];
		                        if (($destinataire == "tousleseleves") || ($destinataire == "touslesparents")) {
						$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
                		                $sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe='$saisie_classe' AND b.code_class='$saisie_classe' AND a.annee_scolaire='$anneeScolaire' ORDER BY nom";
                                		$res=execSql($sql);
		                                $dataEleve=chargeMat($res);
                		        }
		                        $k=0;
                		        if ($destinataire == "tousleselevesdelegue") {
                                		$sql="SELECT  eleve1,eleve2  FROM  ${prefixe}delegue";
		                                $res=execSql($sql);$dataDelegue=chargeMat($res);
                		                for($uu=0;$uu<count($dataDelegue);$uu++) {
                                		        $dataEleve[$k][1]=$dataDelegue[$uu][0];$k++;
		                                        $dataEleve[$k][1]=$dataDelegue[$uu][1];$k++;
                		                }
						$type_personne_dest="ELE";
						$membre_dest="menueleve";
		                        }
		                        if ($destinataire == "tousleselevesecole")  {
						$anneeScolaire=anneeScolaireViaIdClasse();
                		                $sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe=b.code_class AND a.annee_scolaire='$anneeScolaire' ORDER BY a.nom";
		                                $res=execSql($sql);
                		                $dataEleve=chargeMat($res);
						$type_personne_dest="ELE";
						$membre_dest="menueleve";
                		        }
		                        if ($destinataire == "touslesparentsecole")  {
						$anneeScolaire=anneeScolaireViaIdClasse();
                		                $sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe=b.code_class AND a.annee_scolaire='$anneeScolaire' ORDER BY a.nom";
		                                $res=execSql($sql);
                		                $dataEleve=chargeMat($res);
						$type_personne_dest="PAR";
						$membre_dest="menuparent";
                		        }
		                        if ($destinataire == "touslesparentsdelegues")  {
                		                $sql="SELECT  nomparent1,nomparent2  FROM  ${prefixe}delegue";
                                		$res=execSql($sql);$dataDelegue=chargeMat($res);
		                                for($uu=0;$uu<count($dataDelegue);$uu++) {
                		                        $dataEleve[$k][1]=$dataDelegue[$uu][0];$k++;
                                		        $dataEleve[$k][1]=$dataDelegue[$uu][1];$k++;
		                                }
						$type_personne_dest="PAR";
						$membre_dest="menuparent";
                        		}
					for($ii=0;$ii<count($dataEleve);$ii++) {
						$destinataire=$dataEleve[$ii][1];
						if (is_numeric($destinataire)) {
							$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
                		                        $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
	                	                        $destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
							$expediteur=recherche_personne_nom($emetteur,$type_personne);
							$idpiecejointe=$_POST["idpiecejoint"];
							$tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
							for($jj=0;$jj<count($tabfichierjoint);$jj++) {
								$nomfic=$tabfichierjoint[$jj][1];
								$fichierj=$tabfichierjoint[$jj][0];
								if (file_exists("./data/fichiersj/$fichierj")) {
									$tabsuppfichier[]="./data/fichiersj/$fichierj";
									if (!is_dir("./data/tmp")) mkdir("./data/tmp");
									copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
									$fichierjoint.="./data/tmp/$nomfic,";
									$tabsuppfichier[]="./data/tmp/$nomfic";
								}
							}
							$fichierjoint=preg_replace('/,$/','',$fichierjoint);
							mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
		//					print "Support en test -- mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
						}
						if ($envoimessagecompletparmail != '1' ) envoi_message_par_mail(stripslashes($objet),$text,$destinataire,$source);
					}
		
				}else{
					if ($type_personne_dest == "GRPMAIL") {
                                                $data=liste_idpers_mail($destinataire);
                                                $listeid=liste_idpers_grp_mail($data);
                                                foreach($listeid as $idunique) {
                                                        $destinataire=$idunique;
                                                        $type_personne_dest=recherche_type_personne($idunique);
                                                        $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
                                                        $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
                                                        if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
                                                        if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
                                                        if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
                                                        if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
                                                        if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
                                                        if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
                                                        if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }

                                                        if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
                                                        	$destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
                                                        	$expediteur=recherche_personne_nom($emetteur,$type_personne);
                                                        	$idpiecejointe=$_POST["idpiecejoint"];
	                                                        $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
        	                                                for($jj=0;$jj<count($tabfichierjoint);$jj++) {
                	                                                $nomfic=$tabfichierjoint[$jj][1];
                        	                                        $fichierj=$tabfichierjoint[$jj][0];
                                	                                if (file_exists("./data/fichiersj/$fichierj")) {
                                        	                                $tabsuppfichier[]="./data/fichiersj/$fichierj";
                                                	                        if (!is_dir("./data/tmp")) mkdir("./data/tmp");
                                                        	                copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
                                                                	        $fichierjoint.="./data/tmp/$nomfic,";
                                                                        	$tabsuppfichier[]="./data/tmp/$nomfic";
                                                                	}	
                                                        	}
	                                                        $fichierjoint=preg_replace('/,$/','',$fichierjoint);
        	                                                mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
        	               //  print " GRPMAIL mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
                                                        }
                                                }

					}elseif ($type_personne_dest == "TUTEURSTAGE") {

						if ($destinataire == "touslestuteursdestage")  {
	                                                $sql="SELECT pers_id,nom,prenom  FROM  ${prefixe}personnel WHERE type_pers='TUT'";
        	                                       	$res=execSql($sql);
                	                                $dataTuteur=chargeMat($res);
                        	                        $type_personne_dest="TUT";
                                	                $membre_dest="menututeur";
							$type_personne_dest="TUT";
							for($jj=0;$jj<count($dataTuteur);$jj++) {
								$destinataire=$dataTuteur[$jj][0];
								$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
								$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
								if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
                                                                if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
                                                                if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
                                                                if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
                                                                if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
                                                                if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
                                                                if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
								if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
                                                                        $destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
                                                                        $expediteur=recherche_personne_nom($emetteur,$type_personne);
                                                                        $idpiecejointe=$_POST["idpiecejoint"];
                                                                        $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
                                                                        for($jjj=0;$jj<count($tabfichierjoint);$jjj++) {
                                                                                $nomfic=$tabfichierjoint[$jjj][1];
                                                                                $fichierj=$tabfichierjoint[$jjj][0];
                                                                                if (file_exists("./data/fichiersj/$fichierj")) {
                                                                                        $tabsuppfichier[]="./data/fichiersj/$fichierj";
                                                                                        if (!is_dir("./data/tmp")) mkdir("./data/tmp");
                                                                                        copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
                                                                                        $fichierjoint.="./data/tmp/$nomfic,";
                                                                                        $tabsuppfichier[]="./data/tmp/$nomfic";
                                                                                }
                                                                        }
                                                                        $fichierjoint=preg_replace('/,$/','',$fichierjoint);
                                                                        mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
                                      //                                print   "mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
                                                                }
							}
                                   	     	}elseif($destinataire == "touslestuteursdestagedelaclasse" ) {
							$saisie_classe=$_POST["saisie_classe"];
							$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
							$sql="SELECT p.pers_id,p.nom,p.prenom FROM ${prefixe}personnel p , ${prefixe}stage_eleve s , ${prefixe}eleves e , ${prefixe}stage_entreprise t
							                WHERE
							                p.type_pers='TUT' AND
							                s.id_eleve=e.elev_id AND
							                e.classe = '$saisie_classe' AND
							                e.annee_scolaire ='$anneeScolaire' AND
							                s.id_entreprise = t.id_serial AND
							                p.id_societe_tuteur = t.id_serial
									ORDER BY p.pers_id
					                ";
                                                        $res=execSql($sql);
                                                        $dataTuteur=chargeMat($res);
                                                        $type_personne_dest="TUT";
                                                        $membre_dest="menututeur";
                                                        $type_personne_dest="TUT";
                                                        for($jj=0;$jj<count($dataTuteur);$jj++) {
                                                                $destinataire=$dataTuteur[$jj][0];
                                                                $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
                                                                $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
                                                                if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
                                                                if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
                                                                if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
                                                                if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
                                                                if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
                                                                if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
                                                                if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
                                                                if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
                                                                        $destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
                                                                        $expediteur=recherche_personne_nom($emetteur,$type_personne);
                                                                        $idpiecejointe=$_POST["idpiecejoint"];
                                                                        $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
                                                                        for($jjj=0;$jjj<count($tabfichierjoint);$jjj++) {
                                                                                $nomfic=$tabfichierjoint[$jjj][1];
                                                                                $fichierj=$tabfichierjoint[$jjj][0];
                                                                                if (file_exists("./data/fichiersj/$fichierj")) {
                                                                                        $tabsuppfichier[]="./data/fichiersj/$fichierj";
                                                                                        if (!is_dir("./data/tmp")) mkdir("./data/tmp");
                                                                                        copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
                                                                                        $fichierjoint.="./data/tmp/$nomfic,";
                                                                                        $tabsuppfichier[]="./data/tmp/$nomfic";
                                                                                }
                                                                        }
                                                                        $fichierjoint=preg_replace('/,$/','',$fichierjoint);
                                                                        mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
                                                                   //   print   "mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
                                                                }
                                                        }
						}else{
							if  (is_numeric($destinataire)) {
                        	                        	$type_personne_dest="TUT";
                                                        	$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
	                                                        $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
	                                                        $destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
	                                                        $expediteur=recherche_personne_nom($emetteur,$type_personne);
	                                                        $idpiecejointe=$_POST["idpiecejoint"];
        	                                                $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
	                                                        for($jj=0;$jj<count($tabfichierjoint);$jj++) {
	                                                                $nomfic=$tabfichierjoint[$jj][1];
	                                                                $fichierj=$tabfichierjoint[$jj][0];
	                                                                if (file_exists("./data/fichiersj/$fichierj")) {
	                                                                        $tabsuppfichier[]="./data/fichiersj/$fichierj";
	                                                                        if (!is_dir("./data/tmp")) mkdir("./data/tmp");
	                                                                        copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
	                                                                        $fichierjoint.="./data/tmp/$nomfic,";
	                                                                        $tabsuppfichier[]="./data/tmp/$nomfic";
	                                                                }
	                                                        }
	                                                        $fichierjoint=preg_replace('/,$/','',$fichierjoint);
	                                                        mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
	                       //                               print "mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
                                                 	}
						}


					}elseif ($type_personne_dest == "GRPMAILELEV") {
                                                        $data=liste_idpers_mail($destinataire);
                                                        $listeid=liste_idpers_grp_mail($data);
                                                        foreach($listeid as $idunique) {
                                                                $destinataire=$idunique;
                                                                $type_personne_dest="ELE";
                                                                $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
                                                                $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
                                                                if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
                                                                if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
                                                                if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
                                                                if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
                                                                if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
                                                                if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
                                                                if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
                                                                if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
									$destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
                                                               		$expediteur=recherche_personne_nom($emetteur,$type_personne);
	                                                                $idpiecejointe=$_POST["idpiecejoint"];
        	                                                        $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
	                                                                for($jj=0;$jj<count($tabfichierjoint);$jj++) {
	                                                                        $nomfic=$tabfichierjoint[$jj][1];
	                                                                        $fichierj=$tabfichierjoint[$jj][0];
	                                                                        if (file_exists("./data/fichiersj/$fichierj")) {
	                                                                                $tabsuppfichier[]="./data/fichiersj/$fichierj";
	                                                                                if (!is_dir("./data/tmp")) mkdir("./data/tmp");
	                                                                                copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
	                                                                                $fichierjoint.="./data/tmp/$nomfic,";
	                                                                                $tabsuppfichier[]="./data/tmp/$nomfic";
	                                                                        }
        	                                                        }
                	                                                $fichierjoint=preg_replace('/,$/','',$fichierjoint);
	                                                              mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
        	                      //                                  print   "mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
                                                                }
                                                        }
					}else{
						if  (is_numeric($destinataire)) {
                                         		$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
	                                                $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
	                                                $destinataire=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
        	                                        $expediteur=recherche_personne_nom($emetteur,$type_personne);
	                                                $idpiecejointe=$_POST["idpiecejoint"];
	                                                $tabfichierjoint=infoPieceJointe($idpiecejointe); // md5,nom
	                                                for($jj=0;$jj<count($tabfichierjoint);$jj++) {
	                                               		$nomfic=$tabfichierjoint[$jj][1];
	                                                        $fichierj=$tabfichierjoint[$jj][0];
	                                                        if (file_exists("./data/fichiersj/$fichierj")) {
	                                                        	$tabsuppfichier[]="./data/fichiersj/$fichierj";
	                                                                if (!is_dir("./data/tmp")) mkdir("./data/tmp");
	                                                                copy("./data/fichiersj/$fichierj","./data/tmp/$nomfic");
	                                                                $fichierjoint.="./data/tmp/$nomfic,";
	                                                                $tabsuppfichier[]="./data/tmp/$nomfic";
	                                                        }
	                                                }
	                                                $fichierjoint=preg_replace('/,$/','',$fichierjoint);
	                	                        mailTriade(stripslashes($objet),$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);
//	 envoi mail                    print "mailTriade($objet,$text,$text,$destinataire,$source,$source,$expediteur,$fichierjoint);";
	                                         }
					}
				}
			}
		}
		// supprier fichier dans ./data/tmp/ et ./data/fichiersj/
		if ($envoimessagecompletparmail == '1') {
			if (count($tabsuppfichier) > 0) {
				foreach($tabsuppfichier as $key=>$val) { if (file_exists($val)) @unlink($val); } 
				deleteRefPieceJointe($idpiecejointe);
			}
		}
		print "<center><font class=T2>".LANGMESS8."</font></center>";
	}else{

		$saisie_classe=$_POST["saisie_classe"];
		$saisie_envoi=$_POST["saisie_envoi"];

		$objet=$_POST["saisie_objet"];
		if ($objet == "") {
			$objet="Pas d\'objet";
		}
		$text=$_POST["resultat"];

		$listingDest=preg_replace('/,$/','',trim($_POST["saisie_destinataire_value"]));
		$tabdestinataire=explode(',',$listingDest);
		if (count($tabdestinataire) == 0)  {
			print "<script language=JavaScript>location.href='messagerie_envoi_suite.php?saisie_classe=$saisie_classe&saisie_envoi=$saisie_envoi&saisie_obj=$objet&message=$text&erreur=1&brouillon=$brouillon'</script>";
			exit;
		}

		$date=dateDMY2();
		$heure=dateHIS();
		$type_personne_dest=$_POST["saisie_type_personne_dest"];


		$tabdestinataire=array_unique($tabdestinataire);
	foreach($tabdestinataire as $key=>$destinataire) {
		if (trim($destinataire) != "")	{
/*
print "<BR>";
print $emetteur;
print "<BR>";
print $destinataire;
print "<BR>";
print $objet;
print "<BR>";
print $text;
print "<BR>";
print $date;
print "<BR>";
print $heure;
print "<BR>";
print $type_personne_dest;
*/ 

		// chaine indesirable //
		if(!function_exists('str_ireplace')) {
			$text=preg_replace('/\<script/i',"<script", $text);
			$objet=preg_replace('/\<script/i',"<script", $objet);

			$text=str_replace("<script","&lt;script", "$text");
			$objet=str_replace("<SCRIPT","&lt;script", "$objet");
		}else{
			$text=str_ireplace("<script","&lt;script", "$text");
			$objet=str_ireplace("<SCRIPT","&lt;script", "$objet");
		}
		// -----------------  //
		$idpiecejointe=$_POST["idpiecejoint"];

		if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
		if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
		if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
		if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
		if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
		if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
		if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
		if ($type_personne_dest == "TUTEURSTAGE") { $membre_dest="menututeur"; }
	
		if (($destinataire == "tousleseleves") 
				||  ($destinataire == "touslesparents") 
				|| ($destinataire == "tousleselevesdelegue") 
				|| ($destinataire == "tousleselevesecole") 
				|| ($destinataire == "touslesparentsecole") 
				|| ($destinataire == "touslestuteursdestagedelaclasse") 
				|| ($destinataire == "touslestuteursdestage") 
				|| ($destinataire == "touslesparentsdelegues") ) {


			if (($destinataire == "tousleseleves") || ($destinataire == "touslesparents")) {
				$anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
				$sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe='$saisie_classe' AND b.code_class='$saisie_classe' AND a.annee_scolaire='$anneeScolaire' ORDER BY nom";
				$res=execSql($sql);
				$dataEleve=chargeMat($res);
			}
			$k=0;
	

			if ($destinataire == "tousleselevesdelegue") {
			      	$sql="SELECT  eleve1,eleve2  FROM  ${prefixe}delegue";
				$res=execSql($sql);$dataDelegue=chargeMat($res);
				for($uu=0;$uu<count($dataDelegue);$uu++) {
					$dataEleve[$k][1]=$dataDelegue[$uu][0];$k++;
					$dataEleve[$k][1]=$dataDelegue[$uu][1];$k++;
				}
				$type_personne_dest="ELE"; $membre_dest="menueleve";
			}


			// ----------------------------------------------- //

			if ($destinataire == "tousleselevesecole")  {
				$anneeScolaire=anneeScolaireViaIdClasse();
				$sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe=b.code_class AND a.annee_scolaire='$anneeScolaire' ORDER BY a.nom";
				$res=execSql($sql);
				$dataEleve=chargeMat($res);
				$type_personne_dest="ELE";$membre_dest="menueleve";
			}

			if ($destinataire == "touslesparentsecole")  {
				$anneeScolaire=anneeScolaireViaIdClasse();
				$sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe=b.code_class AND a.annee_scolaire='$anneeScolaire' ORDER BY a.nom";
				$res=execSql($sql);
				$dataEleve=chargeMat($res);
				$type_personne_dest="PAR";$membre_dest="menuparent";
			}

			// -------------------------------------------------------------------------------------------------------------- //
			if ($destinataire == "touslesparentsdelegues")  {
				$sql="SELECT  nomparent1,nomparent2  FROM  ${prefixe}delegue";
				$res=execSql($sql);$dataDelegue=chargeMat($res);
				for($uu=0;$uu<count($dataDelegue);$uu++) {
					$dataEleve[$k][1]=$dataDelegue[$uu][0];$k++;
					$dataEleve[$k][1]=$dataDelegue[$uu][1];$k++;
				}
				$type_personne_dest="PAR";$membre_dest="menuparent";
			}

			if ($destinataire == "touslestuteursdestage")  {
                                $sql="SELECT null,pers_id,nom,prenom  FROM  ${prefixe}personnel WHERE type_pers='TUT'";
                                $res=execSql($sql);
                                $dataEleve=chargeMat($res);
                                $type_personne_dest="TUT";
                                $membre_dest="menututeur";
                                $type_personne_dest="TUT";
			}
		
			if ($destinataire == "touslestuteursdestagedelaclasse" ) {
                                $saisie_classe=$_POST["saisie_classe"];
                                $anneeScolaire=anneeScolaireViaIdClasse($saisie_classe);
                                $sql="SELECT null,p.pers_id,p.nom,p.prenom FROM ${prefixe}personnel p , ${prefixe}stage_eleve s , ${prefixe}eleves e , ${prefixe}stage_entreprise t
                                           WHERE
                                           p.type_pers='TUT' AND
                                           s.id_eleve=e.elev_id AND
                                           e.classe = '$saisie_classe' AND
                                           e.annee_scolaire ='$anneeScolaire' AND
                                           s.id_entreprise = t.id_serial AND
                                           p.id_societe_tuteur = t.id_serial
                                ";
				$dataEleve=chargeMat($res);
                                $type_personne_dest="TUT";
                                $membre_dest="menututeur";
                                $type_personne_dest="TUT";
			}

			
		
			for($ii=0;$ii<count($dataEleve);$ii++) {
				$destinataire=$dataEleve[$ii][1];
				$number=md5(uniqid(rand()));
				if ($type_personne_dest == "TUTEURSTAGE") $type_personne_dest="TUT";
				$cr=envoi_messagerie($emetteur,$destinataire,stripslashes($objet),Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe,$brouillon);
		       		if (($cr >= 1) && ($brouillon == 0)) {
					$nbenvoi+=$cr;
		             	        //alertJs("Message envoyé -- Service Triade");
					$personne_envoi=recherche_personne_nom($destinataire,$type_personne_dest)." ".recherche_personne_prenom($destinataire,$type_personne_dest);
		  		        if ($type_personne_dest == "PAR") { $personne_envoi.=" (Parent)"; } 
				        if ($type_personne_dest == "ELE") { $personne_envoi.=" (Elève)"; } 
					history_cmd($_SESSION["nom"],"MESSAGERIE","envoi &agrave; $personne_envoi");
			     		if ((FORWARDMAIL == "oui")  &&  (DEV != "1")) {
				     		@ini_set("sendmail_from",MAILCONTACT);
	     					if ($type_personne_dest == "GRPMAIL") {
							$data=liste_idpers_mail($destinataire);
							$listeid=liste_idpers_grp_mail($data);
							foreach($listeid as $idunique) {
								$destinataire=$idunique;
								$type_personne_dest=recherche_type_personne($idunique);
								$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
								$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
								if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
			                                        if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
			                                        if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
			                                        if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
			                                        if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
			                                        if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
			                                        if ($type_personne_dest == "TUTEURSTAGE") { $membre_dest="menututeur"; }
			                                        if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
					     			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
							     		$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
									$http=protohttps(); // return http:// ou https://
									$lien="$http".$_SERVER["SERVER_NAME"]."/";
									$emetteur1=recherche_personne_nom($emetteur,$type_personne);
							     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,$emetteur1,$number,stripslashes($objet),$destinataire) ;
					     			}
								$type_personne_dest="GRPMAIL";
							}

						}elseif ($type_personne_dest == "GRPMAILELEV") {
							$data=liste_idpers_mail($destinataire);
							$listeid=liste_idpers_grp_mail($data);
							foreach($listeid as $idunique) {
								$destinataire=$idunique;
								$type_personne_dest="ELE";
								$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
								$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
								if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
			                                        if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
			                                        if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
			                                        if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
			                                        if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
			                                        if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
			                                        if ($type_personne_dest == "TUTEURSTAGE") { $membre_dest="menututeur"; }
			                                        if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
					     			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
							     		$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
									$http=protohttps(); // return http:// ou https://
									$lien="$http".$_SERVER["SERVER_NAME"]."/";
									$emetteur1=recherche_personne_nom($emetteur,$type_personne);
							     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,$emetteur1,$number,stripslashes($objet),$destinataire) ;
					     			}
								$type_personne_dest="GRPMAILELEV";
							}
	

						}else{
					     		$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
							$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
				     			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
					     			$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
								$http=protohttps(); // return http:// ou https://
								$lien="$http".$_SERVER["SERVER_NAME"]."/";
								$emetteur1=recherche_personne_nom($emetteur,$type_personne);
					     			envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,$emetteur1,$number,stripslashes($objet),$destinataire) ;
				     			}
						}
					}
			        }
			}
		}else{
			$number=md5(uniqid(rand()));
			if ($_POST["saisie_envoi"] != "mailexterne") {  
				if ($type_personne_dest == "TUTEURSTAGE") $type_personne_dest="TUT";
				$cr=envoi_messagerie($emetteur,$destinataire,stripslashes($objet),Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe,$brouillon);
				$nbenvoi+=$cr;	
			}
		        if (($cr >= 1) && ($brouillon == 0))  {
			     //alertJs("Message envoyé -- Service Triade");
			     $personne_envoi=recherche_personne_nom($destinataire,$type_personne_dest)." ".recherche_personne_prenom($destinataire,$type_personne_dest);
			     if ($type_personne_dest == "PAR") { $personne_envoi.=" (Parent)"; } 
			     if ($type_personne_dest == "ELE") { $personne_envoi.=" (Elève)"; } 
			     history_cmd($_SESSION["nom"],"MESSAGERIE","envoi à $personne_envoi");
			     if ((FORWARDMAIL == "oui") && (DEV != "1")) {
				     	@ini_set("sendmail_from",MAILCONTACT);
	     				if ($type_personne_dest == "GRPMAIL") {
						$data=liste_idpers_mail($destinataire);
						$listeid=liste_idpers_grp_mail($data);
						foreach($listeid as $idunique) {
							$destinataire=$idunique;
							$type_personne_dest=recherche_type_personne($idunique);
							$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
							$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));

							if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
		                                        if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
		                                        if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
		                                        if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
		                                        if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
		                                        if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
			                                if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
	
				     			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
						     		$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
								$http=protohttps(); // return http:// ou https://
								$lien="$http".$_SERVER["SERVER_NAME"]."/";
								$emetteur1=recherche_personne_nom($emetteur,$type_personne);
						     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,$emetteur1,$number,stripslashes($objet),$destinataire) ;
				     			}
							$type_personne_dest="GRPMAIL";
						}

					}elseif ($type_personne_dest == "GRPMAILELEV") {
							$data=liste_idpers_mail($destinataire);
							$listeid=liste_idpers_grp_mail($data);
							foreach($listeid as $idunique) {
								$destinataire=$idunique;
								$type_personne_dest="ELE";
								$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
								$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
								if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
			                                        if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
			                                        if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
			                                        if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
			                                        if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
			                                        if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
			                                        if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }
	
					     			if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
							     		$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
									$http=protohttps(); // return http:// ou https://
									$lien="$http".$_SERVER["SERVER_NAME"]."/";
									$emetteur1=recherche_personne_nom($emetteur,$type_personne);
							     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,$emetteur1,$number,stripslashes($objet),$destinataire) ;
					     			}
								$type_personne_dest="GRPMAILELEV";
							}
					}else{
				     		$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
						$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
				     		if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
					     		$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
							$http=protohttps(); // return http:// ou https://
							$lien="$http".$_SERVER["SERVER_NAME"]."/";
							$emetteur1=recherche_personne_nom($emetteur,$type_personne);
					     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,$emetteur1,$number,stripslashes($objet),$destinataire) ;
				     		}
					}
				}
		        }
		}
	    }
	}
		?>
		<center>
		<font class='T2'><?php print $nbenvoi ?> <?php print LANGMESS8?></font>
		<br><br>
		</center>
<?php
	}
} ?>
	</td></tr></table>
     	<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
</BODY></HTML>
