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
<?php print "Le fichier excel à transmettre DOIT contenir 25 champs" ?>
<BR>
<BR>
<font class=T2><?php print LANGIMP7?></font>
<br>
<BR>
<table width="100%" border="1" bgcolor="#FCE4BA" bordercolor=#000000 style="border-collapse: collapse;"  >
        <tr bgcolor="#FFCC00">
          <td valign=top >1) <?php print "Nom entreprise * " ?></td>
          <td valign=top >2) <?php print "Registre du commerce" ?></td>
	  <td valign=top >3) <?php print "SIREN" ?></td>
	  <td valign=top >4) <?php print "SIRET" ?></td>
	</tr><tr bgcolor="#FFCC00" >
	  <td valign=top >5) <?php print "Forme Juridique" ?></td>
	  <td valign=top >6) <?php print "Secteur Economique" ?></td>
	  <td valign=top >7) <?php print "INSEE" ?></td>
          <td valign=top >8) <?php print "NAF/APE" ?></td>
	</tr><tr bgcolor="#FFCC00" >
          <td valign=top >9) <?php print "NACE" ?></td>
          <td valign=top >10) <?php print "Type d'organisation" ?></td>
          <td valign=top >11) <?php print "Nom du responsable" ?></td>
          <td valign=top >12) <?php print "Fonction du responsable" ?></td>
	</tr><tr bgcolor="#FFCC00" >
          <td valign=top >13) <?php print "Adresse entreprise" ?></td>
          <td valign=top >14) <?php print "Code postal entreprise" ?></td>
          <td valign=top >15) <?php print "Ville entreprise" ?></td>
          <td valign=top >16) <?php print "Pays entreprise" ?></td>
	</tr><tr bgcolor="#FFCC00" >
          <td valign=top >17) <?php print "Secteur activité" ?></td>
          <td valign=top >18) <?php print "2ieme secteur activité" ?></td>
          <td valign=top >19) <?php print "3ieme secteur activité" ?></td>
          <td valign=top >20) <?php print "Activité principale" ?></td>
	</tr><tr bgcolor="#FFCC00" >
          <td valign=top >21) <?php print "Téléphone" ?></td>
          <td valign=top >22) <?php print "Fax" ?></td>
          <td valign=top >23) <?php print "Email" ?></td>
          <td valign=top >24) <?php print "Site Web" ?></td>
	</tr><tr bgcolor="#FFCC00" >
          <td valign=top >25) <?php print "Informations" ?></td>
	</tr>
	
</table>
<br>
<script language=JavaScript>
function suite() {
	location.href="./base_de_donne_key.php?base=<?php print $_GET["id"]?>";
}
</script>
<BR><div align="center">
<input type=button class="BUTTON" value='<?php print "retour" ?>' onclick="open('gestion_stage.php','_self','')" />
&nbsp;&nbsp;&nbsp;
<input type=button class="BUTTON" value='<?php print "Exemple fichier xls" ?>' onclick="open('./librairie_php/import-entreprise.xls','_blank','')" />
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
