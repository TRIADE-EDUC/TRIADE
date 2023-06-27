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
error_reporting(0);
if (isset($_POST["saisieecole"])) :
	if ($_POST["saisieecole"] != '0' ) :
    print "<script language=JavaScript>open('../statistique.php','_parent','');</script>";
 endif ;
endif ;
  ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Statistiques des établissements</font></b></td></tr>
<tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<center>
<FORM method=POST>
<table border=0 align=center>
<tr><td>
<font class=T2>Statistique de l'établissement :</font>
<input type=hidden name='saisieecole' value='<?php print REPECOLE?>'>
</td><td>
<script language=JavaScript>buttonMagicSubmit("Consulter","rien"); //text,nomInput</script>
</td></tr></table>
</FORM>

<center>
<!-- // fin de la saisie -->
</td></tr></table>

<br /><br />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Statistiques - Compteur</font></b></td></tr>
<tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<center>
<?php
include_once('librairie_php/db_triade_admin.php');
include_once("./librairie_php/lib_statistique_valeur.php");
$affiche=resultat_count("compteur_acces.txt");
$affiche_time=resultat_time("compteur_acces.time");
$cnx=cnx();
?>
<table border=1 width=100% bgcolor="#FFFFFF" bordercolor="#000000">
<tr><td align=center bgcolor="yellow"><b>Page analysée</b></td><td align=center width=15% bgcolor="yellow"><b>Nb d'accès</b></td><td align=center bgcolor="yellow"><b>Date dernière accès</b></td></tr>
<tr><td colspan=2></td></tr>
<tr><td align="right"><font class=T1>Consultation accès  :</font> </td><td ><?php print $affiche?></td><td align=center><font class=T1><?php print $affiche_time?></font>
</td></tr></table>
<br><br>
</center>
<ul>Analyse du temps d'execution des pages : </ul>

<?php
$data=analyse_page();
// file,time_max,time_min
print "<table border=1 bgcolor='#FFFFF' width=100% bordercolor='#000000' >";
print "<tr><td  bgcolor='yellow'><b>Fichier</b></td><td bgcolor='yellow'><b> Valeur Min</b></td><td bgcolor='yellow'><b> Valeur Max</b></td></tr>";
for($i=0;$i<count($data);$i++) {
	print "<tr>";
	print "<td width=5><input type=text value=\"".$data[$i][0]."\" size=30></td>";
	print "<td>".$data[$i][2]."</td>";
	print "<td>".$data[$i][1]."</td>";
	print "</tr>";
}
print "</table>";

?>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
