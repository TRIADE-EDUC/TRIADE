<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_empr_codstat_ui.class.php,v 1.3 2021/01/12 07:23:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_empr_codstat_ui extends list_configuration_empr_ui {
	
	protected function _get_query_base() {
		return 'SELECT idcode, libelle FROM empr_codestat';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
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