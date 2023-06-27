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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE27?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<BR>
<center>
<font class=T2><?php print LANGDONENR?></font>
</center><br>
<!-- // fin  -->
<?php
$id=$_POST[saisie_id];
$id_eleve="saisie_id_eleve_".$id;
$motif="saisie_motif_".$id;
$certif="saisie_certif_".$id;
$date_debut="saisie_date_debut_".$id;
$date_fin="saisie_date_fin_".$id;
$matiere="saisie_matiere_".$id;
$heure0="saisie_heure_".$id."_0";
$jour0="saisie_jour_".$id."_0";
$heure1="saisie_heure_".$id."_1";
$jour1="saisie_jour_".$id."_1";
$heure2="saisie_heure_".$id."_2";
$jour2="saisie_jour_".$id."_2";

$id_eleve=$_POST[$id_eleve];
$certif=$_POST[$certif];
$motif=$_POST[$motif];
$date_debut=$_POST[$date_debut];
$date_fin=$_POST[$date_fin];
$matiere=$_POST[$matiere];
$heure0=$_POST[$heure0];
$jour0=$_POST[$jour0];
$heure1=$_POST[$heure1];
$jour1=$_POST[$jour1];
$heure2=$_POST[$heure2];
$jour2=$_POST[$jour2];

if ($certif == "") { $certif="false"; }
/*
print $id_eleve."<BR>";
print $motif."<BR>";
print $certif."<BR>";
print $date_debut."<BR>";
print $date_fin."<BR>";
print $matiere."<BR>";
print $heure0."<BR>";
print $jour0."<BR>";
print $heure1."<BR>";
print $jour1."<BR>";
print $heure2."<BR>";
print $jour2."<BR>";
*/
if ($jour1 == "0") {$jour1="";}
if ($jour2 == "0") {$jour2="";}
if (strtolower($motif) == strtolower(LANGINCONNU)) { $motif="inconnu"; }
$cr=create_dispence($id_eleve,$matiere,dateFormBase($date_debut),dateFormBase($date_fin),dateDMY(),$_SESSION["nom"],$certif,$motif,$heure0,$jour0,$heure1,$jour1,$heure2,$jour2);
if($cr == 1){
//       alertJs("Donnée enregistrée -- Service Triade");
}
else {
     error(0);
}

?>
<!-- // fin  -->
</td></tr></table>
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
<?php
Pgclose();
?>
</BODY></HTML>
