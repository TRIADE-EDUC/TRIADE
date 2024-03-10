<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGbasededoni91 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);

$cnx=cnx();


$nbelevetotal=0;
$nbelevedejaffecte=0;

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];


if ( (!empty($fichier)) && (($type == "application/octet-stream" ) || ($type == "application/vnd.ms-excel" ))) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement.xls");
	@unlink("data/fichier_gep/$fichier");
	$fic_xls="data/fichier_gep/traitement.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
/*
	1) N° eleve  
	2) N° code barre 
*/
		
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		$nbelevetotal++;
		// création du tableau de hash contenant les paramètres de la fonction create_eleve
		$params[num_eleve]= $data->sheets[0]['cells'][$i][1];
		$params[num_code]=  $data->sheets[0]['cells'][$i][2];
		$cr=updateCodeBarre($params);
		if ($cr) {
			$nbeleveaffecte++;
		}
	}		    
}

Pgclose();
?>

<br />
<ul>
<font class='T2'>
- <?php print LANGBASE6bis?> : <?php print $nbelevetotal?><br>
- <?php print "Total d'élève réactualisé"?> : <?php print $nbeleveaffecte?><br>
</font>
<br><br>
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
