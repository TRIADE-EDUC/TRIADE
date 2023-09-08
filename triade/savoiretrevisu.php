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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<?php 
include("./librairie_php/lib_licence.php"); 
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Savoir / être" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign='top' >
<!-- // debut form  -->
<table border='1' width='100%' style="border-collapse: collapse;" >
<tr >
<td bgcolor="yellow"><?php print "Date" ?></td>
<td bgcolor="yellow"><?php print "Enseignant" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à manifester de l'intérêt pour son travail" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à la méthode et au soin" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à écouter" ?></td>
</tr>
<?php
$anneeScolaire=anneeScolaireViaIdClasse($_SESSION["idClasse"]);
$dataInfo=recupSavoirEtre($_SESSION["id_pers"],$_SESSION["idClasse"],$anneeScolaire);
// ponctualite,motivation,dynamisme,id,date,idpers,idmatiere
for($j=0;$j<count($dataInfo);$j++) { 
	$ponct=stripslashes($dataInfo[$j][0]);
	$motiv=stripslashes($dataInfo[$j][1]);
	$dynam=stripslashes($dataInfo[$j][2]);
	$nommatiere=chercheMatiereNom($dataInfo[$j][6]);
	$id=$dataInfo[$j][3];
	if (($ponct == "") && ($motiv == "") && ($dynam == "")) {
		deleteSavoirEtre2($id); 
		continue; 
	}
	$id=$dataInfo[$j][3];
	$date=dateForm($dataInfo[$j][4]);
	$idpers=$dataInfo[$j][5];
        $personne=preg_replace('/ /','&nbsp;',recherche_personne2($idpers));

	$motiv=preg_replace('/"/',"&quot;",$motiv);
	$dynam=preg_replace('/"/',"&quot;",$dynam);
	$ponct=preg_replace('/"/',"&quot;",$ponct); 
	print "<tr bgcolor='#FFFFFF' >";
	print "<td width='10%' valign='top' ><font class='T1'>$date</font></td>";
	print "<td width='10%' valign='top' ><font class='T1'>$personne<br>Matière&nbsp;:&nbsp;$nommatiere</font></td>";
	print "<td width='30%' valign='top' ><font class='T2'>$ponct</font></td>";
	print "<td width='30%' valign='top' ><font class='T2'>$motiv</font></td>";			
	print "<td width='30%' valign='top' ><font class='T2'>$dynam</font></td>";
}
?>
</table>
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
