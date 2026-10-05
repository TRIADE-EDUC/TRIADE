<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_author_form.class.php,v 1.1 2021/05/12 14:08:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_authority_form.class.php');

class interface_entity_author_form extends interface_entity_authority_form {
	
	protected $author_type;
	
	protected function get_function_name_check_perso() {
		return 'check_perso_author_form';
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['213'];
	}
	
	protected function get_js_script_check_fields() {
		return parent::get_js_script_check_fields()."
			if(form.voir_libelle.value.length == 0) {
				form.voir_id.value='';
			}
		";
	}
	
	protected function get_statuses_selector() {
		return authorities_statuts::get_form_for(AUT_TABLE_AUTHORS, $this->num_statut);
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&sub=duplicate&type_autorite=".$this->author_type."&id=".$this->object_id;
	}
	
	protected function get_display_duplicate_action() {
		switch ($this->author_type) {
			case 71:
			case 72:
				return parent::get_display_duplicate_action();
			default:
				return '';
		}
	}
	
	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, AUDIT_AUTHOR);
	}
	
	public function set_author_type($author_type) {
		$this->author_type = $author_type;
		return $this;
	}
	
}