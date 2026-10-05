<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_compta_ui.class.php,v 1.3.8.1 2025/02/21 13:24:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_compta_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM exercices';
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'num_entite' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date_debut', 'desc');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
		$this->set_setting_column('date_debut', 'text', array('italic' => true));
		$this->set_setting_column('date_fin', 'text', array('italic' => true));
		$this->set_setting_column('date_debut', 'datatype', 'date');
		$this->set_setting_column('date_fin', 'datatype', 'date');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('num_entite', 'num_entite', 'integer');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
				'date_debut' => 'calendrier_date_debut',
				'date_fin' => 'calendrier_date_fin',
				'statut' => 'acquisition_statut',
		);
	}
	
	protected function _get_object_property_statut($object) {
		global $msg;
		
		switch ($object->statut) {
			case STA_EXE_CLO :
				return $msg['acquisition_statut_clot'];
			case  STA_EXE_DEF :
				return $msg['acquisition_statut_def'];
			default:
				return $msg['acquisition_statut_actif'];
		}
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&ent='.$object->num_entite.'&id='.$object->id_exercice;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_exer'];
	}
	
	protected function get_button_add() {
	    return $this->get_button('add', $this->get_label_button_add(), "&ent=".$this->filters['num_entite']);
	}
}