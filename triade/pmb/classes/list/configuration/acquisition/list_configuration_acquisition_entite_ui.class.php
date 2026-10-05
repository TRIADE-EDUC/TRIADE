<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_entite_ui.class.php,v 1.2 2022/10/06 11:57:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_entite_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM entites';
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'type_entite' => 1,
				'num_user' => 0
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('raison_sociale');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('raison_sociale', 'text', array('italic' => true));
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('type_entite', 'type_entite');
		if($this->filters['num_user']) {
			$this->query_filters [] = 'autorisations like("% '.$this->filters['num_user'].' %")';
		}
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'raison_sociale' => 'acquisition_raison_soc',
		);
	}
	
	public function get_display_header_list() {
		return '';	
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_entite;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_biblio'];
	}
}