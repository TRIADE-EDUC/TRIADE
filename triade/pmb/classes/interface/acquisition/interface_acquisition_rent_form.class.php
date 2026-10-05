<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_acquisition_rent_form.class.php,v 1.1.4.2 2025/03/19 11:04:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/acquisition/interface_acquisition_form.class.php');

class interface_acquisition_rent_form extends interface_acquisition_form {
	
    protected $num_entity;
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=update&id_bibli=".$this->num_entity."&id=".$this->object_id;
	}
	
	protected function get_action_delete_label() {
	    global $msg, $sub;
	    
	    switch ($this->table_name) {
	        case 'rent_accounts':
	            if ($sub == 'requests') {
	                return $msg['acquisition_request_delete'];
	            } else {
                    return $msg['acquisition_account_delete'];
	            }
	        case 'rent_invoices':
	            return $msg['acquisition_invoice_delete'];
	        default:
	            return parent::get_action_delete_label();
	    }
	}
	
	protected function get_delete_action() {
	    return $this->get_url_base()."&action=delete&id=".$this->object_id;
	}
	
	protected function get_js_script() {
	    global $rent_account_js_form_tpl;
	    
	    switch ($this->table_name) {
	        case 'rent_accounts':
	            return $rent_account_js_form_tpl;
	        case 'rent_invoices':
	            return "<script src='javascript/pricing_systems.js'></script>";
	    }
	}
	
	protected function get_display_cancel_action() {
	    return $this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['function' => 'history.go(-1);']);
	}
	
	protected function get_display_submit_action() {
        return "<input type='submit' class='bouton' name='save_button' id='save_button' value='".$this->get_action_save_label()."' onClick=\"return test_form(this.form)\" />";
	}
	
	public function set_num_entity($num_entity) {
	    $this->num_entity = intval($num_entity);
	    return $this;
	}
}