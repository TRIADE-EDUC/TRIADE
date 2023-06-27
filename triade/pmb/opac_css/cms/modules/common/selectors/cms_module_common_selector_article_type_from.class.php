<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_article_type_from.class.php,v 1.1 2012-12-17 10:35:13 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_article_type_from extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form=parent::get_form();
		$form.= "
			<div class='row'>
				".$this->format_text($this->msg['cms_module_common_selector_article_type_from'])."
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
// 			$fields = new cms_editorial_parametres_perso($this->parameters["type_editorial"]);
// 			$sub = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
// 			$fields->get_values($sub->get_value());
// 			$this->value = $fields->values[$this->parameters['type_editorial_field']][0];
		}
		return $this->value;
	}
}