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
        $cnx=cnx();
        ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS6 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
$data=$_POST["saisie_nb"];
for($j=0;$j<$data;$j++) {
	$info=$_POST["saisie_info"][$j];
	$typesaisie="saisie_$info";
	$duree="saisie_duree_$info";
	$motif="saisie_motif_$info";
	$heure="saisie_heure_$info";
	$justifier="saisie_justifier_$info";

	$departideleve=trim($_POST["departideleve"][$j]);
    	$departdatesaisie=trim($_POST["departdatesaisie"][$j]);
	$departdatertd=trim($_POST["departdatertd"][$j]);
	$departheurertd=trim($_POST["departheurertd"][$j]);

	$typesaisie=$_POST[$typesaisie];
	$duree=trim($_POST[$duree]);
	$motif=trim($_POST[$motif]);
	$heure=trim($_POST[$heure]);
	$justifier=$_POST[$justifier];

	if ($typesaisie == "100")  {
		suppression_retard($departideleve,$departheurertd,$departdatertd);
	}

	$heuredoriginsaisie=$_POST["heuredatesaisie"][$j];
	$heuredoriginret=$_POST["dateorigineret"][$j];



	if ($motif == "") {$motif="Inconnu"; }
	if ($duree == "???") { continue; }
	if ($motif == "Inconnu") { continue; }

	$user=$_SESSION["nom"];
	$date_de_saisie=date("Y-m-d");
	$refRattrapage="";
	if ($typesaisie == "retard") {
		modif_retard($departideleve,$departheurertd,$departdatertd,$duree,$motif,$date_de_saisie,$user,$justifier,$heuredoriginret,$heuredoriginsaisie,$refRattrapage);
	}
	if ($typesaisie == "absent") {
		//$duree,$date,$saisie_pers,$date_saisie,$user,$motif
		 $cr=create_absent($duree,dateForm($departdatertd),$departideleve,$date_de_saisie,$user,$motif,-1,$justifier,$heuredabsence,'','',$refRattrapage);
		 if ($cr) {
		 	suppression_retard($departideleve,$departheurertd,$departdatertd);
		 }


	}

//	print "$typesaisie $duree $motif $heure / $departideleve $departdatesaisie $departdateabs <br>";

}
?>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<font class="T2"><?php print LANGABS66 ?>.</font><br><br>
<center><input type=button onclick="open('liste_rtd.php','_parent','');" value="<?php print LANGABS6bis ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></center>
<br><br>
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
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
