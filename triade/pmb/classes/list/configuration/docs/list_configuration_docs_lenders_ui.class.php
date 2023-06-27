<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_lenders_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_ui.class.php");

class list_configuration_docs_lenders_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idlender,lender_libelle FROM lenders';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'lender_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'lender_libelle' => '558',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idlender;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['555'];
	}
}