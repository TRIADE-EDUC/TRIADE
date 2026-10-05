<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_requests_controller.class.php,v 1.1 2021/04/10 13:50:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/rent/rent_controller.class.php");

class rent_requests_controller extends rent_controller {
	
    protected static $model_class_name = 'rent_request';
    
	protected static $list_ui_class_name = 'list_rent_requests_ui';
	
	
}