<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_lgstat_ui.class.php,v 1.3 2023/03/24 09:26:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_lgstat_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM lignes_actes_statuts';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
		$this->set_setting_column('relance', 'text', array('italic' => true));
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
				'relance' => 'acquisition_lgstat_arelancer',
		);
	}
	
	protected function _get_object_property_relance($object) {
		global $msg;
		
		if($object->relance == 1) {
			return $msg[40];
		} else {
			return $msg[39];
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'libelle':
				if($object->id_statut == 1) {
					return array(
							'style' => 'font-weight:bold;'
					);
				}
		}
		return array();
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_statut;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_lgstat_add'];
	}
}