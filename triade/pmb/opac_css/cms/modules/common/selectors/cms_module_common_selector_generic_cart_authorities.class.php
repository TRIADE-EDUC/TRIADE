<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_cart_authorities.class.php,v 1.2 2019/09/03 15:33:13 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_cart_authorities extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}
	
	public function get_sub_selectors(){
	    return [
	        "cms_module_common_selector_global_var",
	        "cms_module_common_selector_env_var",
	        "cms_module_common_selector_type_section",
	        "cms_module_common_selector_type_article",
	        "cms_module_common_selector_type_article_generic",
	        "cms_module_common_selector_type_section_generic",
	        "cms_module_common_selector_record_cp_val",
	    ];
	}
	
	public function get_value(){
	    if(!$this->value){
	        $sub = $this->get_selected_sub_selector();
	        $this->value = $sub->get_value();
	    }
	    return $this->value;
	}
}