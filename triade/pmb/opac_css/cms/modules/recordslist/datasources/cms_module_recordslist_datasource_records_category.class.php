<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_category.class.php,v 1.4.6.2 2025/01/21 15:29:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_category extends cms_module_common_datasource_records_list{

    public function __construct($id=0){
        parent::__construct($id);
        $this->paging = true;
    }

	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_category"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = array();

		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = intval($selector->get_value());
			if($value){
				$query = "select notcateg_notice as notice_id from notices_categories where num_noeud = '".$value."' ";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notice_id;
					}
				}
				$records = $this->filter_datas("notices",$records);
			}

			if(!is_countable($records) || !count($records)) {
			    return false;
			}

			$return = $this->sort_records($records);
			$return["title"] = $this->msg['cms_module_recordslist_datasource_records_category_title'];

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