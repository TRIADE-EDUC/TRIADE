<?php
session_start();
error_reporting(0);
include_once("../librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
 *   Site                 : http://www.triade-educ.org
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   any later version.
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<title>Triade</title>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >V&eacute;rification et optimisation de la Base</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<i>&nbsp;&nbsp;Afin d'assurer la validit&eacute; des fichiers Triade, nous vous sug&eacute;rons d'installer le patch de r&eacute;f&eacute;rence MD5 avant chaque v&eacute;rification. 
[ <a href="https://www.triade-educ.org/accueil/recupFichierMd5.php?inc=100" target='_blank' >T&eacute;l&eacute;charger la r&eacute;f&eacute;rence MD5</a> ] </i>
<br><br>
<ul>
<?php

@unlink("../common/md5sum.log");

include_once("./librairie_php/db_triade_admin.php");

include_once("../common/lib_patch.php");


$cnx=cnx();

// 0 corrige
// 1 c'est ok
// 2 erreur
// 3 erreur avec ?

$codepatch="2";
supp_rep_patch();


$codemd5="2";

$codeg="2";
$codeg=verif_table_groupe();

$coderep="2";
$coderep=verif_secu_rep();

$codefic="2";
$codefic=verif_fichier(ADMIN);

$codeperm=2;
if (is_writable("../data/install_log/install.inc")) {
	$codeperm=1;
}

$codeaffectation="2";
$codeaffectation=verif_affectation();

$codeoptimization="2";
$codeoptimization=optimize_mysql();

$verifmatiere="2";
$verifmatiere=verif_matiere();

$coderep0="2";
$coderep0=verif_repertoire();

$veriftable="2";
$veriftable=verif_table();

$coderep3="2";
//$coderep3=purgeData();
$coderep3="1";

$codeconfig="2";
$codeconfig=verif_config();
$codeconfig=htaccessRacine();

$configSafeMode="2";
$id=php_ini_get("safe_mode");
if ($id != 1) { $configSafeMode="1"; }

$configRegisterGlobals=2;
$id=php_ini_get("register_globals");
if ($id != 1) { $configRegisterGlobals="1"; }

$configMagicQuotesGPC=2;
$id=php_ini_get("magic_quotes_gpc");
if ($id == 1) { $configMagicQuotesGPC="1"; }


$configGD="";
$id=php_module_load("gd");
if ($id == 1) { $configGD="1"; }


$configSQLite="";
$id=php_module_load("SQLite3");
if ($id == 1) { $configSQLite="1"; }

$configSimpleXML="";
$id=php_module_load("SimpleXML");
if ($id == 1) { $configSimpleXML="1"; }

$dbbstructure="2";
$dbbstructureMD5=verifDbb();

$verifagenda="2";
$verifagenda=verifAgenda();

$intramsn="2";
$intramsn=intraMSN();


$verifmessagerie="2";
$verifmessagerie=verifMessagerie();


?>
<table border=0 >
<tr><td>
	<img src="./image/commun/stat0.gif" align=center>&nbsp;&nbsp;&nbsp;- Corrig&eacute; / r&eacute;actualiser la page pour valider <br>
	<img src="./image/commun/stat1.gif" align=center>&nbsp;&nbsp;&nbsp;- Fonctionnement normal.<br>
	<img src="./image/commun/stat3.gif" align=center> - Erreur cliquer sur l'icone pour plus d'informations <br>
	<img src="./image/commun/stat2.gif" align=center>&nbsp;&nbsp;&nbsp;- Erreur non corrig&eacute;e / contacter le support Triade. &nbsp;&nbsp;<br>
	<img src="./image/commun/stat.gif" align=center>&nbsp;&nbsp;&nbsp;- Extension non impl&eacute;mentatio n&eacute;cessaire dans certains modules. &nbsp;&nbsp;
</td></tr></table>

<br><br>
<table border=0 >

<?php
if (LAN == "oui") {
?>
	
<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des patchs  </font> &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<a href="#" id=lienpatch ><img src="./image/commun/stat<?php print $codepatch ?>.gif" align=center id="verifpatch"  border="0"></a>
&nbsp;&nbsp;&nbsp;
</td></tr>
	<script language="JavaScript" src="https://support.triade-educ.org/support/version-patch.php?v=<?php print VERSIONPATCH ?>"></script>
	<script language="JavaScript">
	if (update == 0) { document.getElementById("verifpatch").src="./image/commun/stat1.gif"; }
	if (update == 1) { 
		document.getElementById("verifpatch").src="./image/commun/stat3.gif"; 
		document.getElementById("lienpatch").href="update.php";
	}
	</script>




<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification fichier MD5 </font>  &nbsp;&nbsp;&nbsp;
</td><td>&nbsp;&nbsp;&nbsp;
	<a href="#" id=lienmd5 target="_blank" ><img src="./image/commun/stat<?php print $codemd5 ?>.gif" align=center id="verifmd5"  border="0"></a>
	&nbsp;&nbsp;&nbsp;
</td></tr>
	<?php 
	if (file_exists("../common/config-md5.php")) {
		include("../common/config-md5.php");
	}else{
		define("VERSIONMD5","000");
	}
	?>
	<script language="JavaScript" src="https://support.triade-educ.net/support/version-md5.php?v=<?php print VERSIONMD5 ?>"></script>
	<script language="JavaScript">
	if (updatemd5 == 0) { document.getElementById("verifmd5").src="./image/commun/stat1.gif"; }
	if (updatemd5 == 1) { 
		document.getElementById("verifmd5").src="./image/commun/stat3.gif"; 
		document.getElementById("lienmd5").href="https://www.triade-educ.org/accueil/recupFichierMd5.php?inc=100";
	}
	</script>




<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des fichiers Triade</font>  &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<?php
if ($codefic == 3) {
?>
	<a href="listchecksum.php"><img src="./image/commun/stat<?php print $codefic ?>.gif" align=center border=0 alt="Consulter" ></a>
	&nbsp;&nbsp;&nbsp;
<?php 
}else{
	print "<img src='./image/commun/stat$codefic.gif' align=center >";
	print "&nbsp;&nbsp;&nbsp;";
}
?>
</td></tr>

<?php
if (false) {
?>
<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification structure base de donn&eacute;e  </font> &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<a href="#" id="lienMD5Structure" ><img src="./image/commun/stat<?php print $dbbstructure ?>.gif" align=center id="verifMD5Structure"  border="0"></a>
&nbsp;&nbsp;&nbsp;
</td></tr>
	<script language="JavaScript" src="https://support.triade-educ.net/support/verif_structure.php?md5=<?php print $dbbstructureMD5 ?>"></script>
	<script language="JavaScript">
	if (updateMD5 == 0) { document.getElementById("verifMD5Structure").src="./image/commun/stat1.gif"; }
	if (updateMD5 == 1) { 
		document.getElementById("verifMD5Structure").src="./image/commun/stat3.gif"; 
		document.getElementById("lienMD5Structure").href="checkSumStructure.php";
	}
	</script>

<tr><td>
<?php } ?>

<?php 
	}else{
		print "<img src='../image/commun/warning2.gif' align='left' /><font id='color3'><center>INFORMATION : Vous devez valider l'acc&egrave;s internet via le module [<a href='configuration.php'><font id='color3'>Config. G&eacute;n&eacute;rale</font></a><font id='color3'>] pour une v&eacute;rification totale.</font></center><br><br>";

	}
?>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des groupes  </font> &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $codeg ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Optimisation des r&eacute;pertoires </font>  &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $coderep3 ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Optimisation de l'Intra-Messenger </font>  &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $intramsn ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des r&eacute;pertoires </font>  &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $coderep0 ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>





<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des mati&egrave;res</font> &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $verifmatiere ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des tables </font>&nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $veriftable ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification de l'agenda </font>&nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $verifagenda ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification de la messagerie </font>&nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $verifmessagerie ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>





<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Optimisation des tables </font>&nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $codeoptimization ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> S&eacute;curit&eacute; des r&eacute;pertoires </font>  &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $coderep ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des droits '&eacute;critures</font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $codeperm ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> V&eacute;rification des fichiers de config.</font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $codeconfig ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Configuration PHP  safe_mode </font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $configSafeMode ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Configuration PHP  register_globals  </font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $configRegisterGlobals ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Extension PHP / GD </font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $configGD ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>

<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Extension SQLite </font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $configSQLite ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


<tr><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/on1.gif" height=8 width=8 align=center ><font class="T2"> Extension SimpleXML </font>   &nbsp;&nbsp;&nbsp;
</td><td>
&nbsp;&nbsp;&nbsp;
<img src="./image/commun/stat<?php print $configSimpleXML ?>.gif" align=center >
&nbsp;&nbsp;&nbsp;
</td></tr>


</table>
</ul>


<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
<?php history_cmd("ADMIN","VERIFICATION","Data & Base"); Pgclose(); ?>
</html>
