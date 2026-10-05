<?php
error_reporting(0);
session_start();
if ($_SESSION["googleauthen"] != "ok") { header("Location:index1.php");exit; }
$id_pers=$_SESSION['id_pers'];
$membre=$_SESSION['membre'];
$email=$_SESSION['email'];
$idparent=$_SESSION['idparent'];
session_set_cookie_params(0);
$_SESSION=array();
session_unset();
session_destroy();
$_SESSION["googleauthen"]="ok";
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
	<tr id='coulBar0' ><td height="2" align="center"><b><font id='menumodule1'><?php print "TRIADE-AUTHENTIFICATOR" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td bgcolor='#FFFFFF'  >
	<br>
&nbsp;&nbsp;<img src="image/commun/Google_Authenticator.png" >
<div style="position:relative;top:-130px;left:140px;width:650px" ><font size=4>Google Authentificator</font><br>
<br>

<?php
if (isset($_GET["init"])) {
?>
<form method='post' action='acces2.php' >
Indiquer le code reçu par email venant de <?php print mask_email($email) ?><br><br>
<input type='text' name='codeverif' > <input type='submit' value='Valider' name='authentificatorinit' />
<input type='hidden' name='idpers' value="<?php print $id_pers ?>" />
<input type='hidden' name='membre' value="<?php print $membre ?>" />
<input type='hidden' name='idparent' value="<?php print $idparent ?>" />
<br><br><br>


<?php 
}else{
?>

<form method='post' action='acces2.php?id' >
Indiquer le code de votre Google authentificator concernant TRIADE.<br><br>
<input type='text' name='code' > <input type='submit' value='Valider' name='authentificator'/>
<input type='hidden' name='idpers' value="<?php print $id_pers ?>" />
<input type='hidden' name='membre' value="<?php print $membre ?>" />
<input type='hidden' name='email' value="<?php print $email ?>" />
<input type='hidden' name='idparent' value="<?php print $idparent ?>" />
<br><br><br>
Recommencer le process d'authentification : <input type='submit' value='Recommencer' class='button' name="resetauthentificator"  />
</form>

<?php } ?>
<br>
<p class="left">&nbsp; &nbsp;<strong>L'&eacute;quipe TRIADE&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
</div>
</td></tr></table>
<script type="text/javascript" src="./librairie_js/menudepart22.js"></script>
</body>
</html>
