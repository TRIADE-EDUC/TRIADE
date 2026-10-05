<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_form.class.php,v 1.16.8.1 2025/03/06 08:40:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/interface_form.class.php');

class interface_admin_form extends interface_form {
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=update&id=".$this->object_id;
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&action=del&id=".$this->object_id;
	}
	
	protected function get_submit_action_parameters() {
		return $this->get_url_base()."&action=update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
	}
	
	protected function get_display_label() {
		switch ($this->name) {
			case 'es_rights':
				return "<h3>".$this->label."</h3>";
			default:
				return parent::get_display_label();
		}
	}
	
	protected function get_display_parameters_actions() {
		global $action;
		
		$display = "
		<div class='left'>
			".(!empty($action) ? $this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['location' => $this->get_url_base()]) : "")."
			".$this->get_display_submit_action()."
		</div>";
		return $display;
	}
	
	public function get_display_parameters() {
		global $current_module;
		
		$display = "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action_parameters()."\" >
			".$this->get_display_label()."
			<div class='form-contenu'>
				".$this->content_form."
			</div>
			<div class='row'>
				".$this->get_display_parameters_actions()."
			</div>
		<div class='row'></div>
		</form>";
		return $display;
	}
}