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
//error_reporting(0);
include_once("./librairie_php/langue.php");
include_once("./librairie_php/lib_verif.php");
include_once("../common/lib_ecole.php");
include_once("../common/lib_admin.php");
include_once("./librairie_php/lib_licence_text.php");
include_once("../common/config.inc.php");
include_once("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade admin</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGVAL?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<table height=100% width=100% border=0 >
<TR><TD align=top id=bordure>
<br><center>
<?php
//----------------------------------------------------------------------------
include_once("./librairie_php/db_triade_admin.php");
$cnx=cnx();

@delete_global();
@verif_secu_rep();

$cr=@validGroup();
if ($cr) {
	$cr=@validperson();
}
if ($cr) {
	$cr=@validtriade($_POST["nom"],$_POST["prenom"],$_POST["mdp"]);
}
if ($cr) {
	$cr=@validabsretard();
}

$fichier_info="../common/md5sum.log";
if (file_exists($fichier_info)) {

	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	foreach ($lines as $line_num => $line) {
		if(preg_match('/ /',$line)){
			list($md5,$fichier)= preg_split ("/  /", $line, 2);
			updateMd5($md5,$fichier);

		}
	}
	
}


// actif le compte
$http=protohttps(); // return http:// ou https://
if ($cr) {
	$date=date("d/m/Y \à G:i:s");
	$fp=fopen("../data/install_log/valid.inc","a+");
	fwrite($fp, "Actif le $date \n");
	fclose($fp);
	?>
	<b><?php print LANGVAL1?></b>.<br>
	<br>
	<?php print LANGVAL3?>
	<br><br>
	<b><?php print $http.$_SERVER["SERVER_NAME"]?>/<?php print REPECOLE?>/</b>
	<br><br><br>
	<font color=red class=T1>Vous avez un dernier message --> <a href="index1.php"><b>ici</b></a></font>
	<br><br>
	<?php
}else {

?>

	<b><?php print LANGVAL4?></b>.<br>
	<br>
		- <?php print LANGVAL5?> <br>
		<br><br>
		- <?php print LANGVAL6?>
		<br><br><br>
		conctacter nous : <br> <br>

		<b>contact@triade-educ.com</b> <br>
		<br>
		 <i>(Objet du message : Erreur 1 - Triade)</i> <br>
	<br>


<?php
}
?>
<br><br>
</center>
<!-- // fin de la saisie -->
</TD></TR></TABLE>
</td></tr></table> <BR><BR><BR>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
<?php Pgclose() ?>

