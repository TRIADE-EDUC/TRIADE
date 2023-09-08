<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_by_categories.class.php,v 1.1 2017-07-27 12:51:28 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_by_categories extends cms_module_common_datasource_records_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_category_permalink",
			"cms_module_common_selector_env_var",
		);
	}
		
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if ($selector && $selector->get_value()) {
			
			$query = "select distinct notcateg_notice from notices_categories where num_noeud = '".($selector->get_value()*1)."' ";			
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) > 0){
				$records = array();
				while($row = pmb_mysql_fetch_object($result)){
					$records[] = $row->notcateg_notice;
				}
			
				$return['records'] = $this->filter_datas("notices",$records);
	
				if(!count($return['records'])) return false;
				
				$return = $this->sort_records($return['records']);
				$return["title"] = "";
				return $return;
			}
		}
		return false;
	}
}