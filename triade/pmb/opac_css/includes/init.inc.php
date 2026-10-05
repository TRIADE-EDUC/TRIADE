<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: init.inc.php,v 1.34.2.1.2.1 2025/05/20 14:00:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

// Securite, on bloque les URL non autorisees
// Exemple :
// http://localhost/opac_css/index.php/
// http://localhost/opac_css/index.php/index.php
$scriptname = strtolower(basename($_SERVER['SCRIPT_NAME']));
if (
	!in_array($scriptname, ['rest.php', 'cms_rest.php', 'visionneuse.php', 'vig_num.php'], true) &&
	!empty($_SERVER['PATH_INFO'])
) {
	http_response_code(403);
	exit();
}

global $include_path, $base_path, $class_path, $javascript_path, $lvl, $user_query, $opac_view, $search_type_asked, $current_module;

//Chemins par défaut de l'application (il faut initialiser $base_path relativement à l'endroit où s'exécute le script)
$include_path=$base_path."/includes";
$class_path=$base_path."/classes";
$javascript_path=$base_path."/includes/javascript";

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

// A n'utiliser QUE dans le contexte des MAP
if (!defined( 'TYPE_RECORD' )) 		define('TYPE_RECORD',11);

if(!defined('TYPE_CONCEPT_PREFLABEL')) 					define('TYPE_CONCEPT_PREFLABEL', 1);
if(!defined('TYPE_TU_RESPONSABILITY')) 					define('TYPE_TU_RESPONSABILITY', 2);
if(!defined('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL')) 	define('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL', 3);
if(!defined('TYPE_NOTICE_RESPONSABILITY_AUTRE')) 		define('TYPE_NOTICE_RESPONSABILITY_AUTRE', 4);
if(!defined('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE')) 	define('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE', 5);
if(!defined('TYPE_TU_RESPONSABILITY_INTERPRETER')) 		define('TYPE_TU_RESPONSABILITY_INTERPRETER', 6);
if(!defined('TYPE_AUTHPERSO_RESPONSABILITY')) 			define('TYPE_AUTHPERSO_RESPONSABILITY', 7);

/**
 * Codes d'erreur de connexion
 */
if (! defined('CONNEXION_ERROR_NONE')) {
    define('CONNEXION_ERROR_NONE', 0);
}
if (! defined('CONNEXION_ERROR_EXPIRED_SUBSCRIPTION')) {
    define('CONNEXION_ERROR_EXPIRED_SUBSCRIPTION', 1);
}
if (! defined('CONNEXION_ERROR_FORBIDDEN_STATUS')) {
    define('CONNEXION_ERROR_FORBIDDEN_STATUS', 2);
}
if (! defined('CONNEXION_ERROR_INVALID_CREDENTIALS')) {
    define('CONNEXION_ERROR_INVALID_CREDENTIALS', 3);
}
if (! defined('CONNEXION_ERROR_WAITING_FOR_OTP_CODE')) {
    define('CONNEXION_ERROR_WAITING_FOR_OTP_CODE', 4);
}
if (! defined('CONNEXION_ERROR_INVALID_OTP_CODE')) {
    define('CONNEXION_ERROR_INVALID_OTP_CODE', 5);
}
if (! defined('CONNEXION_ERROR_INVALID_LINK')) {
    define('CONNEXION_ERROR_INVALID_LINK', 6);
}
if (! defined('CONNEXION_ERROR_TOO_MANY_ATTEMPTS')) {
    define('CONNEXION_ERROR_TOO_MANY_ATTEMPTS', 7);
}
if (! defined('CONNEXION_ERROR_FORBIDDEN_IP')) {
    define('CONNEXION_ERROR_FORBIDDEN_IP', 8);
}
if (! defined('CONNEXION_ERROR_EXPIRED_SESSION')) {
    define('CONNEXION_ERROR_EXPIRED_SESSION', 9);
}
if (! defined('CONNEXION_ERROR_UNKNOWN')) {
    define('CONNEXION_ERROR_UNKNOWN', 10);
}

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

if(!isset($lvl)) $lvl = '';
if(!isset($user_query)) $user_query = '';
if(!isset($opac_view)) $opac_view = '';
if(!isset($search_type_asked)) $search_type_asked = '';
if(!isset($current_module)) $current_module = '';

$lvl = strip_tags($lvl);
$user_query = strip_tags($user_query);
$opac_view = strip_tags($opac_view);
$search_type_asked = strip_tags($search_type_asked);
$current_module = strip_tags($current_module);

// Chargement de l'autoload de gestion pour les librairies externes
require_once '../vendor/autoload.php';
// Chargement de l'autoload front-office
require_once __DIR__."/../classes/autoloader/classLoader.class.php";
$al = classLoader::getInstance();
$al->register();

// Ne peut pas être mis dans misc,Dans misc on a besoin de la BDD et on manipule le cookie avant...
function pmb_setcookie($name, $value = "", $expires = 0, $path = "", $domain = "", $httponly = true){
	switch (true) {
	    case ( !empty($_SERVER['HTTPS']) && ( (strtolower($_SERVER['HTTPS']) === 'on') || ($_SERVER['HTTPS'] == 1) ) ) :
	    case ( !empty($_SERVER['HTTP_SSL_HTTPS']) && ( (strtolower($_SERVER['HTTP_SSL_HTTPS']) === 'on') || ($_SERVER['HTTP_SSL_HTTPS'] == 1) ) ) :
	    case ( !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ) :
	        if (session_status() !== PHP_SESSION_ACTIVE) {
	            // Warning: ini_set(): A session is active
	            ini_set('session.cookie_secure', 1);
	        }
	        break;
	    default :
	        break;
	}

	if (session_status() !== PHP_SESSION_ACTIVE) {
        // Warning: ini_set(): A session is active.
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Lax');
	}

	$params = session_get_cookie_params();

	// Warning: setcookie n'attend pas de clé 'lifetime'
	if (isset($params['lifetime'])) {
		unset($params['lifetime']);
	}
	$params["expires"] = $expires;
	$params["path"] = $path;
	$params["domain"] = $domain;
	$params["httponly"] = $httponly;

	return setcookie($name, $value ?? "", $params);
}

