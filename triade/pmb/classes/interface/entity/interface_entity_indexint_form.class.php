<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_indexint_form.class.php,v 1.2 2023/05/05 12:34:46 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_authority_form.class.php');

class interface_entity_indexint_form extends interface_entity_authority_form {
	
	protected $id_pclass;
	
	protected $exact;
	
	protected function get_function_name_check_perso() {
		return 'check_perso_indexint_form';
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['indexint_name_oblig'];
	}
	
	protected function get_statuses_selector() {
		return authorities_statuts::get_form_for(AUT_TABLE_INDEXINT, $this->num_statut);
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&sub=update&id_pclass=".$this->id_pclass.(!empty($this->object_id) ? "&id=".$this->object_id : "");
	}
	
	protected function get_replace_action() {
		return $this->get_url_base()."&sub=replace&id_pclass=".$this->id_pclass."&id=".$this->object_id;
	}
	
	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, AUDIT_INDEXINT);
	}
	
	protected function get_display_hidden_fields() {
		global $charset;
		
		return parent::get_display_hidden_fields()."
		<input type='hidden' name='exact' value=\"".htmlentities($this->exact ?? "", ENT_QUOTES, $charset)."\" />";
	}
	
	public function set_id_pclass($id_pclass) {
		$this->id_pclass = $id_pclass;
		return $this;
	}
	
	public function set_exact($exact) {
		$this->exact = $exact;
		return $this;
	}
}