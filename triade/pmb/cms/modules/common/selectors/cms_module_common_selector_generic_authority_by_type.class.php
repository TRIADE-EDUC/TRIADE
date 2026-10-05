<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_authority_by_type.class.php,v 1.1 2016/04/15 10:29:21 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_authority_by_type extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}
	
	protected function get_sub_selectors(){
		return array(
// 			"cms_module_common_selector_generic_authority_author",
// 			"cms_module_common_selector_generic_authority_category",
// 			"cms_module_common_selector_generic_authority_publisher",
// 			"cms_module_common_selector_generic_authority_collection",
// 			"cms_module_common_selector_generic_authority_subcollection",
// 			"cms_module_common_selector_generic_authority_serie",
			"cms_module_common_selector_generic_authority_uniform_title",
// 			"cms_module_common_selector_generic_authority_indexint",
			"cms_module_common_selector_generic_authority_concept",
// 			"cms_module_common_selector_generic_authority_authperso"
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