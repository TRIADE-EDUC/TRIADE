<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_codstat_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_ui.class.php");

class list_configuration_docs_codstat_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idcode, codestat_libelle, statisdoc_codage_import, statisdoc_owner, lender_libelle FROM docs_codestat left join lenders on statisdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'codestat_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'codestat_libelle' => '103',
				'lender_libelle' => 'proprio_codage_proprio',
				'statisdoc_codage_import' => 'import_codage'
				
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'codestat_libelle':
				if ($object->statisdoc_owner) {
					$content .= "<i>".$object->codestat_libelle."</i>";
				} else {
					$content .= "<strong>".$object->codestat_libelle."</strong>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idcode;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['99'];
	}
}