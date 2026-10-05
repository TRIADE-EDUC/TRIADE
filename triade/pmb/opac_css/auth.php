<?php

use Pmb\Authentication\Controller\AuthenticationOpacController;

$base_path = ".";
require_once "{$base_path}/includes/init.inc.php";
require_once "{$base_path}/includes/error_report.inc.php";
require_once "{$base_path}/includes/opac_config.inc.php";

if (file_exists("{$base_path}/includes/opac_db_param.inc.php")) {
	require_once "{$base_path}/includes/opac_db_param.inc.php";
}
require_once "{$base_path}/includes/global_vars.inc.php";
require_once "{$base_path}/includes/opac_mysql_connect.inc.php";
$dbh = connection_mysql();

require_once "{$base_path}/includes/session.inc.php";
require_once "{$base_path}/includes/start.inc.php";
require_once "{$base_path}/includes/check_session_time.inc.php";
require_once "{$base_path}/includes/localisation.inc.php";
require_once "{$base_path}/includes/ajax.inc.php";
require_once "{$base_path}/includes/templates/common.tpl.php";

if (!empty($action) && $action == "logout") {
    global $logout;
    $logout = 1;
}

if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
    require_once($base_path.'/includes/ext_auth.inc.php');
}

$controller = new AuthenticationOpacController();
$controller->proceed();