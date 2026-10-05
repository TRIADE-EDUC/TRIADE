<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reservations_controller.class.php,v 1.1 2021/10/21 12:03:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class reservations_controller extends lists_controller {
	
	protected static $model_class_name = 'resa';
	
	protected static $list_ui_class_name = 'list_reservations_ui';
	
	
}