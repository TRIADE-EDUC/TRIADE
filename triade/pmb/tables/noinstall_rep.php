<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install_rep.php,v 1.46 2019-04-03 14:29:38 arenou Exp $

// prevents direct script access
if(preg_match('/noinstall_rep\.php/', $_SERVER['REQUEST_URI'])) {
	include('../includes/forbidden.inc.php'); forbidden();
	}
include('../includes/config.inc.php');
$class_path = "../classes";
include('../includes/mysql_functions.inc.php');

@set_time_limit(1200);

if (isset ($_POST["charset"])) 
	$charset=$_POST["charset"];
else 
	$charset='iso-8859-1';

header("Content-Type: text/html; charset=$charset");
?>
<html>
  <head>
  	<META HTTP-EQUIV="pragma" CONTENT="no-cache">
	<META HTTP-EQUIV="expires" CONTENT="Wed, 30 Sept 2001 12:00:00 GMT">
	<title>Install db PMB</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset;?>">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<?php

function restore($src) {
	global $link;
	global $buffer_sql;
	if($src) {
		$filename=$src;
		if(open_restore_stream($src) && $buffer_sql) {
			// open source file
			$SQL = preg_split('/;\s*\n|;\n/m', $buffer_sql);
			for($i=0; $i < sizeof($SQL); $i++) {
				if($SQL[$i]) $result = pmb_mysql_query($SQL[$i], $link);
			}
		} else {
			die("can't open file $src to restore");
			return FALSE;
		}
	}
	return TRUE;
}


function open_restore_stream($src) {
	global $buffer_sql;
	global $charset;
	$in_file = $src;
	$fptr = @fopen($in_file, 'rb');
	if($fptr) {
		$buffer_sql = fread($fptr, filesize($in_file));
		fclose($fptr);
		return TRUE;
	} else {
		$buffer_sql = '';
		return FALSE;
	}
}


function create_db_param ($dbhost,$dbuser,$dbpassword,$dbname,$charset){

$buffer_fic ="<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+

// paramètres d'accès à la base MySQL

// prevents direct script access
if(preg_match('/db_param\.inc\.php/', \$_SERVER['REQUEST_URI'])) {
	include('./forbidden.inc.php'); forbidden();
}
// inclure ici les tableaux des bases de données accessibles
\$_tableau_databases[0]=\"".$dbname."\" ;
\$_libelle_databases[0]=\"".$dbname."\" ;

// pour multi-bases
if (isset(\$database)) {
	define('LOCATION', \$database) ;
} else {
	if (!isset(\$_COOKIE[\"PhpMyBibli-DATABASE\"]) || !\$_COOKIE[\"PhpMyBibli-DATABASE\"]) define('LOCATION', \$_tableau_databases[0]);
	else define('LOCATION', \$_COOKIE[\"PhpMyBibli-DATABASE\"]) ;
}

// define pour les paramètres de connection. A adapter.
switch(LOCATION):
	case 'remote':	// mettre ici les valeurs pour l'accés distant
		define('SQL_SERVER', 'remote');		// nom du serveur . exemple : http://sql.free.fr
		define('USER_NAME', 'username');	// nom utilisateur
		define('USER_PASS', 'userpwd');		// mot de passe
		define('DATA_BASE', 'dbname');		// nom base de données
		define('SQL_TYPE',  'mysql');		// Type de serveur de base de données
		//\$charset = 'utf-8'; || \$charset = 'iso-8859-1';
		//\$time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
		//\$time_zone_mysql =  \"'-00:00'\"; //Pour modifier l'heure MySQL
		break;
	case '".$dbname."':
		define('SQL_SERVER', '".$dbhost."');		// nom du serveur
		define('USER_NAME', '".$dbuser."');		// nom utilisateur
		define('USER_PASS', '".$dbpassword."');		// mot de passe
		define('DATA_BASE', '".$dbname."');		// nom base de données
		define('SQL_TYPE',  'mysql');			// Type de serveur de base de données
		// Encode de caracteres de la base de données 
		\$charset = \"". $charset. "\" ;
		//\$time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
		//\$time_zone_mysql =  \"'-00:00'\"; //Pour modifier l'heure MySQL
		break;
	default:		// valeurs pour l'accès local
		define('SQL_SERVER', 'localhost');		// nom du serveur
		define('USER_NAME', 'bibli');			// nom utilisateur
		define('USER_PASS', 'bibli');			// mot de passe
		define('DATA_BASE', 'bibli');			// nom base de données
		define('SQL_TYPE',  'mysql');			// Type de serveur de base de données
		//\$charset = 'utf-8'; || \$charset = 'iso-8859-1';
		//\$time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
		//\$time_zone_mysql =  \"'-00:00'\"; //Pour modifier l'heure MySQL
		break;
endswitch;

\$dsn_pear = SQL_TYPE.\"://\".USER_NAME.\":\".USER_PASS.\"@\".SQL_SERVER.\"/\".DATA_BASE ;
";

$opac_buffer_fic ="<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+

// paramètres d'accès à la base MySQL

// prevents direct script access
if(preg_match('/opac_db_param\.inc\.php/', \$_SERVER['REQUEST_URI'])) {
	include('./forbidden.inc.php'); forbidden();
}

// inclure ici les tableaux des bases de données accessibles
\$_tableau_databases[0]=\"".$dbname."\" ;
\$_libelle_databases[0]=\"".$dbname."\" ;

// pour multi-bases
if (!isset(\$database)) {
	if (\$_COOKIE[\"PhpMyBibli-OPACDB\"]) \$database=\$_COOKIE[\"PhpMyBibli-OPACDB\"];
	elseif (\$_COOKIE[\"PhpMyBibli-DATABASE\"]) \$database=\$_COOKIE[\"PhpMyBibli-DATABASE\"];
	else \$database=\$_tableau_databases[0];
}
if (array_search(\$database,\$_tableau_databases)===false) \$database=\$_tableau_databases[0];
define('LOCATION', \$database) ;
\$expiration = time() + 30000000; /* 1 year */
setcookie ('PhpMyBibli-OPACDB', \$database, \$expiration);

// define pour les paramètres de connection. A adapter.
switch(LOCATION):
	case 'remote':	// mettre ici les valeurs pour l'accés distant
		define('SQL_SERVER', 'remote');		// nom du serveur . exemple : http://sql.free.fr
		define('USER_NAME', 'username');	// nom utilisateur
		define('USER_PASS', 'userpwd');		// mot de passe
		define('DATA_BASE', 'dbname');		// nom base de données
		define('SQL_TYPE',  'mysql');		// Type de serveur de base de données
		//\$charset = 'utf-8'; || \$charset = 'iso-8859-1';
		//\$time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
		//\$time_zone_mysql =  \"'-00:00'\"; //Pour modifier l'heure MySQL
		break;
	case '".$dbname."':
		define('SQL_SERVER', '".$dbhost."');		// nom du serveur
		define('USER_NAME', '".$dbuser."');		// nom utilisateur
		define('USER_PASS', '".$dbpassword."');		// mot de passe
		define('DATA_BASE', '".$dbname."');		// nom base de données
		define('SQL_TYPE',  'mysql');			// Type de serveur de base de données
		// Encode de caracteres de la base de données 
		\$charset = \"". $charset. "\" ;
		//\$time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
		//\$time_zone_mysql =  \"'-00:00'\"; //Pour modifier l'heure MySQL
		break;
	default:		// valeurs pour l'accès local
		define('SQL_SERVER', 'localhost');		// nom du serveur
		define('USER_NAME', 'bibli');			// nom utilisateur
		define('USER_PASS', 'bibli');			// mot de passe
		define('DATA_BASE', 'bibli');			// nom base de données
		define('SQL_TYPE',  'mysql');			// Type de serveur de base de données
		//\$charset = 'utf-8'; || \$charset = 'iso-8859-1';
		//\$time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
		//\$time_zone_mysql =  \"'-00:00'\"; //Pour modifier l'heure MySQL
		break;
endswitch;

\$dsn_pear = SQL_TYPE.\"://\".USER_NAME.\":\".USER_PASS.\"@\".SQL_SERVER.\"/\".DATA_BASE ;
";

@copy ("../includes/db_param_old_01.inc.php","../includes/db_param_old_02.inc.php");
@copy ("../includes/db_param.inc.php","../includes/db_param_old_01.inc.php");
$fptr = fopen("../includes/db_param.inc.php", 'w');
fwrite ($fptr, $buffer_fic);
fclose($fptr);

@copy ("../opac_css/includes/opac_db_param_old_01.inc.php","../opac_css/includes/opac_db_param_old_02.inc.php");
@copy ("../opac_css/includes/opac_db_param.inc.php","../opac_css/includes/opac_db_param_old_01.inc.php");
$fptr = fopen("../opac_css/includes/opac_db_param.inc.php", 'w');
fwrite ($fptr, $opac_buffer_fic);
fclose($fptr);
	
}

/* début du script ICI */

$lang ="fr";
if (isset ($_POST["lang"])) 
	$lang=$_POST["lang"];

$Submit ="";
if (isset ($_POST["Submit"])) 
	$Submit = $_POST["Submit"];

$dbname ="";
if (isset ($_POST["dbname"])) 
	$dbname = $_POST["dbname"];
$dbnamedbhost ="";
if (isset ($_POST["dbnamedbhost"])) 
	$dbnamedbhost = $_POST["dbnamedbhost"];
	
if (($Submit == "OK") && (($dbname!="") || ($dbnamedbhost!=""))) {
	
	$usermysql = $_POST["usermysql"];
	$passwordmysql = $_POST["passwdmysql"];
	$dbhost = $_POST["dbhost"]; 
	
	$user = $_POST["user"];
	$password = $_POST["passwd"];
	
	$structure = $_POST["structure"];
	
	if(!isset($_POST["minimum"])){
		$minimum=0;
	}else{
		$minimum = $_POST["minimum"];
	}
	
	if(!isset($_POST["essential"])){
		$essential=0;
	}else{
		$essential = $_POST["essential"];
	}
	
	if(!isset($_POST["data_test"])){
		$data_test=0;
	}else{
		$data_test = $_POST["data_test"];
	}
	
	
	if(!isset($_POST["data_test_cms"])){
		$data_test_cms=0;
	}else{
		$data_test_cms = $_POST["data_test_cms"];
	}
	
	
	if(!isset($_POST["data_test_zen"])){
		$data_test_zen=0;
	}else{
		$data_test_zen = $_POST["data_test_zen"];
	}
	
	if($data_test_cms || $data_test_zen){//On est forcément en Utf-8
		$charset="utf-8";
	}
	
	if(!isset($_POST["thesaurus"])){
		$thesaurus="";
	}else{
		$thesaurus = $_POST["thesaurus"];
	}
	
	
	echo "<span class='center'>lang = $lang charset = $charset<br />user = $user; password = $password; dbhost = $dbhost; dbname = $dbname <br />
			usersystem = $usermysql; passwordsystem = $passwordmysql; dbhost = $dbhost; dbnamesystem = $dbnamedbhost <br />
		</span>";

	include("./$lang/install_rep_inc.php");

	if ($dbnamedbhost) {
		@$link=pmb_mysql_connect($dbhost,$usermysql,$passwordmysql) or die("Impossible de se connecter au serveur MySql en tant qu'admin $usermysql "); // Le @ ordonne a php de ne pas afficher de message d'erreur
		@pmb_mysql_select_db($dbnamedbhost, $link) or die("Impossible de se connecter à la base de données $dbnamedbhost");
		echo "<br />$msg_okconnect_usermysql";
		create_db_param ($dbhost,$usermysql,$passwordmysql,$dbnamedbhost,$charset);
	} else {
		@$link=pmb_mysql_connect($dbhost,$usermysql,$passwordmysql) or die("Impossible de se connecter au serveur MySql en tant qu'admin $usermysql "); // Le @ ordonne a php de ne pas afficher de message d'erreur
		$ligne = "DROP DATABASE $dbname";
		@pmb_mysql_query($ligne,$link);
		$ligne = "CREATE DATABASE $dbname ";
		$ligne.= "character set utf8 COLLATE utf8_unicode_ci";
		if (!pmb_mysql_query($ligne,$link)) {
			echo $msg_nodb;
			exit(0);
		}		
		echo $msg_okdb;
		$sql_userbibli="GRANT SELECT ,INSERT ,UPDATE ,DELETE ,CREATE ,DROP ,INDEX ,ALTER ,CREATE TEMPORARY TABLES ,LOCK TABLES ON ".$dbname.".* to $user@localhost identified by '$password' ";
		pmb_mysql_query($sql_userbibli,$link);
		pmb_mysql_query("flush privileges ",$link);
		pmb_mysql_close($link); // fermeture de la connexion en tant que root
		@$link=pmb_mysql_connect($dbhost,$user,$password) or die("Impossible de se connecter au serveur MySql en tant que $user "); // Le @ ordonne a php de ne pas afficher de message d'erreur
		@pmb_mysql_select_db($dbname, $link) or die("Impossible de se connecter à la base de données $dbname");
		pmb_mysql_query("set names utf8 ", $link);
		echo $msg_okconnect_user;
		create_db_param ($dbhost,$user,$password,$dbname,$charset);
	}
	//Suppression des fichiers temporaires gestion opac
	del_fic_temp("../opac_css/temp/");
	//Suppression des fichiers temporaires gestion
	del_fic_temp("../temp/");
	if(!$data_test_cms && !$data_test_zen){
		if (restore("bibli.sql")) print $msg_crea_01;
		else print $msg_crea_02;
	
		if (restore("$lang/minimum.sql")) print $msg_crea_03;
		else print $msg_crea_04;
	}
	if($data_test_cms=="1") {
		if (restore("bibliportail.sql")) print $msg_crea_33;
		else print $msg_crea_34;
	}else if($data_test_zen=="1") {
		if (restore("biblizen.sql")) print $msg_crea_35;
		else print $msg_crea_36;
	}else if ($data_test=="1") {
		if (restore("$lang/feed_essential.sql")) print $msg_crea_05;
		else print $msg_crea_06;
		if (restore("$lang/data_test.sql")) print $msg_crea_07;
		else print $msg_crea_08;

		if (restore("$lang/agneaux.sql")) print $msg_crea_09;
		else $msg_crea_10;
		if (restore("$lang/indexint_100.sql")) print $msg_crea_11;
		else print $msg_crea_12;
	} else {
		if ($essential) {
			if (restore("$lang/feed_essential.sql")) print $msg_crea_13;
			else print $msg_crea_14;
		}
			
		switch ($thesaurus) {
			case 'unesco' :
				if (restore("./unesco.sql")) 
					print $msg_crea_15;
					else print $msg_crea_16;
				break;
			case 'agneaux' :
				if (restore("$lang/agneaux.sql")) 
					print $msg_crea_17;
					else print $msg_crea_18;
				break;
			case 'environnement' :
				if (restore("$lang/environnement.sql")) 
					print $msg_crea_19;
					else print $msg_crea_20;
				break;
		}
		
		if(!isset($indexint)){
			$indexint="aucun";
		}
		
		switch ($indexint) {
			case 'chambery' :
				if (restore("$lang/indexint_chambery.sql")) print $msg_crea_23;
				else print $msg_crea_24;
				break;
			case 'dewey' :
				if (restore("$lang/indexint_dewey.sql")) print $msg_crea_25;
				else print $msg_crea_26;
				break;
			case 'marguerite' :
				if (restore("$lang/indexint_100.sql")) print $msg_crea_27;
				else $msg_crea_28;
				$rqt = "update parametres set valeur_param='0' where type_param='opac' and sstype_param='show_100cases_browser' " ;
				$result = pmb_mysql_query($rqt, $link);
				$rqt = "update parametres set valeur_param='1' where type_param='opac' and sstype_param='show_marguerite_browser' " ;
				$result = pmb_mysql_query($rqt, $link);
				$rqt = "update parametres set valeur_param='0' where type_param='opac' and sstype_param='show_categ_browser' " ;
				$result = pmb_mysql_query($rqt, $link);
				break;
			case 'aucun' :
				print $msg_crea_29;
				break;
		}
				
	}			
	//Mise à jour du mot de passe admin
	@pmb_mysql_query("UPDATE users SET pwd=PASSWORD('admin'), user_digest = '".md5("admin".":".md5("http://SERVER/DIRECTORY/").":"."admin")."' WHERE username='admin'",$link);
	
	@rename ("./install.php","./noinstall.php");
	@rename ("./install_rep.php","./noinstall_rep.php");
	echo $msg_crea_30;
	echo $msg_crea_31;
	$query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' ";
	$req = pmb_mysql_query($query, $link);
	$data = pmb_mysql_fetch_array($req) ;
	$version_pmb_bdd = $data['valeur_param'];
	
	if ($version_pmb_bdd!=$pmb_version_database_as_it_should_be) {
		echo str_replace("!!pmb_version!!",$version_pmb_bdd,$msg_crea_control_version) ;
	}
	
	pmb_mysql_close($link);
} else {
	print $msg_crea_32;
}

function del_fic_temp($dir){
	if($dh = opendir($dir)){
	    while(($file = readdir($dh))!== false){
	        if(file_exists($dir.$file) && preg_match("/^XML.*?\.tmp$/i",$file)){
	        		@unlink($dir.$file);
	        }
	    }
		closedir($dh);
	}
}
?>
</body>
</html>