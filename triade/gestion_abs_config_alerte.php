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
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("2");
// connexion P

$cnx=cnx();
if (isset($_POST["create"])) {
	$libelle="alertNbAbs";
	$valeur=$_POST["nbabs"];
	if ($valeur == "0") {
		supp_parametrage($libelle);
	}else{
		enr_parametrage($libelle,$valeur);
	}
	$libelle="alertNbRtd";
	$valeur=$_POST["nbrtd"];
	if ($valeur == "0") {
		supp_parametrage($libelle);
	}else{
		enr_parametrage($libelle,$valeur);
	}

	if (!empty($_POST["saisie_liste"])) {
		$idliste=join(",",$_POST["saisie_liste"]);
		enr_parametrage('alertAbsMail',"\{$idliste}");	
		alertJs(LANGDONENR);
	}
}

if (isset($_GET["supplist"])) {
	supp_parametrage('alertNbAbs');
	supp_parametrage('alertAbsMail');
	supp_parametrage('alertNbRtd');
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Gestion des alertes absences et retards"?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<form method="post" action="gestion_abs_config_alerte.php">
<BR>
&nbsp;&nbsp;<font class=T2><?php print LANGCONFIG4 ?> : <br>
<br>&nbsp;&nbsp;<?php print LANGCONFIG5 ?> : 
<select name="nbabs">
<?php 
$val=aff_enr_parametrage("alertNbAbs"); 
if (trim($val[0][1]) != "") {
?>
	<option value='<?php print $val[0][1] ?>' id='select1' ><?php print $val[0][1] ?></option>  
<?php } ?>
<option value='0' id='select0' >0</option>  
<option value='3' id='select0' >3</option>  
<option value='5' id='select0'>5</option>  
<option value='10' id='select0'>10</option>  
<option value='15' id='select0'>15</option>  
<option value='20' id='select0'>20</option>  
<option value='25' id='select0'>25</option>  
</select> <?php print LANGCONFIG7 ?>
<br>
<br>

&nbsp;&nbsp;<?php print LANGCONFIG6 ?> : 
<select name="nbrtd">
<?php 
$val=aff_enr_parametrage("alertNbRtd"); 
if (trim($val[0][1]) != "") {
?>
	<option value='<?php print $val[0][1] ?>' id='select1' ><?php print $val[0][1] ?></option>  
<?php } ?>
<option value='0' id='select0' >0</option>  
<option value='3' id='select0' >3</option>  
<option value='5' id='select0'>5</option>  
<option value='10' id='select0'>10</option>  
<option value='15' id='select0'>15</option>  
<option value='20' id='select0'>20</option>  
<option value='25' id='select0'>25</option>  
</select> <?php print LANGCONFIG7 ?>
<br>
<br>

&nbsp;&nbsp;Avertir les utilisateurs suivants : 
<br><br>
<center>
<table width=100% border="0">
<TR><TD>&nbsp;&nbsp;
<select align=top name="saisie_liste[]" size=20  style="width:190px" multiple="multiple">
<?php
print "<optgroup label='".LANGGEN1."'>";
select_personne('ADM');
print "<optgroup label='".LANGGEN2."'>";
select_personne('MVS');
print "<optgroup label='".LANGGEN3."'>";
select_personne('ENS');
?>
</select>
</TD>
<TD valign=top align=center>
<TABLE border="1" width=80% bordercolor="#000000"  style='border-collapse: collapse;' >
<TR><TD bgcolor="#FFFFFF">
<?php print LANGMESS25?> <font color=red><B><?php print LANGGRP4?></b></font> <?php print LANGGRP5?><BR>  <BR>
</td></tr></table><br>
<div align="left">
<font class=T2>
&nbsp;&nbsp;&nbsp;<u><?php print LANGCONFIG8 ?></u> :<br><br>
</font><font class=T1>
<?php 
$val=aff_enr_parametrage("alertAbsMail"); 
$data=liste_idpers_grp_mail($val[0][1]);
for($i=0;$i<count($data);$i++) {
	if ($data[$i] != "") {
		print "&nbsp;&nbsp;&nbsp;&nbsp;<img src='image/commun/on1.gif' width='8' height='8' /> ".trunchaine(recherche_personne($data[$i]),30);
		print "<br>";
	}
}
?>
</font>
<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[ <a href="gestion_abs_config_alerte.php?supplist">Supprimer la liste</a> ]
</div>
</td></tr>

</table>
<br>
<br>

<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
</td></tr></table>
</form>



<BR><BR>
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

   Pgclose();
     ?>
</BODY></HTML>
