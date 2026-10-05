<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install_rep.php,v 1.58.4.1 2025/02/27 14:02:14 gneveu Exp $

if (preg_match('/noinstall_rep\.php/i', urldecode($_SERVER['REQUEST_URI']))) {
	include('../includes/forbidden.inc.php');
	forbidden();
}

global $class_path, $pmb_version_database_as_it_should_be;
global $install_lang, $mysql_variables, $alter_session_variables;

$base_path = "..";
include $base_path.'/includes/config.inc.php';

$include_path = $base_path.'/includes';
$class_path = "../classes";

require_once $include_path.'/../includes/mysql_functions.inc.php';
require_once "./install.class.php";

if( empty($install_lang)) {
	$install_lang = install::getLanguage();
}

$mysql_variables = empty($mysql_variables) ? [] : json_decode(rawurldecode($mysql_variables), true);

if( empty($alter_session_variables) ) {
    $alter_session_variables = 0;
}

$Submit = "";
if ( isset($_POST["Submit"]) ) {
	$Submit = $_POST["Submit"];
}
$dbname ="";
if ( isset($_POST["dbname"]) ) {
	$dbname = $_POST["dbname"];
}
$dbnamedbhost ="";
if ( isset($_POST["dbnamedbhost"]) ) {
	$dbnamedbhost = $_POST["dbnamedbhost"];
}
$usermysql = '';
if ( isset($_POST["usermysql"]) ) {
	$usermysql = $_POST["usermysql"];
}
$passwordmysql = '';
if ( isset($_POST["passwdmysql"]) ) {
	$passwordmysql = $_POST["passwdmysql"];
}
$dbhost = '';
if ( isset($_POST["dbhost"]) ) {
	$dbhost = $_POST["dbhost"];
}
$user = '';
if( isset($_POST["user"]) ) {
	$user = $_POST['user'];
}
$password = '';
if( isset($_POST["passwd"]) ) {
	$password = $_POST["passwd"];
}
$essential=0;
if( isset($_POST["essential"]) ){
	$essential = $_POST["essential"];
}
$data_test = 0;
if( isset($_POST["data_test"]) ){
	$data_test = $_POST["data_test"];
}

$data_test_cms=0;
if( isset($_POST["data_test_cms"]) ){
	$data_test_cms = $_POST["data_test_cms"];
}

$data_test_zen=0;
if( isset($_POST["data_test_zen"]) ) {
	$data_test_zen = $_POST["data_test_zen"];
}

$thesaurus="";
if( isset($_POST["thesaurus"]) ){
	$thesaurus = $_POST["thesaurus"];
}
$indexint = "aucun";
if( isset($_POST["indexint"]) ){
	$indexint = $_POST["indexint"];
}

if ( ($Submit == "OK") && ( ($dbname!="") || ($dbnamedbhost!="") ) ) {

	//Chargement des messages
	$install_msg = install::getMessages($install_lang);

	@set_time_limit(1200);
	$charset='utf-8';

	header("Content-Type: text/html; charset=$charset");

	//Chargement de la page recapitulatif
	$report_tpl = install::getReportTemplates($install_lang);

	$report_page = $report_tpl['page'];
	$report_page = str_replace('<!-- lang -->', $install_lang, $report_page);
	$report_page = str_replace('<!-- charset -->', $charset, $report_page);

	$report_page = str_replace('<!-- mysql_system_user -->', $usermysql, $report_page);
	$report_page = str_replace('<!-- mysql_system_pwd -->', $passwordmysql, $report_page);
	$report_page = str_replace('<!-- mysql_system_host -->', $dbhost, $report_page);
	$report_page = str_replace('<!-- mysql_system_db -->', $dbnamedbhost, $report_page);

	$report_page = str_replace('<!-- mysql_pmb_user -->', $user, $report_page);
	$report_page = str_replace('<!-- mysql_pmb_pwd -->', $password, $report_page);
	$report_page = str_replace('<!-- mysql_pmb_db -->', $dbname, $report_page);

	//Connexion au serveur MySQL avec l'utilisateur systeme
	@$link = pmb_mysql_connect($dbhost, $usermysql, $passwordmysql);

	if(false != $link) {
		$report_page = str_replace('<!-- report_mysql_system_user_connect -->', $install_msg['report_ok'], $report_page);

	} else {
		$report_page = str_replace('<!-- report_mysql_system_user_connect -->', $install_msg['report_ko'], $report_page);
		echo $report_page;
		@pmb_mysql_close($link);
		exit();
	}

	$use_existing_db = false;
	if($dbnamedbhost) {
		$dbname = $dbnamedbhost;
		$use_existing_db = true;
	}

	if(!$use_existing_db) {

		//Suppression base de donnees PMB existante
		$report_page = str_replace('<!-- tpl_drop_pmb_db -->', $report_tpl['drop_pmb_db'], $report_page);
		$drop_pmb_db_query = "DROP DATABASE IF EXISTS $dbname";
		if( true == @pmb_mysql_query($drop_pmb_db_query, $link) ) {
			$report_page = str_replace('<!-- report_drop_pmb_db -->', $install_msg['report_ok'], $report_page);

		} else {
			$report_page = str_replace('<!-- report_drop_pmb_db -->', $install_msg['report_ko'], $report_page);
			echo $report_page;
			echo pmb_mysql_error($link);
			@pmb_mysql_close($link);
			exit();
		}

		//Creation base de donnees PMB
		$report_page = str_replace('<!-- tpl_create_pmb_db -->', $report_tpl['create_pmb_db'], $report_page);
		$create_pmb_db_query = "CREATE DATABASE $dbname character set utf8 COLLATE utf8_unicode_ci";
		if ( true == @pmb_mysql_query($create_pmb_db_query, $link) ) {
			$report_page = str_replace('<!-- report_create_pmb_db -->', $install_msg['report_ok'], $report_page);

		} else {
			$report_page = str_replace('<!-- report_create_pmb_db -->', $install_msg['report_ko'], $report_page);
			echo $report_page;
			@pmb_mysql_close($link);
			exit();
		}

		//Creation utilisateur PMB
		$report_page = str_replace('<!-- tpl_create_pmb_user -->', $report_tpl['create_pmb_user'], $report_page);
		$create_pmb_user_query = "CREATE USER IF NOT EXISTS $user@localhost identified by '".addcslashes($password, "'")."' ";
		if ( false === @pmb_mysql_query($create_pmb_user_query, $link) ) {
		    $report_page = str_replace('<!-- report_create_pmb_user -->', $install_msg['report_ko'], $report_page);
		    echo $report_page;
		    @pmb_mysql_close($link);
		    exit();
		}
		$create_pmb_user_query = "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ";
		$create_pmb_user_query.= "ON ".$dbname.".* to $user@localhost ";
		if ( true === @pmb_mysql_query($create_pmb_user_query, $link) ) {
			$report_page = str_replace('<!-- report_create_pmb_user -->', $install_msg['report_ok'], $report_page);

		} else {
			$report_page = str_replace('<!-- report_create_pmb_user -->', $install_msg['report_ko'], $report_page);
			echo $report_page;
			@pmb_mysql_close($link);
			exit();
		}

		@pmb_mysql_query("flush privileges ", $link);

		//Deconnexion et reconnexion au serveur MySQL avec l'utilisateur PMB
		$report_page = str_replace('<!-- tpl_mysql_pmb_user_connect -->', $report_tpl['mysql_pmb_user_connect'], $report_page);
		@pmb_mysql_close($link);
		@$link=pmb_mysql_connect($dbhost, $user, $password);
		if(false != $link) {
			$report_page = str_replace('<!-- report_mysql_pmb_user_connect -->', $install_msg['report_ok'], $report_page);

		} else {
			$report_page = str_replace('<!-- report_mysql_pmb_user_connect -->', $install_msg['report_ko'], $report_page);
			echo $report_page;
			@pmb_mysql_close($link);
			exit();
		}

	}

    //Selection base de donnees
	if( true == @pmb_mysql_select_db($dbname, $link)) {
		$report_page = str_replace('<!-- report_select_db -->', $install_msg['report_ok'], $report_page);

	} else {
		$report_page = str_replace('<!-- report_select_db -->', $install_msg['report_ko'], $report_page);
		echo $report_page;
		@pmb_mysql_close($link);
		exit();
	}

	//Modification de l'encodage si base existante
	if($use_existing_db) {

	    $report_page = str_replace('<!-- tpl_alter_db_charset -->', $report_tpl['alter_db_charset'], $report_page);
	    $alter_db_charset_query = "ALTER DATABASE $dbname CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	    if( true === @pmb_mysql_query($alter_db_charset_query, $link) ) {
	        $report_page = str_replace('<!-- report_alter_db_charset -->', $install_msg['report_ok'], $report_page);
	    } else {
	        $report_page = str_replace('<!-- report_alter_db_charset -->', $install_msg['report_ko'], $report_page);
	        echo $report_page;
	        echo pmb_mysql_error($link);
	        @pmb_mysql_close($link);
	        exit();
	    }
	}

	//On utilise l'utilisateur mysql que l'on vient de créer en mode création de base
	//Sinon on utilise l'utilisateur mysql paramétré
	$userToUse = $use_existing_db ? $usermysql : $user;
	$passwordToUse = $use_existing_db ? $passwordmysql : $password;

	//Creation fichiers de connexion
	install::createDbParam($dbhost, $userToUse, $passwordToUse, $dbname, $charset, $mysql_variables, $alter_session_variables);

	if( (is_readable(__DIR__."/../includes/db_param.inc.php")) && (is_readable(__DIR__."/../opac_css/includes/opac_db_param.inc.php")) ) {
		$report_page = str_replace('<!-- report_create_connexion_files -->', $install_msg['report_ok'], $report_page);
	} else {
		$report_page = str_replace('<!-- report_create_connexion_files -->', $install_msg['report_ko'], $report_page);
		echo $report_page;
		@pmb_mysql_close($link);
		exit();
	}

	//Indique au serveur MySQL que le client parle en UTF8
	@pmb_mysql_query("set names utf8 ", $link);

	//Modification des variables MySQL
	$mysql_modified_variables = install::$mysql_modified_variables;
	if(!empty($mysql_modified_variables)) {
	    $alter_mysql_variables_query = "set ".implode(', ', $mysql_modified_variables);
	    if( true === @pmb_mysql_query($alter_mysql_variables_query, $link) ) {
	        $report_page = str_replace('<!-- report_alter_mysql_variables -->', $install_msg['report_ok'], $report_page);
	    } else {
	        $report_page = str_replace('<!-- report_alter_mysql_variables -->', $install_msg['report_ko'], $report_page);
	        echo $report_page;
	        echo pmb_mysql_error($link);
	        @pmb_mysql_close($link);
	        exit();
	    }
	} else {
	    $report_page = str_replace('<!-- report_alter_mysql_variables -->', $install_msg['report_ok'], $report_page);
	}

	//Suppression des fichiers temporaires gestion et opac
	install::delTemporaryFiles("../temp/");
	install::delTemporaryFiles("../opac_css/temp/");
	$report_page = str_replace('<!-- report_drop_temp_files -->', $install_msg['report_ok'], $report_page);

	$reindex = [];

	switch (true) {


		//Portail Pageo
		case ( $data_test_cms == "1" ) :

			$report_page = str_replace('<!-- tpl_load_pageo -->', $report_tpl['load_pageo'], $report_page);
			if ( install::restore("bibliportail.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_pageo -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_pageo -->', $install_msg['report_ko'], $report_page);
			}
			break;


		//Portail Zen
		case ( $data_test_zen == "1" ) :

			$report_page = str_replace('<!-- tpl_load_zen -->', $report_tpl['load_zen'], $report_page);
			if ( install::restore("biblizen.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_zen -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_zen -->', $install_msg['report_ko'], $report_page);
			}
			break;


		//Jeu de test
		case ( $data_test == "1" ) :

			//Structure
			$report_page = str_replace('<!-- tpl_load_structure -->', $report_tpl['load_structure'], $report_page);
			if ( install::restore("bibli.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_structure -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_structure -->', $install_msg['report_ko'], $report_page);
			}

			//Minimum
			$report_page = str_replace('<!-- tpl_load_minimum -->', $report_tpl['load_minimum'], $report_page);
			if ( install::restore("minimum.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_minimum -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_minimum -->', $install_msg['report_ko'], $report_page);
			}

			//Essentiel
			$report_page = str_replace('<!-- tpl_load_essential -->', $report_tpl['load_essential'], $report_page);
			if ( install::restore("feed_essential.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_essential -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_essential -->', $install_msg['report_ko'], $report_page);
			}

			//Jeu de test
			$report_page = str_replace('<!-- tpl_load_data_test -->', $report_tpl['load_data_test'], $report_page);
			if ( install::restore("data_test.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_data_test -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_data_test -->', $install_msg['report_ko'], $report_page);
			}

			//Agneaux
			$report_page = str_replace('<!-- tpl_load_agneaux -->', $report_tpl['load_agneaux'], $report_page);
			if ( install::restore("agneaux.sql", $install_lang, $link) ){
				$report_page = str_replace('<!-- report_load_agneaux -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_agneaux -->', $install_msg['report_ko'], $report_page);
			}
			$reindex[] = 'agneaux';

			//Indexint 100
			$report_page = str_replace('<!-- tpl_load_indexint_100 -->', $report_tpl['load_indexint_100'], $report_page);
			if ( install::restore("indexint_100.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_indexint_100 -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_indexint_100 -->', $install_msg['report_ko'], $report_page);
			}
			$reindex[] = 'indexint_100';

			break;


		default :

			//Structure
			$report_page = str_replace('<!-- tpl_load_structure -->', $report_tpl['load_structure'], $report_page);
			if ( install::restore("bibli.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_structure -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_structure -->', $install_msg['report_ko'], $report_page);
			}

			//Minimum
			$report_page = str_replace('<!-- tpl_load_minimum -->', $report_tpl['load_minimum'], $report_page);
			if ( install::restore("minimum.sql", $install_lang, $link) ) {
				$report_page = str_replace('<!-- report_load_minimum -->', $install_msg['report_ok'], $report_page);
			} else {
				$report_page = str_replace('<!-- report_load_minimum -->', $install_msg['report_ko'], $report_page);
			}

			//Essentiel
			if ($essential) {
				$report_page = str_replace('<!-- tpl_load_essential -->', $report_tpl['load_essential'], $report_page);
				if ( install::restore("feed_essential.sql", $install_lang, $link) ) {
					$report_page = str_replace('<!-- report_load_essential -->', $install_msg['report_ok'], $report_page);
				} else {
					$report_page = str_replace('<!-- report_load_essential -->', $install_msg['report_ko'], $report_page);
				}
			}

			//Thesaurus
			switch ($thesaurus) {

				case 'unesco' :

					$report_page = str_replace('<!-- tpl_load_unesco -->', $report_tpl['load_unesco'], $report_page);
					if ( install::restore("unesco.sql", $install_lang, $link) ) {
						$report_page = str_replace('<!-- report_load_unesco -->', $install_msg['report_ok'], $report_page);
					} else {
						$report_page = str_replace('<!-- report_load_unesco -->', $install_msg['report_ko'], $report_page);
					}
					$reindex[] = 'unesco';
					break;

				case 'agneaux' :

					$report_page = str_replace('<!-- tpl_load_agneaux -->', $report_tpl['load_agneaux'], $report_page);
					if ( install::restore("agneaux.sql", $install_lang, $link) ) {
						$report_page = str_replace('<!-- report_load_agneaux -->', $install_msg['report_ok'], $report_page);
					} else {
						$report_page = str_replace('<!-- report_load_agneaux -->', $install_msg['report_ko'], $report_page);
					}
					$reindex[] = 'agneaux';
					break;

				case 'environnement' :

					$report_page = str_replace('<!-- tpl_load_environnement -->', $report_tpl['load_environnement'], $report_page);
					if ( install::restore("environnement.sql", $install_lang, $link) ) {
						$report_page = str_replace('<!-- report_load_environnement -->', $install_msg['report_ok'], $report_page);
					} else {
						$report_page = str_replace('<!-- report_load_environnement -->', $install_msg['report_ko'], $report_page);
					}
					$reindex[] = 'environnement';
					break;

				default :

					$report_page = str_replace('<!-- tpl_load_no_thesaurus -->', $report_tpl['load_no_thesaurus'], $report_page);
					break;

			}

			//Indexation
			switch ($indexint) {

				case 'chambery' :

					$report_page = str_replace('<!-- tpl_load_bm_chambery -->', $report_tpl['load_bm_chambery'], $report_page);
					if ( install::restore("indexint_chambery.sql", $install_lang, $link) ) {
						$report_page = str_replace('<!-- report_load_bm_chambery -->', $install_msg['report_ok'], $report_page);
					} else {
						$report_page = str_replace('<!-- report_load_bm_chambery -->', $install_msg['report_ko'], $report_page);
					}
					$reindex[] = 'indexint_chambery';
					break;

				case 'dewey' :

					$report_page = str_replace('<!-- tpl_load_dewey -->', $report_tpl['load_dewey'], $report_page);
					if ( install::restore("indexint_dewey.sql", $install_lang, $link) ) {
						$report_page = str_replace('<!-- report_load_dewey -->', $install_msg['report_ok'], $report_page);
					} else {
						$report_page = str_replace('<!-- report_load_dewey -->', $install_msg['report_ko'], $report_page);
					}
					$reindex[] = 'indexint_dewey';
					break;

				case 'marguerite' :

					$report_page = str_replace('<!-- tpl_load_indexint_100 -->', $report_tpl['load_indexint_100'], $report_page);
					if ( install::restore("indexint_100.sql", $install_lang, $link) ) {
						$report_page = str_replace('<!-- report_load_indexint_100 -->', $install_msg['report_ok'], $report_page);
					} else {
						$report_page = str_replace('<!-- report_load_indexint_100 -->', $install_msg['report_ko'], $report_page);
					}

					$rqt = "update parametres set valeur_param='0' where type_param='opac' and sstype_param='show_100cases_browser' " ;
					@pmb_mysql_query($rqt, $link);
					$rqt = "update parametres set valeur_param='1' where type_param='opac' and sstype_param='show_marguerite_browser' " ;
					@pmb_mysql_query($rqt, $link);
					$rqt = "update parametres set valeur_param='0' where type_param='opac' and sstype_param='show_categ_browser' " ;
					@pmb_mysql_query($rqt, $link);
					$reindex[] = 'indexint_100';
					break;

				default :

					$report_page = str_replace('<!-- tpl_load_no_indexation -->', $report_tpl['load_no_indexation'], $report_page);
					break;
			}
			break;

	}
	//Initialisation des parametres necessaires a l'indexation
	install::setPreFillIndexationStackParameters($link);

	//Remplissage de la pile d'indexation si besoin
	if(count($reindex)) {

		install::fillIndexationStack($link);
		install::setPostFillIndexationStackParameters($link);

		$report_page = str_replace('<!-- tpl_reindexation -->', $report_tpl['reindexation'], $report_page);
		foreach($reindex as $index) {
			$report_indexation_row = sprintf($report_tpl['reindexation_row'], $install_msg['report_reindexation_'.$index], $install_msg['report_ok']);
			$report_page = str_replace('<!-- tpl_reindexation_row -->', $report_indexation_row.'<!-- tpl_reindexation_row -->', $report_page);
		}
	}

	//Mise a jour du mot de passe admin
	$update_pmb_admin_password_query = "UPDATE users SET pwd='*4ACFE3202A5FF5CF467898FC58AAB1D615029441'), user_digest = '".md5("admin".":".md5("http://SERVER/DIRECTORY/").":"."admin")."' WHERE username='admin'";
	@pmb_mysql_query($update_pmb_admin_password_query, $link);
	$report_page = str_replace('<!-- report_update_pmb_admin_password -->', $install_msg['report_ok'], $report_page);

	//Renommage des scripts d'installation en .old
	@rename ("./install.php","./noinstall.php.old");
	@rename ("./install_rep.php","./noinstall_rep.php.old");

	if( !(is_readable("./install.php")) &&  !(is_readable("./install.php")) ) {
		$report_page = str_replace('<!-- report_rename_install_scripts -->', $install_msg['report_ok'], $report_page);
	} else {
		$report_page = str_replace('<!-- report_rename_install_scripts -->', $install_msg['report_ko'], $report_page);
	}

	//Information mise a jour de base
	$bdd_version = "";
	$bdd_version_query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' limit 1";
	$res = @pmb_mysql_query($bdd_version_query, $link);
	if($res) {
		$bdd_version = @pmb_mysql_result($res, 0, 0);
	}
	if ($bdd_version != $pmb_version_database_as_it_should_be) {
		$report_page = str_replace('<!-- tpl_bdd_version -->', $report_tpl['bdd_version'], $report_page);
		$report_msg = sprintf($install_msg['report_bdd_version'], $bdd_version, $pmb_version_database_as_it_should_be);
		$report_page = str_replace('<!-- report_bdd_version -->', $report_msg, $report_page);
	}

	//Lien vers la page d'accueil
	$report_page = str_replace('<!-- report_home_link -->', $install_msg['report_home_link'], $report_page);

	echo $report_page;

} else {

	$report_page = $report_tpl['error_page'];
	echo $report_page;
}

@pmb_mysql_close($link);
