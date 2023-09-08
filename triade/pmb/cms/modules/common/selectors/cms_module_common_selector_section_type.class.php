<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_section_type.class.php,v 1.1 2014-11-27 08:30:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_section_type extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form=parent::get_form();
		$form.= "
			<div class='row'>";
		$form.=$this->gen_select();
		$form.="
			</div>";
		return $form;	
		
	}
	
	protected function gen_select(){
		global $charset;
		//si on est en création de cadre
		if(!$this->id){
			$this->parameters['type_editorial'] = array();
		}
		$select = "
		<select name='".$this->get_form_value_name("section")."[]' multiple='yes'>";	
		$types = new cms_editorial_types("section");
		$types->get_types();
		for($i=0 ; $i<count($types->types) ; $i++){
			$select.= "
			<option value='".$types->types[$i]['id']."'".(in_array($types->types[$i]['id'],$this->parameters['type_editorial']) ? " selected='selected'" : "").">".htmlentities($types->types[$i]['label'],ENT_QUOTES,$charset)."</option>";
		}
		$select.= "
		</select>";
		return $select;
	}	
	
	public function save_form(){
		$this->parameters["type_editorial"] = $this->get_value_from_form("section");
		return parent::save_form();
	}
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters['type_editorial'];
		}
		return $this->value;
	}
}