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
validerequete("2");
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
		$recup=$_POST["url"];
		$message=$_POST["message"];
		$message=preg_replace('/°/',' ',$message);
		print "<iframe width='10' height='10' name='TRIADE-SMS' src='vide.html' style='visibility:hidden' ></iframe>";
		print "<form method='post' action='${urlsms}sms-envoi.php' target='TRIADE-SMS' name='formulaire'>";
		print "<input type=hidden name='sms-envoi' value=\"$recup\">";
		print "<input type=hidden name='sms-message' value=\"".stripslashes($message)."\">";
		print "<input type=hidden name='sms-id' value='$idsms'>";
		print "<input type=hidden name='sms-info' value=\"$_SESSION[nom] $_SESSION[prenom]\" >";
		print "</form>";
		print "<script>document.formulaire.submit()</script>";
		print "<center><font class=T2>SMS ENVOYE(S)";
		print "</font></center>";
		history_cmd($_SESSION["nom"],"SMS","Envoi abs/rtd");
	}else{
		print "<center><font color=red class='T2' >".LANGMESS37.".</font></center>";
	}
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR1."</i></center>";
}

print "<br><br><table align='center'><tr><td><script>buttonMagicRetour2('gestion_abs_retard.php','_self','".LANGCIRCU14."')</script></td></tr></table>";
	
	
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
