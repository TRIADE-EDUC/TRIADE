<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4.20.1 2025/02/07 13:49:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

//En fonction de $categ, il inclut les fichiers correspondants

session_write_close();

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');

if (file_exists($base_path.'/includes/opac_db_param.inc.php')) {
    require_once($base_path.'/includes/opac_db_param.inc.php');
} else {
    die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");
}

require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
require_once($base_path."/includes/check_session_time.inc.php");
require_once($base_path.'/includes/localisation.inc.php');
require_once($base_path."/includes/rec_history.inc.php");
require_once($base_path.'/includes/opac_version.inc.php');
require_once($base_path."/includes/notice_affichage.inc.php");

switch($categ) {
    case 'expand':
        include('expand_ajax.inc.php');
        break;

    case 'expand_block':
        include('expand_block_ajax.inc.php');
        break;

    default:
        http_response_code(404);
        break;
}
