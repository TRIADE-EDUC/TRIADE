<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_sections_by_parent_and_cp.class.php,v 1.4.20.1 2025/04/16 08:05:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_sections_by_parent_and_cp extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	
	public function get_sub_selectors(){
		return array(
			"cms_module_common_selector_sections",
			"cms_module_common_selector_type_section_filter",
			"cms_module_common_selector_env_var"
		);
	}

	
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$parent = new cms_module_common_selector_sections($this->get_sub_selector_id("cms_module_common_selector_sections"));
			$cp = new cms_module_common_selector_type_section_filter($this->get_sub_selector_id("cms_module_common_selector_type_section_filter"));
			$parents = $parent->get_value();
			$field = $cp->get_value();
			$var = new cms_module_common_selector_env_var($this->get_sub_selector_id("cms_module_common_selector_env_var"));
			$this->value = array();
			if(is_array($parents) && count($parents)){
				array_walk($parents, 'static::int_caster');
				$query = "select id_section from cms_sections where section_num_parent in ('".implode("','",$parents)."')";
				$result = pmb_mysql_query($query);
				$fields = new cms_editorial_parametres_perso($field['type']);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$fields->get_values($row->id_section);
						if (!empty($fields->values[$field['field']]) && is_array($fields->values[$field['field']])) {
    						if(in_array($var->get_value(),$fields->values[$field['field']])){
    							$this->value[] = $row->id_section;
    						}
						}
					}
				}
				
			}
		}
		return $this->value;
	}
}