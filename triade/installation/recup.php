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


//	error_reporting(0);

	$fichier = "../data/install_log/install.inc";

	if (file_exists($fichier)) {
		header('Location: index.php?inst=1');
		exit ;
	}
//--------------------------------------------------
//définition de la variable PATH_SEPARATOR à utiliser au niveau des
//modification du path_ini. PATH_SEPARATOR n'existe pas ds les 
// version de php < 4.3.4 (enfin je crois...)
if ( !defined('PATH_SEPARATOR') ) {
    define('PATH_SEPARATOR', ( substr(PHP_OS, 0, 3) == 'WIN' ) ? ';' : ':');
}


// creation des repertoires pour le repertoire data
	@mkdir("../data",0755);
	@mkdir("../data/audio",0755);
	@mkdir("../data/compteur",0755);
      	@mkdir("../data/fichier_ASCII",0755);
      	@mkdir("../data/image_banniere",0755);
      	@mkdir("../data/image_eleve",0755);
      	@mkdir("../data/install_log",0755);
      	@mkdir("../data/patch",0755);
    	@mkdir("../data/pdf_bull",0755);
    	@mkdir("../data/recherche",0755);
      	@mkdir("../data/sauvegarde",0755);
      	@mkdir("../data/stockage",0755);
	@mkdir("../data/circulaire",0755);
	@mkdir("../data/dump",0755);
  	@mkdir("../data/fichier_gep",0755);
    	@mkdir("../data/image_diapo",0755);
   	@mkdir("../data/image_pers",0755);
     	@mkdir("../data/parametrage",0755);
      	@mkdir("../data/pdf_abs",0755);
      	@mkdir("../data/pdf_certif",0755);
      	@mkdir("../data/rss",0755);
	@mkdir("../data/forum",0755);

	@mkdir("../data/menuprof",0755);
	@mkdir("../data/menuadmin",0755);
	@mkdir("../data/menueleve",0755);
	@mkdir("../data/menuscolaire",0755);
	@mkdir("../data/menuparent",0755);


	copy("./librairie/liste_prenom","../data/parametrage/liste_prenom");
	
	// gestion des fichiers .htaccess
	copy("./librairie/data.htaccess","../data/.htaccess");

	copy("./librairie/standard.htaccess","../data/dump/.htaccess");
	copy("./librairie/standard.htaccess","../data/fichier_ASCII/.htaccess");
	copy("./librairie/standard.htaccess","../data/fichier_gep/.htaccess");
	copy("./librairie/standard.htaccess","../data/install_log/.htaccess");
	copy("./librairie/standard.htaccess","../data/parametrage/.htaccess");
	copy("./librairie/standard.htaccess","../data/pdf_abs/.htaccess");
	copy("./librairie/standard.htaccess","../data/pdf_bull/.htaccess");
	copy("./librairie/standard.htaccess","../data/pdf_certif/.htaccess");
	copy("./librairie/standard.htaccess","../data/stockage/.htaccess");
	copy("./librairie/standard.htaccess","../data/sauvegarde/.htaccess");
	copy("./librairie/standard.htaccess","../common/.htaccess");


	if (file_exists("../messenger/gestion/.htpasswd")) { @unlink("../messenger/gestion/.htpasswd"); }
	if (file_exists("../messenger/gestion/.htaccess")) { @unlink("../messenger/gestion/.htaccess"); }

//---------------------------------------------------
	if ($_POST["typeserveur"] == "SERVEURFREE") {

		function copydir($origine , $destination){
			$dossier = opendir($origine);

			if (file_exists($destination)){ return 0;}

			mkdir($destination, fileperms($origine));

			$total = 0;

			while ($fichier = readdir($dossier)) {
				$l = array('.', '..');

				if (!in_array( $fichier, $l)){
					if (is_dir($origine."/".$fichier)){
						$total += copydir("$origine/$fichier", "$destination/$fichier");
					}
					else {
						copy("$origine/$fichier", "$destination/$fichier");
						$total++;
					}
				}
			}
			return $total;
		} //fin fonction copydir()


		@unlink("../livreor/.htaccess");
		@mkdir("../../sessions",0755);
		@mkdir("../../include",0755);
		copydir("../common/pear/Console","../../include/Console");
		copydir("../common/pear/DB","../../include/DB");
		copydir("../common/pear/Mail","../../include/Mail");
		copydir("../common/pear/Net","../../include/Net");
		copydir("../common/pear/PEAR","../../include/PEAR");
		copydir("../common/pear/Archive","../../include/Archive");
		copydir("../common/pear/data","../../include/data");
		copydir("../common/pear/OS","../../include/OS");
		copydir("../common/pear/XML","../../include/XML");
		copydir("../common/pear/test","../../include/test");
		copy("../common/pear/DB.php","../../include/DB.php");
		copy("../common/pear/HTTP.php","../../include/HTTP.php");
		copy("../common/pear/Mail.php","../../include/Mail.php");
		copy("../common/pear/pearcmd.php","../../include/pearcmd.php");
		copy("../common/pear/System.php","../../include/System.php");
		copy("../common/pear/PEAR.php","../../include/PEAR.php");
	}
//------------------------------------------------------------------------------------
	if ( empty($_POST["choix_base"])) {
		header('Location: suite.php?erreur=choix_base');
	}

	if ( empty($_POST["typeserveur"])) {
		header('Location: suite.php?erreur=login');
	}	

	if ( empty($_POST["login"])) {
		header('Location: suite.php?erreur=login');
	}

	if ( empty($_POST["nombase"])) {
		header('Location: suite.php?erreur=nombase');
	}

	if ( empty($_POST["repecole"])) {
		header('Location: suite.php?erreur=repecole');
	}

	if ( empty($_POST["repadmin"])) {
		header('Location: suite.php?erreur=repadmin');
	}

	if ( empty($_POST["hostbase"])) {
		header('Location: suite.php?erreur=hostbase');
	}

	$base = trim($_POST["choix_base"]);
	$loginbase = trim($_POST["login"]);
	$passbase = trim($_POST["password"]);
	$nombase = trim($_POST["nombase"]);
	$repecole = trim($_POST["repecole"]);
	$repadmin = trim($_POST["repadmin"]);
	//$langue=trim($_POST["choix_lang"]);
	$typeserveur = trim($_POST["typeserveur"]);
	$host = trim($_POST["hostbase"]);
	$prefixe = trim($_POST["prefixe"]);
	$typetable = trim($_POST["typetable"]);


	if (($base == "mysql") && ($typetable == "")) { $typetable="MYISAM"; }

	$prefixe=preg_replace('/-/','_',$prefixe);

// pour l'installation
//------------------------------------------------------------------------------------//
	$text = "<?php\n";
	$text.= 'define("DEV", "0");'."\n";
	$text.= 'define("INTER", "non");'."\n";
	$text.= 'define("VATEL", "0");'."\n";
	$text.= 'define("ECOLE", "'.$repecole.'");'."\n";
	$text.= 'define("ADMIN", "'.$repadmin.'");'."\n";
	$text.= 'define("SERVEURTYPE", "'.$typeserveur.'");'."\n";


        if ($typeserveur != "FREEEOS") {
                if ($_SERVER["DOCUMENT_ROOT"] != "") {
                        $text2='define("WEBROOT", "'.$_SERVER["DOCUMENT_ROOT"].'");'."\n";
                }else {
                        $path_translated = preg_replace('/\//'.$repecole.'/installation/recup.php','',stripslashes($_SERVER["PATH_TRANSLATED"]));
                        $text2='define("WEBROOT", "'.$path_translated.'");'."\n";
                }
        }else{
                $HOMEFREEOS=preg_replace('/\/installation\/recup.php/','',$_SERVER["SCRIPT_FILENAME"]);
                $text2='define("WEBROOT", "'.$HOMEFREEOS.'");'."\n";
        }


	if ($typeserveur == "IIS") {
		$path_translated = preg_replace('/recup.php/','',stripslashes($_SERVER["PATH_TRANSLATED"]));
		$path_translated = preg_replace('/installation/','',$path_translated);
		$path_translated = preg_replace('/triadev1/','',$path_translated);
		$path_translated = preg_replace('/.$/','',$path_translated);	
		$text2='define("WEBROOT", "'.$path_translated.'");'."\n";
	}


	$text.= $text2;

	
	$gestionmdp=$_POST["gestionMDP"];
	if ($gestionmdp == "DES") { $gestionmdp=""; }

	$text.= 'define("DB", "'.$nombase.'");'."\n";
	$text.= 'define("HOST", "'.$host.'");'."\n";
	$text.= 'define("USER", "'.$loginbase.'");'."\n";
	$text.= 'define("PWD", "'.$passbase.'");'."\n";
	$text.= 'define("PREFIXE", "'.$prefixe.'");'."\n";
	$text.= 'define("TYPETABLE", "'.$typetable.'");'."\n";
	$text.= 'define("VERIFEMAIL", "oui");'."\n";
	$text.= 'define("GESTIONMDP", "'.$gestionmdp.'"); // valeur possible MD5 ou SHA2 (par défaut crypt)'."\n";

//--------------------------------------------------------------------------------------
//--- si ajout voir aussi notemodif3.php, notesupp3.php, notevisu3.php

	if ( ($typeserveur == "EASYPHP") || ($typeserveur == "WAMP" ) || ($typeserveur == "WAMP310" ) || ($typeserveur == "APACHE2TRIAD") ) {
		$path = 'WEBROOT."/".ECOLE."/common/pear/'.PATH_SEPARATOR.'."';
		$text.= 'ini_set("include_path",'.$path.');'."\n";
	}

        if  ($typeserveur == "FREEEOS") {
                $path = 'WEBROOT."/common/pear/'.PATH_SEPARATOR.'.'.PATH_SEPARATOR.'".ini_get(\'include_path\')';
                $text.= 'ini_set(\'include_path\','.$path.');'."\n";
        }


	if ($typeserveur == "SERVEURAUTRENET") {
		$path = 'WEBROOT."/".ECOLE."/common/pear/'.PATH_SEPARATOR.'.'.PATH_SEPARATOR.'".ini_get(\'include_path\')';
		$text.= 'ini_set(\'include_path\','.$path.');'."\n";
	}

	if (($typeserveur == "UNIX") || ($typeserveur == "LINUX")) {
		$path = 'WEBROOT."/".ECOLE."/common/pear/'.PATH_SEPARATOR.'.'.PATH_SEPARATOR.'".ini_get(\'include_path\')';
		$text.= 'ini_set(\'include_path\','.$path.');'."\n";
	}

	if ($typeserveur == "SERVEURVIPDOMAINE") {
		$path = 'WEBROOT."/".ECOLE."/common/pear/'.PATH_SEPARATOR.'"';
		$text.='set_include_path('.$path.'.ini_get("include_path"));'."\n";
	}

	if ( ($typeserveur == "SERVEURFREE")
	  || ($typeserveur == "SERVEUROVH" )
	  || ($typeserveur == "SERVEURMUTUA")
	  || ($typeserveur == "SERVEURONLINENET")
	  || ($typeserveur == "SERVEURKWARTZ")
	  || ($typeserveur == "SERVEUR1AND1")
	  || ($typeserveur == "SERVEURAMEN" ) ) {
		$path = 'WEBROOT."/".ECOLE."/common/pear/'.PATH_SEPARATOR.'.'.PATH_SEPARATOR.'".ini_get(\'include_path\')';  // $path = '"/home/kwartz/www/triade/triadev1/common/pear/".ini_get(\'include_path\')';
		$text.= '	ini_set("include_path",'.$path.');'."\n";
	}

	if ($typeserveur == "IIS") {
	     // $text.= 'set_include_path(get_include_path();WEBROOT."\".ECOLE."\common\pear");'."\n";
		$text.="ini_set(\"include_path\",WEBROOT.\"/\".ECOLE.\"/common/pear/\;.\");\n"; 
	}
//-------------------------------------------------------------------------------------------------------------

	if ($base == "pgsql") {
		$text .= 'define("DBTYPE", "pgsql");'."\n";
		$text .= '$dsn = \'pgsql://\'.USER.\':\'.PWD.\'@\'.HOST.\'/\'.DB;'."\n";
	}

	if ($base == "mysql") {
		$text .= 'define("DBTYPE", "mysql");'."\n";
		$text .= '$dsn = \'mysqli://\'.USER.\':\'.PWD.\'@\'.HOST.\'/\'.DB;'."\n";
	}

	$text .= '?>'."\n";

	$fp = fopen("../common/config.inc.php", "w");
	fwrite($fp,$text);
	fclose($fp);
//------------------------------------------------------------------------------------//

	$text = '<?php'."\n";
	$text.= 'define("REPADMIN", "'.$repadmin.'");'."\n";
	$text.= 'print \'<script type="text/javascript"> var REPADMIN="'.$repadmin.'"; </script>\';'."\n";
	$text.= '?>'."\n";

	$fp = fopen("../common/lib_admin.php", "w");
	fwrite($fp,$text);
	fclose($fp);

//------------------------------------------------------------------------------------//
	$text = '<?php'."\n";
	$text.= 'define("REPECOLE", "'.$repecole.'");'."\n";
	$text.= 'print \'<script type="text/javascript"> var REPECOLE="'.$repecole.'"; </script>\';'."\n";
	$text.= '?>'."\n";

	$fp = fopen("../common/lib_ecole.php", "w");
	fwrite($fp,$text);
	fclose($fp);

//------------------------------------------------------------------------------------//
	$server_name = $_SERVER["SERVER_NAME"];

	$text = '<?php'."\n";
	$text.= 'define("REPECOLE", "'.$repecole.'");'."\n";
	$text.= 'define("REPADMIN", "'.$repadmin.'");'."\n";
	$text.= 'define("SERVER_NAME", "'.$server_name.'");'."\n";
	$text.= '?>'."\n";

	$fp = fopen("../common/lib_phpMyadmin.php", "w");
	fwrite($fp,$text);
	fclose($fp);
//------------------------------------------------------------------------------------//
	include_once("sql/db-triade.php");
	include_once("../common/config.inc.php");


	// Jeux de caracteres
	$fp=fopen("../common/config5.inc.php","w");
	$text3 = '<?php'."\n";
	$text3.= 'define("CHARSET","UTF-8");'."\n";
	$text3.= '?>'."\n";
	fwrite($fp,"$text3");
	fclose($fp);	
//----------------------------------------------------------------------------------//
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
           "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("../common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="librairie/css.css" />
		<title>Triade Installation</title>
	</head>

	<body>

		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center;">

			<div id="mainInst3">
				<img src="./image/logo_triade_licence.gif"
				     alt="logo_triade_licence" />

<?php
	include_once("../common/version.php");
	include_once("./librairie/licence.php");

	$disable="";

	if (isset($_GET["inst"])) {
		$disable='disabled="disabled"';
	}

	if ($base == "mysql") {
		$etapesuivante=$nbetape;
		$fichiersuivant="recup2.php";
	}else{
		$etapesuivante="1";
		$fichiersuivant="recup-fin.php";
	}

?>

				<p>
					Version&nbsp;: <b><?php print VERSION;?></b><br />
					Licence d'utilisation&nbsp;: <?php print LICENCE; ?><br />
					Product ID&nbsp;= <b><?php print PRODUCTID; ?></b><br />
				</p>
				<p style="margin-left: 25px;">
					<span class="T2">
						INSTALLATION DE LA BASE SQL&nbsp;:<br />
						<br/>
						Etape <b>1/2 </b>&nbsp;&nbsp;&nbsp;&nbsp;<img src="./image/stat1.gif" alt="Ok" />
					</span>
				</p>
				<form action="recup2.php" id="form" method="post" onsubmit="document.getElementById('form').val.disabled=true" >
						
						<div style="text-align: right;
					            padding-right: 100px;
					            margin-bottom: 1em;">
						<input type="submit" onclick="this.value='Patientez S.V.P.';"
						       name="val" value=" Suivant --&gt; "
						       class="BUTTON" <?php print $disable ?> />
					</div>
				</form>
			</div>
		</div>

<?php	include_once("./librairie/pied_page.php");  ?>

	</body>
</html>
