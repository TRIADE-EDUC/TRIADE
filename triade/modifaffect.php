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
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
	print "<script>";
	print "location.href='./affectation_modif_key.php'";
    	print "</script>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE19?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br />
<table width='80%'><tr><td>
<form method='post' action='modifaffect.php' >
&nbsp;&nbsp;&nbsp;<font class="T2"><?php print "Choix " ?> :</font>
<select name="saisie_tri" >
<?php
if (isset($_GET["sClasseGrp"])) $sClasseGrp=$_GET["sClasseGrp"];
if (isset($_POST["sClasseGrp"])) $sClasseGrp=$_POST["sClasseGrp"];

$tri='tous';
include_once('librairie_php/db_triade.php');
if (isset($_POST["saisie_tri"])) {
	$libelle=libelleTrimestre($_POST["saisie_tri"]);
	print "<option value='".$_POST["saisie_tri"]."' id='select0' >$libelle</option>";
	$tri=$_POST["saisie_tri"];
	$anneeScolaire=$_POST["anneeScolaire"];
}
?>
<option value='tous'  id='select0' ><?php print "Toute l'année" ?></option>
<option value='trimestre1'  id='select1' ><?php print "Trimestre 1 / Semestre 1" ?></option>
<option value='trimestre2'  id='select1' ><?php print "Trimestre 2 / Semestre 2" ?></option>
<option value='trimestre3'  id='select1' ><?php print "Trimestre 3" ?></option>
</select> 
<font class="T2">/ Ann&eacute;e Scolaire :</font>
<select name="anneeScolaire" >
<?php filtreAnneeScolaireSelectNote($anneeScolaire); ?>
</select>&nbsp;&nbsp;
<input type='submit' value='<?php print VALIDER ?>' class='BUTTON' />
<input type='hidden'  value="<?php print $sClasseGrp ?>" name='sClasseGrp' />
</form>

</td><td align='right'><?php if ($_SESSION["membre"] == "menuprof") { print "<script>buttonMagicRetour('profp.php','_self')</script>"; } ?></td></tr></table>
<!-- //  debut -->
<br>
<table border=1 bordercolor=#000000" align=center width='90%' style="border-collapse: collapse;"  >
<TR>
<td bgcolor="yellow" align=center><?php print ucwords(LANGPER25)?></td>
<td bgcolor="yellow" width=10% align=center><?php print ucwords(LANGPER30)?></td>
<td bgcolor="yellow" width=10% align=center><?php print ucwords("Ordre")?></td>
</TR>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
	$cr=verif_profp_class_sans_blacklist($_SESSION["id_pers"],$sClasseGrp);
	if ($cr == "ok") $data=visu_affectation_2_prof($tri,$sClasseGrp,$anneeScolaire);
}else{
	$data=visu_affectation_2($tri,$anneeScolaire);
}
for($i=0;$i<count($data);$i++)
        {
	?>
	<!-- autant de form que de classes -->
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<TD><?php $classe=chercheClasse($data[$i][0]);print $classe[0][1];?></td>
	<TD><input type=button onclick="PopupCentrerAttente('./modifaffect2.php?saisie_classe_envoi=<?php print $data[$i][0]?>&saisie_tri=<?php print $tri?>&anneeScolaire=<?php print $anneeScolaire ?>',1000,500,'tollbar=no,menubar=no,scrollbars=yes,resizable=yes');" value="<?php print ucwords(LANGPER30)?>" class=bouton2 ></td>
	<TD><input type=button onclick="PopupCentrerAttente('./modifaffect4.php?saisie_classe_envoi=<?php print $data[$i][0]?>&saisie_tri=<?php print $tri?>&anneeScolaire=<?php print $anneeScolaire ?>',1000,500,'tollbar=no,menubar=no,scrollbars=yes,resizable=yes');" value="<?php print ucwords(LANGPER30)?>" class=bouton2 ></td>
	</tr>

	<?php
	}
unset($data);
Pgclose();


?>
</table>
<BR>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
