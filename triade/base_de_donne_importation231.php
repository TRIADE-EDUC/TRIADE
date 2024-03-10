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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(120);
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
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importation d'un fichier Excel" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);
$cnx=cnx();

$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];
$nbeleveaffecte=0;
$ok=0;

$taille=2000000;
$taille2="2Mo";
include_once("librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(600); // en secondes
	$taille=8000000;
	$taille2="8Mo";
}

if ( (!empty($fichier)) && (($type == "application/octet-stream" ) || ($type == "application/vnd.ms-excel" ))) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	@unlink("data/fichier_gep/traitement.xls");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/traitement.xls");
	@unlink("data/fichier_gep/$fichier");
	print "<br /><font class=T2><center>".LANGIMP40."</center></font><br />";

	$fic_xls="data/fichier_gep/traitement.xls";
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
	/*
	 * 1) libelle  
	 * 2) libelle court
	 * 3) libelle long
	 * 4) code matiere  
	 * 5) libelle en
	 */ 
	$nb=0;
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		$libelle="";
		$libelle_long="";
		$code_matiere="";
		$libelle_en="";

	 	$libelle=trim(addslashes($data->sheets[0]['cells'][$i][1]));
		if ($libelle == "") { continue; }
	 	$libelle_long=trim(addslashes($data->sheets[0]['cells'][$i][2]));
		$code_matiere=trim(addslashes($data->sheets[0]['cells'][$i][3]));
		$libelle_en=trim(addslashes($data->sheets[0]['cells'][$i][4]));
		$cr=create_matiere_3($libelle,$libelle_long,$code_matiere,$libelle_en);
		if ($cr) $nb++;
	}

}else {
	$ok=1;
}

@unlink("$fic_xls");

if ($ok == 1) {
?>
	<br /><center><font color=red ><?php print LANGIMP43 ?><BR><BR>
	<?php print LANGIMP44 ?></font><br /><br />
	<input type=button Value="<?php print LANGBT24?>" onclick="javascript:history.go(-3)" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br />
	<br /></center>
<?php
}else{
		// creation ou mise a jour du fichier log  avec prise en
		$today=dateDMY();
		$fichier_s=fopen("./".REPADMIN."/data/fic_opinion.txt","a+");
		$donnee=fwrite($fichier_s,"<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $_SESSION[nom] $_SESSION[prenom]</FONT> <BR>Membre : <font color=red> $_SESSION[membre] </FONT><BR> <B>Message :</B> <font color=red> IMPORT MATIERES</font> - avec fichier XLS <BR>  Etablissement : <font color=red>".REPECOLE."</font> ");
		fclose($fichier_s);
		// suppression du fichier ASCII
		@unlink($fic_ascii);
?>
<ul>
<font class='T2'><?php print LANGTMESS501 ?> <?php print $nb ?><br><br>
<script language=JavaScript>buttonMagic("<?php print LANGBT16?>","list_matiere.php","_parent","","");</script>
<script language=JavaScript>buttonMagicRetour("base_de_donne_importation230.php","_parent")</script>&nbsp;&nbsp;
<?php
}
Pgclose();
?>

<br><br />
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
