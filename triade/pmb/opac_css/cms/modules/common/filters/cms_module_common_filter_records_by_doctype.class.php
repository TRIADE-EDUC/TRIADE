<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_records_by_doctype.class.php,v 1.3 2016-09-21 15:38:44 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_records_by_doctype extends cms_module_common_filter{

	public function get_filter_from_selectors(){
		return array(
			"cms_module_common_selector_record_doctype_from"
		);
	}

	public function get_filter_by_selectors(){
		return array(
			"cms_module_common_selector_record_doctype"
		);
	}
	
	public function filter($datas){
		$filtered_datas= $filter = array();

		$selector_by = $this->get_selected_selector("by");
		$field_by = $selector_by->get_value();
		if(count($field_by) && count($datas)){
			array_walk($field_by, 'static::int_caster');
			array_walk($datas, 'static::int_caster');
			$query = "select notice_id from notices where notice_id in ('".implode("','",$datas)."') and typdoc in ('".implode("','",$field_by)."')";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$filtered_datas[] = $row->notice_id;
				}
			}
		}
		return $filtered_datas;
	}
}