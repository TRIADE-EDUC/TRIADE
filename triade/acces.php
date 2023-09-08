<?php
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
session_start();

include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");


//error_reporting(0);

if ($_COOKIE["langue-triade"] == "fr") {
	include_once("./librairie_php/langue-text-fr.php");
}elseif ($_COOKIE["langue-triade"] == "en") {
	include_once("./librairie_php/langue-text-en.php");
}elseif ($_COOKIE["langue-triade"] == "es") {
	include_once("./librairie_php/langue-text-es.php");
}elseif ($_COOKIE["langue-triade"] == "bret") {
	include_once("./librairie_php/langue-text-bret.php");
}elseif ($_COOKIE["langue-triade"] == "arabe") {
	include_once("./librairie_php/langue-text-arabe.php");
}elseif ($_COOKIE["langue-triade"] == "it") {
	include_once("./librairie_php/langue-text-it.php");
}elseif ($_COOKIE["langue-triade"] == "occitan") {
	include_once("./librairie_php/langue-text-oc.php");
}else {
	include_once("./librairie_php/langue-text-fr.php");
}



$choixlangue=$_POST["saisielangue"];
setcookie("langue-triade",$choixlangue,time()+3600*24*30);

$_SESSION=array();
session_unset();

include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

include_once("./librairie_php/timezone.php");
$cnx=cnx();

$nomsPostVar=array('saisie_membre','membre','saisienom','nom','saisieprenom','prenom','saisiepasswd','pwd');
$hashPostVar=hashPostVar($nomsPostVar);
$code=acces($hashPostVar);
$nav_info=$_POST["info_nav"];

if  ((preg_match('/microsoft/i',$nav_info)) || (preg_match('/internet explorer/i',$nav_info))) {
	$navigateur="IE";
}else {
	$navigateur="NONIE";
}



// test si compte blacklister
$nom=trim(ucwords($hashPostVar[nom]));
$prenom=trim($hashPostVar[prenom]);
$membre=trim($hashPostVar[membre]);
$data=verifblacklist(strtolower($nom),strtolower($prenom),strtolower($membre));
if (count($data) > 0) {
      header("Location: acces_depart.php?bl=1&message=".LANGTERREURCONNECT."&saisie_membre=$hashPostVar[membre]&saisie_titre=$hashPostVar[membre]");
      exit;
}

if ((file_exists("./data/parametrage/noacces.ete")) && ($membre != "administrateur")) {
	header("Location: acces_depart.php?bl=1&message=".LANGTERREURCONNECT."&saisie_membre=$hashPostVar[membre]&saisie_titre=$hashPostVar[membre]");
	exit;
}



// recuperation des informations de l'utilisateur
// IP, OS, navigateur
include_once("./librairie_php/lib_verif_nav.php");
$ip=$_SERVER["REMOTE_ADDR"];
$os=verif_os();
$nav=verif_navigateur();
$id_session=session_id();


// -------------------------------------------------------
include_once("./librairie_php/lib_statistique.php");
// 
if (($hashPostVar[membre] == 'administrateur') && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");

      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menuadmin" ;
      $id_pers=chercheIdPersonne(strtolower($nom),strtolower($prenom),'ADM');
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menuadmin";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["widthfen"]=$_POST["saisiewidth"];
      $_SESSION['KCFINDER'] = array();
      $_SESSION['KCFINDER']['disabled'] = false;
      $_SESSION["nav"]=$nav;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Administrateur");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menuadmin",$id_session);
      statConecParHeure(dateH());
      ip_timeout_clear($ip);
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      header("Location: acces2.php?id");


   elseif ($hashPostVar[membre] == 'parent' && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");
      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menuparent" ;
      $id_pers=chercheIdEleve(strtolower($nom),strtolower($prenom));
      $idClasse=chercheIdClasseDunEleve($id_pers);
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menuparent";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["idClasse"]=$idClasse;
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["widthfen"]=$_POST["saisiewidth"];
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["nav"]=$nav;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      $idparent=rechercheParent($id_pers,$_POST["saisiepasswd"]);
      $_SESSION["idparent"]=$idparent;  // si 1 tuteur1 si 2 tuteur2

      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Parent");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menuparent",$id_session);
      ip_timeout_clear($ip);
      statConecParHeure(dateH());
      header("Location: acces2.php?id");


   elseif ($hashPostVar[membre] == 'eleve' && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");
      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menueleve" ;
      $id_pers=chercheIdEleve(strtolower($nom),strtolower($prenom));
      updatePwdMoodle($id_pers,$_POST["saisiepasswd"]);
      $idClasse=chercheIdClasseDunEleve($id_pers);
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menueleve";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["MDP"]=$_POST["saisiepasswd"];
      $_SESSION["idClasse"]=$idClasse;
      $_SESSION["widthfen"]=$_POST["saisiewidth"];
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["nav"]=$nav;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      $_SESSION["pwd"]=$_POST["saisiepasswd"];
      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Elève");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menueleve",$id_session);
      ip_timeout_clear($ip);
      statConecParHeure(dateH());
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      header("Location: acces2.php?id");
      

   elseif ($hashPostVar[membre] == 'vie scolaire' && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");
      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menuscolaire" ;
      $id_pers=chercheIdPersonne(strtolower($nom),strtolower($prenom),'MVS');
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menuscolaire";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["widthfen"]=$_POST["saisiewidth"];
      $_SESSION["nav"]=$nav;
      $_SESSION['KCFINDER'] = array();
      $_SESSION['KCFINDER']['disabled'] = false;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Vie Scolaire");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menuscolaire",$id_session);
      ip_timeout_clear($ip);
      statConecParHeure(dateH());
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      header("Location: acces2.php?id");

   elseif ($hashPostVar[membre] == 'tuteurstage' && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");
      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menututeur" ;
      $id_pers=chercheIdPersonne(strtolower($nom),strtolower($prenom),'TUT');
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menututeur";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["widthfen"]=$_POST["saisiewidth"];
      $_SESSION["nav"]=$nav;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Tuteur Stage");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menututeur",$id_session);
      ip_timeout_clear($ip);
      statConecParHeure(dateH());
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      header("Location: acces2.php?id");

 elseif ($hashPostVar[membre] == 'personnel' && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");
      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menupersonnel" ;
      $id_pers=chercheIdPersonne(strtolower($nom),strtolower($prenom),'PER');
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menupersonnel";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["widthfen"]=$_POST["saisiewidth"];
      $_SESSION['KCFINDER'] = array();
      $_SESSION['KCFINDER']['disabled'] = false;
      $_SESSION["nav"]=$nav;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Personnel");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menupersonnel",$id_session);
      ip_timeout_clear($ip);
      statConecParHeure(dateH());
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      header("Location: acces2.php?id");



   elseif ($hashPostVar[membre] == 'enseignant' && $code==1) :

      count_saisie("./data/compteur/compteur_acces.txt","visited","7200","compteur_acces.time");
      $nom=trim(ucwords($hashPostVar[nom]));
      $prenom=trim($hashPostVar[prenom]) ;
      $membre="menuprof" ;
      $id_pers=chercheIdPersonne(strtolower($nom),strtolower($prenom),'ENS');
      $_SESSION["id_suppleant"]=$id_pers;
      $id_pers=verif_si_suppleant($id_pers); // verification si compte suppleant retour du id du compte
      if ($id_pers == "compteexpire") {
		header("Location:acces_depart.php?saisie_membre=enseignant&saisie_titre=Enseignants&expire=1");
		exit;
      }
      $_SESSION["nom"]=$nom;
      $_SESSION["prenom"]=$prenom;
      $_SESSION["membre"]="menuprof";
      $_SESSION["id_pers"]=$id_pers;
      $_SESSION["navigateur"]=$navigateur;
      $_SESSION["langue"]=$_POST["saisielangue"];
      $_SESSION["widthfen"]=$_POST["saisiewidth"];      
      $_SESSION["nav"]=$nav;
      $_SESSION['KCFINDER'] = array();
      $_SESSION['KCFINDER']['disabled'] = false;
      $_SESSION["os"]=$os;
      $_SESSION["ip"]=$ip;
      $_SESSION["id_session"]=$id_session;
      enr_trace(addslashes($nav),addslashes($os),$ip,addslashes($nom),addslashes($prenom),"Enseignant");
      enr_statUtilisateur(addslashes($nom),addslashes($prenom),$id_pers,"menuprof",$id_session);
      ip_timeout_clear($ip);
      statConecParHeure(dateH());
      setcookie("nom","$nom");
      setcookie("prenom","$prenom");
      setcookie("id_pers","$id_pers");
      $datap=config_param_visu("pagecnx$id_pers");
      $pageconnexion=$datap[0][0];
      if (trim($pageconnexion) == "abs") { header("Location: retardprof.php");exit; }
      if (trim($pageconnexion) == "messagerie") { header("Location: messagerie_reception.php");exit; }
      header("Location: acces2.php?id");
   else :
  	session_set_cookie_params(0);
   	$_SESSION=array();
   	session_unset();
	session_destroy();
	$passwd=$_POST["saisiepasswd"];
   	acceslog("ERREUR CONNEXION#$nav#$os#$ip#$nom#$prenom#membre : $membre#$passwd");
   	ip_timeout($ip);
	header("Location: acces_depart.php?message=".LANGTERREURCONNECT."&saisie_membre=$hashPostVar[membre]&saisie_titre=$hashPostVar[membre]");
	exit;
endif ;


Pgclose();


?>
