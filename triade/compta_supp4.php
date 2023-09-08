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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
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
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Gestion d'un encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<br><br>
<?php

if (isset($_POST["supp"])) {
   if ( (isset($_POST["ideleve"])) && (isset($_POST["modepaiement"])) && (isset($_POST["montant"])) && (isset($_POST["typeversement"])) && (isset($_POST["dateversement"]))){
		$typeversement=$_POST["typeversement"];
		$montant=$_POST["montant"];
		$dateversement=$_POST["dateversement"];
		$ideleve=$_POST["ideleve"];
		$cr=suppVersementEleve($typeversement,$montant,$dateversement,$ideleve);
		if ($cr) {
			print "<center><font class='T2'>"."Données supprimées"."</font></center>";
			$nomeleve=recherche_eleve_nom($ideleve);
			history_cmd($_SESSION["nom"],"SUPPRESSION","Encaissement $nomeleve");
		}else{
			print "<center><font class='T2'>Encaissement déjà supprimé pour cette même date.</font></center>";
		}
	}else{
		print "<center><font class='T2'>Données incomplètes.</font></center>";
	}
}
?>
<br><br>
<table align='center'>
	<tr>
	<td><script language=JavaScript>buttonMagicRetour2("compta_supp2.php?ideleve=<?php print $_POST["ideleve"] ?>","_parent","Autre suppression")</script></td>
	<td><script language=JavaScript>buttonMagicRetour2("comtpa_ajout.php","_parent","Encaissement sur autre élève")</script></td>
	<td><script language=JavaScript>buttonMagicRetour2("comptavers.php","_parent","Quitter")</script></td>
	</tr>
</table>

<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
