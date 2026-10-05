<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_authorities_authperso_form.class.php,v 1.1 2022/05/12 12:40:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_authorities_authperso_form extends interface_admin_form {
	
	protected function get_action_cancel_label() {
		global $msg;
		return $msg['admin_authperso_exit'];
	}
	
	protected function get_action_save_label() {
		global $msg;
		return $msg['admin_authperso_save'];
	}
	
	protected function get_action_delete_label() {
		global $msg;
		
		return $msg['admin_authperso_delete'];
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&auth_action=save&id_authperso=".$this->object_id;
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&auth_action=delete&id_authperso=".$this->object_id;
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		
		return $msg["admin_authperso_name_error"];
	}
}