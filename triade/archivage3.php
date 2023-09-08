<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) { set_time_limit(0); }
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/lib_attente.php");
include_once("./librairie_php/timezone.php");
?>
<script>AfficheAttente()</script>
<?php
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Archive des bulletins" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<br /><br />
<center>
<table border="0" align="center">  

<?php
include_once("./librairie_php/db_triade.php");
$anneeScolaire=trim(preg_replace('/ /','',$_POST["annee_scolaire"]));
$rep="./data/archive/bulletin/$anneeScolaire";
if ((is_dir($rep)) && ($anneeScolaire != "")) {

	@mkdir("./data/archive/tmp/");
	@mkdir("./data/archive/tmp/$anneeScolaire");
	
	$cnx=cnx();

	$dir=opendir("$rep");
        while ($file = readdir($dir)) {
		$file=trim($file);
                if (($file != ".triade") && ($file != ".htaccess" )  && ($file != "." ) && ($file != ".." )) {
			$repanalyse="$rep/$file";
			$file=preg_replace('/_/','',$file);
			$nomEtudiantPrenom=recherche_eleve_nom($file)." ".recherche_eleve_prenom($file);
			if (trim($nomEtudiantPrenom) == "") continue; 
			$nomEtudiantPrenom=preg_replace('/ /','_',$nomEtudiantPrenom);
			$destination="./data/archive/tmp/$anneeScolaire/$nomEtudiantPrenom/";
			@mkdir("$destination");
			if ((is_dir($repanalyse)) && (trim($file) != "")) {
				$dir2=opendir("$repanalyse");
				while($file2 = readdir($dir2)) {
                			if (($file2 != ".triade") && ($file2 != ".htaccess" )  && ($file2 != "." ) && ($file2 != ".." )) {
						@copy("$repanalyse/$file2","$destination/$file2");			
					}
				}
        			closedir($dir2);
			}
                }
        }
        closedir($dir);
	pgClose();


	include_once('./librairie_php/pclzip.lib.php');
	@unlink('./data/archive/bulletin/'.$anneeScolaire.'.zip');
	$archive = new PclZip('./data/archive/bulletin/'.$anneeScolaire.'.zip');
	$archive->create('./data/archive/tmp/'.$anneeScolaire,PCLZIP_OPT_REMOVE_PATH, 'data/archive/tmp');
	$fichier='./data/archive/bulletin/'.$anneeScolaire.'.zip';

	recursive_delete("./data/archive/tmp/$anneeScolaire");

	
	print "<br><br><table align='center'>";
	print "<tr><td><script>buttonMagic('".LANGSTAGE73."','archivage2.php','_self','','')</script>";
	print "</td>";
	print "<td>";
	print "<input type='button' value='Télécharger le fichier ZIP' class=BUTTON onClick=\"open('telecharger.php?fichier=$fichier','_blank','')\" />"; 
	print "</td>";
	print "</tr></table><br><br>";

}else{
	print "<font class='T2'>";
	print "Pas de bulletin pour cette année scolaire : ".$_POST["annee_scolaire"];
	print "</font>";
	print "<br><br><table align='center'>";
	print "<tr><td><script>buttonMagic('".LANGSTAGE73."','archivage2.php','_self','','')</script>";
	print "</td></tr></table><br><br>";
}
?>
</font>
<br /><br />
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
