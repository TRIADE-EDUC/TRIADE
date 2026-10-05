<?php
session_start();
if ($_SESSION["googleauthen"] != "ok") { header("Location:index1.php");exit; }
if ($_SESSION['id_pers'] == "") { header("Location:index1.php");exit; }
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
if (isset($_POST['saveauthen'])) {
	$cnx=cnx();
	$secret=$_POST['secret'];
	enr_authenticator($_SESSION['ip'],$_SESSION['id_pers'],$_SESSION['membre'],$_SESSION['idparent']);
	save_key_authenticator($_SESSION['ip'],$_SESSION['id_pers'],$_SESSION['membre'],$secret,$_SESSION['idparent']);
	Pgclose();
	session_set_cookie_params(0);
	$_SESSION=array();
	session_unset();
	session_destroy();
	header("Location:./index1.php");
	exit;
}


require_once 'librairie_php/GoogleAuthenticator.php';
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();
$qrCodeUrl = $ga->getQRCodeGoogleUrl('TRIADE', $secret);
//echo "Google Charts URL for the QR-Code: ".$qrCodeUrl."<br><br><img src='$qrCodeUrl' /><br><br>";
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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<html>
<head>
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
   <meta http-equiv="pragma" content = "no-cache">
   <meta http-equiv="Cache" content="no store" />
   <meta http-equiv="expires" content = -1>
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="stylesheet" type="text/CSS" href="./librairie_css/css.css" media="screen" />
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/logo.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<?php
	include_once("./librairie_php/lib_netscape.php");
	include_once("./librairie_php/lib_licence2.php");
	include_once("./common/lib_ecole.php");
	$https=protohttps();
 	$noCache=time();
	include_once("./common/version.php");
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
	}elseif ($_COOKIE["langue-triade"] == "it") {
        	print "<script type=text/javascript src='librairie_js/langueitmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueitfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-it.php");
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}
	print "<script type='text/javascript'>var http='http://';</script>\n";
	if ((defined("POPUP")) && (POPUP == "non")) {
		print "<script type='text/javascript'>var popup='non';</script>\n";
	}else {
		print "<script type='text/javascript'>var popup='oui';</script>\n";
	}
	if ($ecoute == 1) {
		print "<script type='text/javascript'>var vocalmess='accueil';</script>\n";
	}else{
		print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
	}
	print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";
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
	<script type="text/javascript" src="./librairie_js/menudepart.js"></script>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center" >

	<div align='center'><?php top_h(); ?>
	<script type="text/javascript" src="./librairie_js/menudepart1.js"></script>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="65">
	<tr id='coulBar0' ><td height="2" align="center"><b><font id='menumodule1'><?php print "TRIADE-AUTHENTIFICATOR"  ?></font></b></td></tr>
	<tr id='cadreCentral0'><td bgcolor='#FFFFFF'  >
	<br>
&nbsp;&nbsp;<img src="image/commun/Google_Authenticator.png" >
<div style="position:relative;top:-130px;left:140px;width:650px" ><font size=4>Google Authentificator</font><br>
<br>
Votre Triade utilise la double vérification d'authentification. <br>
<br>
Afin de pouvoir accès à vos informations, merci d'ajouter à votre application Google Authenficator* cette nouvelle entrée. <br>
<br>
<img src='<?php print $qrCodeUrl ?>' /><br>

<br><br>
<i>*Google Authentificator est disponible gratuitement sous Google Store et Apple Store</i>
<br><br>
<b><i>Vous devez scanner ce QR-CODE via l'application Google Authentificator de votre téléphone portable</i></b>
<br><br>
<form method='post' action='authentificator-enr.php' >
<input type='hidden' value='<?php print $secret ?>' name='secret' />
<input type='submit' class='bouton2' name='saveauthen' value="Cliquer ICI lorsque vous aurez ajouter ce QR-CODE dans votre application Google Authentificator" />
<br>
</form>
<br><br>
<p class="left">&nbsp; &nbsp;<strong>L'&eacute;quipe TRIADE&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
</div>
</td></tr></table>
<script type="text/javascript" src="./librairie_js/menudepart22.js"></script>
</body>
</html>
