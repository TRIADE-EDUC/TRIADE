<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.org
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php 
include_once("./librairie_php/lib_netscape.php"); 
include_once("./librairie_php/lib_licence2.php");
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
include_once("./common/config2.inc.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (HTTPS == "non") {
	print "<script type='text/javascript'>var http='http://';</script>\n";
}else{
	print "<script type='text/javascript'>var http='https://';</script>\n";
}
print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";
?>
<script type="text/javascript" >var mailcontact="<?php if (MAILCONTACT != "") { print MAILCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact="<?php if (URLCONTACT != "") { print URLCONTACT; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact="<?php if (URLNOMCONTACT != "") { print URLNOMCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepartconnection1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTTITRE5 ?></FONT></td>
</tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

if (!file_exists("./data/tmpmdp")) mkdir("./data/tmpmdp/");
$file=scandir("./data/tmpmdp/");
foreach($file as $key=>$value) {
	if ($value == ".") continue;
        if ($value == "..") continue;
        $time=filemtime("./data/tmpmdp/$value");
        $time2=time();
        $time2-=180;
        if ($time2 > $time) unlink("./data/tmpmdp/$value");
}

$ref=urldecode($_GET["ref"]);
preg_replace('/\./','x',$ref);

if ((file_exists("./data/tmpmdp/${ref}.txt")) && ($ref != "")) {
	$fd=fopen("./data/tmpmdp/${ref}.txt","r");
	$data=fread($fd,filesize("./data/tmpmdp/${ref}.txt"));
	fclose($fd);
	list($membre,$idpers,$email)=preg_split('/:/',$data);
//	print "$membre $idpers $email";
	if (!is_numeric($idpers)) exit;
	if (($membre != "pers") && ($membre != "tuteur2") && ($membre != "tuteur1") && ($membre != "eleve")) { exit; }

	$mdp=passwd_random2();	
	modifPassOublie($mdp,$idpers,$membre,$email);
	print "<ul>";
	print "<br><br>  Un mot de passe temporaire vient de vous être envoyé.";
	print "<br/><br> Merci de consulter vos emails.<br><br>";	
	print "</ul>";
?>
	<br>
<br>
	</form>
<?php	
}else{
	print "<ul>";
	print "<br><br>  Votre demande de changement de mot de passe a expir&eacute;e !!";
	print "<br/><br> Vous devez renouveler votre demande.<br><br>";	
	print "</ul>";
}
?>



</tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
