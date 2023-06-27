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
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGVIES8 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br><br>
     <!-- // fin  -->
<form method="post" name="formulaire" action="cumul_rtd_impr2.php" onsubmit="return validrtdcumul();">
<table width="100%" border="0" align="center">
<tr>
<td align="right"><font class="T2"><?php print LANGVIES9  ?> :</font></td>
<td colspan="2"   align="left">
<select name="saisie_mois" >
<option value="00" id="select0" ><?php print LANGCHOIX ?></option>
<option value="01" id="select1" ><?php print LANGMOIS1 ?></option>
<option value="02" id="select1" ><?php print LANGMOIS2 ?></option>
<option value="03" id="select1" ><?php print LANGMOIS3 ?></option>
<option value="04" id="select1" ><?php print LANGMOIS4 ?></option>
<option value="05" id="select1" ><?php print LANGMOIS5 ?></option>
<option value="06" id="select1" ><?php print LANGMOIS6 ?></option>
<option value="07" id="select1" ><?php print LANGMOIS7 ?></option>
<option value="08" id="select1" ><?php print LANGMOIS8 ?></option>
<option value="09" id="select1" ><?php print LANGMOIS9 ?></option>
<option value="10" id="select1" ><?php print LANGMOIS10 ?></option>
<option value="11" id="select1" ><?php print LANGMOIS11 ?></option>
<option value="12" id="select1" ><?php print LANGMOIS12 ?></option>
</select>

</td>
</tr>
<tr><td height=10></td></tr>
<?php 
$annee00=date("Y");
$annee01=date("Y")+1;
$annee02=date("Y")-1;
?>
<td align="right"><font class="T2"><?php print "Indiquer l'année"  ?> :</font></td>
<td colspan="2"   align="left">
<select name="saisie_annee" >
<option  id="select0" ><?php print LANGCHOIX ?></option>
<option value="<?php print $annee01?>" id="select1" ><?php print $annee01 ?></option>
<option value="<?php print $annee00?>" id="select1" ><?php print $annee00 ?></option>
<option value="<?php print $annee02?>" id="select1" ><?php print $annee02 ?></option>
</select>

</td>
</tr>

<tr>
<td ><div align="right"><br><font class="T2"><?php print LANGVIES10 ?> : </font></div></td>
<td colspan="2" ><br>
<select name="saisie_classe">
<option selected value=0    STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
<option value=-10  STYLE='color:#000066;background-color:#FCE4BA' ><?php print "Toutes les classes" ?></option>
<?php select_classe2(25);?>
</select>
</td></tr>
</table>
<br>
<table border=0 align=center><tr><td>
<script language=JavaScript>buttonMagicRetour('gestion_abs_retard.php','_parent') </script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGaffec_cre41 ?>","rien",""); </script></td></tr></table>
</form>
<br>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if ($_SESSION[membre] == "menuadmin") :
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
