<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_import_form.class.php,v 1.1.2.2.2.1 2025/03/06 08:40:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_import_form extends interface_form {
	
	protected function get_display_cancel_action() {
		return $this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['function' => 'history.go(-1);']);
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=import";
	}
	
	protected function get_display_submit_action() {
		if(isset($this->field_focus) && $this->field_focus) {
			return "<input type='submit' class='bouton' name='import_button' id='import_button' value='".$this->get_action_import_label()."' onClick=\"return test_form(this.form)\" />";
		} else {
		    return "<input type='submit' class='bouton' name='import_button' id='import_button' value='".$this->get_action_import_label()."' />";
		}
	}
	
	protected function get_display_actions() {
		$display = "
		<div class='row'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
		</div>";
		return $display;
	}
}