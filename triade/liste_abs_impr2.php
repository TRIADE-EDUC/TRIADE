<?php
session_start();
$emailcc=$_POST["emailcc"];
setcookie("emailcc", $emailcc, time() + (86400 * 30), "/"); 
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
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS5?></font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br>
<!-- // fin  -->
<?php

$dateDuJour=dateDMY();
$nb=$_POST["nb"];
for($i=0;$i<$nb;$i++) {
	list($idEleve,$datedebut,$datefin,$duree,$heure,$minute,$seconde,$compte) = preg_split("/:/",$_POST["liste"][$i]);
	if ($idEleve == "")  { continue; }	

		
	$dureeunite="";
	if (preg_match('/heure\(s\)$/',$duree)) {
		$duree=preg_replace("/heure(s)/","",$duree);
		$dureeunite="heure(s)";
	}
	$duree=trim($duree);
	
	// declaration variable
	
	
	$datedebut=dateForm($datedebut);
	$datefin=dateForm($datefin);

	if ($datedebut == $datefin) {
		$dateaffiche="
	le $datedebut";
	}else{
		$dateaffiche="
	du $datedebut au $datefin";
	}
/*
	$nomEleve
	$prenomEleve
	$classe_nom
	$datedebut
	$datefin
	$duree
	$NomResponsable1
	$listingABS
	$dateDuJour
 */




	enrHistoEleve($idEleve,$date,"Envoi email absence non justifiée","");
	valideEnvoiCourrier($idEleve,$datedebut,$datefin,"$heure:$minute:$seconde");
	$message="$dateaffiche d'une durée de $duree \n";
	$tabMail[$idEleve."_".$compte].=$message;
	$tabMail[$idEleve."_".$compte].=$message;
}

foreach($tabMail as $idEleve => $messageabs) {

	list($idEleve,$compte)=preg_split('/_/',$idEleve);

	$nomEleve=strtoupper(recherche_eleve_nom($idEleve));
	$prenomEleve=recherche_eleve_prenom($idEleve);
	$idClasse=chercheIdClasseDunEleve($idEleve);
	$classe_nom=strtoupper(chercheClasse_nom($idClasse));
	// adresse de l'élève
	// elev_id,nomtuteur, prenomtuteur, adr1, code_post_adr1, commune_adr1, adr2, code_post_adr2, commune_adr2, numero_eleve, class_ant, date_naissance, regime, civ_1, civ_2,nom,prenom,nom_resp_2,prenom_resp_2,lieu_naissance
	$dataadresse=chercheadresse($idEleve);
	$nomtuteur=strtoupper($dataadresse[0][1]);
	$prenomtuteur=$dataadresse[0][2];
	$NomResponsable1=strtoupper($nomtuteur);
	$civ_1=civ($dataadresse[0][13]);

	$emailtuteurstage=recupEmailTuteurStage($idEleve);

	$idsite=chercherIdSiteClasse($idClasse);
	$data=visu_paramViaIdSite($idsite);
        //nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire FROM
	for($i=0;$i<count($data);$i++) {
		$nom_etablissement=trim($data[$i][0]);
	       	$mail=trim($data[$i][5]);
  	}

	if ($compte == "tuteurstage") {

                $message="

        Bonjour,

        Nous vous informons que $nomEleve $prenomEleve fût absent(e) :
        $messageabs
";
        }


	if ($compte == "tuteur") {

		$message="

	Bonjour,

	Nous vous informons que $nomEleve $prenomEleve fût absent(e) :
	$messageabs
";
	}


	if ($compte == "eleve") {

                $message="

        Bonjour,

        Nous vous informons $nomEleve $prenomEleve fût absent(e) :
        $messageabs
";
        }




	if ($_POST['filtreABS'] == "NonJustifie") {

		$message.="
	Merci de nous contacter afin de justifier ce ou ces absences.

";
	}

	$objet=" Absence(s) de $nomEleve $prenomEleve";
	$objet=TextNoAccent($objet) ;
	$objet=stripslashes($objet);
	$sujet = "$nom_etablissement : $objet";
	$nom_expediteur=expediteur_triade();
	$email_expediteur=MAILREPLY;
	$message=TextNoAccent($message);
	$email_expediteur=trim($email_expediteur);
	$emailparent1=cherchemailparent($idEleve);

	
	if ($compte == "tuteurstage") {
                if (ValideMail($emailtuteurstage)) {
                        $to=$emailtuteurstage;
                        if (ValideMail($emailcc)) $to.=",".$emailcc;
                        mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
//print "$sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur";
//print "<hr>";
                }

                $emailparent2=cherchemailparent2($idEleve);
                if (ValideMail($emailparent2)) {
                        $to=$emailparent2;
                        if (ValideMail($emailcc)) $to.=",".$emailcc;
                        mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
//print "$sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur";
//print "<hr>";
                }
        }

	if ($compte == "tuteur") { 
		if (ValideMail($emailparent1)) {
			$to=$emailparent1;
			if (ValideMail($emailcc)) $to.=",".$emailcc;
			mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
//print "$sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur";
//print "<hr>";
		}
	
		$emailparent2=cherchemailparent2($idEleve);
		if (ValideMail($emailparent2)) {
			$to=$emailparent2;
			if (ValideMail($emailcc)) $to.=",".$emailcc;
			mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
//print "$sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur";
//print "<hr>";
		}
	}

	if ($compte == "eleve") { 
		$email_eleve=cherchemaileleve($idEleve);
		$email_eleve0=$email_eleve;
	        if (ValideMail($email_eleve)) {
	                $to=$email_eleve;
	                if (ValideMail($emailcc)) $to.=",".$emailcc;
	                mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
//print "$sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur";
//print "<hr>";
	        }
	}

}

$bouton="<table align='center'><tr><td><script>buttonMagicRetour2('liste_abs_impr.php','_self','Retour')</script></td></tr></table>";

?>

<br />
<center><font class=T2><?php print "Email Envoyé(s)" ?></font></center>
<br><br>
<?php print $bouton ?>
<br><br>
<!-- // fin  -->

</ul>
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
