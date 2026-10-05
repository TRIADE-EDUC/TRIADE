<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_faq_type_ui.class.php,v 1.2 2021/01/14 08:49:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_faq_type_ui extends list_configuration_faq_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM faq_types';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle_type');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle_type', 'text', array('italic' => true));
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle_type' => '103',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_type;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["faq_no_type_available"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['faq_add_type'];
	}
}