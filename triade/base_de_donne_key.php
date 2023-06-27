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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("3");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<?php


if ($_GET["saisie_resultat"] == "erreur" ) :
   $message_erreur="<font size=3 color='red'>".LANGTERREURCONNECT."</font><BR><br>";
endif ;
?>
<form method=post action='./base_de_donne_central.php' >
<TABLE border=1 bordercolor="#000000" width='100%' height='200' >
<TR>
<TD align=center bordercolor="#FFFFFF" id='bordure' >
<?php print "$message_erreur" ?>
<font class="T2">
<b><?php print LANGPER12?></b>
</font><br/><br/>
<CENTER><br>
<input type='password' name='saisie_code1'  size=10> ----
<input type='password' name='saisie_code2'  size=10> ----
<input type='password' name='saisie_code3'  size=10>
</CENTER><BR>
<input type='hidden' name='base' value="<?php print $_GET["base"]?>">
<input type='hidden' name='dbf_name' value="<?php print $_GET["dbf_name"]?>">
<input type='hidden' name='modulepost' value="<?php print $_POST["modulepost"]?>">
<input type='hidden' name='modulesecurite' value="<?php print $_GET["key"]?>">
<input type='hidden' name='eid' value="<?php print $_GET["eid"]?>">
<input type='hidden' name='supp_date_cal' value="<?php print $_POST["supp_date_cal"]?>" />
<input type='hidden' name="supp_date_dst" value="<?php print $_POST["supp_date_dst"]?>" /> 
<input type='hidden' name="supp_date_edt" value="<?php print $_POST["supp_date_edt"]?>" /> 
<input type='hidden' name="sClasseGrp" value="<?php print $_GET["sClasseGrp"]?>" /> 
<table align=center>
<tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPER13?>","rien"); //text,nomInput</script>
</td></tr></table>
<br /><br />
<img src="image/commun/important.png" align='center'><font class='T1'><?php print LANGMESS376 ?> <a href="admin/index.php" target="_blank" ><?php print LANGMESS377 ?></a> <?php print LANGMESS378 ?></font>
</TD>
<TR>
</table>
</form>
<?php
if ($_SESSION["adminplus"] == "suppreme") {
?>
	<script language=JavaScript>document.forms[0].submit();</script>
<?php
} 
?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
