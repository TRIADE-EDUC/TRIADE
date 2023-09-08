<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<?php include_once("./common/productId.php"); ?>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="185">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Accréditation à une centrale de stage "?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<form method='post' >
<br><br>
<!-- // fin  -->
<?php 
$productid=PRODUCTID;
$option=file_get_contents("https://support.triade-educ.org/centralestage/ajaxclientcentrale.php?productid=$productid"); 
?>
<ul>
<font class='T2'>Centrale de stage : 
<select name='centrale' id='centrale' >
<?php
print $option;
?>
</select><br><br>
Mot de passe associé : <input type='text' name='pass' size='40' />
<br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT19 ?>","create"); //text,nomInput</script>
</font>
</ul>
</form>
<?php
if (isset($_POST["create"])) {
	$pass=$_POST["pass"];
	$url=$_POST["centrale"];
	@unlink("./common/config.centralStageClient.php");
	$f=fopen("./common/config.centralStageClient.php","w");
	fwrite($f,"<?php\n");
	fwrite($f,"define(\"URLCENTRALSTAGE\",\"$url\");\n");
	fwrite($f,"define(\"PASSCENTRALSTAGE\",\"$pass\");\n");
	fwrite($f,"?>\n");
	fclose($f);
	print "<br><br><center><font class=T2 id=color2 >".LANGDONENR."</font></center><br><br>";
}
?>
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
<script>upcentrale('<?php print PRODUCTID ?>');</script>
</BODY></HTML>
