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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<!-- // debut de la saisie -->

<?php 
include_once("../librairie_php/lib_defilement.php");
include_once("../common/config2.inc.php");
if ((HTTPS == "non") && (defined("HTTPS")))  {
	print "<script type='text/javascript'>var http='http://';</script>\n";
}else{
	print "<script type='text/javascript'>var http='https://';</script>\n";
}
if (defined("POPUP")) {
	if (POPUP == "non") {
	        print "<script language='JavaScript'>var popup='non';</script>";
	}else {
        	print "<script language='JavaScript'>var popup='oui';</script>";
	}
}else{
	print "<script language='JavaScript'>var popup='oui';</script>";
}
print "<script type='text/javascript'>var vocalmess='apropos';</script>\n";
if (defined("GRAPH")) {  
	print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n"; 
}else{
	print "<script type='text/javascript'>var inc='0';</script>\n"; 
}
if (file_exists("../common/lib_patch.php")){
	include_once('../common/lib_patch.php');
	if (!defined("VERSIONPATCH")) {
		$VERSIONPATCH="";
	}else{
		$VERSIONPATCH=VERSIONPATCH;
	}
	$rev="<br>Rev : <i>".$VERSIONPATCH."</i> ";
	if (defined("VERSIONMD5")) $rev.=" - <i>".VERSIONMD5."</i>";
}

?>

<table border="1" bgcolor=#FFFFFF  bordercolor="#000000" cellpadding="3" cellspacing="1" width="100%"  height="85" style="box-shadow: 10px 10px 5px #656565;border-radius: 25px;";
>

<tr ><td  id='bordure'> <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='../image/commun/logo_triade_licence.png' width='50%' />
<ul>
<?php
include_once("../common/version.php");
include_once("../librairie_php/langue-text-fr.php");
?>
<BR><BR><?php print LANGAPROPOS1 ?> : <b><?php print VERSION?></b>
<?php print $rev ?>
<BR><?php print LANGAPROPOS2 ?> <BR>
<?php print LANGAPROPOS3 ?>  : <?php print LICENCE?> <BR>
<?php print LANGAPROPOS4 ?> = <font class='T1'>  <?php print PRODUCTID?> </font>
<BR><BR>
<textarea cols=80 rows=10 STYLE='font-family: Arial;font-size:10px;color:#080A66;background-color:#CACCEF;font-weight:bold;'>
<?php droit(); ?>
</textarea>
<br><br>
<?php 
if (defined("DATEOUT")) {
	$DATEOUT=DATEOUT;
}else{
	$DATEOUT="";
}
?>
Triade &copy;, <?php print DATEOUT ?> <br>
<a href="http://www.triade-educ.org" target="_blank">www.triade-educ.org</a>
</ul>
</td></tr></table>
<!-- // fin de la saisie -->
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
