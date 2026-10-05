<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_sections_by_publication_state.class.php,v 1.2.8.1 2025/01/17 10:40:44 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_sections_by_publication_state extends cms_module_common_filter{

	public function get_filter_from_selectors(){
		return array(
			"cms_module_common_selector_publication_state_from"
		);
	}

	public function get_filter_by_selectors(){
		return array(
			"cms_module_common_selector_publication_state"
		);
	}
	
	public function filter($datas){
		$filtered_datas= $filter = array();

		$selector_by = $this->get_selected_selector("by");
		$field_by = $selector_by->get_value();
		if(is_countable($field_by) && count($field_by)){
			array_walk($field_by, 'static::int_caster');
			$query = "select id_section from cms_sections where section_publication_state in ('".implode("','",$field_by)."')";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$filter[] = $row->id_section;
				}
				foreach($datas as $rubrique){
					if(in_array($rubrique,$filter)){
						$filtered_datas[]=$rubrique;
					}
				}
			}
		}
		return $filtered_datas;
	}
}