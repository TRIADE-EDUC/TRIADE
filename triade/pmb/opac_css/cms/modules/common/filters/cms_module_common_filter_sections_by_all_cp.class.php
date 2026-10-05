<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_sections_by_all_cp.class.php,v 1.2 2017/07/04 08:57:50 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_sections_by_all_cp extends cms_module_common_filter{
	protected $generic_type = 0;
	
	public function __construct($id=0){
		parent::__construct($id);
		$query = 'select id_editorial_type from cms_editorial_types where editorial_type_element = "section_generic"';
		$result = pmb_mysql_query($query);
		$this->generic_type = pmb_mysql_result($result,0,0);
	}
	
	public function get_filter_from_selectors(){
		return array(
			"cms_module_common_selector_generic_section_filter"
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