<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_articles_by_cp.class.php,v 1.3 2014-11-26 11:24:21 dgoron Exp $

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
			"cms_module_common_selector_value"
		);
	}
}