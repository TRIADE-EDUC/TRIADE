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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des données du personnel" ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<ul><font class=T2><?php print "Indiquer les donn&eacute;es &agrave; exporter" ?></font></ul>
<br />

<form method="post" action="export_personnel_2.php" >

&nbsp;&nbsp;&nbsp;<font class="T2"><?php print "Type membre " ?>  :</font> <select name="saisie_type">
    <option id='select0' value='0' ><?php print LANGCHOIX?></option>
    <option id='select1' value="ENS" ><?php print "Enseignant"?></option>
    <option id='select1' value="ADM" ><?php print "Direction"?></option>
    <option id='select1' value="TUT" ><?php print "Tuteur de stage"?></option>
    <option id='select1' value="PER" ><?php print "Personnel"?></option>
    <option id='select1' value="MVS" ><?php print "Vie Scolaire"?></option>
</select><br><br><br>



<table border=1 width="100%" bordercolor='#000000' style="-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;padding:5px" >
<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='civ_1' > Civilit&eacute; </td>
<td id='bordure' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<input type="checkbox" name="liste[]" value='nom' > nom   </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='prenom' > pr&eacute;nom   </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='adr1' >adresse</td>
</tr>


<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='code_post_adr1' >Code postal </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='commune_adr1' >commune </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_port_1' >T&eacute;l. port.</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='email' > Email  </td>
</tr>


<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='telephone' >T&eacute;l&eacute;phone</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='identifiant' >Identifiant</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='indice_salaire' >Indice salaire</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='code_barre' >Code barre</td>
</tr>


</table>
<br>

<font class=T2> Ajouter d'autres colonnes : <input type=text size=3 name="nbcolplus" value="0" /></font> (<i>nbr de colonne(s) suppl&eacute;mentaire(s)</i>)
<br><br>
<center><input type="submit" value="Suivant -->" class="BUTTON" name="create" /> </center>
</form>

<br />
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
