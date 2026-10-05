<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2022/04/05 07:19:45 tsamson Exp $
use Pmb\Ark\Controller\NaanController;
use Pmb\Ark\Controller\ArkGenerateController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $action;
global $data;
global $sub;

switch ($sub) {
    case 'naan':
        $data = json_decode(stripslashes($data));
        $naanController = new NaanController();
        $naanController->proceed($action, $data);
        break;
    case 'generate':
        $generateController = new ArkGenerateController();
        $generateController->proceed($action);
    default:
        break;
}