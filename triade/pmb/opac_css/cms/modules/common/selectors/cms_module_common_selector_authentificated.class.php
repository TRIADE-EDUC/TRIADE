<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authentificated.class.php,v 1.4 2016/07/15 10:09:59 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_authentificated extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_authentificated_authentificated'>".$this->format_text($this->msg['cms_module_common_selector_authentificated_authentificated'])."</label>
				</div>
				<div class='colonne-suite'>
					<span>".$this->format_text($this->msg['cms_module_common_selector_authentificated_yes'])."&nbsp;<input type='radio' ".($this->parameters ? "checked='checked'" : "")." name='".$this->get_form_value_name("authentificated")."' value='1'/></span>&nbsp;
					<span>".$this->format_text($this->msg['cms_module_common_selector_authentificated_no'])."&nbsp;<input type='radio' ". ($this->parameters ? "" : "checked='checked'")." name='".$this->get_form_value_name("authentificated")."' value='0'/></span>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = false;
		if($this->get_value_from_form("authentificated") == 1){
			$this->parameters = true;
		}
		return parent::save_form();
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
	
	public function get_human_description_selector(){
		if($this->parameters){
			$description = $this->format_text($this->msg['cms_module_common_selector_authentificated_yes']);
		}else{
			$description = $this->format_text($this->msg['cms_module_common_selector_authentificated_no']);
		}		
		return $description;
	}
}