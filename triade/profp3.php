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
        <script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
        <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
        <script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
        <script language="JavaScript" src="./librairie_js/function.js"></script>
        <script language="JavaScript" src="./librairie_js/lib_css.js"></script>
        <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
verif_profp_eleve($_GET['eid'],$_SESSION["id_pers"],$_SESSION["membre"]);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<?php
$id_eleve=$_GET["eid"];
$nomE=recherche_eleve_nom($id_eleve);
$prenomE=recherche_eleve_prenom($id_eleve);
$idClasse=chercheIdClasseDunEleve($id_eleve);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=2 ><b><font   id='menumodule1' >
<B><?php print LANGPROFP5 ?> <font id="color2"><?php print recherche_eleve($id_eleve);?></font></B></font></td></tr>
<tr id='cadreCentral0'><td>
<br>
&nbsp;&nbsp;&nbsp;
<input type=button class=BUTTON value="<-- <?php print LANGPRECE?>" onclick="open('profplisteeleve.php?sClasseGrp=<?php print $idClasse?>','_parent','')">
	<br><br>
	<div align=center>
	<table border="0" width="70%" height="100">
	<tr><td id='bordure' align="right" ><font class="T2"><?php print LANGPROF27?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","profpficheeleve.php?eid=<?php print $id_eleve?>","_parent","","");</script>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

	<tr><td id='bordure' align="right" ><font class="T2"><?php print LANGCARNET1 ?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","profpcarnet.php?Seid=<?php print $id_eleve?>&Scid=<?php print $idClasse?>&Snom=<?php print urlencode($nomE)?>&Sprenom=<?php print urlencode($prenomE)?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

	<tr><td id='bordure' align="right" ><font class="T2"><?php print LANGPROF38 ?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","profpprojo.php?idClasse=<?php print $idClasse?>","video","width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>


	<tr><td id='bordure' align=right>&nbsp;&nbsp;<font class="T2"><?php print "Absences et retards" ?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","gestion_abs_retard_donne.php?Seid=<?php print $id_eleve?>","_parent","","");</script>
	</td></tr>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

	<tr><td id='bordure' align="right" ><font class="T2"><?php print LANGPROF39 ?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","profpcomplement.php?eid=<?php print $id_eleve?>","_parent","","");</script>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>

	<tr><td id='bordure' align="right" ><font class="T2"><?php print LANGPROF28 ?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","profpviescol.php?eid=<?php print $id_eleve?>","_parent","","");</script>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php if ( (defined("INFOMEDIC2")) && (INFOMEDIC2 == "oui")) { ?>
<tr><td id='bordure' align="right" ><font class="T2"><?php print LANGPROF29 ?> : </font></td>
	<td id='bordure'>
	<script language=JavaScript>buttonMagic("cliquez-ici","profpmedic.php?eid=<?php print $id_eleve?>","_parent","","");</script>
	<tr><td colspan=2></td></tr>
	<tr><td colspan=2></td></tr>
<?php } ?>
	</table>
	</div>
	<br><br>
</td></tr>
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
<?php @Pgclose() ?>
</BODY>
</HTML>
