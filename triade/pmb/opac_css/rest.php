<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rest.php,v 1.6 2023/11/07 16:10:00 rtigero Exp $

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;

if (! defined('NOT_FOUND')) {
	define('NOT_FOUND', 404);
}
if (! defined('METHOD_NOT_ALLOWED')) {
	define('METHOD_NOT_ALLOWED', 405);
}
if (! defined('FORBIDEN')) {
	define('FORBIDEN', 403);
}

require_once "{$base_path}/includes/init.inc.php";
require_once "{$base_path}/includes/error_report.inc.php";
require_once "{$base_path}/includes/opac_config.inc.php";

if (file_exists("{$base_path}/includes/opac_db_param.inc.php")) {
	require_once "{$base_path}/includes/opac_db_param.inc.php";
} else  {
	http_response_code(FORBIDEN);
	exit();
}

if ($charset != "utf-8") {
	$_POST = encoding_normalize::utf8_decode($_POST);
}

require_once "{$base_path}/includes/global_vars.inc.php";
require_once "{$base_path}/includes/opac_mysql_connect.inc.php";
$dbh = connection_mysql();

require_once "{$base_path}/includes/session.inc.php";
require_once "{$base_path}/includes/start.inc.php";
require_once "{$base_path}/includes/check_session_time.inc.php";
require_once "{$base_path}/includes/localisation.inc.php";
require_once "{$base_path}/includes/ajax.inc.php";


$matches = array();
preg_match_all('/\/([^\/:]+):?\/(.*)/mi', $_SERVER['PATH_INFO'], $matches, PREG_SET_ORDER, 0);
if (empty($matches)) {
    http_response_code(NOT_FOUND);
    exit();
}

$portal_module = $matches[0][1];
$portal_url = $matches[0][2];

$class_name = "\\Pmb\\REST\\" . ucfirst(strtolower($portal_module) . "RouterRest");
if (! class_exists($class_name)) {
    http_response_code(NOT_FOUND);
    exit();
}

if (! $class_name::ALLOW_OPAC) {
	http_response_code(FORBIDEN);
	exit();
}

$instance = new $class_name($portal_url);
$instance->proceed();