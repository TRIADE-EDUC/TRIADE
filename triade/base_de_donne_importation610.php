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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
	if (empty($_SESSION["adminplus"])) {
		print "<script>";
		print "location.href='./base_de_donne_importation.php'";
		print "</script>";
		exit;
	}
	?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importation du fichier XML"?></font></b></td></tr>
     <tr id='cadreCentral0'>
     <td >
     <!-- // fin  -->

<br />
<?php
if (isset($_GET["err"])) {
	print "<center><font id=color3><b>Mot de passe non conforme ! </b></font></center>";
	print "<br>";
}
?>
<ul>
<form method=post  action='./base_de_donne_importation620.php' name=formulaire ENCTYPE="multipart/form-data">

<font class=T1><?php print LANGGEP2?> : (<b>XML</b>)  <input type="file" name="fichier1" size=20 > <br /><br />

<font class=T1><?php print "Mot de passe par défaut pour tous les enseignants "?> :  <input type="text" name="passwd" size=10 > <?php
$affiche=affichageMessageSecurite();
?>
<A href='#' onMouseOver="AffBulle3('ATTENTION','image/commun/warning.jpg','<font face=Verdana size=1><?php print $affiche ?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center border=0></A>

<br /><br />

<br />
<ul><ul>
<script language=JavaScript>buttonMagicSubmit2("<?php print "Confirmer Importation "?>","<?php print LANGbasededon201?>","<?php print LANGBT5?>"); //text,nomInput</script>
<!-- <script language=JavaScript>buttonMagicSubmit("<?php print LANGbasededon20?>","<?php print LANGbasededon201?>"); //text,nomInput</script> -->
</ul></ul></ul>
<br><br><br><br>
</font>
&nbsp;&nbsp;<?php print LANGbasededon21?>

</form>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
<SCRIPT language="JavaScript">InitBulle('#000000','#FFFFFF','red',1);</SCRIPT>
</BODY></HTML>
