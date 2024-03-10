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
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
		
	</head>
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
	<script type="text/javascript" src="./librairie_js/messagerie_fenetre.js"></script>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Soutien Scolaire avec elanel.fr" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td valign='top' >
	<!-- // fin  -->
	&nbsp;&nbsp;&nbsp;&nbsp;<a href='http://www.elanel.fr' target='_blank'><img src='./image/elanel/logo.png' border='0'/></a>


	<ul>
	<table>
	<tr><td valign='top'><img src="./image/elanel/Browses1.png" /></td><td><font class='T2' color='blue'>S'entrainer</font><br /><br /><font class='T2'>Plus de 100 exercices interactifs de Maths, Français, Physique, Histoire et Philosophie.</font></td></tr>
	<tr><td height='20'></td></tr>
	<tr><td valign='top'><img src="./image/elanel/Browses1.png" /></td><td><font class='T2' color='blue'>S'informer et S'orienter</font><br /><br /><font class='T2'>50 métiers sous forme de fiches et animations, conseils et tests d'auto-évaluation, repères économiques, historiques et artistiques.</font></td></tr>
	<tr><td height='20'></td></tr>
	<tr><td valign='top'><img src="./image/elanel/Browses1.png" /></td><td><font class='T2' color='blue'>S'appuyer sur l'expérience</font><br /><br /><font class='T2'>Les contenus sont rédigés par 6 professeurs de l'Éducation nationale, 2 journalistes et 1 conseillère en orientation.</font></td></tr>
	</table>
	</ul>

	<center><font class='T2'><b>Un seul abonnement pour toute la famille !!</b></font></center>
	<br /><br />
	<table width='100%' border=0>
	<tr><td align='center'>
	<table><tr><td><script language=JavaScript>buttonMagic2("Demande d'inscription","http://www.elanel.fr/Creer-un-compte.html","_blank","","")</script></td></tr></table><br><br>
	<table><tr><td><script language=JavaScript>buttonMagic2("Connexion à votre compte Elanel","http://www.elanel.fr/Se-connecter.html","_blank","","")</script></td></tr></table></td>
	<td width='30%'><img src="image/elanel/offre.gif" border='0' /></td></tr></table>
	
	<br />
	<?php
	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
		print "</SCRIPT>";
	}
	
?>
</BODY></HTML>
