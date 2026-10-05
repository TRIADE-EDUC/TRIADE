<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authorities_by_type.class.php,v 1.2 2021/03/16 09:11:54 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authorities_by_type extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}
	
	protected function get_sub_selectors(){
		return array(
 			"cms_module_common_selector_generic_authorities_authors",
// 			"cms_module_common_selector_generic_authorities_categories",
// 			"cms_module_common_selector_generic_authorities_publishers",
// 			"cms_module_common_selector_generic_authorities_collections",
// 			"cms_module_common_selector_generic_authorities_subcollections",
// 			"cms_module_common_selector_generic_authorities_series",
			"cms_module_common_selector_generic_authorities_uniform_titles",
// 			"cms_module_common_selector_generic_authorities_indexint",
			"cms_module_common_selector_generic_authorities_concepts",
// 			"cms_module_common_selector_generic_authorities_authpersos"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$sub = $this->get_selected_sub_selector();
			$this->value = $sub->get_value();
		}
		return $this->value;
	}
}