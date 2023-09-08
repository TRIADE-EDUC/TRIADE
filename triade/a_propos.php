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
<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence2.php");
include_once("./common/config2.inc.php");
        if ($_COOKIE["langue-triade"] == "fr") {
	        include_once("./librairie_php/langue-text-fr.php");
	        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
	}elseif ($_COOKIE["langue-triade"] == "en") {
	        print "<script type=text/javascript src='librairie_js/langueenmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueenfunction-depart.js'></script>\n";
	       include_once("./librairie_php/langue-text-en.php");
	}elseif ($_COOKIE["langue-triade"] == "es") {
	        print "<script type=text/javascript src='librairie_js/langueesmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueesfunction-depart.js'></script>\n";
	        include_once("./librairie_php/langue-text-es.php");
	}elseif ($_COOKIE["langue-triade"] == "bret") {
	        print "<script type=text/javascript src='librairie_js/languebretmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languebretfunction-depart.js'></script>\n";
	       include_once("./librairie_php/langue-text-bret.php");
	}elseif ($_COOKIE["langue-triade"] == "arabe") {
	        print "<script type=text/javascript src='librairie_js/languearabemenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languearabefunction-depart.js'></script>\n";
	        include_once("./librairie_php/langue-text-arabe.php");
	}else {
	        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
	        include_once("./librairie_php/langue-text-fr.php");
	}
?>
<script type="text/javascript" >var mailcontact="<?php 
		if ((MAILCONTACT != "") && (defined("MAILCONTACT")) ) { 
			print MAILCONTACT; 
		}else{ 
			print ""; 
		} ?>";</script>
	<script type="text/javascript" >var urlcontact="<?php 
		if ((URLCONTACT != "") && (defined("URLCONTACT"))) { 
			print URLCONTACT; 
		}else{ 
			print ""; 
		}  ?>"; </script>
	<script type="text/javascript" >var urlnomcontact="<?php 
		if ((URLNOMCONTACT != "") && (defined("URLNOMCONTACT"))) { 
			$urlnomcontact=preg_replace('/ /',"&nbsp;",URLNOMCONTACT);
			print URLNOMCONTACT; 
		}else{ 
			print ""; 
		} ?>"; </script>

<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php 
include("librairie_php/lib_defilement.php");
include_once("./common/config2.inc.php");
if (HTTPS == "non") {
	print "<script type='text/javascript'>var http='http://';</script>\n";
}else{
	print "<script type='text/javascript'>var http='https://';</script>\n";
}
if (POPUP == "non") {
        print "<script language='JavaScript'>var popup='non';</script>";
}else {
        print "<script language='JavaScript'>var popup='oui';</script>";
}

print "<script type='text/javascript'>var vocalmess='apropos';</script>\n";
print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";


if (file_exists("./common/lib_patch.php")){
	include_once('./common/lib_patch.php');
	$rev="<br>Rev : <i>".VERSIONPATCH."</i> ";
	if (defined(VERSIONMD5)) $rev.=" - <i>".VERSIONMD5."</i>";
}

?>

</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="1" bgcolor=#FFFFFF  bordercolor="#000000" cellpadding="3" cellspacing="1" width="100%"  height="85" style="box-shadow: 10px 10px 5px #656565;border-radius: 25px;";
>

<tr ><td  id='bordure'> <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='./image/commun/logo_triade_licence.gif'>
<ul>
<?php
include_once("common/version.php");
?>
<BR><BR><?php print LANGAPROPOS1 ?> : <b><?php print VERSION?></b>
<?php print $rev ?>
<BR><?php print LANGAPROPOS2 ?> <BR>
<?php print LANGAPROPOS3 ?>  : <?php print LICENCE?> <BR>
<?php print LANGAPROPOS4 ?> = <font class='T1'>  <?php print PRODUCTID?> </font>
<BR><BR>
<textarea cols=60 rows=8 STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'>
<?php droit(); ?>
</textarea>
<br><br>
Triade &copy;, <?php print DATEOUT ?> <br>
<a href="http://www.triade-educ.org" target="_blank">www.triade-educ.org</a>
</ul>
<!-- // fin de la saisie -->
<br><br>
</TD></TR></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
