<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_filter_by_interest.class.php,v 1.1 2017-06-05 09:56:07 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_filter_by_interest extends cms_module_common_filter{

		
	public function filter($datas){
		$filtered_datas = array();
		if(count($datas)){
			foreach($datas as $item){
				if($item['interesting']){
					$filtered_datas[] = $item;
				}
			}
		}
		return $filtered_datas;
	}

	//Surcharge du formulaire, nous n'avons pas besoin de données à comparer
	public function get_form(){
		return "";
	}
}