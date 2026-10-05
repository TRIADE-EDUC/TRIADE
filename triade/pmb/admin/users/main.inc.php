<?php
use Pmb\Users\Controller\RolesController;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13.2.2 2024/11/20 08:06:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $admin_user_javascript, $id;
global $action, $data;

switch ($sub) {
	case 'groups':
		require_once("./admin/users/users_groups.inc.php");
		break;
	case 'roles':
	    switch ($action) {
	        case 'add':
	        case 'edit':
	            if (isset($data)) {
	                $data = json_decode(stripslashes($data));
	            }
	            if (empty($data)) {
	                $data = new stdClass();
	            }
	            if (isset($id)) {
	                $data->id = intval($id);
	            }
	            $controller = new RolesController($data);
	            $controller->proceed($action);
	            break;
	        default:
	            configuration_controller::set_list_ui_class_name('list_configuration_users_roles_ui');
	            configuration_controller::proceed($id);
	            break;
	    }
	    break;
	case 'users' :
	default:
		require_once($class_path.'/users/users_controller.class.php');
		print $admin_user_javascript;
		users_controller::proceed($id);
		break;
}
