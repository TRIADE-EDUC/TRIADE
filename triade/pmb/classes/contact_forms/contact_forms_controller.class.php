<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_forms_controller.class.php,v 1.3 2021/02/12 08:19:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/contact_forms/contact_form.class.php");

class contact_forms_controller extends lists_controller {
	
	protected static $model_class_name = 'contact_form';
	
	protected static $list_ui_class_name = 'list_contact_forms_ui';
	
}