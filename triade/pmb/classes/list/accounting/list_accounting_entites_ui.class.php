<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_entites_ui.class.php,v 1.4 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_accounting_entites_ui extends list_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_entite as id, entites.* FROM entites';
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['acquisition_menu_chx_ent'], ENT_QUOTES, $charset);
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'type_entite' => 1,
				'num_user' => SESSuserid
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('raison_sociale');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('raison_sociale', 'text', array('italic' => true));
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'raison_sociale' => 'acquisition_raison_soc',
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('raison_sociale');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('type_entite', 'type_entite');
		if($this->filters['num_user']) {
			$this->query_filters [] = 'autorisations like("% '.$this->filters['num_user'].' %")';
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=list&id_bibli=".$object->id_entite."\"";
		return $attributes;
	}
	
	public function get_display_header_list() {
		return '';
	}
}