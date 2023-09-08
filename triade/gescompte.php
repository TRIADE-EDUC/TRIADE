<?php
session_start();
error_reporting(0);
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
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom] ") ?></title>
	</head>

	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

	<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
	<?php 
	include_once("./librairie_php/lib_rss.php"); // mettre à jour
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade.php");
	$cnx=cnx();
	$alerte=0;
	if (isset($_POST["modif"])) {
		if (ValideMail($_POST["email"])) {
			modifEmail($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"],$_POST["email"]);
			$message="<br><br><center><font id='color3'>".LANGPARAM16."</font></center>";
		}else{
			$message="<br><br><center><font id='color3'>".LANGTMESS408."</font></center>";
		}
	}

	if (VATEL == 1) {
		$email=recupEmailVatel($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
	}else{
		$email=recupEmail($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
	}

	if ((isset($_GET["alerte"])) || ($alerte == 1)) {
		$message="<br><br><center><font id='color3'>".LANGTMESS409;
		if ($alerte == 1) {
			$message.="<br><br><i>".LANGTMESS410."<br>".LANGTMESS411."</i>";
		}
		$message.="</font></center>";
	}



	?>
	
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS161 ?></font></b></td></tr>
	<tr id='cadreCentral0'><td ><br>
	<!-- // fin  -->
	<?php
	if (VERIFEMAIL != "non") { $onblur=" onblur='verifEmail(this)' "; }
	print "<br /><form method='post' action='gescompte.php' >";
	print "<font class='T2'>&nbsp;&nbsp;E-mail <img src='image/commun/email.gif' align='center' /> : <input type='text' name='email' value=\"$email\" maxlenght='250' size='50' $onblur  />";
	print "&nbsp;&nbsp;<input type='submit' value='ok' class='BUTTON' name='modif' />";
	print "</form>";

	print $message;

	if (MODIFTROMBIELEVE == "oui" ) {
		print "<br><br><br>";
		print "&nbsp;&nbsp;<font class='T2'>Modifier votre trombinoscope : <input type='button' value='Modifier' class='button' onclick=\"open('photoajouteleve.php','photo','width=450,height=280')\" /></font>";
      	}
	?>
	<?php
	print "<br><br><br></td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
		print "</SCRIPT>";
	}
	Pgclose();
?>
</BODY></HTML>
