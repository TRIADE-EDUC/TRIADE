<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_location_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_ui.class.php");

class list_configuration_docs_location_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idlocation,location_libelle, locdoc_owner, locdoc_codage_import, lender_libelle, location_visible_opac, css_style FROM docs_location left join lenders on locdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'location_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'location_libelle' => '103',
				'location_visible_opac' => 'opac_object_visible_short',
				'lender_libelle' => 'proprio_codage_proprio',
				'locdoc_codage_import' => 'import_codage'
				
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'location_libelle':
				if ($object->locdoc_owner) {
					$content .= "<i>".$object->location_libelle."</i>";
				} else {
					$content .= "<strong>".$object->location_libelle."</strong>";
				}
				break;
			case 'location_visible_opac':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idlocation;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['106'];
	}
}