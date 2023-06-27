<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: init.inc.php,v 1.13 2019-06-05 06:41:22 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $base_path, $class_path, $javascript_path, $lvl, $user_query, $opac_view, $search_type_asked, $current_module;

if (PHP_MAJOR_VERSION == "5") @ini_set("zend.ze1_compatibility_mode", "1");

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

// A n'utiliser QUE dans le contexte des MAP
if (!defined( 'TYPE_RECORD' )) 		define('TYPE_RECORD',11);

if(!defined('TYPE_CONCEPT_PREFLABEL')) 					define('TYPE_CONCEPT_PREFLABEL', 1);
if(!defined('TYPE_TU_RESPONSABILITY')) 					define('TYPE_TU_RESPONSABILITY', 2);
if(!defined('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL')) 	define('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL', 3);
if(!defined('TYPE_NOTICE_RESPONSABILITY_AUTRE')) 		define('TYPE_NOTICE_RESPONSABILITY_AUTRE', 4);
if(!defined('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE')) 	define('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE', 5);
if(!defined('TYPE_TU_RESPONSABILITY_INTERPRETER')) 		define('TYPE_TU_RESPONSABILITY_INTERPRETER', 6);

if(!isset($lvl)) $lvl = '';
if(!isset($user_query)) $user_query = '';
if(!isset($opac_view)) $opac_view = '';
if(!isset($search_type_asked)) $search_type_asked = '';
if(!isset($current_module)) $current_module = '';

// Chargement de l'autoload des librairies externes
require_once $base_path.'/vendor/autoload.php';
?>