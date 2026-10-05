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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<style>
.pa-msg-ok{background:#e8f5e9;border:1px solid #a5d6a7;border-radius:7px;padding:12px 16px;font-size:12px;color:#2e7d32;margin:8px 0}
.pa-msg-err{background:#fce4ec;border:1px solid #f48fb1;border-radius:7px;padding:12px 16px;font-size:12px;color:#c62828;margin:8px 0}
</style>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/logo.js"></script>
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
if (isset($_POST["mdp"])) {
	$cnx=cnx();
	$email=$_POST["email"];
	if (($_POST["membre"] == "ELE") || ($_POST["membre"] == "PAR")) {
		$info=rechercheCompteEmailMdp($email);
	}else{
		$info=rechercheCompteEmailMdpPersonnel($email,$_POST["membre"]);
	}
	if ( (trim($info) != "") && (trim($email) != "") ) {
		$mdp=passwd_random2();
		list($membre,$idpers)=preg_split('/:/',$info);
		envoiMailModifMotdePasse($membre,$idpers,$email);
		$messageOk = true;
		$message=LANGTMESS400."<br>".LANGTMESS401." : <b>$email</b>";
	}else{
		$messageOk = false;
		$message=LANGTMESS402."<br><br>".LANGTMESS403." ".LANGTMESS404.": <a href='probleme_acces.php?id'>".LANGTMESS405."</a>";
	}
	Pgclose();
?>
<table border='0' width='100%' style="background:#f8f9ff;border:2px solid #c5cae9;border-radius:8px;box-shadow:0 2px 8px rgba(8,10,102,.10)">
<tr><td style="padding:16px 18px">
	<div class="<?php print $messageOk ? 'pa-msg-ok' : 'pa-msg-err' ?>"><?php print $message ?></div>
	<p style="font-size:12px;color:#555;margin:10px 0 0 0"><?php print LANGattente3 ?></p>
</td></tr>
</table>

<?php }else{ ?>
<form name="formulaire" method="post">
<table border='0' width='100%' style="background:#f8f9ff;border:2px solid #c5cae9;border-radius:8px;box-shadow:0 2px 8px rgba(8,10,102,.10)">
<tr><td style="padding:16px 18px">

	<p style="font-size:13px;font-weight:700;color:#080A66;margin:0 0 6px 0"><?php print LANGMESS151 ?></p>
	<p style="font-size:12px;color:#555;margin:0 0 14px 0"><?php print LANGMESS152 ?></p>

	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
	<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap">Mode d'accès :</td>
	<td style="padding:5px 0">
	<select name="membre" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:200px">
		<option value="" id='select0'><?php print LANGCHOIX ?></option>
		<option value="ELE" id='select1'>Etudiant/Elève</option>
		<option value="PAR" id='select1'>Parent d'élève</option>
		<option value="ENS" id='select1'>Enseignant</option>
		<option value="MVS" id='select1'>Vie Scolaire</option>
		<option value="PER" id='select1'>Personnels</option>
		<option value="TUT" id='select1'>Tuteur de stage</option>
		<option value="ADM" id='select1'>Direction</option>
	</select>
	</td></tr>
	<tr>
	<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap"><?php print LANGELE244 ?> :</td>
	<td style="padding:5px 0">
	<input type=text name="email" size=40 value="<?php print htmlspecialchars($_POST["email"]) ?>" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:260px">
	</td></tr>
	</table>

	<br><center><input type=submit name="mdp" value="<?php print LANGMESS153 ?>" style="background:#080A66;color:#fff;border:none;border-radius:7px;padding:9px 22px;font-size:12px;font-weight:700;cursor:pointer"></center>

</td></tr>
</table>
</form>
<?php } ?>
<br>
</tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
