<?php
session_start();
if (!isset($_SESSION["id_pers"])) {
	session_set_cookie_params(0);
	$_SESSION=array();
	session_unset();
	session_destroy();
	header("Location: consult.php");
}
$emetteur=$_SESSION["id_pers"];
$_SESSION=array();
session_unset();
session_destroy();
			   
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
<title>Triade - Messagerie</title>
</head>
<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/langue.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
?>
<body id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" align=center>
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><b><?php print LANGMESS1?>  <?php print dateDMY()   ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<BR>
<?php

$saisie_classe=$_POST["saisie_classe"];
$saisie_envoi=$_POST["saisie_envoi"];
$destinataire=$_POST["saisie_destinataire"];
$emetteurMessage=$_POST["saisie_emetteur"];


$objet=$_POST["saisie_objet"];
if ($objet == "") {
	$objet="Pas d\'objet";
}
$text=$_POST["resultat"];


$date=dateDMY2();
$heure=dateHIS();
$type_personne_dest=$_POST["saisie_type_personne_dest"];
$type_personne=$_POST["saisie_type_personne_emetteur"];
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
print "<BR>";
print recherche_personne($emetteur);
print "<BR>";
print $type_personne;
*/
// chaine indesirable //
//$text=str_replace("<script","", "$text");
//$text=str_replace("<SCRIPT","", "$text");
// -----------------  //

$number=md5(uniqid(rand()));
$cr=envoi_messagerie($emetteur,$destinataire,$objet,Crypte($text,$number),$date,$heure,$type_personne,$type_personne_dest,$number,'',0);
        if($cr == 1){
        //     alertJs("Message envoyé -- Service Triade");
	     $personne_envoi=recherche_personne($destinataire);
	     history_cmd($emetteurMessage,"MESSAGERIE","envoi &agrave; $personne_envoi");
	     include_once("./common/config2.inc.php");
	     if (FORWARDMAIL == "oui") {
		     $nomemetteur=strtolower(recherche_personne_nom($destinataire,$type_personne_dest));
		     $prenomemetteur=strtolower(recherche_personne_prenom($destinataire,$type_personne_dest));
		     if (check_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$type_personne_dest)) {
		     	 ini_set("sendmail_from",MAILCONTACT);
			     $email=mess_mail_forward($nomemetteur,$prenomemetteur,$destinataire,$type_personne_dest);
			     $http=protohttps(); // return http:// ou https://
			     $lien="$http".$_SERVER["SERVER_NAME"]."/";
			     envoi_mail_forward($nomemetteur,$prenomemetteur,$text,$email,$lien,recherche_personne($emetteur),$number,$objet,$destinataire) ;
			}
		}
        }
        else {
              error();
        }
?>
<center>
<font class=T2><?php print LANGMESS8?></font>
</center>
<script language="javascript">parent.window.close();</script>
</BODY></HTML>
