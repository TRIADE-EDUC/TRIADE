<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: db_param.model.php,v 1.2 2023/04/07 14:25:37 dbellamy Exp $
// paramètres d'accès à la base MySQL

// prevents direct script access
if (preg_match('/db_param\.inc\.php/', $_SERVER['REQUEST_URI'])) {
    include ('./forbidden.inc.php');
    forbidden();
}

if (file_exists('../../../common/config.inc.php')) include_once('../../../common/config.inc.php');
if (file_exists('../../common/config.inc.php')) include_once('../../common/config.inc.php');
if (file_exists('../common/config.inc.php')) include_once('../common/config.inc.php');


// inclure ici les tableaux des bases de données accessibles
$_tableau_databases[0]=DB ;
$_libelle_databases[0]=DB ;


// pour multi-bases
if ( empty($database) ){
    if( !empty($_COOKIE["PhpMyBibli-DATABASE"]) ) {
        $database = $_COOKIE["PhpMyBibli-DATABASE"];
    } else {
        $database = $_tableau_databases[0];
    }
}
if ( !in_array($database, $_tableau_databases) ) {
    $database = $_tableau_databases[0];
}
define('LOCATION', $database);

// define pour les paramètres de connection. A adapter.
switch (LOCATION) {

    default :
    case DB:
	define('SQL_SERVER', HOST );            // nom du serveur
        define('USER_NAME', USER );             // nom utilisateur
        define('USER_PASS', PWD );              // mot de passe
        define('DATA_BASE', DB );               // nom base de données
	define('SQL_TYPE',  'mysql');                   // Type de serveur de base de données
        // $charset = 'utf-8'; || $charset = 'iso-8859-1';
        // $time_zone = 'Europe/Paris'; //Pour modifier l'heure PHP
        // $time_zone_mysql = "'-00:00'"; //Pour modifier l'heure MySQL
        // $SQL_VARIABLES = "sql_mode='NO_AUTOCREATE_USER',join_buffer_size=1000000";

        $charset = "utf-8";
        /* SQL_VARIABLES */
        $SQL_VARIABLES = "session tmp_table_size=268435456";

        break;

}

$dsn_pear = SQL_TYPE."://".USER_NAME.":".USER_PASS."@".SQL_SERVER."/".DATA_BASE ;
