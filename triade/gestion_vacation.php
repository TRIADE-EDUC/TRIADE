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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Gestion des vacations" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<br><br>
<!-- // debut form  -->
<table align="center">
<tr>
<form action='gestion_vacation_config_periode.php' method="post" >
<td align=right><font class="T2"><?php print "Configuration de la période" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>

<tr><td height="20"></td></tr>

<tr>
<form action='gestion_vacation_ens.php' method="post" >
<td align=right><font class="T2"><?php print "Commande de vacation d'un enseignant" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<?php 
if ($_SESSION["membre"] == "menuadmin") { 
?>
<tr><td height="20"></td></tr>

<tr>
<form action='gestion_vacation_releve_ens.php' method="post" >
<td align=right><font class="T2"><?php print "Relevé de vacation d'un enseignant" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>

<tr><td height="20"></td></tr>

<tr>
<form action='gestion_vacation_paiement_ens.php' method="post" >
<td align=right><font class="T2"><?php print "Paiement de vacation d'un enseignant" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>





<tr><td height="20"></td></tr>


<tr>
<form action='gestion_vacation_config.php' method="post" >
<td align=right><font class="T2"><?php print "Configuration des prestations" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>

<tr><td height="20"></td></tr>

<tr>
<form action='gestion_vacation_horaire.php' method="post" >
<td align=right><font class="T2"><?php print "Ajustement horaire des prestations" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>

<tr><td height="20"></td></tr>

<tr>
<form action='gestion_entretient_enseignant.php' method="post" >
<td align=right><font class="T2"><?php print "Entretien des enseignants" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>

<tr><td height="20"></td></tr>

<tr>
<form action='gestion_statistique.php' method="post" >
<td align=right><font class="T2"><?php print "Tableau de statistique" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accèder"?>","rien"); //text,nomInput</script></td>
</form>
</tr>

<?php } ?>

</table>

<br><br>

<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
