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


	error_reporting(0);
	set_time_limit(0);
	ini_set("memory_limit",-1);

	$fichier = "../data/install_log/install.inc";

	if (file_exists($fichier)) {
		header('Location: index.php?inst=1');
		exit;
	}

	include_once("sql/db-triade.php");
	include_once("../common/config.inc.php");
	include_once("./install_base.php");
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("../common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet"
		      href="librairie/css.css" />
		<title>Triade Installation</title>
	</head>

	<body>

		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center;">
			<div id="mainInst3">
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
				Version : <b><?php print VERSION?></b><br />
     				Licence d'utilisation : <?php print LICENCE?><br />
		     		Product ID = <b>  <?php print PRODUCTID?> </b>
				</p>
				<p style="margin-left: 25px;">
					<span class="T2">
						INSTALLATION DE LA BASE SQL&nbsp;:<br />
						<br />
						Etape <b>2/2 </b>&nbsp;&nbsp;&nbsp;&nbsp;<img src='./image/stat1.gif' alt='Ok' />
					</span>
				</p>
				<form action="recup-fin.php" method="post" id="form" onsubmit="document.getElementById('form').val.disabled=true">
					
					<div style="text-align: right;
					            padding-right: 100px;
					            margin-bottom: 1em;">
						<input type="submit" onclick="open('recup-fin.php','_parent','');this.value='Patientez S.V.P.';"
						       name="val" value=" Suivant --&gt; "
						       class="BUTTON" <?php print $disable ?> />
					</div>
				</form>
			</div>
		</div>

<?php	include_once("./librairie/pied_page.php"); ?>
	</body>
</html>
