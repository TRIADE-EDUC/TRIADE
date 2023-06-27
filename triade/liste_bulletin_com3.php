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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<!-- // fin  -->
<BR>
<?php
include_once("librairie_php/db_triade.php");
validerequete("profadmin");
$cnx=cnx();
error($cnx);

$emetteur=$_SESSION["id_pers"];
$saisie_envoi=$_POST["saisie_envoi"];

$objet=$_POST["saisie_objet"];
if ($objet == "") {
	$objet="Pas d\'objet";
}
$text=$_POST["resultat"];

$destinataire=$_POST["saisie_destinataire"];
if (trim($destinataire) == "") {
	print "<script language=JavaScript>location.href='editer_bulletin.php'</script>";
	exit;
}

$date=dateDMY2();
$heure=dateHIS();
$type_personne_dest=$_POST["saisie_type_personne_dest"];


// chaine indesirable //
//$text=str_replace("<script","", "$text");
//$text=str_replace("<SCRIPT","", "$text");
// -----------------  //


if ($type_personne_dest == "ELE") { $membre_dest="menueleve"; }
if ($type_personne_dest == "PAR") { $membre_dest="menuparent"; }
if ($type_personne_dest == "ADM") { $membre_dest="menuadmin"; }
if ($type_personne_dest == "MVS") { $membre_dest="menuscolaire"; }
if ($type_personne_dest == "ENS") { $membre_dest="menuprof"; }


if ($_SESSION["membre"] == "menueleve") { $type_personne="ELE"; }
if ($_SESSION["membre"] == "menuparent") { $type_personne="PAR"; }
if ($_SESSION["membre"] == "menuadmin") { $type_personne="ADM"; }
if ($_SESSION["membre"] == "menuscolaire") { $type_personnet="MVS"; }
if ($_SESSION["membre"] == "menuprof") { $type_personne="ENS"; }


$tabid=preg_split('/:/',$destinataire);
foreach ($tabid as $destinataire) {
	if (trim($destinataire) != "") {
	/*
	 	print "<BR>";
		print "emetteur ". $emetteur;
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

$number=md5(uniqid(rand()));
$cr=envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number);
        if($cr == 1){
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
        else {
              error(0);
	}
	}
}
?>
<center>
<font class=T2><?php print LANGMESS8?></font><br ><br />
<table align=center><tr><td><script language=JavaScript>buttonMagicRetour("editer_bulletin.php?sClasseGrp=<?php print $_POST["saisie_classe"]?>","_parent")</script></td></tr></table>
</center>
<!-- // fin  -->

</BODY></HTML>
