<?php
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
<!--
/************************************************************
Last updated: 01/12/2004    par Taesch  Eric

Last updated: 31/07/2006    par Pirio Mikaël
  - Correction du code pour la validation XHTML 1.0 - strict
*************************************************************/
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php define("CHARSET","iso-8859-1"); ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="librairie/css.css" />
		<title>Triade Installation</title>
	</head>

	<body>

		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center;">

			<div id="mainInst2">
				<img src="./image/logo_triade_licence.gif"
				     alt="logo_triade_licence" />

<?php
	include_once("../common/version.php");
	include_once("./librairie/licence.php");

	$disable="";

	if (isset($_GET["inst"])) {
		$disable="disabled=\"disabled\"";
	}
?>

				<p>
				<font class=T2>Version : <b><?php print VERSION; ?></b><br />
				Licence d'utilisation  : <?php print LICENCE; ?> <br />
				Product ID = <b> <?php print PRODUCTID; ?> </b><br />
				</font></p>
				<div style="text-align: center">
				<textarea cols="80" rows="13"
				          style="font-family: Arial;
				                 font-size:10px;
				                 color:#CC0000;
				                 background-color:#CCCCFF;
				                 font-weight:bold;
				                 margin-right: auto;
						 width:450px;
				                 margin-left: auto;">
					<?php droit(); ?>
				</textarea>
				</div>
				<font class=T2><p>Triade&copy;, 2000 - <?php print date("Y") ?></p></font>
				<?php
				 $fichier = "../data/install_log/install.inc";
				 if (file_exists($fichier)) {
				 	$disable="disabled='disabled'"; 
				 }
				?>							
				<div style="text-align: right;
		  		          padding-right: 100px;
		  	  	        margin-bottom: 1em;" >
					<input type="submit" onclick="open('suite0.php','_parent','')"
				  	     value="Accepter" class="BUTTON" <?php print $disable; ?> /><br><br>
					     <?php if (file_exists($fichier)) { ?>  <b><font class=T2 color='red' >Triade est d&eacute;j&agrave; install&eacute; !</font></b> <?php } ?>
				</div>
			</div>
		</div>

<?php
	include_once("librairie/pied_page.php");

	if (isset($_GET["inst"])) {
		if ($_GET["inst"] == "1") { print "<script type='text/javascript'>alert(\"Triade est déjà installé.\")</script>"; }
		if ($_GET["inst"] == "2") { print "<script type='text/javascript'>alert(\"Triade est déjà upgradé. \")</script>"; }
	}
?>

	</body>
</html>
