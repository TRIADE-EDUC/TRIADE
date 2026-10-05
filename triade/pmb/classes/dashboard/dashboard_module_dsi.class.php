<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard_module_dsi.class.php,v 1.3 2023/05/03 14:51:13 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/dashboard/dashboard_module.class.php");

class dashboard_module_dsi extends dashboard_module {
	public function __construct(){
		global $msg;
		$this->template = "template";
		$this->module = "dsi";
		$this->module_name = $msg['dsi_menu_title'];
		parent::__construct();
	}	
}