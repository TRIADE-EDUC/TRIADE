<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importation d'un fichier Excel " ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<TABLE border=0 width=100%>
<TR><TD align=top><font class=T2><?php print "Module d'importation de fichier Excel" ?> </font></TD>
</TR></TABLE><br>
<?php print "Le fichier à transmettre DOIT contenir 41 champs" ?>
<BR>
<BR>
<font class=T2><?php print LANGIMP7?></font>
<br>
<BR>
						
<table width="100%" border="1" bgcolor="#FCE4BA" bordercolor="#000000" >
	
	<tr bgcolor="#FFCC00">
	  <td valign=top width=25%>1) N° MATRIC </td>
	  <td valign=top width=25%>2) GRILLE</td>
          <td valign=top width=25%>3) CLASSE *</td>
          <td valign=top width=25%>4) ANNÉE</td>
	</tr>

        <tr bgcolor="#FFCC00">
          <td valign=top>5) NOM *</td>
          <td valign=top>6) PRÉNOM *</td>
          <td valign=top>7) SEXE </td>
          <td valign=top>8) ADRESSE </td>
	</tr>

        <tr bgcolor="#FFCC00">
          <td valign=top>9) CP </td>
          <td valign=top>10) LOCALITÉ </td>
          <td valign=top>11) ENT. COM. </td>
          <td valign=top>12) DATE NAIS. *</td>
        </tr>

        <tr bgcolor="#FFCC00">
          <td valign=top>13) LIEU NAIS. </td>
          <td valign=top>14) NATIONALITE </td>
          <td valign=top>15) ETABLIS_AN </td>
          <td valign=top>16) ORIGINE </td>
        </tr>

        <tr bgcolor="#FFCC00">
          <td valign=top>17) C. PHILOS. <BR></td>
          <td valign=top>18) 2E L </td>
          <td valign=top>19) INT /EXT </td>
          <td valign=top>20) MIDI (CTD) </td>
	</tr>
			
        <tr bgcolor="#FFCC00">
          <td valign=top>21) PERS. RESP.</td>
          <td valign=top>22) PRÉNOM RESP. </td>
          <td valign=top>23) D.P. </td>
          <td valign=top>24) PROFESSION </td>
        </tr>

          <TR bgcolor="#FFCC00">
          <td valign=top>25) CONJOINT </td>
          <td valign=top>26) PROFES. </td>
          <td valign=top>27) TÉL. PRIVÉ </td>
	  <td valign=top>28) TÉL.TRAV.P </td>
          </TR>

          <TR bgcolor="#FFCC00">
	  <td valign=top>29) GSM PÈRE </td>
	  <td valign=top>30) TÉL TRAV. M. </td>
	  <td valign=top>31) GSM MÈRE </td>
	  <td valign=top>32) RAPPORT 1È </td>
          </TR>

          <TR bgcolor="#FFCC00">
	  <td valign=top>33) RAPPORT 2È </td>
	  <td valign=top>34) INFO </td>
	  <td valign=top>35) DATE INSCR. </td>
	  <td valign=top>36) CASIER </td>
          </TR>

          <TR bgcolor="#FFCC00">
	  <td valign=top>37) RAPPORT 3È </td>
	  <td valign=top>38) PASSE </td>
	  <td valign=top>39) REMÉD </td>
	  <td valign=top>40) RAPPOT 1BI </td>
          </TR>

          <TR bgcolor="#FFCC00">
	  <td valign=top>41) RAPPORT 2B </td>
          </TR>

      </table>
<br>
<script language=JavaScript>
function suite() {
	location.href="./base_de_donne_key.php?base=ctixls";
}
</script>
<BR><div align="center"> <input type=button class="BUTTON" value='<?php print LANGBTS?>' onclick='suite();'> </div><br />
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
