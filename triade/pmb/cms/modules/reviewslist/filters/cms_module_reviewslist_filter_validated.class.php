<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_filter_validated.class.php,v 1.2.6.1 2025/01/17 10:40:45 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_reviewslist_filter_validated extends cms_module_common_filter{

	public function get_filter_from_selectors(){
		return array(
				"cms_module_reviewslist_selector_validated_from"
		);
	}

	public function get_filter_by_selectors(){
		return array(
				"cms_module_reviewslist_selector_validated"
		);
	}

	public function filter($datas){
		$final_datas = array();
		if (is_countable($datas) && count($datas)){
			array_walk($datas, 'static::int_caster');
			$query = "select id_avis from avis where id_avis in ('".implode("','",$datas)."')";
			$query .= " and valide = 1";
			$result = pmb_mysql_query($query);
			$filtered_datas = array();
			if($result && pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_assoc($result)){
					$filtered_datas[] = $row["id_avis"];
				}
			}
			//les données sont déjà triées...dont on s'assure que ca ne bouge pas !
			$final_datas = array_merge(array_intersect($datas, $filtered_datas));
		}
		return $final_datas;
	}
}