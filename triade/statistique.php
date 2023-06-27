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
<?php
include_once("./common/lib_ecole.php");
include_once("./common/lib_admin.php");
include_once("./common/config.inc.php");
include_once("./".REPADMIN."/librairie_php/lib_error.php");
include_once("./".REPADMIN."/librairie_php/mactu.php");
if (empty($_SESSION["admin1"])) {
    print "<script language='javascript'>";
    print "location.href='/".REPECOLE."/".REPADMIN."/acces_refuse.php'";
    print "</script>";
    exit;
}
?>
<script>var largeurfen='1024'</script>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade</title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart.js"></SCRIPT>
<?php include("./".REPADMIN."/librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Statistique de l'établissement</font></b></td></tr>
<tr  id='cadreCentral0'><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<br /><br />
<table border=1 width=80% align=center bgcolor="#ffffff" style="border-collapse: collapse;" >
<tr>
<td><font class=T1>Information Nav., Version, OS, Langue </font></td>
<td width=10%>  <input type=button value="Cliquez ici" onclick="open('statistique_nav.php','_parent','')"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" ></td></tr>
<tr>
<td><font class=T1>Information type de connection </font></td>
<td><input type=button value="Cliquez ici" onclick="open('statistique_debit.php','_parent','')"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" ></td></tr>
<tr>
<td><font class=T1>Information Connection par heure </font></td>
<td><input type=button value="Cliquez ici" onclick="open('statistique_conc_heure.php','_parent','')"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" ></td></tr>
<tr>
<td><font class=T1>Information Utilisateur enregistré</font></td>
<td><input type=button value="Cliquez ici" onclick="open('statistique_conc_utilisateur.php','_parent','')"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" ></td></tr>
<tr>
<td><font class=T1>Information type d'écran </font></td>
<td><input type=button value="Cliquez ici" onclick="open('statistique_ecran.php','_parent','')"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" ></td>
</tr></table>
<br /><br />
<!-- // fin de la saisie -->
</blockquote>

<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_GET["limit"])) {
	$data=trace_aff('0');
}else{
	$data=trace_aff('400');
}
// nom,ip,date,heure,os,navigateur,membre
$date=dateDMY();
$date=datemoinsn($date,365) ;
print "&nbsp;&nbsp;<i>(Les informations de connexion sont conservées pendant <b>UNE</b> année.)</i> <br><br> &nbsp;&nbsp;Les 400 dernieres connexions.<br><br>";
print "<table width=100% align=center border=1 bgcolor='#FFFFFF' style='border-collapse: collapse;' >";
print "<tr bgcolor='yellow'><td align=center>Date</td><td align=center>Nom - Prénom</td><td align=center>Adresse IP</td><td align=center>System - Navigateur</td></tr>";
for($i=0;$i<count($data);$i++) {
	print "<tr >";
	print "<td width=5 align=center >".dateForm($data[$i][2])."<br>&nbsp;".$data[$i][3]."</td>";
	print "<td>&nbsp;".$data[$i][0]."<br>&nbsp;".$data[$i][6]."</td>";
	print "<td>&nbsp;".$data[$i][1]."</td>";
	print "<td>&nbsp;".$data[$i][4]." - ".$data[$i][5]."</td>";
	print "</tr>";
}
print "</table>";
print "<br><br>";
?>
<?php
if ( ! isset($_GET["limit"])) {
?>
<script language=JavaScript>buttonMagic("Liste complete depuis le <?php print dateForm($date) ?>","statistique.php?limit","_self","","");</script>
<br><br><br>
<?php } ?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart2.js"></SCRIPT>
</body>
</html>
