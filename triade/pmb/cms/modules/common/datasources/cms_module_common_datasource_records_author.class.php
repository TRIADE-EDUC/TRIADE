<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_author.class.php,v 1.9.6.1 2025/01/17 10:40:41 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_author extends cms_module_common_datasource_records_list{

    public function __construct($id=0){
        parent::__construct($id);
        $this->paging = true;
    }
    
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_principal_author"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			$value['author'] = intval($value['author']);
			$value['record'] = intval($value['record']);
			if($value['author'] != 0){
				$query = "select distinct responsability_notice from responsability where responsability_author = '".$value['author']."' and responsability_notice != '".$value['record']."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->responsability_notice;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}

			if(!is_countable($return['records']) || !count($return['records'])) {
			    return false;
			}
			
			$return = $this->sort_records($return['records']);
			$return["title"] = "Du même auteur";
			
			// Pagination
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($return['records']);
			    $return['records'] = $this->cut_paging_list($return['records'], $return["paging"]);
			}
			
			return $return;
		}
		return false;
	}
}