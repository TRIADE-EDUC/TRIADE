<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_article.class.php,v 1.4.8.1 2024/12/18 16:01:30 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_generic_article extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}

	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_article",
			"cms_module_common_selector_env_var",
		    "cms_module_common_selector_global_var",
		    "cms_module_common_selector_article_by_section_and_cp_and_reader_status",
		    "cms_module_common_selector_article_by_section_and_cp_and_reader_category",
		    "cms_module_common_selector_article_by_section_and_cp_and_search_segment",
		    "cms_module_common_selector_article_by_section_and_cp_and_search_universe"
		);
	}

	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$sub = $this->get_selected_sub_selector();
			$this->value = intval($sub->get_value() ?? 0);
		}
		return $this->value;
	}
}