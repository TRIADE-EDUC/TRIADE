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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit='return validecreatetude()' name="formulaire" action="gestion_etude_eleve_ajout2.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGETUDE33 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<ul><BR>
<font class="T2">
<?php print LANGETUDE34 ?> : </font><select name=saisie_etude>
		<option STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
		<?php
		liste_etude_option();
		?>
		</select>
<BR>
<BR><?php print LANGETUDE35 ?><BR><BR></UL>
<center>
<table width=100% border=0>
<TR><TD>&nbsp;&nbsp;
<select align=top name="saisie_liste[]" size=6  style="width:120px" multiple="multiple">
<?php
select_classe(); // creation des options
?>
</select>
</TD>
<TD valign=top align=center>
<TABLE border="1" width=80% bordercolor="#000000">
<TR><TD bgcolor="#FFFFFF" id=bordure>
<?php print LANGGRP3?> <font color=red><B><?php print LANGGRP4?></b></font> <?php print LANGGRP5?><BR>  <BR>
</td></tr>
</table>
</TD></TR></TABLE></center>
<BR><BR><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT13?>","rien"); //text,nomInput</script>
<ul><br> <br><br>
<!-- // fin  -->
</td></tr></table>
</form>
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
?>
</BODY></HTML>

