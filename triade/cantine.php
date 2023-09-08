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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Gestionnaire de cantine" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
include_once('./librairie_php/db_triade.php');
$cnx=cnx();
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
?>

<!-- // fin  -->
<br><br>
<table border=0 align=center width=100%>

<tr>
<form action='cantine_nb_passage.php'  method='post'>
<td align=right><font class=T2><?php print "Nombre de passage aujourd'hui" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<td align=right><font class=T2><?php print "Passage cantine" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagic("<?php print LANGBREVET1 ?>","cantine_passage.php","cantine","width=800,height=600,resizable=yes,scrollbars=yes","")</script>&nbsp;&nbsp;</td>
</tr>
<tr><td height='20'></td></tr>
<tr>
<form action='cantine_config.php'  method='post'>
<td align=right><font class=T2><?php print "Configuration des repas" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td height='20'></td></tr>

<tr>
<form action='regime_ajout.php'  method='post'>
<td align=right><font class=T2><?php print "Configuration des régimes" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td height='20'></td></tr>

<tr>
<form action='regime_affectation.php'  method='post'>
<td align=right><font class=T2><?php print "Affectation des régimes" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='cantine_compta.php'  method='post'>
<td align=right><font class=T2><?php print "Comptabilité d'un compte" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1 ?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
<td></td></tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='cantine_export.php'  method='post'>
<td align=right><font class=T2><?php print "Exportation des donn&eacute;es " ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1 ?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
<td></td></tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='cantine_stat.php'  method='post'>
<td align=right><font class=T2><?php print "Statistique / Comptage" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php  print LANGBREVET1 ?>","rien"); //text,nomInput</script>&nbsp;&nbsp;</td>
<td></td></tr>
</form>
</table>

<?php 
}else{
	accesNonReserve();
} 
?>

<br><br>
<!-- // fin  -->
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
