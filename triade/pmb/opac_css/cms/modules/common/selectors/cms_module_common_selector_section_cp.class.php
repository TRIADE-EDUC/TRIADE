<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_section_cp.class.php,v 1.1 2019/05/20 10:28:43 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_section_cp extends cms_module_common_selector {	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = false;
	}

	protected function get_sub_selectors(){
		return array(
		    'cms_module_common_selector_generic_section',
		    'cms_module_common_selector_type_section_filter',
		);
	}
	
	public function get_value()
	{
	    $selector_section = new cms_module_common_selector_generic_section($this->get_sub_selector_id("cms_module_common_selector_generic_section"));
	    $cp = new cms_module_common_selector_type_section_filter($this->get_sub_selector_id("cms_module_common_selector_type_section_filter"));
	    $parent = $selector_section->get_value();
	    $field = $cp->get_value();
	    $fields = new cms_editorial_parametres_perso($field['type']);
	    $fields->get_values($parent);
		if(!empty($fields->values[$field['field']])){
		    $this->value = $fields->values[$field['field']];
		}
	    return $this->value;
	}
	
	
}