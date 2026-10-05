<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_form.class.php,v 1.2.10.2 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_entity_form extends interface_form {

	protected $controller;

	protected $document_title;

	public function set_controller($controller) {
		$this->controller = $controller;
		return $this;
	}

	public function get_document_title() {
		return $this->document_title;
	}

	public function set_document_title($document_title) {
		$this->document_title = $document_title;
		return $this;
	}

	protected function get_cancel_action() {
		if(isset($this->controller) && is_object($this->controller)) {
			return 	$this->controller->get_back_url();
		} else {
			return $this->get_url_base();
		}
	}

	protected function get_display_submit_action() {
		return "<input type='button' value='".$this->get_action_save_label()."' class='bouton' id='btsubmit' onClick=\"document.getElementById('save_and_continue').value=0;if (test_form(this.form)) this.form.submit();\" />";
	}

	protected function get_display_replace_action() {
		return "<input type='button' value='".$this->get_action_replace_label()."' class='bouton' id='btreplace' onClick=\"unload_off();document.location='".$this->get_replace_action()."';\" />";
	}

	protected function get_display_duplicate_action() {
		global $charset;

		return "<input type='button' class='bouton' name='duplicate_button' id='duplicate_button' value='".htmlentities($this->get_action_duplicate_label(), ENT_QUOTES, $charset)."' onclick=\"unload_off();document.location='".$this->get_duplicate_action()."';\" />";
	}

	protected function get_display_move_action() {
		return $this->get_display_action('move_button', $this->get_action_move_label(), ['location' => $this->get_move_action()]);
	}

	protected function get_display_audit_action() {
		return '';
	}

	protected function get_delete_action() {
		if(isset($this->controller) && is_object($this->controller)) {
			return 	$this->controller->get_delete_url();
		} else {
			return $this->get_url_base()."&sub=delete&id=".$this->object_id;
		}
	}

	protected function get_display_delete_action() {
		return $this->get_display_action('delete_button', $this->get_action_delete_label(), ['function' => 'confirm_delete();']);
	}

	public function get_url_base() {
		if(isset($this->controller) && is_object($this->controller)) {
			return 	$this->controller->get_url_base();
		} else {
			return parent::get_url_base();
		}
	}

	protected function get_action_move_label() {
		return '';
	}

	protected function get_replace_action() {
		return '';
	}

	protected function get_move_action() {
		return '';
	}
}