<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: init.inc.php,v 1.90.4.1 2025/05/20 14:00:08 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

use Pmb\Security\Library\Auth;

// Securite, on bloque les URL non autorisees
// Exemple :
// http://localhost/index.php/
// http://localhost/index.php/index.php
$scriptname = strtolower(basename($_SERVER['SCRIPT_NAME']));
if (
	!in_array($scriptname, ['rest.php', 'connector_out.php', 'vig_num.php'], true) &&
	!empty($_SERVER['PATH_INFO'])
) {
	http_response_code(403);
	exit();
}

// Cet include permet de réduire considérablement les trucs à mettre au départ d'un script
// Six paramêtres à fournir en fixant les valeurs avant l'include de ce fichier
//	$base_path="../.."; par ex : = chemin pour accéder à la racine de l'applic PMB
//	$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH"; les droits du user à tester
//	$base_title = "Titre de la fenêtre"; le titre de la page : facultatif
//		si besoin d'une variable : $base_title = "\$msg[28]";
//	$base_noheader = 0; par défaut, pas obligatoire, si non vide : pas d'envoi du début de page (header & co)
//	$base_nocheck = 0; par défaut, pas obligatoire : si non vide : pas de checkuser ( session, droits )
//	$base_nobody = 0; par défaut, pas obligatoire : si non vide : pas de <body> après le header envoyé
//  $base_nosession =0; par défaut, pas obligatoire, si non vide pas d'envoi du cookie de session dans global_vars.inc.php
//
//	l'exemple ci-dessus correspond à l'inclusion dans le fichier : admin/sauvegarde/launch.php :
//		$base_path="../..";
//		$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH";
//		$base_title = "Lancement d'une sauvegarde";
//		require_once ("$base_path/includes/init.inc.php");
//	l'exemple ci-dessus correspond à l'inclusion dans le fichier : catalog/z3950/z_progession_main.php :
//		J'ai besoin du header mais pas du <body> à cause des frames
//		$base_path="../..";
//		$base_auth = "CIRCULATION_AUTH";
//		$base_title = "";
//		$base_nobody = 1;
//		require_once ("$base_path/includes/init.inc.php");

if (!$base_path) $base_path=".";

include_once $base_path."/includes/error_report.inc.php";
require_once $base_path."/includes/pmb_cookie.inc.php";
require_once $base_path."/includes/config.inc.php";


$include_path      = $base_path."/".$include_path;
$class_path        = $base_path."/".$class_path;
$javascript_path   = $base_path."/".$javascript_path;
$styles_path       = $base_path."/".$styles_path;

if (!defined('GESTION'))            define('GESTION', 1);

if (!defined('TYPE_NOTICE')) 		define('TYPE_NOTICE',1);
if (!defined('TYPE_AUTHOR')) 		define('TYPE_AUTHOR',2);
if (!defined('TYPE_CATEGORY'))		define('TYPE_CATEGORY',3);
if (!defined('TYPE_PUBLISHER')) 	define('TYPE_PUBLISHER',4);
if (!defined('TYPE_COLLECTION')) 	define('TYPE_COLLECTION',5);
if (!defined('TYPE_SUBCOLLECTION')) define('TYPE_SUBCOLLECTION',6);
if (!defined('TYPE_SERIE')) 		define('TYPE_SERIE',7);
if (!defined('TYPE_TITRE_UNIFORME')) define('TYPE_TITRE_UNIFORME',8);
if (!defined('TYPE_INDEXINT'))		define('TYPE_INDEXINT',9);
if (!defined('TYPE_EXPL'))			define('TYPE_EXPL',10);
if (!defined('TYPE_EXPLNUM')) 		define('TYPE_EXPLNUM',11);
if (!defined('TYPE_AUTHPERSO')) 	define('TYPE_AUTHPERSO',12);
if (!defined('TYPE_CMS_SECTION')) 	define('TYPE_CMS_SECTION',13);
if (!defined('TYPE_CMS_ARTICLE')) 	define('TYPE_CMS_ARTICLE',14);
if (!defined('TYPE_LOCATION'))		define('TYPE_LOCATION',15);
if (!defined('TYPE_SUR_LOCATION'))	define('TYPE_SUR_LOCATION',16);
if (!defined('TYPE_CONCEPT'))		define('TYPE_CONCEPT',17);
if (!defined('TYPE_ONTOLOGY'))		define('TYPE_ONTOLOGY',18);
if (!defined('TYPE_DOCWATCH'))		define('TYPE_DOCWATCH',19);
if (!defined('TYPE_EXTERNAL'))		define('TYPE_EXTERNAL',20);
if (!defined('TYPE_ANIMATION'))		define('TYPE_ANIMATION',21);
if (!defined('TYPE_BULLETIN'))		define('TYPE_BULLETIN',22);
if (!defined('TYPE_AUTHORITY'))		define('TYPE_AUTHORITY',23);
if (!defined('TYPE_DSI_DIFFUSION'))		define('TYPE_DSI_DIFFUSION',24);
if (!defined('TYPE_CMS_EDITORIAL'))		define('TYPE_CMS_EDITORIAL',25);
if (!defined('TYPE_SHARED_LIST_EXPLNUM'))		define('TYPE_SHARED_LIST_EXPLNUM',26);

// A n'utiliser QUE dans le contexte des MAP
if (!defined('TYPE_RECORD')) 		define('TYPE_RECORD',11);

if (!defined('TYPE_CONCEPT_PREFLABEL')) 					            define('TYPE_CONCEPT_PREFLABEL', 1);
if (!defined('TYPE_TU_RESPONSABILITY')) 					            define('TYPE_TU_RESPONSABILITY', 2);
if (!defined('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL')) 	                define('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL', 3);
if (!defined('TYPE_NOTICE_RESPONSABILITY_AUTRE')) 		                define('TYPE_NOTICE_RESPONSABILITY_AUTRE', 4);
if (!defined('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE')) 	                define('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE', 5);
if (!defined('TYPE_TU_RESPONSABILITY_INTERPRETER')) 		            define('TYPE_TU_RESPONSABILITY_INTERPRETER', 6);
if (!defined('TYPE_AUTHPERSO_RESPONSABILITY')) 			                define('TYPE_AUTHPERSO_RESPONSABILITY', 7);

// define pour différent flags de situation document
define ('EX_OK', 1);
define ('EX_INCONNU', 2);
define ('HAS_RESA_GOOD', 4); // l'exemplaire est réservé pour ce lecteur
define ('NON_PRETABLE', 8);
define ('HAS_NOTE', 16);
define ('HAS_RESA_FALSE', 32); // l'exemplaire est réservé pour un autre lecteur
define ('ALREADY_LOANED', 64); // cet emprunteur a déjà emprunté ce document
define ('ALREADY_BORROWED', 128); // ce document est emprunté par un autre emprunteur
define ('HAS_RESA_PLANNED_FALSE', 256); //Les prévisions sur le document sont égales ou supérieures au nb d'exemplaires disponibles
define ('IS_TRUSTED',512); //l'exemplaire est monopolisé
define ('IS_GROUP',1024); //l'exemplaire fait parti d'un groupe d'exemplaires

// Lien des entitiés dans PMB
if (!defined('LIEN_NOTICE')) define('LIEN_NOTICE', "./catalog.php?categ=isbd&id=!!id!!");
if (!defined('LIEN_AUTEUR')) define('LIEN_AUTEUR', "./autorites.php?categ=see&sub=author&id=!!id!!");
if (!defined('LIEN_CATEG')) define('LIEN_CATEG', "./autorites.php?categ=see&sub=category&id=!!id!!");
if (!defined('LIEN_EDITEUR')) define('LIEN_EDITEUR', "./autorites.php?categ=see&sub=publisher&id=!!id!!");
if (!defined('LIEN_COLLECTION')) define('LIEN_COLLECTION', "./autorites.php?categ=see&sub=collection&id=!!id!!");
if (!defined('LIEN_SUBCOLLECTION')) define('LIEN_SUBCOLLECTION', "./autorites.php?categ=see&sub=subcollection&id=!!id!!");
if (!defined('LIEN_SERIE')) define('LIEN_SERIE', "./autorites.php?categ=see&sub=serie&id=!!id!!");
if (!defined('LIEN_TITRE_UNIFORM')) define('LIEN_TITRE_UNIFORM', "./autorites.php?categ=see&sub=titre_uniforme&id=!!id!!");
if (!defined('LIEN_INDEXINT')) define('LIEN_INDEXINT', "./autorites.php?categ=see&sub=indexint&id=!!id!!");
if (!defined('LIEN_CONCEPT')) define('LIEN_CONCEPT', "./autorites.php?categ=see&sub=concept&id=!!id!!");
if (!defined('LIEN_AUTHPERSO')) define('LIEN_AUTHPERSO', "./autorites.php?categ=see&sub=authperso&id=!!id!!");

// Store sparql
if (!defined('ONTOLOGY_NAMESPACE')) {
    define('ONTOLOGY_NAMESPACE', array(
        "skos"	=> "http://www.w3.org/2004/02/skos/core#",
        "dc"	=> "http://purl.org/dc/elements/1.1",
        "dct"	=> "http://purl.org/dc/terms/",
        "owl"	=> "http://www.w3.org/2002/07/owl#",
        "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
        "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
        "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
        "pmb"	=> "http://www.pmbservices.fr/ontology#",
        "pmb_onto"	=> "http://www.pmbservices.fr/ontology_description#"
    ));
}

require_once "$class_path/XMLlist.class.php";

// fichier de déf. pour gestion des erreurs
require_once "$include_path/error_handler.inc.php";

// Chargement du fichier de paramétrage de la BDD
if(file_exists("$include_path/db_param.inc.php")){
    require_once "$include_path/db_param.inc.php";
}else{
    // Pas de fichier présent, on s'assure quand même qu'il n'y a pas déjà eue une installation
     if(file_exists($base_path."/tables/install.php")){
         // Fichier d'installation présent, on renvoie dessus !
         header("Location: $base_path/tables/install.php");
     }else{
         die("Fichier db_param.inc.php absent / Missing file Fichier db_param.inc.php");
     }
}

if (isset($_tableau_databases[1]) && isset($base_title)) {
	// multi-databases
	$database_window_title=$_libelle_databases[array_search(LOCATION,$_tableau_databases)].": ";
} else $database_window_title="" ;

require_once "$include_path/mysql_connect.inc.php";
$dbh = connection_mysql();

// On vérifie si la connexion à la BDD est bonne
if(!$dbh){
    // ON uniformise avec le même message que pour le fichier index.php !
    // -> Début de inclusion reprise depuis index.php
    require_once "$include_path/misc.inc.php";
    // localisation (fichier XML)
    include_once "$class_path/XMLlist.class.php";

    $messages = new XMLlist("$include_path/messages/$lang.xml", 0);
    $messages->analyser();
    $msg = $messages->table;

    // temporaire :
    $inst_language = "";

    require_once "$include_path/templates/index.tpl.php";
    header ("Content-Type: text/html; charset=".$charset);
    print $index_header;
    print $extra_version;
    print "<br /><br /><div class='erreur'> $__erreur_cnx_base__ </div><br /><br />" ;
    print $msg["cnx_base_err1"]." <a href='./tables".$inst_language."/install.php'>./tables/install.php</a> ? <br /><br />.".$msg["cnx_base_err2"];
    print $index_footer;
    exit ;
    // -> Fin de l'inclusion reprise depuis index.php
}

require_once "$include_path/sessions.inc.php";
require_once "$include_path/misc.inc.php";
require_once "$javascript_path/misc.inc.php";
require_once "$class_path/pmb_error.class.php";
require_once "$include_path/user_error.inc.php";

// Chargement de l'autoload des librairies externes
require_once $base_path.'/vendor/autoload.php';
// Chargement de l'autoload back-office
require_once __DIR__."/../classes/autoloader/classLoader.class.php";
$al = classLoader::getInstance();
$al->register();

if(!isset($_SESSION['CURRENT'])) $_SESSION['CURRENT'] = '';
if(!isset($_SESSION['ext_type'])) $_SESSION['ext_type'] = '';
if(!isset($_SESSION['opac_view'])) $_SESSION['opac_view'] = '';
if(!isset($_SESSION['id_empr_session'])) $_SESSION['id_empr_session'] = '';
if(!isset($_SESSION['user_code'])) $_SESSION['user_code'] = '';
if(!isset($_SESSION['tri'])) $_SESSION['tri'] = '';

if(!isset($sub)) $sub = '';
if(!isset($action)) $action = '';

// Get current page...  pour marquer l'onglet...
if (!isset($current_alert)) {
	$current = current_page();
	$current_module=str_replace(".php","",$current);
} else  {
	$current = '';
	// Vulnérabilités, on pouvait passer du script dans current_alert
	$current_alert = htmlentities($current_alert, ENT_QUOTES, $charset);
	$current_module = $current_alert;
}

// On peut être amené à naviguer dans le sélecteur (actuellement dans les ontologies, dans ce cas, on peut déjà avoir le module dans une variable
if($current_module == "select" && !empty($module_from)){
    $current_module = htmlentities($module_from, ENT_QUOTES, $charset);;
}else if(in_array($current_module, array('select', 'cart', 'print', 'print_cart', 'download')) && isset($_SERVER["HTTP_REFERER"])) {
	$short_referer = substr($_SERVER["HTTP_REFERER"], strrpos($_SERVER["HTTP_REFERER"], "/")+1);
	$current_module .= " ".substr($short_referer, 0, strpos($short_referer, '.'));
	$module_from = $current_module;
}
if (!$current_module) $current_module = "index" ;

include "$include_path/start.inc.php";

require_once "$include_path/clean_pret_temp.inc.php";
if(isset($categ) && ($categ=='pret' || $categ=='retour')) {
	if (!isset($clean_pret_tmp)) clean_pret_temp();
}

if(!isset($base_auth)) $base_auth = '';
if ($base_auth) eval("\$auth=".$base_auth.";");
	else $auth="";

// durée depuis le dernier rafraichissement
if(!defined('SESSION_REACTIVATE')) {
	if(!empty($pmb_session_reactivate)) {
		define('SESSION_REACTIVATE', $pmb_session_reactivate);
	} else {
		define('SESSION_REACTIVATE', 7200); // refresh max = 120 minutes
	}
}

// durée depuis le début de la session
if(!defined('SESSION_MAXTIME')) {
	if(!empty($pmb_session_maxtime)) {
		define('SESSION_MAXTIME', $pmb_session_maxtime);
	} else {
		define('SESSION_MAXTIME', 86400);	// durée de vie maximum d'une session = 24h
	}
}

if (!isset($base_nocheck)) {
	$base_nocheck = 0;
}

if (!$base_nocheck) {
	if(!checkUser('PhpMyBibli', $auth)) {
		// localisation (fichier XML) (valeur par défaut)
		$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
 		$messages->analyser();
		$msg = $messages->table;

		//Inclusion/initialisation du système de plugins
		require_once $class_path.'/plugins.class.php';
		$plugins = plugins::get_instance();

		//Inclusion/initialisation du système d'évenements !
		require_once $class_path.'/event/events_handler.class.php';
		$evth = events_handler::get_instance();
		$evth->discover();
		$requires = $evth->get_requires();
		for($i=0 ; $i<count($requires) ; $i++){
			require_once $requires[$i];
		}

		include "$include_path/templates/common.tpl.php";

		if(!isset($base_is_http_request) || !$base_is_http_request) {
 			header ("Content-Type: text/html; charset=$charset");
			print $std_header;
		}
		print "<body class='$current_module $pmb_dojo_gestion_style' id='body_current_module' page_name='$current_module'>";
		require_once "$class_path/pmb_error.class.php";
		require_once "$include_path/user_error.inc.php";
		switch ($checkuser_type_erreur) {
			case CHECK_USER_NO_SESSION :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_no_session'], 1, './index.php',basename($_SERVER['REQUEST_URI']))."</div>";
				break;
			case CHECK_USER_SESSION_DEPASSEE :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_session_depassee'], 1, './index.php', basename($_SERVER['REQUEST_URI']))."</div>";
				break;
			case CHECK_USER_SESSION_INVALIDE :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_session_invalide'], 1, './index.php', basename($_SERVER['REQUEST_URI']))."</div>";
				break;
			case CHECK_USER_AUCUN_DROITS :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_aucun_droit'], 1)."</div>";
				break;
			case CHECK_USER_PB_ENREG_SESSION :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_pb_enreg_session'], 1, './index.php')."</div>";
				break;
			case CHECK_USER_PB_OUVERTURE_SESSION :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_pb_ouverture_session'], 1, './index.php')."</div>";
				break;
			default :
				print "<div id='login-box'>".return_error_message($msg[11], $msg[12], 1)."</div>";
				break;
			}
		print $footer;
		exit;
	} else {
		$auth_instance = Auth::getInstance();
		if ($auth_instance->isInBlackList() && strpos($_SERVER['SCRIPT_NAME'], 'logout.php') === false) {
			header('Location: ./logout.php', true, 302);
			exit();
		}
	}

	if( defined('SESSlang') && SESSlang ) {
		$lang=SESSlang;
		$helpdir = $lang;
	}

	if (!$pmb_indexation_lang) $pmb_indexation_lang = $lang;

	// localisation (fichier XML)
	$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
 	$messages->analyser();
	$msg = $messages->table;

	// Recuperation des paramètres traduits dans la langue de l'utilisateur
    parameter::get_translated_parameters();

	//Inclusion/initialisation du système de plugins
	require_once "$class_path/plugins.class.php";
	$plugins = plugins::get_instance();

	//Inclusion/initialisation du système d'évenements !
	require_once $class_path.'/event/events_handler.class.php';
	$evth = events_handler::get_instance();
	$evth->discover();
	$requires = $evth->get_requires();

	for($i=0 ; $i<count($requires) ; $i++){
		require_once $requires[$i];
	}

	require "$include_path/templates/common.tpl.php";

	//
	$champs_base=array();
}

if (!isset($base_noheader)) {
	$base_noheader = 0;
}
if (!$base_noheader) {
    ob_start();
 	header ("Content-Type: text/html; charset=$charset");
	print $std_header;
	if (!isset($base_nobody)) {
		$base_nobody = 0;
	}
	if (!$base_nobody) print "<body class='$current_module $pmb_dojo_gestion_style' id='body_current_module' page_name='$current_module'>";
	if (isset($base_title)) {
		eval ("\$base_title_temp=\"".$database_window_title.$base_title."\";") ;
		echo window_title($base_title_temp);
	}
}

// Paramétrage de la RFID, en fonction éventuellement de la localisation
require_once "$class_path/parameters_subst.class.php";
if (file_exists($include_path."/parameters_subst/rfid_per_localisations_subst.xml")){
	$parameter_subst = new parameters_subst($include_path."/parameters_subst/rfid_per_localisations_subst.xml", (isset($deflt2docs_location) ? $deflt2docs_location : 0));
} else {
	$parameter_subst = new parameters_subst($include_path."/parameters_subst/rfid_per_localisations.xml", (isset($deflt2docs_location) ? $deflt2docs_location : 0));
}
$parameter_subst->extract();

// Activation RFID selon les prefs user
if (!isset($param_rfid_activate)) $param_rfid_activate = '';
if($pmb_rfid_activate)	$pmb_rfid_activate=$param_rfid_activate;
// Préparation des js sripts pour la RFID
if($pmb_rfid_activate) {
	require_once "$include_path/rfid_config.inc.php";
	get_rfid_js_header();
} else {
	$rfid_js_header = "";
}

require_once $class_path.'/event/events_handler.class.php';
require_once $class_path.'/event/event.class.php';
$evth = events_handler::get_instance();
$evth->send(new event('init', 'finished'));

require_once "$class_path/interface/interface_form.class.php";
require_once "$class_path/interface/interface_date.class.php";
require_once "$class_path/mfa/mfa_totp.class.php";
