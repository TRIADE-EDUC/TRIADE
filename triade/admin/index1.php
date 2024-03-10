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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="alternate" type="application/rss+xml" title="Actualité Triade" href="http://www.triade-educ.com/accueil/news/rss.xml" />
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="../librairie_js/ticker.js"></script>
<LINK REL="SHORTCUT ICON" href="./favicon.ico">
<title>Triade admin</title>
<?php
$fichier="../data/install_log/valid.inc";
if (! file_exists($fichier)) {
	print "<script>location.href='./valid0.php';</script>";
	exit ;
}
?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include("./librairie_php/lib_netscape.php");
include("./librairie_php/lib_licence.php");

$fichier="../data/erreurs.log";
if (file_exists($fichier))  {
       $fic=fopen($fichier,"r");
       $donnee=fread($fic,10000);
       fclose($fic);
       if (trim($donnee) != "") {
	       $affiche="<script>marquee(\"<font color=red> INFORMATION</font> :  <a href='erreur.php'>Consulter le module Warning </a> </marquee>\")</script>";
       }
}
?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<?php

include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$cnx=cnx();
if ($cnx == 0) {
	print "<br />";
	print "<img src='../image/commun/kitwarning.gif' align=left><font class=T2 color=red>";
	print "<b>"."Suite à un problème technique,"."</b><br />";
	print "l'accès au serveur est indisponible. L'équipe Triade intervient actuellement sur le serveur." ;
	print " <i>(Code : 0A01)</i>";
	print "<br /><br />";
}
?>
<?php print $affiche ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Message(s) Interne(s)</font></b></td></tr>
<tr id='cadreCentral0' ><td >

<table height=100% width=100% border=0 >
<TR><TD align=top>
<font class=T1>
	<?php
	//zone pour les suggestions des membres
	$fichier="../data/fic_opinion.txt";
        if (file_exists($fichier) && (filesize($fichier) > 0)) {
		print "<br>";
	        print "<ul><font class='T2'>Messages Internes</font></ul>";
        	readfile($fichier);
		print "<BR>";
		print "<div align=right><input type=button onclick=\"open('opinion_a_zero.php','_parent','');\" value='Supprimer Messages' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	}
	// zone pour les questions faq
	$fichier="../data/fic_question_faq.txt";
        if ((file_exists($fichier)) && (filesize($fichier) > 0)) {
		print "<HR width=80%>";
		print "<br /><ul><font class=T2>Question</font></ul><BR>";
		readfile($fichier);
		print "<BR><BR>";
		print "<div align=right><input type=button onclick=\"open('question_faq_a_zero.php','_parent','');\" value='Supprimer Questions' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	}
	// zone pour les problèmes d'accès
	$fichier="../data/fic_probleme.txt";
        if ((file_exists($fichier)) && (filesize($fichier) > 0)) {
		print "<HR width=80%>";
		print "<br /><ul><font class=T2>Problème d'accès</font></ul><BR>";
		readfile($fichier);
		print "<BR><BR>";
		print "<div align=right><input type=button onclick=\"open('probleme_acces_a_zero.php','_parent','');\" value='Supprimer Problème' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	}

	$fichier="../data/bug_report.txt";
	if ((file_exists($fichier)) && (filesize($fichier) > 0)) {
		print "<ul><font class=T2>Message(s) Utilisateur(s)</font></ul><br>";
		readfile($fichier);
		print "<div align=right>";
		print "<input type=button onclick=\"open('bug.php','_parent','');\" value='Transmettre au support Triade' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;";
		print "<input type=button onclick=\"open('bug_a_zero.php','_parent','');\" value='Supprimer Messages' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	}
?>
</font></td></tr></table>

<?php 
if (!file_exists('../moodle/config.php')) {
?>

	</font></TD></TR></TABLE>
	<br><br>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Installation de Moodle</font></b></td>
	</tr>
	<tr id='cadreCentral0' >
	<td >
	<table height=100% width=100% border=0 >
	<TR><TD align=top>
	Veuillez terminer l'installation : 
        <input type=button onclick="open('../moodle/','_blank','');" value='Installer Moodle' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;
	</td></tr></table>

<?php
} 
?>

<!-- // fin de la saisie -->
</td></tr></table> <BR><BR><BR>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
