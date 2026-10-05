<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_ui.class.php,v 1.1 2022/01/13 10:54:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc.class.php");

class list_serialcirc_ui extends list_ui {
	
	protected function get_link_action($action) {
		return array(
				'href' => static::get_controller_url_base()."&action=".$action
		);
	}
	
	protected function init_default_selection_actions() {
		parent::init_default_selection_actions();
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
}