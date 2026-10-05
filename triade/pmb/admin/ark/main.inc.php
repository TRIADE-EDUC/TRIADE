<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2023/05/04 09:36:37 gneveu Exp $
use Pmb\Ark\Controller\NaanController;
use Pmb\Ark\Controller\ArkGenerateController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $id;
global $data;
global $action;
global $sub;

switch ($sub) {
    case 'naan':
        if (isset($data)) {
            $data = json_decode(stripslashes($data));
        }
        $naanController = new NaanController();
        $naanController->proceed($action, $data);
        break;
    case 'generate':
        if (isset($data)) {
            $data = json_decode(stripslashes($data));
        }
        $generateController = new ArkGenerateController();
        $generateController->proceed($action, $data);
        break;
    default:
        include("$include_path/messages/help/$lang/admin_ark.txt");
        break;
}
