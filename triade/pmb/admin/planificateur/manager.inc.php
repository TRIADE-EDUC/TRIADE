<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: manager.inc.php,v 1.12 2023/03/28 13:02:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $act, $action, $id;

require_once($class_path."/scheduler/scheduler_manager_controller.class.php");

if(empty($action) && !empty($act)) {
	$action = $act;
}
scheduler_manager_controller::proceed($id);