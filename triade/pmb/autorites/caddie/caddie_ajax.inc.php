<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_ajax.inc.php,v 1.3 2020/10/21 11:14:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $class_path, $object_type, $caddie, $object;

switch($action) {
    case 'list':
        require_once "$class_path/caddie/caddie_root_lists_controller.class.php";
        caddie_root_lists_controller::proceed_ajax($object_type, 'caddie_content');
        break;
    default:
        if (isset($caddie)) {
            $idcaddie = substr($caddie, strrpos($caddie, '_') + 1);
        }
        if (isset($object)) {
            $id_item = substr($object, strrpos($object, '_') + 1);
        }
        authorities_caddie_controller::proceed_ajax($idcaddie, $id_item);
        break;
}