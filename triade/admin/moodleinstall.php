<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  - 
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
//error_reporting(0);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<?php include("./librairie_php/lib_licence.php"); ?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Procédure d'installation de Moodle</font></b></td></tr>
<tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
if (file_exists('no-install-moodle')) {
	$webRoot = realpath($_SERVER['DOCUMENT_ROOT']);
	$nonWebRoot = dirname($webRoot);
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$host = parse_url($url, PHP_URL_HOST);
	if (!is_dir("$nonWebRoot/moodle")) { mkdir("$nonWebRoot/moodle/"); }
	print "<br><br>Création du répertoire Moodle hors Document_Root : $nonWebRoot/moodle/$host/moodle"; 
	if (!is_dir("$nonWebRoot/moodle/$host")) { mkdir("$nonWebRoot/moodle/$host"); } 
	print "<br><br>Création du répertoire Moodledata hors Document_Root : $nonWebRoot/moodle/$host/moodledata"; 
	if (!is_dir("$nonWebRoot/moodle/$host/moodledata")) { mkdir("$nonWebRoot/moodle/$host/moodledata"); } 
	print "<br><br>";

	if (!class_exists('ZipArchive')) { 
		print '<center><font color=red>Extension ZIP non activée, Procédure d\'installation interrompue !! </font></center>';  
	}else{
		$zipFile = "../installation/data/moodle.zip";
		if (file_exists($zipFile)) {
			$dest="$nonWebRoot/moodle/$host/";
			$zip=new ZipArchive();
			if ($zip->open($zipFile) === true) {
			    	$zip->extractTo($dest);
			    	$zip->close();
				echo 'Décompression terminée<br><br>';

				$target="$nonWebRoot/moodle/$host/moodle/public";
				$link="../moodle";
				symlink($target,$link);

				print "Finaliser la procédure d'installation en cliquant sur ce lien : "; 
				print "<input type=button onclick=\"open('../moodle/','_blank','');\" value=\"Finir l'installation de Moodle\" STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>";
				unlink("./no-install-moodle");
			} else {
			    echo '<center><font color=red>Impossible d\'ouvrir le zip</font></center><br><br>';
			}
		}else{
	   		echo '<center><font color=red>Impossible d\'ouvrir le zip</font></center><br><br>';
		}
	}

}else{
	print "<center><font class='T2'>Moodle est déjà installé.</font></center>";
}
?>
<br><br><br>
<!-- // fin de la saisie -->
</blockquote> </td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
