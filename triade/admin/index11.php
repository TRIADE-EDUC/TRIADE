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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="https://support.triade-educ.org/support/actu.php?admin"></script>
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

$fichier="../data/error.log";
if (file_exists($fichier))  {
       $fichier=fopen($fichier,"r");
       $donnee=fread($fichier,10000);
       if (trim($donnee) != "") {
      	 $affiche="<table border=0 cellpadding='3' cellspacing='1' width='468' bgcolor='#0B3A0C'><TR><TD id='coulfond1' > <font color=red><marquee> ATTENTION - WARNING - WARNING - ERREUR DETECTE - WARNING - WARNING </marquee></font> </TD></TR></table><BR><BR>";
       }
}
?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<?php print $affiche ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Messagerie Interne</font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<table height=100% width=100% border=0 >
<TR><TD align=top>
<font class=T1>
	<?php
	//zone pour les suggestions des membres
	$fichier="../data/fic_opinion.txt";
        if (file_exists($fichier)) {
        $fichier=fopen($fichier,"r");
        $donnee=fread($fichier,100000);
        $donnee=stripslashes($donnee);
        $donnee=nl2br($donnee);
	    print "<br>";
        print "<ul><U><font class=T2>Messages Internes</font></U></ul>";
        print "$donnee";
        print "<BR>";
        print "<div align=right><input type=button onclick=\"open('opinion_a_zero.php','_parent','');\" value='Supprimer Messages' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	}
	// zone pour les questions faq
	$fichier="../data/fic_question_faq.txt";
        if (file_exists($fichier)) {
        $fichier=fopen($fichier,"r");
        $donnee=fread($fichier,100000);
	$donnee=stripslashes($donnee);
        $donnee=nl2br($donnee);
	print "<HR width=80%>";
	print "<ul><U><font class=T2>Question F.A.Q</font></U></ul><BR>";
	print "$donnee";
	print "<BR><BR>";
	print "<div align=right><input type=button onclick=\"open('question_faq_a_zero.php','_parent','');\" value='Supprimer Questions' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	print "<HR width=80%>";
	}
	// zone pour les problèmes d'accès
	$fichier="../data/fic_probleme.txt";
        if (file_exists($fichier)) {
        $fichier=fopen($fichier,"r");
        $donnee=fread($fichier,100000);
	$donnee=stripslashes($donnee);
        $donnee=nl2br($donnee);
	print "<ul><U><font class=T2>Problème d'accès</font></U></ul><BR>";
	print "$donnee";
	print "<BR><BR>";
	print "<div align=right><input type=button onclick=\"open('probleme_acces_a_zero.php','_parent','');\" value='Supprimer Problème' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";
	}
	?>
</font>
</TR></TD></TABLE>
</td>
</tr></table>   <BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Message Urgent</font></b></td></tr>
<tr id='cadreCentral0' ><td > <p align="left"><font color="#000000">
<TABLE  width=100%  border=0 >
<TR><TD align=top>
<br><font color="#000000" size="1" class=T1>
<!-- // debut de la saisie -->
<?php
print "<ul><u><font class=T2>Message Système</font></u></ul><br>";
$fichier="../data/bug_report.txt";
if (file_exists($fichier)) {
             $fichier=fopen($fichier,"r");
             $donnee=fread ($fichier, 10000);
             $donnee=stripslashes($donnee);
             $donnee=nl2br($donnee);
      print $donnee;
     fclose($fichier);
}

print "<div align=right><input type=button onclick=\"open('bug_a_zero.php','_parent','');\" value='Supprimer Messages' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>";

            ?>
<!-- // fin de la saisie -->
</font></TD></TR></TABLE>
</td></tr></table> <BR><BR><BR>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
