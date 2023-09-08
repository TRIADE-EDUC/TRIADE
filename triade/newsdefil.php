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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-type" content = "text/html; charset=iso-8859-1" />
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
   <script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
   <script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
   <script type="text/javascript" src="./librairie_js/function.js"></script>
   <script type="text/javascript" src="./librairie_js/lib_css.js"></script>
   
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php verifplus("menudeux",$_SESSION["id_pers"],$_SESSION["membre"]); ?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= dateDMY();  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h();?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>

<form name="formulaire" method="POST" action="newsdefil1.php" onsubmit="Switch()">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE3?></font></font></b></td></tr>
<tr id='cadreCentral0'><td >
<p align="left"><font color="#000000">
		
		<br />&nbsp;&nbsp;Titre : <input type="text"  name="saisietitre" maxlength="30"  size="35" /><br /><br />
	      	<?php
		if ( $_SESSION["navigateur"] == "IE" ) {
	        	include("./messagerie/messagerie.php");
		 	print "<textarea name='resultat' style='visibility:hidden;position:absolute;top:0px;left:0px' cols='6' rows='96' ></textarea>";
	      	}else {
	         	print "&nbsp;&nbsp;<textarea name='resultat' rows='6' cols='95' ></textarea><br /><br />";
            	}
         	?>
<br /><center>
<?php
if ((MESSDEFIL == "oui") && (DEFILMESSAGEHORI == "oui")) {
	print "<font color=red>".LANGMESS37."</font>";
}else {
	print "<script type='text/javascript' >buttonMagicSubmit(\"".LANGBT1."\",\"Submit\");</script>";
	print "<script type='text/javascript' >buttonMagicSubmit(\"Supprimer message en cours\",\"Supp\");</script>";
}
?>
	      <br><br></center>
             </font></p>
	     <?php brmozilla($_SESSION["navigateur"]); ?>
	     <?php brmozilla($_SESSION["navigateur"]); ?>
     <!-- // fin  -->
     </td></tr></table></form>

     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";
	    top_d();
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
