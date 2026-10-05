<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authorities_uniform_titles_by_expressions_of.class.php,v 1.3 2022/02/24 15:17:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_authorities_uniform_titles_by_expressions_of extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_sub_selectors(){
		return array(
			"cms_module_common_selector_generic_authority_uniform_title"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = array();
			$sub = new cms_module_common_selector_generic_authority_uniform_title($this->get_sub_selector_id("cms_module_common_selector_generic_authority_uniform_title"));
			$value = intval($sub->get_authority_raw_id());
			if ($value) {
				$query = 'select oeuvre_link_from from tu_oeuvres_links where oeuvre_link_expression = 1 and oeuvre_link_to = "'.$value.'" order by oeuvre_link_order';
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$this->value[] = $row->oeuvre_link_from;
					}
				}
			}
		}
		return $this->value;
	}
}