<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_section.class.php,v 1.3 2021/03/16 16:56:13 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_section extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}
	
	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_section",
			"cms_module_common_selector_env_var",
			"cms_module_common_selector_global_var",
		    "cms_module_common_selector_section_by_cp_and_search_segment",
		    "cms_module_common_selector_section_by_cp_and_search_universe"
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