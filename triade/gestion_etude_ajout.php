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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGETUDE19 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");
?>
<br>
<ul>
<font class="T2">
<form method=post onsubmit="return valideetude()" name="formulaire">
<table border=0 align=center height=300>
<tr><td valign=top align=right ><font class="T2"><?php print LANGETUDE20 ?> :</font></td><td><input type=text name="nometude" size=12 maxlength=15 class=bouton2></td></tr>
<tr><td valign=top align=right ><font class="T2"><?php print LANGETUDE21 ?> :</font></td><td valign=top>
			     <input type=checkbox value=1 name="jour1" class=btradio1 > <?php print LANGLUNDI ?>
			     <br><input type=checkbox value=2 name="jour2"  class=btradio1> <?php print LANGMARDI ?>
			     <br><input type=checkbox value=3 name="jour3"  class=btradio1> <?php print LANGMERCREDI ?>
			     <br><input type=checkbox value=4 name="jour4"  class=btradio1> <?php print LANGJEUDI ?>
			     <br><input type=checkbox value=5 name="jour5"  class=btradio1> <?php print LANGVENDREDI ?>
			     <br><input type=checkbox value=6 name="jour6"  class=btradio1> <?php print LANGSAMEDI ?>

</td></tr>
<tr><td valign=top align=right ><font class="T2"><?php print LANGETUDE22 ?> :</font></td><td><input type=text value="<?php print LANGETUDE24 ?>" name="heure_etude" size=6 maxlength=5 class=bouton2  onKeyPress="onlyChar2(event)" ></td></tr>
<tr><td valign=top align=right ><font class="T2"><?php print LANGETUDE23 ?> :</font></td><td>
<select name=duree_etude>
<option value=0 id="select0"><?php print LANGCHOIX ?></option>
<option value=1h id="select1"> 1h </option>
<option value=1h30 id="select1" > 1h30 </option>
<option value=2h id="select1" > 2h </option>
<option value=2h30 id="select1" > 2h30 </option>
<option value=3h id="select1" > 3h </option>
</select>
</td></tr>
<tr><td valign=top align=right ><font class="T2"><?php print LANGETUDE25 ?> :</font></td><td><input type=text name="salleetude" size=12 maxlength=15 class=bouton2></td></tr>
<tr><td valign=top align=right ><font class="T2"><?php print LANGETUDE26 ?> :</font></td><td>
<select name="saisie_pers_supp">
<option value="-1" STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<optgroup label="Enseignant">
<?php
select_personne_nom('ENS'); // creation des options
?>
<optgroup label="Vie Scolaire">
<?php
select_personne_nom('MVS'); // creation des options
?>
<optgroup label="Administration">
<?php
select_personne_nom('ADM'); // creation des options
?>
</select>
</td></tr>

<tr><td colspan=2 align=center><br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
</td></tr>
</table>
<br><br>
</form>
</ul>
<?php
if (isset($_POST["create"])) {
	$jour="";
	if (!empty($_POST["jour1"])) { $jour.=$_POST["jour1"].","; }
	if (!empty($_POST["jour2"])) { $jour.=$_POST["jour2"].","; }
	if (!empty($_POST["jour3"])) { $jour.=$_POST["jour3"].","; }
	if (!empty($_POST["jour4"])) { $jour.=$_POST["jour4"].","; }
	if (!empty($_POST["jour5"])) { $jour.=$_POST["jour5"].","; }
	if (!empty($_POST["jour6"])) { $jour.=$_POST["jour6"]; }
	$jour=preg_replace('/,$/','',$jour);

	$cr=etude_ajout($_POST["nometude"],$jour,$_POST["heure_etude"],$_POST["duree_etude"],$_POST["saisie_pers_supp"],$_POST["salleetude"]);
	if($cr){
           history_cmd($_SESSION[nom],"CREATION","Etude");
	   print "<font color=red class=T2><center>".LANGETUDE27.".</center></font><br><br>";
	}
}
?>


</font>
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
