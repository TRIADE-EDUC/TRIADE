<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard_module_edit.class.php,v 1.2 2021/04/23 11:48:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/dashboard/dashboard_module.class.php");

class dashboard_module_edit extends dashboard_module {

	
	public function __construct(){
		global $msg;
		$this->template = "template";
		$this->module = "edit";
		$this->module_name = $msg['edit_menu_title'];
		parent::__construct();
	}	
}