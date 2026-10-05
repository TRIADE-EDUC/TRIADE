<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_record_doctype_from.class.php,v 1.1 2016/05/17 10:25:19 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_record_doctype_from extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form=parent::get_form();
		$form.= "
			<div class='row'>
				".$this->format_text($this->msg['cms_module_common_selector_record_doctype_from'])."
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
		return $this->value;
	}
}