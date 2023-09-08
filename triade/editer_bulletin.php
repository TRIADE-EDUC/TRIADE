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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade Editer Bulletin</title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include("./librairie_php/lib_licence.php"); 
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
$cnx=cnx();
?>
<BR><BR><BR><BR>
<form method=post action="editer_bulletin02.php" onsubmit="return valide_choix_projo()" name="formulaire">
<table border=1 bordercolor="#000000" width=500 align=center bgcolor="#FFFFFF" height="140">
<tr><td colspan=2 id="bordure" ><font class=T2>&nbsp;&nbsp;<?php print LANGPROFP35 ?>.</font></td><tr>

<?php if ($_SESSION["membre"] != "menuprof") { ?>
<tr><td width=50% align=right id="bordure" >
<font size=3><?php print LANGPROJ1?> :</font> </td><td id="bordure" ><select name="saisie_classe">
<option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select>&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr><tr>
<?php }else{ print "<input type='hidden' name='saisie_classe' value='".$_GET["sClasseGrp"]."' >"; } ?>
<td align=right id="bordure"  ><font size=3><?php print LANGPROJ2?> :</font> </td>
<td id="bordure" >
<select name="saisie_trimestre">
<option value='0' STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<option value="trimestre1" <?php print ($_COOKIE["saisie_trimestre"] == "trimestre1") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
<option value="trimestre2" <?php print ($_COOKIE["saisie_trimestre"] == "trimestre2") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
<option value="trimestre3" <?php print ($_COOKIE["saisie_trimestre"] == "trimestre3") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</Select>
</td>
</tr>
<tr>
<td id='bordure'  align=right ><font size="3"><?php print LANGBULL3 ?> :</font></td><td id='bordure' >
                 <select name='anneeScolaire'  >
                 <?php
                 $anneeScolaire=$_COOKIE["anneeScolaire"];
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
</td></tr>



<?php if (COMBULTINTYPE == "oui") {  ?>
<tr><td align=right id="bordure"  >
<font size="3">&nbsp;Choix&nbsp;du&nbsp;commentaire&nbsp;:</font>
</td><td id="bordure" >
<select name="typecom" >
<optgroup label="Standard">
<option value="0" id="select1">Appréciations, Conseils pour progresser.</option>
<option value="5" id="select1">Elèments du programme travaillés.</option>
<optgroup label="Spécif.">
<option value="1"  id="select1">Points d'appui. Progrès. Efforts</option>
<option value="2"  id="select1">Ecarts par rapport aux objectifs attendu</option>
<option value="3"  id="select1">Conseils pour progresser</option>
<optgroup label="Examen">
<option value="4"  id="select1">Partiel Blanc</option>
</select>
</td>
</tr>
<?php } ?>



<tr>
<td  colspan=2 align=center id="bordure"  >
<table align=center><tr><td>
<?php
$valeur=aff_Trimestre();
if (count($valeur)) {
	$disabled="";
	$alert="";
}else{
	$disabled="disabled=disabled";
	$alert=LANGMESS10."<br><br>".LANGMESS11."<br><br>".LANGMESS12;
}
?>
<script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
</td></tr></table>
</td></tr>
</table>
</form>
<br><br>
<?php
if (isset($_GET["info"])) {
	print "<center><b>".LANGPROJ6."</b></center>";
	print "<br><br>";
}
?>
<form method=post action="liste_bulletin_com.php" onsubmit="return valide_choix_projo2()" name="formulaire2">
<table border=1 bordercolor="#000000" width=500 align=center bgcolor="#FFFFFF" height="140">
<tr><td colspan=2 id="bordure" ><font class=T2>&nbsp;&nbsp;Listes des commentaires des bulletins effectués.</font></td><tr>

<?php if ($_SESSION["membre"] != "menuprof") { ?>

<tr><td width=50% align=right id="bordure" >
<font size=3><?php print LANGPROJ1?> :</font> </td><td id="bordure" ><select name="saisie_classe">
<option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
select_classe(); // creation des options
?>
</select>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

<?php }else{ print "<input type='hidden' name='saisie_classe' value='".$_GET["sClasseGrp"]."' >"; } ?>
<tr>
<td align=right id="bordure"  ><font size=3><?php print LANGPROJ2?> :</font> </td>
<td id="bordure" >
<select name="saisie_trimestre">
<option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<option value="trimestre1"  <?php print ($_COOKIE["saisie_trimestre"] == "trimestre1") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
<option value="trimestre2"  <?php print ($_COOKIE["saisie_trimestre"] == "trimestre2") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
<option value="trimestre3"  <?php print ($_COOKIE["saisie_trimestre"] == "trimestre3") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</Select>
</td>
</tr>
<tr>
<td id='bordure'  align=right ><font size="3"><?php print LANGBULL3 ?> :</font></td><td id='bordure' >
                 <select name='anneeScolaire'  >
                 <?php
                 $anneeScolaire=$_COOKIE["anneeScolaire"];
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
</td></tr>

<tr>
<td  colspan=2 align=center id="bordure"  >
<table align=center><tr><td>
<?php
$valeur=aff_Trimestre();
if (count($valeur)) {
	$disabled="";
	$alert="";
}else{
	$disabled="disabled=disabled";
	$alert=LANGMESS10."<br><br>".LANGMESS11."<br><br>".LANGMESS12;
}
?>
<script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
</td></tr></table>
</td></tr>
</table>
</form>

<br><br>

<form method="post" onsubmit="return verifAccesNote5()" name="formulaire5" action="bulletin_comm_classe22.php">
<table border=1 bordercolor="#000000" width=500 align=center bgcolor="#FFFFFF" height="140">
<tr><td colspan=2 id="bordure" >
<font class='T2'><?php print "Consulter les commentaires de la classe par matière." ?></font>


<tr><td align='right' id='bordure' >
<font class="T2"><?php print LANGPROJ1 ?> :</font></td><td  id='bordure' >
<select name="sClasseGrp" size="1" onChange="upSelectMat(this)">
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?> </option>
<?php
select_classe(); // creation des options
?>
</select>
</td></tr>
<tr><td align='right' id='bordure' >
<font class='T2'>
<?php print LANGPROJ2 ?> :
<?php
$choix_tri=recherche_trimestre_en_cours_via_classe($cid);

$choix_tri_text=$choix_tri;
//$tri=recherche_intervalle_trimestre($choix_tri_text);

if ($choix_tri_text == "trimestre1") {
	$choix_tri_text=LANGPROJ3. " ou ".LANGPROJ19;
}
if ($choix_tri_text == "trimestre2") {
    $choix_tri_text=LANGPROJ4. " ou ".LANGPROJ20;
}
if ($choix_tri_text == "trimestre3") {
    $choix_tri_text=LANGPROJ5;
}
?></font></td><td  id='bordure' >
<select name="choix_trimestre">
	<option value='<?php print $choix_tri?>' STYLE="color:#000066;background-color:#FCE4BA"><?php print ucfirst($choix_tri_text)?></option>
	<option value='trimestre1'  <?php print ($_COOKIE["saisie_trimestre"] == "trimestre1") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3. " ou ".LANGPROJ19?></option>
	<option value='trimestre2'  <?php print ($_COOKIE["saisie_trimestre"] == "trimestre2") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4. " ou ".LANGPROJ20?></option>
	<option value='trimestre3'  <?php print ($_COOKIE["saisie_trimestre"] == "trimestre3") ? "selected='selected'" : "" ?> STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</select>
</font>
</td></tr>
<tr><td align='right' id='bordure' >
<font class="T2"><?php print LANGBULL3 ?> :</font></td><td  id='bordure' >
                 <select name='anneeScolaire' >
                 <?php
                 filtreAnneeScolaireSelectNote($_COOKIE["anneeScolaire"],3);
                 ?>
                 </select>
</td></tr>
<td  colspan=2 align=center id="bordure"  >
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
</td></tr></table>
</form>
</td></tr></table>


<center><font class=T3><b><?php print $alert ?></b></font></center>
<?php Pgclose(); ?>
</body>
</html>
