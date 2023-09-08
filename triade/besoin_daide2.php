<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
include_once("./librairie_php/lib_licence.php");
include_once("./common/lib_admin.php");
include_once("./common/lib_ecole.php");
include_once("./common/config.inc.php");
include_once("librairie_php/db_triade.php");

?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("librairie_php/langue.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGASS1?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<TABLE border=0 width=100% >
<TR><TD valign=top>
<?php
$cnx=cnx();
error($cnx);
$cr=create_assistance($_POST["saisie_membre"],$_POST["saisie_action"],$_POST["saisie_service"],$_POST["saisie_com"],$_SESSION["nom"],$_SESSION["prenom"]);
if($cr){
	$fichier=fopen("./data/bug_report.txt","a+");
	fwrite($fichier,"<font class=T2> Le ".date("d/m/Y")."<br>");
	fwrite($fichier,"Utilisateur : $_SESSION[nom] $_SESSION[prenom] <br>");
	fwrite($fichier,"Description : $_POST[saisie_com] </font><br><br>");
	fwrite($fichier,"<hr><br>");
	fclose($fichier);
	$type="Demande d'assistance de ".$_SESSION["nom"]." ".$_SESSION["prenom"] ;
    	if (MAILMESSINTER == "oui") { mailAdmin("Message Assistance"); }
}else{
       error(0);
}
Pgclose();
//------------------------------
?>
<font class="T2">
<ul><?php print LANGASS28?><br><br>

<?php print LANGASS29?>
</font>
</TD></TR></TABLE>
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
</BODY></HTML>
