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
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >TRIADE-ANALYTICS</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top >
<!-- // debut de la saisie -->
<?php
if (LAN == "oui") {
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade_admin.php");
	$cnx=cnx();
?>
<table ><tr><td valign='top' ><br />
<font class="T2" />
TRIADE-ANALYTICS vous permet d'avoir un retour des différents modules 
utilis&eacute;s. Il analyse le trafic de votre TRIADE et fournit 
des rapports clairs et précis au sujet de vos visiteurs, vous informant 
de leur provenance, de leur fréquentation du site. <br>
<br>
Nous utilisons la plateforme Matomo. Une solution Entièrement conforme
au RGPD et en tant que leader dans le traitement éthique des données,
Matomo propose un outil d'analytique Web qui protège les internautes et leur vie privé
 tout en étant similaire &agrave; GA3 dans son utilisation.

</td><td>
<img src="image/intro_small_new.jpg" align=right />
</td></tr></table>
<br><br><center>
<?php 
$IDGOOGLE=recupcomptegoogleanalytic();

if (verifcomptegoogleanalytic()) {
	print "<font class=T2>";
	print "<a href='https://analytics.triade-educ.net' target='_blank'>";
	print "Acc&egrave;s &agrave; TRIADE-ANALYTICS";
	print "</a></font>";

}else{
	?>
	<font class=T2><a href="https://www.triade-educ.org/accueil/triade-analytics.php" target="_blank" >Inscrivez-vous dès maintenant : c'est facile et gratuit !</a></font>
	<?php } ?>
</center>
<br>
<br />   
<hr>
<font class=T2>
Une fois votre compte créé vous pourrez effectuer la demande d'analyse de votre site TRIADE.
Il vous suffira de consulter vos emails, et de suivre les instructions que l'on vous demande.<br>
<br>
Un num&eacute;ro de code vous sera retourn&eacute;.
<br><br>
<form method='post' action="triade-analytics2.php" />
Indiquer ID-ANALYTICS que vous avez re&ccedil;u par mail : <input type=text size=5 name="idref" value="<?php print $IDGOOGLE ?>"  /> <input type=submit value="Valider" class="button" name="creategoogle" />
</form>

</font>


</font>
<?php
	PgClose();
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR2."</i></center>";
}
?>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
