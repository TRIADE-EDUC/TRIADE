<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_query_proc_admin_ui.class.php,v 1.1.2.2 2024/09/13 09:24:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_query_proc_admin_ui extends list_query_proc_ui {
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'visible', false);
	    $this->set_setting_display('query', 'human', false);
	    $this->set_setting_display('pager', 'visible', false);
	}

	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	public static function get_controller_url_base() {
	    global $force_exec;
	    
	    return parent::get_controller_url_base()."&action=final&id=".static::$id_proc."&force_exec=".$force_exec;
	}
	
	public static function get_ajax_controller_url_base() {
	    global $force_exec;
	    
	    return parent::get_ajax_controller_url_base()."&id=".static::$id_proc."&force_exec=".$force_exec;
	}
}