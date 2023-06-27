<?php
session_start();
error_reporting(0);
if (empty($_SESSION["nom"]))  {
    header('Location: ../acces_refuse.php');
    exit;
}
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="../librairie_js/acces.js"></script>
<script language="JavaScript" src="../librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="../librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyforum' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("../librairie_php/lib_licence_forum.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0' ><td height="2" valign='top' ><b><font   id='menumodule1' >
<?php 
if ($_SESSION["membre"] == "menueleve") {
	print "Forum Elève ";
}
if ($_SESSION["membre"] == "menuadmin") {
	print "Forum Direction ";
}
if ($_SESSION["membre"] == "menuparent") {
	print "Forum Parent ";
}
if ($_SESSION["membre"] == "menuprof") {
	print "Forum Enseignant ";
}
if ($_SESSION["membre"] == "menuscolaire") {
	print "Forum Vie Scolaire ";
}
print LANGFORUM1 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td  valign='top'>
<!-- // fin  -->
<?php 
if ( ! file_exists("../data/forum") ) {
	@mkdir("../data/forum",0755);
	$text="<Files \"*\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>";
	$fp = fopen("../data/forum/.htaccess", "w");
	fwrite($fp,$text);
	fclose($fp);
}

$repforum="../data/forum/".$_SESSION["membre"];

if ( ! file_exists($repforum) ) {
	@mkdir("$repforum",0755);
	$text="<Files \"*\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>";
	$fp = fopen("${repforum}/.htaccess", "w");
	fwrite($fp,$text);
	fclose($fp);
}


include_once("./listemessages.php"); 

?>
<!-- // fin  -->
</td></tr></table>
</BODY></HTML>
