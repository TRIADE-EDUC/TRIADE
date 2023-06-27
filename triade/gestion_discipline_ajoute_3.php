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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC14?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<BR>
<center>
<?php print LANGDONNEENR ?>
</center>
<BR>
     <!-- // fin  -->
<?php

$sanction=$_POST["saisie_sanction"];
$motif="Cumul de la sanction :".rechercheSanction($sanction);
$qui=$_SESSION["nom"];
$id=$_POST["saisie_id"];
/*
print $sanction;
print "<Br>";
print $motif;
print "<Br>";
print $qui;
print "<Br>";
print $id;
*/
for ($i=0;$i<=$id;$i++) {

	$choisi="saisie_choisi_".$i;
	$choisi=$_POST[$choisi];
	$id_eleve="saisie_eleveid_".$i;
	$id_eleve=$_POST[$id_eleve];
	$en_retenue="saisie_retenu_".$i;
	$en_retenue=$_POST[$en_retenue];
	if ($en_retenue == "1" ) {

		$date_retenue="saisie_date_retenue_".$i;
		$date_retenue=$_POST[$date_retenue];
		$heure_retenue="saisie_heure_retenue_".$i;
		$heure_retenue=$_POST[$heure_retenue];
		$duree_retenue="saisie_duree_retenue_".$i;
		$duree_retenue=$_POST[$duree_retenue];



		$cr=create_discipline_retenue($id_eleve,dateFormBase($date_retenue),$heure_retenue,dateDMY2(),$_SESSION[nom],$sanction,$qui,$motif,$duree_retenue);
       	if($cr){
 			modif_discipline_sanction_2($id_eleve,$_SESSION["nom"],dateDMY2(),$sanction);
       	}else {
            print "<script language=JavaScript>location.href='gestion_discipline.php?err=1&id=$id_eleve'</script>";
       	}





	}
}
?>
     <!-- // fin  -->
     </td></tr></table>
     </form>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
