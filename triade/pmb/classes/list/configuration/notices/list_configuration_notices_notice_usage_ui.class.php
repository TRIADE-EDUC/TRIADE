<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_notice_usage_ui.class.php,v 1.3 2021/01/20 07:47:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_notices_notice_usage_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM notice_usage';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('usage_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'usage_libelle' => 'notice_usage_libelle',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_usage;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['notice_usage_ajout'];
	}
}