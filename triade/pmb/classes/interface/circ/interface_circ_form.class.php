<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_circ_form.class.php,v 1.2.8.2 2025/03/20 08:37:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_circ_form extends interface_form {
	
	protected function get_display_cancel_action() {
		global $action;
		
		switch ($this->table_name) {
			case 'empr_caddie':
				switch ($action) {
					case 'new_cart':
					case 'duplicate_cart':
					    return $this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['function' => 'history.go(-1);']);
				}
				return parent::get_display_cancel_action();
			default:
				return parent::get_display_cancel_action();
		}
	}
	
	protected function get_cancel_action() {
		switch ($this->table_name) {
			case 'groupe':
				if(!empty($this->object_id)) {
					return $this->get_url_base()."&action=showgroup&groupID=".$this->object_id;
				} else {
					return parent::get_cancel_action();
				}
			default:
				return parent::get_cancel_action();
		}
	}
	
	protected function get_submit_action() {
		switch ($this->table_name) {
			case 'groupe':
				return $this->get_url_base()."&action=update".(!empty($this->object_id) ? "&groupID=".$this->object_id : "");
			case 'empr_caddie':
				if($this->object_id) {
					return $this->get_url_base()."&action=save_cart&idemprcaddie=".$this->object_id;
				} else {
					return $this->get_url_base()."&action=valid_new_cart";
				}
			case 'empr_caddie_procs':
			    if($this->object_id) {
			        return $this->get_url_base()."&action=modif&id=".$this->object_id;
			    } else {
			        return $this->get_url_base()."&action=add";
			    }
			default:
				return parent::get_submit_action();
		}
	}
	
	protected function get_duplicate_action() {
		switch ($this->table_name) {
			case 'empr_caddie':
				return $this->get_url_base()."&action=duplicate_cart&idemprcaddie=".$this->object_id;
			default:
				return parent::get_duplicate_action();
		}
	}
	
	protected function get_delete_action() {
		switch ($this->table_name) {
			case 'groupe':
				return $this->get_url_base()."&action=delgroup&groupID=".$this->object_id;
			case 'empr_caddie':
				return $this->get_url_base()."&action=del_cart&idemprcaddie=".$this->object_id;
			case 'empr_caddie_procs':
			    return $this->get_url_base()."&action=del&id=".$this->object_id;
			default:
				return parent::get_delete_action();
		}
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		
		switch ($this->table_name) {
			case 'groupe':
				return $msg['915'];
			default:
				return parent::get_js_script_error_label();
		}
	}
	
}