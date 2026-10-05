<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_budget_ui.class.php,v 1.3.8.1 2025/02/21 13:24:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_budget_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM budgets';
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'num_entite' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('statut');
		$this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('num_entite', 'num_entite', 'integer');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
				'statut' => 'acquisition_statut',
		);
	}
	
	protected function _get_object_property_statut($object) {
		global $msg;
		
		switch ($object->statut) {
			case STA_BUD_VAL :
				return $msg['acquisition_statut_actif'];
			case  STA_BUD_CLO :
				return $msg['acquisition_statut_clot'];
			default:
				return $msg['acquisition_budg_pre'];
		}
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id_bibli='.$object->num_entite.'&id_bud='.$object->id_budget;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_budg'];
	}
	
	protected function get_button_add() {
	    return $this->get_button('add', $this->get_label_button_add(), "&id_bibli=".$this->filters['num_entite']);
	}
}