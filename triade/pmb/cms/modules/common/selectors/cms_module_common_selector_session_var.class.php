<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_session_var.class.php,v 1.1 2020/02/21 14:36:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_session_var extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_session_var'>".$this->format_text($this->msg['cms_module_common_selector_session_var_label'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='".$this->get_form_value_name("session_var")."' value='".$this->addslashes($this->format_text($this->parameters))."'/>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("session_var");
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$var = $this->parameters;
			$this->value = $_SESSION[$var];
		}
		return $this->value;
	}
}