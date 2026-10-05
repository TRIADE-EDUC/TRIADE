<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_location_ui.class.php,v 1.5 2023/03/24 07:44:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_docs_location_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idlocation,location_libelle, locdoc_owner, locdoc_codage_import, lender_libelle, location_visible_opac, css_style FROM docs_location left join lenders on locdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('location_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'location_libelle' => '103',
				'location_visible_opac' => 'opac_object_visible_short',
				'lender_libelle' => 'proprio_codage_proprio',
				'locdoc_codage_import' => 'import_codage'
				
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('location_visible_opac', 'align', 'center');
		$this->set_setting_column('location_visible_opac', 'datatype', 'boolean');
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'location_libelle':
				if ($object->locdoc_owner) {
					return array(
							'style' => 'font-style:italic;'
					);
				} else {
					return array(
							'style' => 'font-weight:bold;'
					);
				}
		}
		return parent::get_default_attributes_format_cell($object, $property);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idlocation;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['106'];
	}
}