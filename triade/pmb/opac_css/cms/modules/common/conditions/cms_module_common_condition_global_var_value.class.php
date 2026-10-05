<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_global_var_value.class.php,v 1.1 2013/01/18 14:35:13 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_global_var_value extends cms_module_common_condition{

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_global_var",
		);
	}
	
	public function get_form(){
		$form = parent::get_form();
		$form.= "
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("value")."'>".$this->format_text($this->msg['cms_module_common_condition_global_var_value_label'])."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='".$this->get_form_value_name("value")."' value='".$this->format_text($this->parameters['value'])."'/>
				</div>
			</div>";
		return $form;
	}
	
	public function save_form(){
		$this->parameters['value'] = $this->get_value_from_form("value");
		return parent::save_form();
	}
	
	public function check_condition(){
		$selector = $this->get_selected_selector();
		$value = $selector->get_value();
		//on regarde si on est sur la bonne page...
		if($value == $this->parameters['value']){
			return true;
		}else{
			return false;
		}
	}
}