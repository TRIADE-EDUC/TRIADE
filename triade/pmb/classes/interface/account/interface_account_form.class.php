<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_account_form.class.php,v 1.6 2023/06/28 14:40:56 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_account_form extends interface_form {
	
	protected function get_action_delete_label() {
		global $msg;
		switch ($this->table_name) {
			case 'lists':
			case 'modules':
			case 'selectors':
			case 'tabs':
			case 'forms':
				return $msg['initialize'];
			default:
				return parent::get_action_delete_label();
		}
	}
	
	protected function get_display_cancel_action() {
		switch ($this->table_name) {
			case 'mails_configuration':
			case 'users':
				return '';
			default:
				return parent::get_display_cancel_action();
		}
	}

	protected function get_display_delete_action() {
		switch ($this->name) {
			case 'mfa_form':             
				return '';
			default:
				return parent::get_display_delete_action();
		}
	}

	protected function get_display_submit_action() {
		global $action;
		switch ($this->name) {
			case 'mfa_form':
				if($action == 'initialization' || $action == 'initialized') {
					return parent::get_display_submit_action();
				}
				return '';
			default:
				return parent::get_display_submit_action();
		}
	}

	protected function get_action_save_label() {
		global $msg, $action;
		switch ($this->name) {
			case 'mfa_form':
				if($action == 'initialization') {
					return $msg['mfa_validate_button'];
				}
			default:
				return parent::get_action_save_label();
		}
	}

	protected function get_submit_action() {
		global $action;

		switch ($this->name) {
			case 'mfa_form':
				if($action == 'initialization') {
					return $this->get_url_base() . "&action=validate_mfa";
				}

				if($action == 'initialized') {
					return $this->get_url_base() . "&action=save_mfa";
				}
				
				return parent::get_submit_action();
			default:
				return parent::get_submit_action();
		}
	}

	protected function get_js_script() {
		global $msg;

		switch ($this->name) {
			case 'mfa_form':
				if(isset($this->field_focus) && $this->field_focus) {
					return "
						<script type='text/javascript'>
							if(typeof test_form == 'undefined') {
								function test_form(form) {
									if(form.mfa_confirm_code.value == '') {
										alert('" . $msg['mfa_invalid_code'] . "');
										document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
										return false;
									}

									let req = new http_request();
									req.request('./ajax.php?module=account&categ=authentication', 1, '&action=check_initialization&secret_code=' + form.mfa_secret_code_hidden.value + '&code=' + form.mfa_confirm_code.value);
									if(req.get_text() == 0) {
										alert('" . $msg['mfa_invalid_code'] . "');
										return false;
									}

									return true;
								}
							}
						</script>
					";
				}
			default:
				return parent::get_js_script();
		}
	}
}