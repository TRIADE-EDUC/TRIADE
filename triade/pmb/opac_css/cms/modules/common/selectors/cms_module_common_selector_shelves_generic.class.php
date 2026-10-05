<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_shelves_generic.class.php,v 1.4 2014/12/05 09:57:16 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_shelves_generic extends cms_module_common_selector {
	
	public function __construct($id=0){
		parent::__construct($id);
		if (!is_array($this->parameters)) $this->parameters = array();
		$this->once_sub_selector = true;
	}
	
	public function get_sub_selectors(){
		return array(
			"cms_module_common_selector_shelves",
			"cms_module_common_selector_type_section",
			"cms_module_common_selector_type_article",
			"cms_module_common_selector_type_article_generic",
			"cms_module_common_selector_type_section_generic"
		);
	}

	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if($this->parameters['sub_selector']){
			$sub_selector = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
			return $sub_selector->get_value();
		}else{
			return array();
		}
		
	}
}