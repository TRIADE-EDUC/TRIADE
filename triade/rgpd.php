<?php
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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/logo.js"></script>
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

<script type="text/javascript" >var urlcontact2="<?php if ((URLCONTACT2 != "")  && (defined("URLCONTACT2")))  { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if ((URLNOMCONTACT2 != "")  && (defined("URLNOMCONTACT2"))) { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if ((URLCONTACT3 != "")  && (defined("URLCONTACT3"))) { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if ((URLNOMCONTACT3 != "")  && (defined("URLNOMCONTACT3"))) { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if ((URLCONTACT4 != "")  && (defined("URLCONTACT4"))) { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if ((URLNOMCONTACT4 != "")  && (defined("URLNOMCONTACT4"))) { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php 
include_once("librairie_php/lib_defilement.php");
include_once("./common/config2.inc.php");
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
if (file_exists("./common/lib_patch.php")){
	include_once('./common/lib_patch.php');
	if (!defined("VERSIONPATCH")) {
		$VERSIONPATCH="";
	}else{
		$VERSIONPATCH=VERSIONPATCH;
	}
	$rev="<br>Rev : <i>".$VERSIONPATCH."</i> ";
	if (defined("VERSIONMD5")) $rev.=" - <i>".VERSIONMD5."</i>";
}

?>

</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="1" bgcolor=#FFFFFF  bordercolor="#000000" cellpadding="3" cellspacing="1" width="100%"  height="85" style="box-shadow: 0 8px 32px rgba(0,0,0,.18), 0 2px 8px rgba(0,0,0,.10), 0 1px 2px rgba(0,0,0,.08);border-radius: 25px;"
>

<tr ><td  id='bordure'> <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='./image/commun/logo_triade_licence.png' width='50%' />
<ul>
<?php
if (file_exists('./common/version.php')) 	include_once("./common/version.php");
if (file_exists('../common/version.php')) 	include_once("../common/version.php");
?>
<BR><BR><?php print LANGAPROPOS1 ?> : <b><?php print VERSION?></b>
<?php print $rev ?>
<BR><?php print LANGAPROPOS2 ?> <BR>
<?php print LANGAPROPOS3 ?>  : <?php print LICENCE?> <BR>
<?php if (defined("LANGAPROPOS4") && defined("PRODUCTID")) { print LANGAPROPOS4 . " = <font class='T1'>" . PRODUCTID . "</font><BR>"; } ?>
<BR>
<div style="display:block;margin:16px 16px 16px 0;width:calc(100% - 16px);box-sizing:border-box;padding:14px 18px;border:1px solid #c5cae9;border-radius:8px;font-size:12px;color:#333;background:#f8f9ff;line-height:1.6">
<div style="font-size:13px;font-weight:bold;color:#080A66;margin-bottom:10px;">Règlement Général sur la Protection des Données</div>
L'application TRIADE a pour objectif de suivre les résultats et la vie scolaire des élèves tout au long de leur scolarité au sein d'un établissement.<br><br>
L'accès est réalisé par différents comptes : La direction, les enseignants, la vie scolaire, parents, élèves, entreprises et personnels administratifs.<br><br>
Politique de confidentialité du logiciel TRIADE : <input type='button' value='Consulter' onClick="open('https://triade-educ.org/fr/confidentialite-logiciel.php','_blank','')" style="font-family:Electrolize,Arial,sans-serif;font-size:11px;font-weight:600;color:#080A66;background-color:#eef0f8;border:1px solid #c5cae9;border-radius:5px;padding:3px 10px;cursor:pointer;" />
<?php
if (file_exists("./data/parametrage/registre_RGPD_triade.rtf")) {
?>
<br><br>Voici le registre RGPD de l'établissement scolaire : <input type='button' value='Editer le registre RGPD' onClick="open('telechargerrgpd.php','_blank','')" style="font-family:Electrolize,Arial,sans-serif;font-size:11px;font-weight:600;color:#080A66;background-color:#eef0f8;border:1px solid #c5cae9;border-radius:5px;padding:3px 10px;cursor:pointer;" />
<?php } ?>
</div>
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
<!-- // fin de la saisie -->
<br><br>
</TD></TR></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
