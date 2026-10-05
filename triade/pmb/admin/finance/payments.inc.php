<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: payments.inc.php,v 1.2 2024/01/03 11:24:14 gneveu Exp $

use Pmb\Payments\Controller\OrganizationController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $include_path, $action, $msg, $data, $id;

$organizationController = new OrganizationController();

switch ($action) {
    case 'add':
        $organizationController->proceed($action);
        break;
    case 'edit':
        $organizationController->setData($id);
        $organizationController->proceed($action);
        break;
    case 'save':
        $data = json_decode(stripslashes($data));
        $organizationController->setData($data);
        $organizationController->proceed($action, $data);
        break;
    case 'delete':
        $organizationController->proceed($action);
        break;
    default:
        $organizationController->proceed();
        break;
}
