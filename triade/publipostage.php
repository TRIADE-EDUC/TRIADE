<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS327 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<blockquote><BR>
<form method=post onsubmit="return validVignette1()" name="formulaire" action="publipostage_2.php">

<font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='anneeScolaire'  >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
                <br><br>

<font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe">
               <option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options

if (isset($_COOKIE["publipomatricule"])) 	{ $matriculep=$_COOKIE["publipomatricule"]; }
if (isset($_COOKIE["publipoadresse"])) 		{ $adressep=$_COOKIE["publipoadresse"]; }
if (isset($_COOKIE["publipoadresseinfo"])) 	{ $adresseinfop=$_COOKIE["publipoadresseinfo"]; }
if (isset($_COOKIE["publipomembre"])) 		{ $membrep=$_COOKIE["publipomembre"]; }
if (isset($_COOKIE["publicivilite"])) 		{ $civilitep=$_COOKIE["publicivilite"]; }
if (isset($_COOKIE["publiclasse"])) 		{ $classep=$_COOKIE["publiclasse"]; }

if ($membrep == "PAR") $checked1="checked='checked'";
if ($membrep == "ELE") $checked2="checked='checked'";

if ($adresseinfop == "PAR1") $checked3="checked='checked'";
if ($adresseinfop == "PAR2") $checked4="checked='checked'";
if ($adresseinfop == "ELE")  $checked5="checked='checked'";

if ($civilitep == "1")  $checked8="checked='checked'";
/*
if ($adressep == "oui")  $checked6="checked='checked'";
if ($matriculep == "oui")  $checked7="checked='checked'";
if ($classep == "oui")  $checked9="checked='checked'";
 */
?>

</select> <br /><br />
<font class='T2'> <?php print LANGMESS245 ?>  
<input type=radio name="membre" value="PAR" <?php print $checked1 ?> /> <?php print LANGMESS246 ?> 
<input type=radio name="membre" value="ELE" <?php print $checked2 ?> /> <?php print INTITULEELEVE ?> </font>
<br><br>
<font class='T2'> <?php print LANGMESS248 ?>  
<input type=radio name="adresseinfo" value="PAR1" <?php print $checked3 ?> /> <?php print LANGMESS249 ?> 1 
<input type=radio name="adresseinfo" value="PAR2" <?php print $checked4 ?> /> <?php print LANGMESS249 ?> 2
<input type=radio name="adresseinfo" value="ELE"  <?php print $checked5 ?> /> <?php print INTITULEELEVES ?> </font>
<br><br>
<font class=T2><?php print LANGMESS412 ?> :</font> <select id="id_vignette" name="id_vignette">
	       <option id='select0' value='0' ><?php print LANGCHOIX?></option>
               <option id='select0' value='2' >2 <?php print LANGTMESS434 ?> (105x39)</option>
               <option id='select0' value='3' >2 <?php print LANGTMESS434 ?> (105x39) avec marge</option>
               <option id='select0' value='6' >2 <?php print LANGTMESS434 ?> (105x37)</option>
	       <option id='select0' value='5' >2 <?php print LANGTMESS434 ?> (102x41)</option>
	       <option id='select0' value='1' >3 <?php print LANGTMESS434 ?> (70x42,3)</option>
	       <option id='select0' value='4' >3 <?php print LANGTMESS434 ?> (70x37)</option>
</select> <br /><br />
<font class='T2'> <?php print LANGMESS328 ?> <input type=checkbox name="civeleve"  value="1"  <?php print $checked8 ?> /> </font><i>(<?php print LANGOUI ?>) </i> <br><br>

<font class='T2'> <?php print LANGMESS329 ?> <input type='checkbox' <?php print $checked7 ?> name="matricule" id='matricule' value="oui" onclick="document.getElementById('adresse').checked=false ;" /> </font><i>(<?php print LANGOUI ?>) </i> <br><br>

<font class='T2'> <?php print LANGMESS330 ?> <input type='checkbox'  name="classe" value="oui" <?php print $checked9 ?> id='classe' onclick="document.getElementById('adresse').checked=false ;" /> </font><i>(<?php print LANGOUI ?>) </i><br><br>

<font class='T2'> <?php print LANGMESS331 ?> <input type='checkbox' <?php print $checked6 ?> name="adresse" value="oui"  id='adresse' onclick="document.getElementById('matricule').checked=false ; document.getElementById('classe').checked=false"  /> </font><i>(<?php print LANGOUI ?>) </i><br><br>

<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult1"); //text,nomInput</script>
</UL></UL></UL>
</form>

<br><br>
<hr>
<br><br>
<form method=post action="publipostage_2.php" name='formulaire2' onsubmit="return validVignette2()" >
<font class="T2"><?php print LANGMESS413 ?>  :</font> <select name="saisie_type">

    <option id='select0' value='0' ><?php print LANGCHOIX?></option>
    <option id='select1' value="ENS" ><?php print LANGPER18 ?></option>
    <option id='select1' value="ADM" ><?php print LANGMESS217 ?></option>
    <option id='select1' value="TUT" ><?php print LANGTMESS435 ?></option>
    <option id='select1' value="PER" ><?php print LANGMESS220 ?></option>
    <option id='select1' value="MVS" ><?php print LANGMESS219 ?></option>
</select>
<br><br>
<font class=T2><?php print LANGMESS412 ?> :</font> <select id="id_vignette" name="id_vignette">
	       <option id='select0' value='0' ><?php print LANGCHOIX?></option>
               <option id='select0' value='2' >2 <?php print LANGTMESS434 ?> (105x39)</option>
               <option id='select0' value='3' >2 <?php print LANGTMESS434 ?> (105x39) avec marge</option>
               <option id='select0' value='6' >2 <?php print LANGTMESS434 ?> (105x37)</option>
	       <option id='select0' value='5' >2 <?php print LANGTMESS434 ?> (102x41)</option>
	       <option id='select0' value='1' >3 <?php print LANGTMESS434 ?> (70x42,3)</option>
	       <option id='select0' value='4' >3 <?php print LANGTMESS434 ?> (70x37)</option>
</select> <br /><br />
<font class='T2'> <?php print LANGTMESS436 ?> : <input type='checkbox' <?php print $checked6 ?> name="adresse" value="oui"  id='adresse' onclick="document.getElementById('matricule').checked=false ; document.getElementById('classe').checked=false"  /> </font><i>(<?php print LANGOUI ?>) </i><br><br>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","consult2"); //text,nomInput</script>
<br><br>
</form>

</blockquote>
<br /><br /><br />
<!-- // fin form -->
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

</BODY>
</HTML>
