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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Relevé complet des absences d'une classe" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br><br>
     <!-- // fin  -->
<form method=post name=formulaire  action="releve_abs_rtd_classe2.php">
<table width="100%" border="0" align="center">
<tr>
<td align="right"><font class="T2"><?php print LANGDISC47 ?> :</font></td>
<td colspan="2"   align="left"><input type="text" value="" name="saisie_date_debut" TYPE="text" size=13  class=bouton2 onKeyPress="onlyChar(event)">
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0");
?>
</td>
</tr>
<tr>
<td  align="right"><br><font class="T2"><?php print LANGDISC48 ?> : </font></td>
<td colspan="2"  align="left"><br><input type="text" value="" name="saisie_date_fin" TYPE="text" size=13 class=bouton2 onKeyPress="onlyChar(event)">
<?php
 include_once("librairie_php/calendar.php");
 calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0");
?>
</td>
</tr>
<tr>
<td ><div align="right"><br><font class="T2"><?php print LANGDISC49 ?> : </font></div></td>
<td colspan="2" ><br>
<select name="saisie_classe">
<option  value=0 selected   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
<option  value="tous" selected   STYLE='color:#000066;background-color:#FCE4BA' ><?php print "Toutes les classes" ?></option>
<?php select_classe2(25);?>
</select>
</td></tr>
</table>
<br>
<table border=0 align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGPER27 ?>","rien",""); //text,nomInput</script><script language=JavaScript>buttonMagicRetour("gestion_abs_retard.php","_parent")</script>&nbsp;&nbsp;</td></tr></table>
</form>
<br>
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
