<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_articles_by_cp.class.php,v 1.5 2021/02/22 13:12:25 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_articles_by_cp extends cms_module_common_filter{

	public function get_filter_from_selectors(){
		return array(
			"cms_module_common_selector_type_article_filter"
		);
	}
	
	public function get_filter_by_selectors(){
		return array(
			"cms_module_common_selector_env_var",
			"cms_module_common_selector_empr_infos",
			"cms_module_common_selector_value",
			"cms_module_common_selector_session_var",
			"cms_module_common_selector_global_var"
		);
	}
}