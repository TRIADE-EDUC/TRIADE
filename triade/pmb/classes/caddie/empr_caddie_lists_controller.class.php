<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie_lists_controller.class.php,v 1.1 2021/10/11 13:55:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/caddie/caddie_root_lists_controller.class.php");

class empr_caddie_lists_controller extends caddie_root_lists_controller {
	
	protected static $model_class_name = 'empr_caddie';

	protected static $list_ui_class_name = 'list_empr_caddie_content_ui';
	
}