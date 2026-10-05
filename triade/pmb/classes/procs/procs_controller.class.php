<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs_controller.class.php,v 1.1 2021/05/11 07:39:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class procs_controller extends lists_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = 'list_procs_ui';
	
}