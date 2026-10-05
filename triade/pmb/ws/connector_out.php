<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connector_out.php,v 1.16.4.1 2025/02/11 09:20:04 dgoron Exp $
//Here be komodo dragons

//Les erreurs php font taches dans les protocoles des connecteurs
if (!isset($_GET["debug"])) {
    ini_set('display_errors', 0);
    error_reporting(0);
}

$base_path = "..";
$base_nobody = 1;
$base_noheader = 1;
$base_nocheck = 1;
$base_nodojo = 1;

$class_path = $base_path . "/classes";
$include_path = $base_path . "/includes";
$javascript_path = $base_path . "/javascript";


//Cette fonction recrée un environnement de session, comme si l'utilisateur était loggué
function create_user_environment($user_id) {

    // Copié de /includes/sessions.inc.php
    global $dbh; // le lien MySQL
    global $stylesheet; /* pour qu'à l'ouverture de la session le user récupère de suite son style */
    global $PMBuserid, $PMBgrp_num;
    global $checkuser_type_erreur;
    global $PMBdatabase;
    global $database;
    global $deflt_styles;
    global $pmb_indexation_lang;

    if ( empty($PMBdatabase) ) {
        $PMBdatabase = $database;
    }
    $user_id = intval($user_id);
    $query = "SELECT rights, username, user_lang FROM users WHERE userid=$user_id";
    $result = pmb_mysql_query($query, $dbh);
    if (! $result) {
        return false;
    }
    $ff = pmb_mysql_fetch_object($result);
    $flag = $ff->rights;

    // mise à disposition des variables de la session
    define('SESSlogin', $ff->username);
    define('SESSname', 'PhpMyBibli');
    define('SESSid', 0);
    define('SESSstart', 0);
    define('SESSlang', $ff->user_lang);
    define('SESSrights', $flag);

    /* param par défaut */
    load_user_param();
    define('SESSuserid', $PMBuserid);
    /* on va chercher la feuille de style du user */
    global $deflt_styles;
    $stylesheet = $deflt_styles;

    // Récupération de l'historique
    $admin_session = new admin_session();
    $admin_session->load();
    return true;
}

//Ignition sequence:
require_once ("$base_path/includes/init.inc.php");
//Les erreurs php font taches dans les protocoles des connecteurs
if ( !isset($_GET["debug"]) ) {
    ini_set('display_errors', 0);
    error_reporting(~ E_ALL);
}

require_once ($class_path . "/admin_session.class.php");
require_once ($class_path . "/connecteurs_out.class.php");
require_once ($class_path . "/external_services.class.php");

$source_id = !empty($_GET["source_id"]) ? intval($_GET["source_id"]) : 0 ;
if (!$source_id) {
    die();
}
// Trouvons de quel connecteur dépend la source
$sql = "SELECT connectors_out_sources_connectornum FROM connectors_out_sources WHERE connectors_out_source_id = " . $source_id;
$res = pmb_mysql_query($sql, $dbh);
if (! pmb_mysql_num_rows($res)) {
    die();
}
$connector_id = pmb_mysql_result($res, 0, 0);
if (! $connector_id) {
    die();
}
//Instantions le connecteur
$daconn = instantiate_connecteur_out($connector_id);
//Cherchons l'id de l'utilisateur pmb qui doit faire tourner les fonctions
$running_pmb_user_id = $daconn->get_running_pmb_userid($source_id);

//Créons un environnement de session virtuel.
if (! create_user_environment($running_pmb_user_id)) {
    die();
}
if (defined('SESSlang') && SESSlang) {
    $lang = SESSlang;
    $helpdir = $lang;
} else {
    $lang = "fr_FR";
    $helpdir = "fr_FR";
}

if ($daconn->need_global_messages()) {
    // Allons chercher les messages
    include_once ("$class_path/XMLlist.class.php");
    $messages = new XMLlist("$include_path/messages/$lang.xml", 0);
    $messages->analyser();
    $msg = $messages->table;
}

if (! $daconn) {
    die(); // Oups!
}

// Inclusion/initialisation du système de plugins
require_once $class_path . '/plugins.class.php';
$plugins = plugins::get_instance();

// Inclusion/initialisation du système d'évenements !
require_once $class_path . '/event/events_handler.class.php';
$evth = events_handler::get_instance();
$evth->discover();
$requires = $evth->get_requires();
for ($i = 0; $i < count($requires); $i ++) {
    require_once $requires[$i];
}

// Au boulot le connecteur!
$daconn->process($source_id, $running_pmb_user_id);

