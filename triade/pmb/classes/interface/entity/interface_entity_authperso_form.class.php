<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_authperso_form.class.php,v 1.2 2021/06/08 08:22:20 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_authority_form.class.php');

class interface_entity_authperso_form extends interface_entity_authority_form {
	
	protected $id_authperso;
	
	protected function get_function_name_check_perso() {
	    return 'check_perso_authperso_form';
	}
	
	protected function get_statuses_selector() {
		return authorities_statuts::get_form_for(1000+$this->id_authperso, $this->num_statut);
	}
	
	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, ($this->id_authperso + 1000));
	}
	
	public function set_id_authperso($id_authperso) {
		$this->id_authperso = $id_authperso;
		return $this;
	}
	
}