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
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="125">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Paiement vacation enseignant" ?></font></b></td></tr>
<tr id='cadreCentral0'><td ><br><br>
<?php
if ($_POST["idprof"] > 0) {
	$idprof=$_POST["idprof"];
	$dateDebut=$_POST["saisie_date_debut"];
	$dateFin=$_POST["saisie_date_fin"];
	$infopaiement=stripslashes($_POST["infopaiement"]);
	$montantHT=$_POST["montantHT"];
	$montantTTC=$_POST["montantTTC"];
	$idpiecejointe=$_POST["idpiecejointe"];
	$tva=$_POST["tva"];
	$nomprenom=recherche_personne($idprof);
	paiementVacation($idprof,$dateDebut,$dateFin,$infopaiement,$montantHT,$montantTTC,$tva,$idpiecejointe);
	history_cmd($_SESSION["nom"],"PAIEMENT","Effectué à $nomprenom");
	$nomprenom=recherche_personne($idprof);
	?>
	<center><font class='T2'>Paiement pour <?php print $nomprenom ?> effectué</font></center>
	<br><br>
<?php } ?>
<table align='center'><tr><td>
<script type="text/javascript" >buttonMagicRetour("gestion_vacation.php","_parent")</script>
</td></tr></table>


<br>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
<?php Pgclose(); ?>

