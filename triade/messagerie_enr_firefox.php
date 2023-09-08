<?php
session_start();
/*
include_once("librairie_php/securite.php");
$autorise=verifDroitEnvoiMessage($_SESSION["membre"],$_POST["saisie_type_personne_dest"],$_POST["saisie_envoi"]);
if ($autorise == "0") {
	$aqui=$_POST["saisie_type_personne_dest"];
	header("Location:messagerie_envoi.php?autorise=non&aqui=$aqui");
}
 */
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<br />
<?php include("./librairie_php/lib_licence.php"); ?>
<!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_POST["idsuppbrouillon"])) { //suppression_message_brouillon($_POST["idsuppbrouillon"]); 
}

$brouillon=$_POST["brouillon"];

if ($_POST["saisie_envoi"] == "mailexterne") {
	$objet=$_POST["saisie_objet"];
	$text=$_POST["resultat"];
	$destinataire=$_POST["saisie_destinataire"];
	$source=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"]); 
	envoi_message_par_mail($objet,$text,$destinataire,$source);
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

$saisie_classe=$_POST["saisie_classe"];
$saisie_envoi=$_POST["saisie_envoi"];

$objet=$_POST["saisie_objet"];
if ($objet == "") {
	$objet="Pas d\'objet";
}
$text=$_POST["resultat"];
$idpiecejointe=$_POST["idpiecejoint"];

$destinataire=$_POST["saisie_destinataire_value"];
if ($destinataire == LANGCHOIX) {
	print "<script language=JavaScript>location.href='messagerie_envoi_suite.php?saisie_classe=$saisie_classe&saisie_envoi=$saisie_envoi&saisie_obj=$objet&message=$text&erreur=1&brouillon=$brouillon'</script>";
	exit;
}

$date=dateDMY2();
$heure=dateHIS();
$type_personne_dest=$_POST["saisie_type_personne_dest"];
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
//$text=str_replace("<script","", "$text");
//$text=str_replace("<SCRIPT","", "$text");
// -----------------  //


if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }
if ($type_personne_dest == "TUT") { $membre_dest="menututeur"; }
if ($type_personne_dest == "PER") { $membre_dest="menupersonnel"; }

$number=md5(uniqid(rand()));
$cr=envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,$idpiecejointe,$brouillon);
        if (($cr == 1) && ($brouillon == 0)) {
             //   alertJs("Message envoyé -- Service Triade");
	     $personne_envoi=recherche_personne($destinataire);
	     history_cmd($_SESSION["nom"],"MESSAGERIE","envoi à $personne_envoi");
	     if (FORWARDMAIL == "oui") {
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
				     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
		     			}
				}
			}else{
		     		$nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
				$prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
		     		if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest)) {
			     		$email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$membre_dest);
					$http=protohttps(); // return http:// ou https://
					$lien="$http".$_SERVER["SERVER_NAME"]."/";
			     		envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
		     		}
			}
		}
        }
}
?>
<center>
<font class=T2><b><?php print LANGMESS8?></b></font><br /><br />
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture()</script></td></table>
</center>
<!-- // fin  -->
</BODY></HTML>
