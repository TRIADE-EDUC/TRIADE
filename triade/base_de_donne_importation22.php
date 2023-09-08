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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<FORM method=POST action="">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Module d'importation de fichier" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<TABLE border=0 width=100%>
<TR><TD align=top><font class=T2><?php print "Module d'importation de fichier Excel" ?> </font></TD>
</TR></TABLE><br>
<?php print "Le fichier excel transmettre DOIT contenir 9 champs" ?>
<BR>
<BR>
<font class=T2><?php print LANGIMP7?></font>
<br>
<BR>
<table width="100%" border="1" bgcolor="#FCE4BA" bordercolor=#000000 >
<!-- //$nom,$pren,$mdp,$tp,$civ,$pren2='',$rue,$adr,$codepostal,$tel,$mail,$commune -->
        <tr bgcolor="#FFCC00">
          <td valign=top >1) <?php print LANGIMP47?> *</td>
          <td valign=top >2) <?php print LANGIMP48?> *</td>
          <td valign=top >3) <?php print LANGIMP46?> *</td>
	</tr>
	<tr bgcolor="#FFCC00">
	  <td valign=top >4) <?php print LANGIMP46bis?> </td>
	   <td valign=top >5) <?php print LANGIMP55 ?></td>
	   <td valign=top >6) <?php print LANGIMP56 ?></td>
	</tr>
	<tr bgcolor="#FFCC00">
	   <td valign=top >7) <?php print LANGIMP57 ?></td>
	   <td valign=top >8) <?php print LANGIMP58 ?></td>
	   <td valign=top >9) <?php print LANGIMP59 ?></td>
	</tr>
</table>
<br>
<table>
<tr>
<td valign='top'>
<u><?php print LANGbasededoni51 ?></u> : <br>
<i>
<?php print LANGbasededoni52 ?>
<?php print LANGbasededoni53 ?>
<?php print LANGbasededoni54 ?>
<?php print LANGbasededoni54_2 ?>
<?php print LANGbasededoni54_3 ?>
<?php print LANGbasededoni54_4 ?>
<?php if (CIVARMEE == "oui") { ?>
valeur acceptée : <b>9 </b>ou Général <br>
valeur acceptée : <b>10 </b>ou Colonel<br />
valeur acceptée : <b>11 </b>ou Lieutenant-colonel<br />
valeur acceptée : <b>12 </b>ou Commandant<br />
valeur acceptée : <b>13 </b>ou Capitaine<br />
<?php } ?>
</i>
</td>
<td valign='top'>
<i>
<?php if (CIVARMEE == "oui") { ?>
valeur acceptée : <b>14 </b>ou Lieutenant<br />
valeur acceptée : <b>15 </b>ou Sous-lieutenant<br />
valeur acceptée : <b>16 </b>ou Aspirant<br />
valeur acceptée : <b>17 </b>ou Major<br />
valeur acceptée : <b>18 </b>ou Adjudant-chef<br />
valeur acceptée : <b>19 </b>ou Adjudant<br />
valeur acceptée : <b>20 </b>ou Sergent-chef<br />
valeur acceptée : <b>21 </b>ou Sergent<br />
valeur acceptée : <b>22 </b>ou Caporal-chef<br />
valeur acceptée : <b>23 </b>ou Caporal<br />
valeur acceptée : <b>24 </b>ou Aviateur<br />
<?php } ?>
</i>
</td>
</tr>
</table>
<script language=JavaScript>
function suite() {
	location.href="./base_de_donne_key.php?base=<?php print $_GET["id"]?>";
}
</script>
<BR><div align="center">
<input type=button class="BUTTON" value='<?php print "Exemple fichier xls" ?>' onclick="open('./librairie_php/import-personnel.xls','_blank','')" />
&nbsp;&nbsp;&nbsp;
<input type=button class="BUTTON" value='<?php print LANGBTS?>' onclick='suite();'> </div><br />
<br>
<br>
<font color=red>
<?php print LANGIMP49?>
</font></b>
<!-- // fin  -->
</td></tr></table> </form>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
</BODY></HTML>
