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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(3000);
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGbasededon2011?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete2($_SESSION["adminplus"]);

if (file_exists("./data/fic_pass2.txt")) {
	@unlink("./data/fic_pass2.txt");  // destruction du fichier mot de passe
}

$nb=$_POST["nb"];
$nbpr=0;
$log=0;
for($i=0;$i<$nb;$i++) {
	$valeur=$_POST["saisie_ref"][$i];
	list($nom1,$prenom1,$naissance1,$civil1,$tel1,$rue1,$adres1,$ccp1,$ville1) = preg_split('/\#/',$valeur,9);
	if (trim($nom1) != "") {
			$passwd=passwd_random();; // creation du mot de passe
			$civil1=civ2(trim($civil1));
			$cr=ajout_prof_gep($nom1,$prenom1,$naissance1,$civil1,$tel1,$rue1,$adres1,$ccp1,$ville1,$passwd);
			if ($cr) {
				$log=1;
				$f_pass=fopen("./data/fic_pass2.txt","a+");
				fwrite($f_pass,strtolower(trim($nom1)).";".strtolower(trim($prenom1)).";".$passwd."<br />");
				fclose($f_pass);
				$nbpr++;
			}
	}
}

if ($log) {
	history_cmd($_SESSION["nom"],"IMPORT","GEP fichier enseignant");
}

Pgclose();
?>
<br />
<br />
<center><font class=T2> <?php print $nbpr ?> enseignant(s) enregistré(s). </font></center>
<?php
if (file_exists("./data/fic_pass2.txt")) {
?>
<font class=T2><ul>Mot de passe  enseignant(s) : </font><input type=button class=BUTTON value="<?php print LANGBT40?>" onclick="open('recupepw2.php','_blank','')">
<br /> <br /><font color="red"><?php print LANGBASE17 ?></font>
<?php
}

?>
<br><br>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
