<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_bulletinage_form.class.php,v 1.4 2023/10/24 09:57:08 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_record_form.class.php');

class interface_entity_bulletinage_form extends interface_entity_record_form {
	
	protected $biblio_level = 'b';
	
	protected $hierar_level = '2';
	
	protected $serial_id;
	
	protected function get_function_name_check_perso() {
		return 'check_perso_bull_form';
	}
	
	protected function get_js_script_check_fields() {
		global $msg;
		return "
			test1 = form.bul_no.value+form.bul_date.value+form.bul_titre.value;// concaténation des valeurs à tester
			test = test1.replace(/^\s+|\s+$/g, ''); //trim de la valeur
			if(test.length == 0) {
				alert(\"$msg[serial_BulletinDate]\");
				form.bul_no.focus();
				return false;
			}
		";
	}
	
	protected function get_js_form_mapper() {
		return "";
	}
	
	protected function get_cancel_action() {
		return $this->get_url_base()."&action=view&bul_id=" . intval($this->object_id);
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=update".(!empty($this->object_id) ? "&bul_id=".$this->object_id : "");
	}
	
	protected function get_display_hidden_fields() {
		return parent::get_display_hidden_fields()."
			<input type='hidden' name='serial_id' value='".$this->serial_id."' />";
	}
	
	protected function get_display_actions() {
		global $pmb_type_audit;
		
		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
			".($this->object_id ? $this->get_display_move_action() : "")."
			".($pmb_type_audit && $this->object_id ? $this->get_display_audit_action() : "")."
			".$this->get_display_hidden_fields()."
		</div>";
		return $display;
	}
	
	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['bulletin_duplicate_bouton'];
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&action=bul_duplicate&bul_id=".$this->object_id;
	}
	
	protected function get_action_move_label() {
		global $msg;
		return $msg['bulletin_move_bouton'];
	}
	
	protected function get_move_action() {
		return $this->get_url_base()."&action=bul_move&bul_id=".$this->object_id;
	}
	
	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, 3);
	}

	public function set_serial_id($serial_id) {
		$this->serial_id = intval($serial_id);
		return $this;
	}
}