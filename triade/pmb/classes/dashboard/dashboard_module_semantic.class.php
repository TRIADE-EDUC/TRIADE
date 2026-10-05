<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard_module_semantic.class.php,v 1.1 2022/10/28 15:45:05 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/dashboard/dashboard_module.class.php");

class dashboard_module_semantic extends dashboard_module {

	
	public function __construct(){
		global $msg,$base_path;
		$this->template = "template";
		$this->module = "semantic";
		$this->module_name = $msg['semantic_menu_title'];
		$this->alert_url = $base_path."/ajax.php?module=ajax&categ=alert&current_alert=".$this->module;
		parent::__construct();
	}
}