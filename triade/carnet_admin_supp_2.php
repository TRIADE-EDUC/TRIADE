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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if  ($_POST["saisie_carnet"] > 0) {
	if (isset($_POST["saisie_carnet"])) {
		$idcarnet=$_POST["saisie_carnet"];
	}
	$nom_carnet=chercheNomCarnet($idcarnet);
}else{
	print "<script>location.href='carnet_admin_supp.php?erreur'</script>";
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET61." : <font id='color2'> $nom_carnet </font>" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top align=center>
<!-- // fin  -->
<br />

<?php 
if (isset($_POST["supp"])) {
	supprimer_carnet($_POST["saisie_carnet"]);	
	print "<font class='T2'>Carnet de Suivi supprimé.</font>";
}else{
	print "<font class='T2'>Carnet de Suivi non supprimé.</font>";
}

?>


<br /><br />
<ul><ul>
<script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
</ul></ul>
<br /><br /><br />
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
