<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_serial_form.class.php,v 1.2 2021/05/31 12:29:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_record_form.class.php');

class interface_entity_serial_form extends interface_entity_record_form {
	
	protected $biblio_level = 's';
	
	protected $hierar_level = '1';
	
	protected function get_function_name_check_perso() {
		return 'check_perso_serial_form';
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['277'];
	}
	
	protected function get_js_form_mapper() {
		return "";	
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&sub=update".(!empty($this->object_id) ? "&serial_id=".$this->object_id : "");
	}
	
	protected function get_display_hidden_fields() {
		return parent::get_display_hidden_fields()."
		<input type='hidden' name='id_form' value='".md5(microtime())."' />";
	}
	
	protected function get_display_actions() {
		global $pmb_type_audit;
		
		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
			".($pmb_type_audit && $this->object_id ? $this->get_display_audit_action() : "")."
			".$this->get_display_hidden_fields()."
		</div>";
		return $display;
	}
	
	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['serial_duplicate_bouton'];
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&sub=serial_duplicate&serial_id=".$this->object_id;
	}
}