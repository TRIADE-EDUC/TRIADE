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
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method='post' name="formulaire" action='modifier_groupe.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Modifier Année Scolaire du groupe" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
if (isset($_POST["modif"])) {
	modifAnneeScolaireGroupe($_POST["idgroupe"],$_POST["annee_scolaire"]);
}
$idgroupe=$_GET["gid"];
if (isset($_POST["idgroupe"])) $idgroupe=$_POST["idgroupe"]; 
$data=recupInfoGroupe($idgroupe); // group_id,libelle,annee_scolaire 
$anneeScolaire=$data[0][2];
$libelle=$data[0][1];
?>
<ul><BR>
<font class=T2><?php print LANGGRP1?> : <?php print $libelle ?> </font> 
<br><br>
<font class="T2"><?php print LANGBULL3?> : </font>
<input type='hidden' name="idgroupe" value="<?php print $idgroupe?>" />
<select name="annee_scolaire" size="1">
<?php
filtreAnneeScolaireSelectNote("$anneeScolaire",3); // creation des options
?>
</select>
<br>
<BR><BR><UL>
<script language=JavaScript>buttonMagic("<?php print LANGBT12?>","liste_groupe.php","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","modif"); //text,nomInput</script>
</form>
</ul>
<?php 
if (isset($_POST["modif"])) {
	print "<br><br><br>";
	print "<center><font class='T2'>".LANGDONENR."</font></center>";
}
?>
<br><br><br>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
<?php Pgclose(); ?>
