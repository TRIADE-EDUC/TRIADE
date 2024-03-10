<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
if (AFFICHAGEVATEL == "oui") header("Location:./vatel/"); 

if (defined("HTTPS")) {
	if (HTTPS == "oui") {
		if ($_SERVER['HTTPS'] != "on") {
			$serv=$_SERVER["SERVER_NAME"];
			header("Location: https://$serv/triade/index1.php");
		}
	}
}

$ecoute=0;
if (!isset($_COOKIE["agentwebecole"])) {
        setcookie("agentwebecole","actu",time()+3600*24*2);
        $ecoute=1;
}


if (isset($_GET["deconnexion"])) {
	session_start();
	// suppression des fichiers tmp
	// ----------------------------
	// fichier des PDF de releve de note pour une classe et 1 matiere
	$fichier="./data/pdf_bull/edition_".$_SESSION["id_pers"].".pdf";
	@unlink($fichier); // pdf tmp
	$fichier="./data/pdf_bull/graph_".$_SESSION["id_pers"].".jpg";
	@unlink($fichier);
	// fichier du module de recherche
	$fichier="./data/recherche/rapport_".$_SESSION["id_pers"].".txt";
	@unlink($fichier);
	$fichier="./data/recherche/rapport_".$_SESSION["id_pers"].".sdc";
	@unlink($fichier);
	$fichier="./data/recherche/rapport_".$_SESSION["id_pers"].".sxi";
	@unlink($fichier);
	$fichier="./data/recherche/rapport_".$_SESSION["id_pers"].".doc";
	@unlink($fichier);
	$fichier="./data/recherche/rapport_".$_SESSION["id_pers"].".xsl";
	@unlink($fichier);
	// Suppresion de la session
	session_set_cookie_params(0);
	$_SESSION=array();
	session_unset();
	session_destroy();
}

if (!file_exists("./common/config-module.php")) { 
	$fp = fopen("./common/config-module.php", "w");
	fwrite($fp,"");
	fclose($fp);
}

$fichier="./data/install_log/install.inc";
if (! file_exists($fichier)) {
	header('Location: ./installation/');
	exit ;
}
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<?php
	include_once("./librairie_php/lib_netscape.php");
	include_once("./librairie_php/lib_licence2.php");
	include_once("./common/lib_ecole.php");
	include_once("./common/config2.inc.php");
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
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
	if (POPUP == "non") {
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
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2" align="center"><b><font id='menumodule1'><?php print LANGTMESS482 ?></font></b></td></tr>
	<tr id='cadreCentral0'><td bgcolor='#FFFFFF'  >
	<?php  if ((LAN == "non") || (preg_match('/demo.triade-educ.net/',WEBROOT))) { ?>
	<div id="introduction"><br>
      	<p class="center" style="color: #000; font-size:12px;">TRIADE
	<strong><?php print VERSION?></strong> est l'outil d'interface entre quatre p&ocirc;les essentiels : <br /> <?php print ucfirst(INTITULEDIRECTION) ?>s - Enseignants - Parents - <?php print ucfirst(TextNoAccent(INTITULEELEVE)) ?>s .</p>
    <p class="center">
       C'est une plateforme internet qui vous permet, <strong style="color: #003399;">au jour le jour</strong>, d'acc&eacute;der aux informations relatives &agrave; la vie de votre l'&eacute;tablissement, que vous soyez une &eacute;cole primaire, un coll&egrave;ge, un lyc&eacute;e ou une universit&eacute;,  &agrave; savoir : </p>
       <ul>
	<table border="0" width="90%"><tr><td>
         <li>les notes quotidiennes des &eacute;l&egrave;ves,</li>
         <li>les bulletins trimestriels, semestriels ou p&eacute;riodiques, </li>
         <li>les circulaires administratives envoy&eacute;es aux parents, </li>
         <li>les circulaires administratives envoy&eacute;es aux professeurs,</li>
         <li>les informations sur la vie scolaire (retards, absences),</li>
         <li>l'envoi de messages personnels aux parents, aux enseignants, </li>
	 <li>l'envoi de SMS aux parents pour les absences et retards, </li>
	 <li>le carnet de suivi, l'emploi du temps, </li>
	 <li>etc... </li>

	<br /><br />
	De <em style="color:#003399">n'importe quel ordinateur</em>
        dot&eacute; d'un acc&egrave;s &agrave; Internet, la Vie
        Scolaire peut transmettre tous ses messages, les enseignants
        peuvent transmettre leurs notes et &nbsp;les parents peuvent
	consulter les r&eacute;sultats scolaires de leur enfant.
	<br />
	<br />Notre site s'efforce de r&eacute;pondre au mieux aux exigences engendr&eacute;es 
	par les nouvelles technologies, nous esp&eacute;rons qu'il vous donnera satisfaction.

	</td></tr></table>
     </ul>
  <p class="right">&nbsp; &nbsp;<strong>L'&eacute;quipe TRIADE&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
 </div>
<?php
}else{
	error_reporting(0);
	if (file_exists("./data/fic_news_page_contenu.txt")) {
		
		$fic=fopen("./data/fic_news_page_contenu.txt","r");
    		$text=fread($fic,filesize("./data/fic_news_page_contenu.txt"));
		fclose($fic);
		if (file_exists("./data/fic_news_page_titre.txt")) {
			$fic=fopen("./data/fic_news_page_titre.txt","r");
    			$titre=fread($fic,filesize("./data/fic_news_page_titre.txt"));
			fclose($fic);
		}
		if (trim($text) != "") {
			if (file_exists("./data/fic_news_page_date.txt")) {
				$fic=fopen("./data/fic_news_page_date.txt","r");
    				$date=fread($fic,filesize("./data/fic_news_page_date.txt"));
				fclose($fic);
			}
		}
	        $text=preg_replace('/&nbsp;/'," ",$text);
       		$text=stripslashes($text);
        	//$text=filtreUFT8($text);
?>
        <table align='center' width='98%' border='0' height='80%' bordercolor='#000000' >
        <tr><td width=90% height=15 id='bordure' >&nbsp;&nbsp;<font size=2><b><?php print stripslashes($titre) ?></b></font></td>
        <td align=center id='bordure' >&nbsp;<i></i>&nbsp;</td></tr>
        <tr><td colspan=2 valign=top id='bordure' > 
		<?php
		$text=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', "\n",$text);
	//	$text=preg_replace('#\n\n\n\n#', "<br />",$text);
	//	$text=nl2br($text);
		$text=stripslashes($text);
		?>
		<br><table border=0 width=98%  cellpadding=5 ><tr><td><?php print $text ?></td></tr></table>
        </td>
	</tr></table>
<?php
	}elseif (defined("HTTPS")) {
		print "<script type='text/javascript' src='https://www.triade-educ.org/sponsor/accueil_js.php?aff=tous'></script>\n";
	}else{
		print "<script type='text/javascript' src='https://www.triade-educ.org/sponsor/accueil_js.php?aff=tous'></script>\n";
	}
} 
?>
</td></tr></table>
<script type="text/javascript" src="./librairie_js/menudepart22.js"></script>
<?php
if ((POPUP == "non") && (LAN == "oui") && (HTTPS != "oui") ) { include_once('librairie_php/xiti.php'); }

if (md5_file("librairie_php/mactu.php") != "7f1e92090ce16c5d90d1acf8e1077010") { ?>


<?php } ?>
</body>
</html>
