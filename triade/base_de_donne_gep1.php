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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
	<?php
	if (empty($_SESSION["adminplus"])) {
		print "<script>";
		print "location.href='./base_de_donne_importation.php'";
		print "</script>";
	}
	?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGEP1?></font></b></td></tr>
     <tr id='cadreCentral0'>
     <td >
     <!-- // fin  -->
<ul>
<br />
<form method=post  action='./base_de_donne_gep2.php' name=formulaire ENCTYPE="multipart/form-data">

<font class=T2><?php print LANGGEP2?> : <b>F_ELE.DBF</b>  <input type="file" name="fichier1" size=20 ></font> <br /><br />


<br />
<br />
<?php print LANGBASE41 ?> : <input type=checkbox name="vide_eleve" value="oui" > (<?php print LANGOUI ?>)
<br />
<?php
$annee=date("Y")-1;
$annee=$annee."-".date("Y");
if (file_exists("./data/archive/$annee.sqlite")) {
	print "<font color=red>Attention la suppression des l'élèves, supprimera toutes les archives !!</font><br>";
}
?>
<br />
<br />
<ul><ul>
<script language=JavaScript>buttonMagicSubmit2("<?php print LANGBT23?>","<?php print LANGbasededon201?>","<?php print LANGBT5?>"); //text,nomInput</script>
<!-- <script language=JavaScript>buttonMagicSubmit("<?php print LANGbasededon20?>","<?php print LANGbasededon201?>"); //text,nomInput</script> -->
</ul></ul></ul>
<br><br><br>
</font>
&nbsp;&nbsp;<?php print LANGbasededon21?>
<br>
</form>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
