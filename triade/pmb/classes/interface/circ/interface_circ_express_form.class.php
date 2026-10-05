<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_circ_express_form.class.php,v 1.1.4.2 2025/05/30 12:37:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/circ/interface_circ_form.class.php');

class interface_circ_express_form extends interface_circ_form {
	
	protected function get_cancel_action() {
	    global $id_empr, $groupID, $pmb_short_loan_management, $short_loan;
	    return "./circ.php?categ=pret&sub=&id_empr=$id_empr&groupID=$groupID".(($pmb_short_loan_management==1 && $short_loan==1)?'&short_loan=1':'');
	}
	
	protected function get_submit_action() {
	    global $id_empr, $groupID, $pmb_short_loan_management, $short_loan;
	    return "./circ.php?categ=pret&sub=pret_express&id_empr=$id_empr&groupID=$groupID".(($pmb_short_loan_management==1 && $short_loan==1)?'&short_loan=1':'');
	}
	
	protected function get_action_save_label() {
	    global $msg;
	    return $msg['pret_express_reg'];
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		
		return $msg['pret_express_err'];
	}
	
	protected function get_js_script() {
	    $js_script = "";
	    if(isset($this->field_focus) && $this->field_focus) {
	        $js_script .= "
			<script type='text/javascript'>
				if(typeof test_form == 'undefined') {
					function test_form(form) {
						if(form.".$this->field_focus.".value.replace(/^\s+|\s+$/g, '').length == 0) {
							alert('".addslashes($this->get_js_script_error_label())."');
							document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
							return false;
						}
                        if(form.pe_excb.value.replace(/^\s+|\s+$/g, '').length == 0) {
                            form.pe_excb.value = '';
                        }
						return true;
					}
				}
			</script>
			";
	    }
	    return $js_script;
	}
	
}