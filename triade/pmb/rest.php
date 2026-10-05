<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rest.php,v 1.9 2023/03/21 14:34:32 qvarin Exp $
$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;
$base_nodojo = 1;
$clean_pret_tmp = 1;
$base_is_http_request = 1;

require_once $base_path . '/vendor/autoload.php';

if (! defined('NOT_FOUND')) {
    define('NOT_FOUND', 404);
}
if (! defined('METHOD_NOT_ALLOWED')) {
    define('METHOD_NOT_ALLOWED', 405);
}
if (! defined('FORBIDEN')) {
    define('FORBIDEN', 403);
}

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

$base_auth = $class_name::BASE_AUTH;

$instance = new $class_name($portal_url);

require_once ($base_path . "/includes/init.inc.php");
require_once ($include_path . "/ajax.inc.php");


if (defined("SESSrights") && ! SESSrights) {
    http_response_code(FORBIDEN);
    exit();
}

$instance->proceed();