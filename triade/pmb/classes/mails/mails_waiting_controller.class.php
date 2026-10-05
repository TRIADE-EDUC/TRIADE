<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_waiting_controller.class.php,v 1.1 2021/10/18 13:32:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mails/mails_controller.class.php");
require_once($class_path."/mail_waiting.class.php");

class mails_waiting_controller extends mails_controller {
	
	protected static $model_class_name = 'mail_waiting';
	
	protected static $list_ui_class_name = 'list_mails_waiting_ui';
	
}