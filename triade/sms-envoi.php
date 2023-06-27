<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Envoi SMS pour les retards du " ?> <?php print dateDMY()?> </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<?php
if (LAN == "oui") {
	if (file_exists("./common/config-sms.php")) {
		include_once("./common/config-sms.php");
		$idsms=SMSKEY;
		$urlsms=SMSURL;
		
		$message=config_param_visu("sms-message");
		$message=$message[0][0];
		if (trim($message) == "") { 
			$message="Nous vous signalons que votre enfant ELEVE est absent(e) aujourd'hui (DATE) "; 
		}
		$message=preg_replace('/"/',"'",$message);
		$message=preg_replace('/&/'," ",$message);
	
		$nb=0;
		$nbsms=0;

		for ($i=0;$i<$_POST["nb"];$i++) {
			if ($_POST["sms"][$i] == "") { continue; }
			$recup=$_POST["sms"][$i];
			$tab=preg_split('/#/',$recup);
			$nomEleve=recherche_eleve_nom($tab[0]);
			$prenomEleve=trunchaine(recherche_eleve_prenom($tab[0]),15);
			$classenom=chercheClasse_nom(chercheClasseEleve($tab[0]));
			$date_ab=$tab[2];
			$time=$tab[3];
			$date=dateDMY();
			$message=preg_replace('/DATE/',$date,$message);
			$message=preg_replace('/TYPE/',$_POST["type"],$message);
			enrHistoEleve($tab[0],$date,"Envoi SMS abs/rtd","");
		
			signeEnvoiSmsAbsRtd($_POST["type"],$tab[0],$date_ab,$time);

			$tel=$tab[1];
			$url.="$prenomEleve $nomEleve&$tel&$nomEleve&$prenomEleve&$classenom#";
			$nbsms++;
		}
		
		print "<br><ul><font class=T2>";
		$nb = file_get_contents($urlsms."sms-info-nb.php?idsms=$idsms");	
		print "Nombre de SMS restant(s) avant l'envoi : <strong>$nb</script></strong><br /><br />";
		print "Nombre de SMS à envoyer : <strong>$nbsms</strong><br />";
		print "<br><form method='post' action='sms-envoi2.php' >";
		print "<input type=hidden name='url' value='".$url."'>";
		print "<input type=hidden name='message' value=\"".stripslashes($message)."\">";
		if ($nb > 0) {
			if ($nbsms < 0) {
				print "<b>Aucun SMS à envoyer</b>";		
			}else{
				print "<br /><table align=center border='0'><tr><td><script language=JavaScript>buttonMagicSubmit(\"VALIDER ENVOI SMS\",\"create\"); </script></td></tr></table>";
			}
		}else{
			print "<br><img src='image/commun/warning2.gif' align='center'> <font class=T2><b>Crédit SMS Epuisé !!</b></font>";
		}
		print "</form></font></ul>";
	

	}else{
		print "<center><font color=red class='T2' >".LANGMESS37.".</font></center>";
	}
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR1."</i></center>";
}

	
	
?>
<BR><BR>
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
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
