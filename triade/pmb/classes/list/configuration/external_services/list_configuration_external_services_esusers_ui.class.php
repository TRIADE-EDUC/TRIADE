<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_external_services_esusers_ui.class.php,v 1.1 2021/01/15 10:25:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services_esusers.class.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_ui.class.php");

class list_configuration_external_services_esusers_ui extends list_configuration_external_services_ui {
	
	protected function _get_query_base() {
		return 'SELECT esuser_id FROM es_esusers';
	}
	
	protected function get_object_instance($row) {
		return new es_esuser($row->esuser_id);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('es_user_username');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'esuser_username' => 'es_user_username',
				'esuser_fullname' => 'es_user_fullname',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->esuser_id;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["es_users_noesusers"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['es_users_add'];
	}
}