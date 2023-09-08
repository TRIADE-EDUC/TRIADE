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


	error_reporting(0);

	$fichier = "../data/install_log/install.inc";

	if (file_exists($fichier)) {
	//	header('Location: index.php?inst=1');
	//	exit;
	}

	include_once("../common/lib_admin.php");
	include_once("../common/lib_ecole.php");
	include_once("../common/config.inc.php");

	$repecole = REPECOLE;
	$repadmin = REPADMIN;
//------------------------------------------------------------------------------------//

	$text = '<?php'."\n";
	$text.= '	if ( $_SESSION["langue"] == "fr") {'."\n";
	$text.= '		define("LANGUE", "french");'."\n";
	$text.= '	}'."\n";
	$text.= '	elseif( $_SESSION["langue"] == "en") {'."\n";
	$text.= '		define("LANGUE", "anglais");'."\n";
	$text.= '	}'."\n";
	$text.= '	elseif( $_SESSION["langue"] == "es") {'."\n";
	$text.= '		define("LANGUE", "espagnol");'."\n";
	$text.= '	}'."\n";
	$text.= '	elseif( $_SESSION["langue"] == "bret") {'."\n";
	$text.= '		define("LANGUE", "breton");'."\n";
	$text.= '	}'."\n";
	$text.= '	elseif( $_SESSION["langue"] == "arabe") {'."\n";
	$text.= '		define("LANGUE", "arabe");'."\n";
	$text.= '	}'."\n";
	$text.= '	elseif( $_SESSION["langue"] == "it") {'."\n";
	$text.= '		define("LANGUE", "italien");'."\n";
	$text.= '	}'."\n";
	$text.= '	else {'."\n";
	$text.= '		define("LANGUE", "french");'."\n";
	$text.= '	}'."\n";
	$text.= '?>'."\n";

	$fp = fopen("../common/choixlangue.php", "w");
	fwrite($fp,$text);
	fclose($fp);

	//------------------------------------------------------------------------------------//
	copy("./data/config2.inc.php","../common/config2.inc.php");
	//---------------------------- 
	copy("./data/config-module.php","../common/config-module.php");
	//-----------------------------
	$texte = '<?php'."\n";
	$texte.= '?>'."\n";
	$fp=fopen("../common/config3.inc.php","w");
	fwrite($fp,"$texte");
	fclose($fp);
	//-----------------------------
	$texte = '<?php'."\n";
	$texte.= 'define("MAILBLACKLIST","non");'."\n";
	$texte.= 'define("MAILMESSSYS","non");'."\n";
	$texte.= 'define("MAILMESSINTER","non");'."\n";
	$texte.= '?>'."\n";
	$fp=fopen("../common/config4.inc.php","w");
	fwrite($fp,"$texte");
	fclose($fp);
	//-----------------------------
	// Jeux de caracteres
	$fp=fopen("../common/config5.inc.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("CHARSET","UTF-8");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//-----------------------------
	$fp=fopen("../common/config6.inc.php","w");
	$text2 = '<?php'."\n";
	$text2.= 'define("MAXUPLOAD","non");'."\n";
	$text2.= '?>'."\n";
	fwrite($fp,"$text2");
	fclose($fp);
	//-----------------------------
	$fp=fopen("../common/config7.inc.php","w");
	$text2 = '<?php'."\n";
	$text2.= 'define("KEYENR","0");'."\n";
	$text2.= '?>'."\n";
	fwrite($fp,"$text2");
	fclose($fp);
	//-----------------------------
	$fp=fopen('../common/config8.inc.php','w');
	$text2 = '<?php'."\n";
	$text2.= 'define("AGENTWEBPRENOM","Lise");'."\n";
	$text2.= 'print "<script language=\"javascript\">";'."\n";
	$text2.= 'print "var agentweb=\"Lise\";";'."\n";
	$text2.= 'print "</script>"'."\n";
	$text2.= '?>'."\n";
	fwrite($fp,"$text2");
	fclose($fp);
	//-----------------------------
	// Lib_access_inc.php
	$fp=fopen("../common/lib_acces_inc.php","w");
	$text3 = '<?php'."\n";
	$text3.= '$LOGIN="administrateur";'."\n";
	$text3.= '$PASSWORD="T2FCyiQz0KthE";'."\n";   // mdp : admin
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//-----------------------------
	// Lib_patch.php
	$fp=fopen("../common/lib_patch.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("VERSIONPATCH","000-00");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//-----------------------------
	// config-fen.php
	$fp=fopen("../common/config-fen.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("LARGEURFEN","780");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//------------------------------
	// mdep.php
	$fp=fopen("../common/mdep.php","w");
	$text3 = '<?php'."\n";
	$text3.= '$MDP="T2FCyiQz0KthE";'."\n";  // mdp : admin
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//------------------------------
	// productId.php
	$fp=fopen("../common/productId.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("PRODUCTID","000");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//config-messenger.php
	$fp=fopen("../common/config-messenger.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("MESSENGERPERS","oui");'."\n";
	$text3.= 'define("MESSENGERELEV","oui");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);
	//
	//config-color.php
	$fp=fopen("../common/config-color.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("COLORTBD","#09CCFF");'."\n";
	$text3.= 'define("COLORTBF","#ADEEFF");'."\n";
	$text3.= 'define("COLORBD","#9ACD22");'."\n";
	$text3.= 'define("COLORBF","#CBFECA");'."\n";
	$text3.= 'define("COLORMD","#FF990B");'."\n";
	$text3.= 'define("COLORMF","#FFD291");'."\n";
	$text3.= 'define("COLORID","#9B396E");'."\n";
	$text3.= 'define("COLORIF","#DCBAFE");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);

	@unlink("../common/config-md5.php"); // suppression du fichier config 000-MD5
	@unlink("../common/crondump.inc.php"); // suppression patch du BACKUP
	@unlink("../common/config-sms.php"); // suppression patch du SMS
	@copy("./librairie/lib_stockage.php","../common/lib_stockage.php");
	@copy("../librairie_css/css.css-31","../librairie_css/css.css");

// -----------------------------------------------------------------------------------//
// gestion du fichier robots.txt
	$text = 'User-agent: Mediapartners-Google'."\n";
	$text.= 'Disallow: /'.$repecole.'/admin/'."\n";
	$text.= 'Disallow: /'.$repecole.'/agenda/'."\n";
	$text.= 'Disallow: /'.$repecole.'/common/'."\n";
	$text.= 'Disallow: /'.$repecole.'/data/'."\n";
	$text.= 'Disallow: /'.$repecole.'/forum/'."\n";
	$text.= 'Disallow: /'.$repecole.'/gedt/'."\n";
	$text.= 'Disallow: /'.$repecole.'/image/'."\n";
	$text.= 'Disallow: /'.$repecole.'/jpgraph/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_css/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_js/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_pdf/'."\n";
	$text.= 'Disallow: /'.$repecole.'/librairie_php/'."\n";
	$text.= 'Disallow: /'.$repecole.'/livreor/'."\n";
	$text.= 'Disallow: /'.$repecole.'/messagerie/'."\n";
	$text.= 'Disallow: /'.$repecole.'/wap/'."\n";
	$text.= 'Disallow: /'.$repecole.'/meteo/'."\n";
	$text.= 'Disallow: /'.$repecole.'/dokeos/'."\n";
	$text.= 'Disallow: /'.$repecole.'/moodle/'."\n";
	$text.= 'Disallow: /'.$repecole.'/installation/'."\n";
	$text.= 'Disallow: /'.$repecole.'/cache/'."\n";
	$text.= 'Disallow: /'.$repecole.'/audio/'."\n";
	$text.= 'Disallow: /'.$repecole.'/module_chambres/'."\n";
	$text.= 'Disallow: /'.$repecole.'/module_financier/'."\n";
	$text.= 'Disallow: /'.$repecole.'/include/'."\n";
	$text.= 'User-agent: *'."\n";
	$text.= 'Disallow: /'.$repecole.'/'."\n";
	$fp=fopen("../../robots.txt","w");
	fwrite($fp,$text);
	fclose($fp);
//------------------------------------------------------------------------------------//
// fin de la gestion du fichier robots.txt
//------------------------------------------------------------------------------------//
// gestion du fichier .htaccess
	$text = '';
	$text.= 'ErrorDocument 404 /'.$repecole.'/err404.php'."\n";
	$text.= 'ErrorDocument 403 /'.$repecole.'/err403.php'."\n";
	
	$text2 = '<?php'."\n";
	$text2.= 'define("MAXUPLOAD","non");'."\n";
	$text2.= '?>'."\n";

	if ((SERVEURTYPE == "APACHE2TRIAD") ||  (SERVEURTYPE == "EASYPHP") || (SERVEURTYPE == "WAMP")){
		include_once("../librairie_php/lib_get_init.php");
		$id=php_ini_get("safe_mode");
		if ($id != 1) {
			$text.= 'php_value upload_max_filesize 8000000'."\n";
			$text.= 'php_value post_max_size 10000000'."\n";
			$text2= '<?php'."\n";
			$text2.= 'define("MAXUPLOAD","oui");'."\n";
			$text2.= '?>'."\n";
		}
	}

	$fp=fopen("../../.htaccess","w");
	fwrite($fp,$text);
	fclose($fp);
	
	


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("../common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet"
		      href="librairie/css.css" />
		<title>Triade Installation</title>
	</head>

	<body>

		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center;">

			<div id="mainInst">
				<img src="./image/logo_triade_licence.gif"
				     alt="logo_triade_licence" />

<?php
	include_once("../common/version.php");
	include_once("./librairie/licence.php");

	$disable="";
	if (isset($_GET["inst"])) {
		$disable="disabled=\"disabled\"";
	}
?>

				<p>
					Version : <b><?php print VERSION; ?></b><br />
					Licence d'utilisation  : <?php print LICENCE; ?> <br />
					Product ID = <b> <?php print PRODUCTID; ?> </b><br />
				</p>

				<p>
					<hr />

					<span class="T1"><u>Connexion Administrateur Triade</u> :</span><br />
					<br />
					login : <b>administrateur</b><br />
					<br />
					Mot de passe : <b>admin</b><br />
					<br />
					<i>Penser &agrave; changer votre mot de passe par la suite.</i>
				</p>
				<p>Triade&copy;, 2000 - <?php print date("Y") ?></p>
				<br />
				<div style="text-align: right;
				            padding-right: 100px;
										margin-bottom: 1em;">
					<input type="submit" onclick="open('<?php print "../".REPADMIN."/" ?>','_blank','')"
					       value="Connexion Administrateur Triade"
					       class="BUTTON" <?php print $disable ?> />
				</div>

			</div>
		</div>

<?php



	$chaine = "&nbsp;Triade est maintenant opérationnel.<br><br>Nous vous invitons à  consulter le menu de gauche, afin de modifier votre mot de passe d'admistrateur, mais aussi le choix de la configuration de Triade et ne pas oublier de vous enregistrer afin de limiter l'accès à  la mise en place des affectation ou de l'importation d'une base<br><br>";
        $chaine .= "&nbsp;Pour toutes informations complémentaires consulter ";
        $chaine .= "<a href='http://forum.triade-educ.com' target='_blank'><b>le forum technique</b></a> du site officiel.<br>";
        $chaine .= "<br><br>";
	$fp=fopen("../data/fic_opinion.txt","w");
	fwrite($fp,$chaine);
	fclose($fp);

	if (!is_dir("../data/audio")) mkdir("../data/audio");
	copy("data/audio/actu.mp3","../data/audio/actu.mp3");
	if (!is_dir("../data/parametrage")) mkdir("../data/parametrage");
	$fp=fopen("../data/parametrage/audio.txt","w");
	$chaineaudio="<font size=1>Le ".date("d/m/Y").",</font> <br><font class=T1>TRIADE-AUDIO</font>#||#";
	fwrite($fp,$chaineaudio);
	fclose($fp);


	include_once("./librairie/pied_page.php");
	// renomer le repertoire d'admin.
	if (trim($repadmin) != "admin") { rename("../admin", "../$repadmin"); }	

//------------------------------------------------------------------------------------//
	include_once("../common/version.php");
	include_once("../librairie_php/lib_get_init.php");

	$texte = 'Installation le '.date("d/m/Y \à G:i:s")."\n";
	$texte.= 'Version : '.VERSION."\n";
	$texte.= 'ProductId : '.PRODUCTID."\n";
	$texte.= 'Server Name : '.$_SERVER["SERVER_NAME"]."\n";
	$texte.= 'Server Software : '.$_SERVER["SERVER_SOFTWARE"]."\n";
	$texte.= 'Uname  : '.php_uname()."\n";
	$texte.= 'OS : '.PHP_OS."\n";
	$texte.= 'Php Version : '.phpversion()."\n";
	$texte.= 'Version Zend : '.zend_version()."\n";
	$texte.= 'Possesseur des scripts : '.get_current_user()."\n";
	$texte.= 'Module Dbase : '.php_module_load("dbase")."\n";
	$texte.= 'Module Gd : '.php_module_load("gd")."\n";

	$fp=fopen("../data/install_log/install.inc","w");
	fwrite($fp,$texte);
	fclose($fp);

	@unlink("./update/upgrade.log"); // ceci est une installe et non un upgrade
	
	if (!is_dir("../data/image_banniere")) @mkdir("../data/image_banniere");

	@copy("data/banniere-4.jpg","../data/image_banniere/banniere000.jpg");
	@copy("data/librairie_js/menuadmin.js","../librairie_js/menuadmin.js");
	@copy("data/librairie_js/menudepart.js","../librairie_js/menudepart.js");
	@copy("data/librairie_js/menueleve.js","../librairie_js/menueleve.js");
	@copy("data/librairie_js/menuparent.js","../librairie_js/menuparent.js");
	@copy("data/librairie_js/menupersonnel.js","../librairie_js/menupersonnel.js");
	@copy("data/librairie_js/menuprof.js","../librairie_js/menuprof.js");
	@copy("data/librairie_js/menuscolaire.js","../librairie_js/menuscolaire.js");
	@copy("data/librairie_js/menututeur.js","../librairie_js/menututeur.js");
	@copy("data/admin_librairie_js/menudepart.js","../admin/librairie_js/menudepart.js");

//------------------------------------------------------------------------------------//

	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
	$cnx=cnx();
	$prefixe=PREFIXE;
	miseAjourBase(); // Mise a jour de la base avec des infos internes
	Pgclose();

?>
</body>
</html>
