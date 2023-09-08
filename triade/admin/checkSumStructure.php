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
<title>Triade</title>
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/valide.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Vérification et optimisation de la Base</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br>
<div id="infopatch" align="center"></div>
<script language="JavaScript" src="https://support.triade-educ.org/support/version-patch.php?v=<?php print VERSIONPATCH ?>"></script>

<?php
include_once("./librairie_php/db_triade_admin.php");
if (LAN == "oui") {
	$cnx=cnx();
?>
<ul>
<font class='T2'>Structure de votre base de donnée : </font>
<br><br>
<form method="post" action="listchecksum2.php"  name="formulaire" >
<?php
print "<textarea cols=60 rows=20  STYLE=\"font-family: Arial;font-size:12px;background-color:#FCE4BA;\" name='liste' readonly='readonly'>";
$fichier="../data/dump/structure.sql";
$fichier=fopen($fichier,"r");
$donnee=fread($fichier,900000);
print $donnee;
fclose($fichier);
print "</textarea>";

?>
<br /><br />
</form>
<br /><br />
<?php
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
<?php Pgclose() ?>
<script language="JavaScript">
if (update == 1) { 
	document.getElementById("infopatch").innerHTML="<font color=red><b>ATTENTION, VOUS DEVEZ PATCHER VOTRE TRIADE,<br /> AVANT D'EFFECTUER CETTE OPERATION, <br />PATCH DISPONIBLE : <a href='update.php'><font color=red>MODULE \"Triade update\"</font></a></b></font>"; 
	document.formulaire.liste.disabled=true;
}else{
	document.getElementById("infopatch").innerHTML="<font class=T2>Envoyer la structure au support (support@triade-educ.org) <br />en indiquant votre version Triade, et votre N° de patch.</font>";

}
</script>


</html>
