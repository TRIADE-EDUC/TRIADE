<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_dashboard_controller.class.php,v 1.1 2018-11-08 16:20:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/lists_controller.class.php");
require_once($class_path."/scheduler/scheduler_dashboard.class.php");
require_once($class_path."/list/list_scheduler_dashboard_ui.class.php");

class scheduler_dashboard_controller extends lists_controller {
	
	protected static $model_class_name = 'scheduler_dashboard';
	
	protected static $list_ui_class_name = 'list_scheduler_dashboard_ui';
	
}