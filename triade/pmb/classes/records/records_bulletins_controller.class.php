<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: records_bulletins_controller.class.php,v 1.1 2022/02/15 08:32:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class records_bulletins_controller extends lists_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = 'list_records_bulletins_ui';
	
}