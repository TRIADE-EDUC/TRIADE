<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_empr_codstat_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/empr/list_configuration_empr_ui.class.php");

class list_configuration_empr_codstat_ui extends list_configuration_empr_ui {
	
	protected function _get_query_base() {
		return 'SELECT idcode, libelle FROM empr_codestat';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idcode;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['99'];
	}
}