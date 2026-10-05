<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: config.inc.php,v 1.250.2.28.2.19 2025/06/09 13:52:19 qvarin Exp $

// fichier de configuration générale

$pmb_version = "</b>8.0.1</b>";
$pmb_version_brut = "8.0.1";
$pmb_version_patch = "9";
$pmb_version_database_as_it_should_be = "v6.01";
$pmb_subversion_database_as_it_shouldbe = "32";

$pmb_version_web = "" ;

// permet de transcrire les caractères du cp1252 en 8x et 9x ... (désactivé par défaut)
//$pmb_cp1252_normalize = 1;

// prevents direct script access
if(isset($HTTP_SERVER_VARS) && strpos($HTTP_SERVER_VARS['PHP_SELF'],'config.inc.php')) {
	echo $pmb_version_brut ;
	exit ;
}

$default_lang = 'fr_FR';
$default_helpdir = $default_lang;
// Character set = encodage des données. Attention ne pas modifier en cours d'utilisation, votre base de données serait pleine de caracteres bizarres !!!
$charset= 'utf-8';

// feuille de style à utiliser
$stylesheet = 'enjoy';

// utilisation des raccourcis clavier (0=non ; 1=oui)
$use_shortcuts = 1;

// taille des fenêtres de selecteurs
$selector_x_size = 400; 	# largeur
$selector_y_size = 400;		# hauteur

// niveau du fichier de log :
// stable=erreurs utilisateur seulement
// unstable=toutes erreurs
// off=pas de gestion des erreurs

$loglevel = 'off';

// fichier de log
$logfile = './journal.log';

// flags pour la gestion des droits utilisateurs
define('CIRCULATION_AUTH'			,    1);
define('CATALOGAGE_AUTH'			,    2);
define('AUTORITES_AUTH'				,    4);
define('ADMINISTRATION_AUTH'		,    8);
define('EDIT_AUTH'					,   16);
define('SAUV_AUTH'					,   32);
define('DSI_AUTH'					,   64);
define('PREF_AUTH'					,  128);
define('ACQUISITION_AUTH'			,  256);
define('RESTRICTCIRC_AUTH'			,  512);
define('RESTRICTCATAL_AUTH'			, 1024);
define('THESAURUS_AUTH'				, 2048);
define('TRANSFERTS_AUTH'			, 4096);
define('EXTENSIONS_AUTH'			, 8192);
define('DEMANDES_AUTH'				, 16384);
define('FICHES_AUTH'				, 32768);
define('CMS_AUTH'					, 65536);
define('EDIT_FORCING_AUTH'			,131072);
define('CATAL_MODIF_CB_EXPL_AUTH'	,262144);
define('ACQUISITION_ACCOUNT_INVOICE_AUTH',524288);
define('CMS_BUILD_AUTH'				, 1048576);
define('SEMANTIC_AUTH'				, 2097152);
define('CONCEPTS_AUTH'				, 4194304);
define('FRBR_AUTH'					, 8388608);
define('MODELLING_AUTH'				, 16777216);
define('ANIMATION_AUTH'				, 33554432);

$CACHE_ENGINE = 'apcu';//Type de moteur de cache php utilisé
$CACHE_MAXTIME = 86400;//Duree de mise en cache
$KEY_CACHE_FILE_XML = 'key_cache_file_xml'.md5(str_replace(array('/admin/netbase'), '', getcwd()));//Prefix pour la cle des variables en cache pour les fichiers XML

//Variables MYSQL / PHP
$SQL_MOTOR_TYPE = '';
$SQL_VARIABLES = "sql_mode=''";
$time_zone_mysql = '';
$time_zone = '';

// définition des périodicités de pério
define('ABT_PERIODICITE_JOUR'		,    1);

// définition des types d'audit
define('AUDIT_NOTICE'	,    1);
define('AUDIT_EXPL'		,    2);
define('AUDIT_BULLETIN'	,    3);
define('AUDIT_ACQUIS'	,    4);
define('AUDIT_PRET'		,    5);
define('AUDIT_AUTHOR'	,    6);
define('AUDIT_COLLECTION',   7);
define('AUDIT_SUB_COLLECTION',8);
define('AUDIT_INDEXINT'	,    9);
define('AUDIT_PUBLISHER',    10);
define('AUDIT_SERIE'	,    11);
define('AUDIT_CATEG'	,    12);
define('AUDIT_TITRE_UNIFORME',13);
define('AUDIT_DEMANDE'	,    14);
define('AUDIT_ACTION'	,    15);
define('AUDIT_NOTE',16);
define('AUDIT_EDITORIAL_ARTICLE',20);
define('AUDIT_EDITORIAL_SECTION',21);
define('AUDIT_EXPLNUM',22);
define('AUDIT_CONCEPT', 23);
define('AUDIT_BANNETTE', 24);

/* la langue est fixée sur la valeur par défaut pour l'instant */
$lang= $default_lang;
$helpdir = $lang;

/* répertoire où sont stockées les sauvegardes (dans le rép 'admin/backup') */
$backup_dir = "backups";

// est stockée en base mais par défaut, si vide ...
if (!isset($pmb_opac_url)) $pmb_opac_url = "./opac_css/";

/* Nbre d'enregistrements affichés par page */
/* autorités */                  /* each was 10 */
$nb_per_page_author = 20 ;
$nb_per_page_publisher = 20 ;
$nb_per_page_collection = 20 ;
$nb_per_page_subcollection = 20 ;
$nb_per_page_serie = 20 ;

/* recherches */
/* author */
$nb_per_page_a_search = 10 ; /* was 3 */
/* publisher */
$nb_per_page_p_search = 10 ; /* was 4 */
/* subject */
$nb_per_page_s_search = 10 ; /* was 4 */

/* lecteur */
$nb_per_page_empr = 10 ; /* was 4 */

/* selectors */
/* author */
$nb_per_page_a_select = 10 ; /* was 10 */
/* collection */
$nb_per_page_c_select = 10 ; /* was 10 */
/* sub-collection */
$nb_per_page_sc_select = 10 ; /* was 10 */
/* publisher */
$nb_per_page_p_select = 10 ; /* was 10 */
/* serie */
$nb_per_page_s_select = 10 ; /* was 10 */
/* groups */
$nb_per_page_group = 10; /* is 10 */

$include_path      = 'includes';               // includes
$class_path        = 'classes';                // classes
$javascript_path   = 'javascript';             // scripts
$styles_path       = 'styles';                 // styles

// alertes sonores, en tableau pour pouvoir en mettre plusieurs dans le futur :
$alertsound["critique"]="<embed src='sounds/boing.ogg' autostart='true' loop='false' hidden='true' width='0' height='0'>";
$alertsound["information"]="<embed src='sounds/waou.ogg' autostart='true' loop='false' hidden='true' width='0' height='0'>";
$alertsound["question"]="<embed src='sounds/boing.ogg' autostart='true' loop='false' hidden='true' width='0' height='0'>";
$alertsound["application"]="<embed src='sounds/boing.ogg' autostart='true' loop='false' hidden='true' width='0' height='0'>";
$param_sounds = 1 ;

$homepage = 'http://www.sigb.net/';

@include_once("includes/config_local.inc.php") ;
@include_once("config_local.inc.php") ;

@include_once("includes/global_vars.inc.php") ;
@include_once("global_vars.inc.php") ;