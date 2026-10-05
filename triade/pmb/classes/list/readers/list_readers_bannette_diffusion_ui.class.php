<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_bannette_diffusion_ui.class.php,v 1.1 2023/03/07 14:51:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/bannette.class.php");

class list_readers_bannette_diffusion_ui extends list_readers_bannette_ui {

	protected $bannette_diffusion;
	
	protected function get_title() {
		global $msg, $charset;
		
		return "<h2>".htmlentities($msg['bannette_diffusion_recipients'], ENT_QUOTES, $charset)."</h2>";
	}
	
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['diffusion_state'] = 'bannette_diffusion_state';
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_default_columns() {
		$this->add_column('empr_name');
		$this->add_column('mail');
		$this->add_column('diffusion_state');
	}
	
	protected function init_columns($columns=array()) {
		list_ui::init_columns($columns);
	}
	
	protected function _get_object_property_diffusion_state($object) {
		return $this->get_bannette_diffusion()->get_diffusion_state($object->get_id());
	}
	
	public function get_error_message_empty_list() {
		global $msg;
		return $msg['dsi_lect_aucun_trouve'];
	}
	
	public function get_bannette_diffusion() {
		if(!isset($this->bannette_diffusion)) {
			$this->bannette_diffusion = new bannette_diffusion($this->filters['id_diffusion']);
		}
		return $this->bannette_diffusion;
	}
}