<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_controller.class.php,v 1.1 2021/10/18 13:32:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mail.class.php");

class mails_controller extends lists_controller {
	
	protected static $model_class_name = 'mail';
	
	protected static $list_ui_class_name = 'list_mails_ui';
	
}