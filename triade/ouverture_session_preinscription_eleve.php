<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
   <meta http-equiv="Content-type" content = "text/html; charset=iso-8859-1" />
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="alternate" type="application/rss+xml" title="Actualité Triade" href="http://www.triade-educ.com/accueil/news/rss.xml" />
     <link rel="stylesheet" type="text/CSS" href="./librairie_css/css.css" media="screen" />
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Envoi des candidatures</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
  <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
  <script language="JavaScript" src="./librairie_js/lib_css.js"></script>
	<?php
	include_once("./librairie_php/lib_netscape.php");
	include_once("./librairie_php/lib_licence2.php");
	include_once("./common/lib_ecole.php");
	include_once("./common/config2.inc.php");
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
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}
	if (POPUP == "non") {
		print "<script type='text/javascript'>var popup='non';</script>\n";
	}else {
		print "<script type='text/javascript'>var popup='oui';</script>\n";
	}
	if (HTTPS == "non") {
		print "<script type='text/javascript'>var http='http://';</script>\n";
	}else{
		print "<script type='text/javascript'>var http='https://';</script>\n";
	}
	print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
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
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Merci de vous identifier</font></B></td></tr>
<tr id='cadreCentral0'><td >

<br>
<?php
			if (isset($_GET["error"])) {
			print "<div align=center><font class=T2 color=red><b>Erreur de connexion</b></font></div>";
			}
?>
<ul>
<form method="POST" action="./affiche_preinscription.php" name="loginForm">
   <table border=0 cellpadding=1 >
      <tr><td align="right" ><font class=T2>Adresse email</font></td><td><font class=T2>:</font> <input type="text" name="mail" size="30"  maxlength="50"></td></tr>
      <tr><td  align="right" ><font class=T2>Mot de passe élève</font></td><td><font class=T2>:</font> <input type="password" name="password" size="30"  maxlength="50"></td></tr>
      <tr><td height="10"></td></tr>
      <tr><td></td><td><script language=JavaScript>buttonMagicSubmit('Envoyer','create'); //text,nomInput</script></td></tr>		
   </table>
</form>
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
