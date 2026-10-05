<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_value.class.php,v 1.2 2023/02/03 15:24:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_value extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_value_label'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='".$this->get_form_value_name("manual_value")."' value='".$this->addslashes($this->format_text($this->parameters))."'/>
                    <i style='cursor:pointer;' class='fa fa-info-circle' aria-hidden='true' title='".$this->format_text($this->msg['cms_module_common_selector_value_help'])."'></i>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("manual_value");
		return parent ::save_form();
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
}