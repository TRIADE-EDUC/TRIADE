<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_publisher_form.class.php,v 1.1 2021/05/12 14:08:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_authority_form.class.php');

class interface_entity_publisher_form extends interface_entity_authority_form {
	
	protected function get_function_name_check_perso() {
		return 'check_perso_publisher_form';
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['144'];
	}
	
	protected function get_statuses_selector() {
		return authorities_statuts::get_form_for(AUT_TABLE_PUBLISHERS, $this->num_statut);
	}
	
	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, AUDIT_PUBLISHER);
	}
}