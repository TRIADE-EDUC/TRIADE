<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_analysis_form.class.php,v 1.4 2023/10/24 09:57:08 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_record_form.class.php');

class interface_entity_analysis_form extends interface_entity_record_form {
	
	protected $biblio_level = 'a';
	
	protected $hierar_level = '2';
	
	protected $serial_id;
	
	protected $bulletin_id;
	
	protected function get_function_name_check_perso() {
		return 'check_perso_analysis_form';
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['277'];
	}
	
	protected function get_js_form_mapper() {
		return "";
	}
	
	protected function get_js_script_check_fields() {
		global $msg;
		return parent::get_js_script_check_fields()."
			if(document.forms['notice'].elements['perio_type_use_existing']){
				var perio_type = document.forms['notice'].elements['perio_type_use_existing'].checked;
				var bull_type =  document.forms['notice'].elements['bull_type_use_existing'].checked;
				var perio_type_new = document.forms['notice'].elements['perio_type_new'].checked;
				var bull_type_new =  document.forms['notice'].elements['bull_type_new'].checked;
	
				if(!perio_type && bull_type) {
					alert(\"".$msg['z3950_bull_already_linked']."\")
					return false;
				}
				if(perio_type_new && (document.getElementById('f_perio_new').value == '')){
					alert(\"".$msg['z3950_serial_title_mandatory']."\")
					return false;
				}
	
				if(bull_type_new && (document.getElementById('f_bull_new_titre').value == '') && (document.getElementById('f_bull_new_mention').value == '')
				&& (document.getElementById('f_bull_new_date').value == '') && (document.getElementById('f_bull_new_num').value == '')){
					alert(\"".$msg['z3950_fill_bull']."\")
					return false;
				}
	
				if(perio_type && bull_type && (document.getElementById('bul_id').value) == '0'){
						alert(\"".$msg['z3950_no_bull_selected']."\")
						return false;
				}
			}
		";
	}
	
	protected function get_cancel_action() {
		global $current_module,$base_path;
		
		switch ($current_module) {
			case 'catalog':
				return $base_path."/catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($this->bulletin_id);
			default:
				return parent::get_cancel_action();
		}
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=update".(!empty($this->object_id) ? "&analysis_id=".$this->object_id : "");
	}
	
	protected function get_display_hidden_fields() {
		return parent::get_display_hidden_fields()."
		<input type=\"hidden\" name=\"serial_id\" id=\"serial_id\" value=\"".$this->serial_id."\">
		<input type=\"hidden\" name=\"bul_id\" id=\"bul_id\" value=\"".$this->bulletin_id."\">
		<input type=\"hidden\" name=\"id_form\" value=\"".md5(microtime())."\">";
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
		</div>
		<div class='right'>
			".($this->object_id ? $this->get_display_delete_action() : "")."
		</div>";
		return $display;
	}
	
	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['analysis_duplicate_bouton'];
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&action=analysis_duplicate&bul_id=".$this->bulletin_id."&analysis_id=".$this->object_id;
	}
	
	protected function get_action_move_label() {
		global $msg;
		return $msg['analysis_move_bouton'];
	}
	
	protected function get_move_action() {
		return $this->get_url_base()."&action=analysis_move&bul_id=".$this->bulletin_id."&analysis_id=".$this->object_id;
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&action=delete&bul_id=".$this->bulletin_id."&analysis_id=".$this->object_id;
	}
	
	public function set_serial_id($serial_id) {
		$this->serial_id = intval($serial_id);
		return $this;
	}
	
	public function set_bulletin_id($bulletin_id) {
		$this->bulletin_id = intval($bulletin_id);
		return $this;
	}
}