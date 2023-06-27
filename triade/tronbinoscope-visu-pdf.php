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
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"trombinoscopeRead")){
		validerequete("2");
	}
}else{
	validerequete("2");
	$visu=1;
	$visu2=1;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post  name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE32?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<blockquote><BR>
<table border=0>
<tr><td><font class="T2"><?php print LANGELE4?> :</font> <select id="saisie_classe" name="saisie_classe">
                         <option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select></td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
</td></tr>
<tr><td height=20></td></tr>
<tr><td><font class="T2"><?php print "Régime"?> :</font> <select id="saisie_regime" name="saisie_regime">
                         <option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_regime(); // creation des options
?>
</select></td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consultregime"); //text,nomInput</script>
</td></tr>
</table>
<br><br>
<?php
if ((isset($_POST["consult"]))  && ($_POST["saisie_classe"] != 0)) { 
	$nomclasse=chercheClasse_nom($_POST["saisie_classe"]);

?>
<script language=JavaScript>buttonMagic("<?php print "Accès au PDF de la classe $nomclasse " ?>","tronbinoscope-impr-pdf.php?idclasse=<?php print $_POST["saisie_classe"]?>","impr","width=800,height=600,scrollbars=yes,menubar=yes","") </script>&nbsp;&nbsp;
<?php } 

if ((isset($_POST["consultregime"])) && ($_POST["saisie_regime"] != "0" ))  {
	$nomregime=$_POST["saisie_regime"];
	if ($_POST["saisie_regime"] > 0) $nomregime=rechercheNomRegime($_POST["saisie_regime"]);
?>
<script language=JavaScript>buttonMagic("<?php print "Accès au PDF du régime" ?>","tronbinoscope-impr-pdf.php?nomregime=<?php print $nomregime ?>","impr","width=800,height=600,scrollbars=yes,menubar=yes","") </script>&nbsp;&nbsp;
<?php } ?>
</UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>
<!-- // fin form -->
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
</BODY>
</HTML>
