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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
	print "<script>";
        print "location.href='./affectation_creation_key.php'";
        print "</script>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE16?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td>
<!-- //  debut -->
<ul>
<form method=post onsubmit="return affectation_classe2()" name="formulaire" action="affectation_creation2.php" >
<table border='0' >
<tr><td colspan=2>
<br>
<font class='T2 shadow' id='color3' >IMPORTANT, LA CREATION D'AFFECTATION SUPPRIME TOUTES LES INFORMATIONS DE NOTATION DE LA NOUVELLE CLASSE CONCERNEE !!</font>
<BR>
<BR>
<font class="T2"><?php print LANGAFF1?> : </font><select name="saisie_classe_envoi">
<option value=0  id='select0' ><?php print LANGCHOIX?></option>
<optgroup label="Classe">
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
select_classe(); // creation des options
?>
</select><br><br>
<td></tr>
<tr><td><font class="T2"><?php print LANGPER14?> :</font> <input type=text name="saisie_nb_matiere" size=3></td></tr>
<tr>
<td>
<br><font class="T2"><?php print "Année/Trimestre/Semestre :" ?></font> 
<select name="saisie_tri">
<option value='tous'  id='select0' selected='selected' ><?php print "Toute l'année" ?></option>
<option value='trimestre1'  id='select1' ><?php print "Trimestre 1 / Semestre 1" ?></option>
<option value='trimestre2'  id='select1' ><?php print "Trimestre 2 / Semestre 2" ?></option>
<option value='trimestre3'  id='select1' ><?php print "Trimestre 3" ?></option>
</select>
</td>
</tr>
<tr><td height='20'></td></tr>
<tr>
<td><font class="T2"><?php print LANGMESS145 ?> :</font>
<select name='anneeScolaire'>
<?php
print "<option value=''id='select0' >".LANGCHOIX."</option>";
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
</td></tr>

<tr>
<td><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT19?>","rien"); //text,nomInput</script>
</td></tr></table>
</form>
</ul>
<br>
<!-- <center><b><font color="#000000" class="T1"><b><?php print LANGAFF2?></b></font></center> -->
<hr>
<form method='post' action='affectation_creation_copy.php' >
<ul>
<font class='T2 shadow' id='color3' >IMPORTANT, LA COPIE D'AFFECTATION SUPPRIME TOUTES LES INFORMATIONS DE NOTATION DE LA NOUVELLE CLASSE CONCERNEE !!</font>
<BR>
<BR>
<font class='T2'>Copier l'affectation de la classe :
<select name="saisie_classe_source">
<option value=0  id='select0' ><?php print LANGCHOIX?></option>
<optgroup label="Classe">
<?php
select_classe(); // creation des options
?>
</select> de l'année scolaire : <select name='anneeScolaireSource'>
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>

<br><br>pour la classe :
<select name="saisie_classe_destination">
<option value=0  id='select0' ><?php print LANGCHOIX?></option>
<optgroup label="Classe">
<?php
select_classe(); // creation des options
?>
</select>
de l'année scolaire : 
<select name='anneeScolaireDest'>
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
<br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print 'Copier Affectation' ?>","rien"); //text,nomInput</script>
<br><br>
</form>
<?php
if (isset($_GET["errorcopy"])) {
	print "<br><center><font id='color3' class='T2 shadow'>ERREUR DE COPIE</font></center><br>";

}
?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
