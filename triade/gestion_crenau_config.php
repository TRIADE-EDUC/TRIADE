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
// connexion P
$cnx=cnx();

if(isset($_POST["creat_creneau"])):
        $cr=create_creneau($_POST["saisie_intitule"],$_POST["saisie_depH"],$_POST["saisie_finH"]);
        if($cr != 1){
                alertJs("Créneau non créé, déjà en place -- Service Triade");
        }
endif;

if(isset($_POST["creat_supp"])):
        $cr2=supp_creneau($_POST["saisie_int_supp"]);
        if($cr2 == 1){ alertJs("Créneau supprimé -- Service Triade"); }
endif;

if (isset($_POST["creat_default"])) {
	if ($_POST["saisie_int_default"] != "aucun") { config_param_ajout($_POST["saisie_int_default"],"creneau"); }
	if ($_POST["saisie_int_default"] == "aucun") { supp_param_creneaux(); }
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Config. créneaux horaires "?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<BR>
<blockquote>
<font class="T2"><B><?php print "Enregistrer les créneaux horaires"?>.</B>
<form method=post >
<table border=0><tr><td>
<font class="T2"><?php print "Intitulé du créneau" ?> :</font></td><td>
<input type=text size=20 maxlength=20 name=saisie_intitule>
</td></tr>

<tr><td align=right><font class=T2>De : </font> </td> <td><input type=text size=5 maxlength=8 name='saisie_depH' onKeyPress="onlyChar2(event)" > <i>(hh:mm)</i> </td></tr>
<tr><td align=right><font class=T2>à : </font> </td> <td><input type=text size=5 maxlength=8 name='saisie_finH' onKeyPress="onlyChar2(event)" >  <i>(hh:mm)</i> </td></tr>
<tr><td colspan=2><br><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","creat_creneau"); //text,nomInput</script>
</td></tr></table>
</td></tr>

</td></tr></table>
</form>
<BR>
<hr>
<form method="post" >
<?php print "Liste des créneaux" ?> :
<select name="saisie_int_supp">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGPROJ13 ?></option>
<?php
select_creneaux();
?>
</select> <input type=submit name="creat_supp" value="Supprimer" class="button" >
</form>
<BR>
<form method="post" >
<?php print "Créneaux par défaut" ?> :
<select name="saisie_int_default">
<?php
$data=recupCreneauDefault("creneau"); // libelle,text
if (count($data) > 0) {
	print "<option id='select1' value='".$data[0][1]."' >".$data[0][1]."</option>";
	print "<option id='select0' value='aucun' >Aucun</option>";
}else{
	print "<option id='select0' value='aucun' >Aucun</option>";
}
select_creneaux();
?>
	</select> <input type=submit name="creat_default" value="<?php print VALIDER ?>" class="button" >
</form>
<BR>
<hr>
<table border=1 bgcolor="#FFFFFF" bordercolor="#000000" ><tr>
<td bgcolor='yellow'><font class=T2>&nbsp;Nom du créneau&nbsp;</font></td>
<td bgcolor='yellow'><font class=T2>&nbsp;Heure de départ&nbsp;</font></td>
<td bgcolor='yellow'><font class=T2>&nbsp;Heure de fin&nbsp;</font></td>
</tr>
<?php 
$data=affCreneaux(); // libelle, dep_h,fin_h 
for($i=0;$i<count($data);$i++) {
	print "<tr>";
	print "<td id='bordure'><font class=T2>&nbsp;".$data[$i][0]."&nbsp;</font></td>";
	print "<td id='bordure'><font class=T2>&nbsp;".timeForm($data[$i][1])."&nbsp;</font></td>";
	print "<td id='bordure'><font class=T2>&nbsp;".timeForm($data[$i][2])."&nbsp;</font></td>";
	print "</tr>";
}

?>
</table>

</font>
</blockquote>
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
