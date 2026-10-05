<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_bannettes_classement_from.class.php,v 1.2 2021/02/12 15:50:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_bannettes_classement_from extends cms_module_common_selector {
	
	public function get_form(){
		$form=parent::get_form();
		$form.= "<div class='row'>
    				<label for='cms_module_common_selector_bannettes_classement_from'>
                        ".$this->format_text($this->msg['cms_module_common_selector_bannettes_classement_from'])."
                    </label>
    			</div>";
		return $form;	
	}
	
	public function save_form(){
		$this->parameters = array();
		return parent::save_form();
	} 
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = "";
		}
		return $this->value;
	}
}