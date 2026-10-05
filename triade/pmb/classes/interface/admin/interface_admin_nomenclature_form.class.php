<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_nomenclature_form.class.php,v 1.2 2021/01/28 08:04:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_nomenclature_form extends interface_admin_form {
	
	protected $object_type;
	
	protected function get_submit_action() {
		global $num_family, $num_formation;
		
		$submit_action = $this->get_url_base()."&action=save&id=".$this->object_id;
		switch ($this->object_type) {
			case 'family_musicstand':
				return $submit_action."&num_family=".$num_family;
			case 'formation_type':
				return $submit_action."&num_formation=".$num_formation;
			default:
				return $submit_action;
		}
	}
	
	protected function get_display_label() {
		return "<h3>".$this->label."</h3>";
	}
	
	protected function get_action_cancel_label() {
		global $msg;
		return $msg['admin_nomenclature_'.$this->object_type.'_form_exit'];
	}
	
	protected function get_action_save_label() {
		global $msg;
		return $msg['admin_nomenclature_'.$this->object_type.'_form_save'];
	}
	
	protected function get_action_delete_label() {
		global $msg;
		return $msg['admin_nomenclature_'.$this->object_type.'_form_del'];
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['admin_nomenclature_'.$this->object_type.'_form_name_error'];
	}
	
	protected function get_js_script() {
		if(isset($this->field_focus) && $this->field_focus) {
			return "
			<script type='text/javascript'>
				if(typeof test_form == 'undefined') {
					function test_form(form) {
						if(form.name.value.length == 0) {
							alert('".addslashes($this->get_js_script_error_label())."');
							document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
							return false;
						}
						return true;
					}
				}
				</script>
			";
		}
		return "";
	}
	
	public function set_object_type($object_type) {
		$this->object_type = $object_type;
		return $this;
	}
}