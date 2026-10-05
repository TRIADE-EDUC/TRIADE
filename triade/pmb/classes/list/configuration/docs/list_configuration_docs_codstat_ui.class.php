<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_codstat_ui.class.php,v 1.4 2023/03/24 07:44:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_docs_codstat_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idcode, codestat_libelle, statisdoc_codage_import, statisdoc_owner, lender_libelle FROM docs_codestat left join lenders on statisdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('codestat_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'codestat_libelle' => '103',
				'lender_libelle' => 'proprio_codage_proprio',
				'statisdoc_codage_import' => 'import_codage'
				
		);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'codestat_libelle':
				if ($object->statisdoc_owner) {
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
		return static::get_controller_url_base().'&action=modif&id='.$object->idcode;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['99'];
	}
}