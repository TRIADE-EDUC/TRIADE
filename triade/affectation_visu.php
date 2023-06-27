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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?>
</SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?>
</SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE18?></font></b> <span id='nbeleve'></span></td>
</tr>
<tr id='cadreCentral0'>
<td >
<br />
<form method='post' action='affectation_visu.php' >
&nbsp;&nbsp;&nbsp;<font class="T2"><?php print LANGMESS340 ?> :</font>
<select name="saisie_tri" >
<?php
$tri='tous';
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if (isset($_POST["saisie_tri"])) {
	$libelle=libelleTrimestre($_POST["saisie_tri"]);
	print "<option value='".$_POST["saisie_tri"]."' id='select0' >$libelle</option>";
	$tri=$_POST["saisie_tri"];
	$anneeScolaire=$_POST["anneeScolaire"];
}
?>
<option value='tous'  id='select0' ><?php print LANGMESS341 ?></option>
<option value='trimestre1'  id='select1' ><?php print LANGMESS342 ?></option>
<option value='trimestre2'  id='select1' ><?php print LANGMESS343 ?></option>
<option value='trimestre3'  id='select1' ><?php print LANGMESS344 ?></option>
</select> / <?php print LANGBULL3 ?> : 
<select name="anneeScolaire" >
<?php filtreAnneeScolaireSelect($anneeScolaire); ?>
</select>&nbsp;&nbsp;
<input type='submit' value='<?php print VALIDER ?>' class='BUTTON' />
<br /><br /> 
</form>

<table border=1 bordercolor=#000000" align=center width='95%' style="border-collapse: collapse;" >
<TR>
<td bgcolor="yellow" align=center><?php print ucwords(LANGPER25)?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;".LANGPER16."&nbsp;".ucwords(LANGBULL32)."&nbsp;"; ?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;".LANGPER16."&nbsp;".ucwords(LANGBULL31)."&nbsp;"; ?></td>
<td bgcolor="yellow" width=10% align='center'>&nbsp;<?php print ucwords(LANGPER26)?>&nbsp;</td>
</TR>
<?php
$nbeleveTotal='0';
verif_table_classe();
verif_table_groupe();
$data=visu_affectation_2($tri,$anneeScolaire);
for($i=0;$i<count($data);$i++) {
	$nbeleve=nbEleve($data[$i][0],$anneeScolaire);
	$nbeleveTotal+=$nbeleve;
?>
	<form method=post name="formulaire<?php print $i?>" action="affectation_visu2.php" >
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<TD><?php $classe=chercheClasse($data[$i][0]);print ucwords($classe[0][1]);?></td>
	<TD align="center"><?php print nbMatiere2($data[$i][0],$anneeScolaire); ?></td>
	<TD align="center"><?php print $nbeleve ?></td>
	<TD align='center'><input type='submit' value="<?php print LANGPER27?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td>
	<input type='hidden' name="saisie_classe" value="<?php print $data[$i][0]?>">
	<input type='hidden' name="saisie_tri" value="<?php print $tri ?>">
	<input type='hidden' name="anneeScolaire" value="<?php print $anneeScolaire ?>">
	</form>
	</tr>
<?php
}
unset($data);
Pgclose();
?>
</table>
<script>document.getElementById('nbeleve').innerHTML=" <font id='color2'><b><?php print $nbeleveTotal ?></b></font><font  id='menumodule1' > <?php print LANGTMESS464 ?></font>"; </script>
<BR>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
