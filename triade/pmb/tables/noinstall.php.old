<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install.php,v 1.36.4.1 2025/02/27 14:02:14 gneveu Exp $

if (preg_match('/noinstall\.php/i', urldecode($_SERVER['REQUEST_URI']))) {
    include ('../includes/forbidden.inc.php');
    forbidden();
}

global $charset, $base_path, $include_path, $class_path;
global $install_step, $install_lang;
global $install_msg;

$base_path = "..";
include $base_path . '/includes/config.inc.php';

$include_path = $base_path . '/includes';
$class_path = $base_path . '/classes';

require_once "./install.class.php";

header("Content-Type: text/html; charset={$charset}");

if (empty($install_lang)) {
    $install_lang = install::getLanguage();
}
if (empty($install_step)) {
    $install_step = 'lang';
}

if( empty($alter_session_variables) ) {
    $alter_session_variables = 0;
}

// Chargement des messages
$install_msg = install::getMessages($install_lang);

switch ($install_step) {

    case 'requirements':
        require_once "./requirements/php_requirements.inc.php";
        print $requirements_page;
        break;

    case 'mysql_requirements':
        require_once "./requirements/mysql_requirements.inc.php";
        print $mysql_page;
        break;

    case 'install':
        $install_page = install::getInstallPage($install_lang);
        print $install_page;
        break;

    case 'lang':
    default:
        $lang_page = install::getLanguagePage($install_lang);
        print $lang_page;
        break;
}
exit();
