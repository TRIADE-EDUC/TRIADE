<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_by_categories.class.php,v 1.2.6.2.2.1 2025/02/10 15:45:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_by_categories extends cms_module_common_datasource_records_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
		$this->paging = true;
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
		    $num_noeud = intval($selector->get_value());
		    $query = "select distinct notcateg_notice from notices_categories where num_noeud = '".$num_noeud."' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) > 0){
				$records = array();
				$return = array();
				while($row = pmb_mysql_fetch_object($result)){
					$records[] = $row->notcateg_notice;
				}

				$return['records'] = $this->filter_datas("notices",$records);

				if (!is_countable($return['records']) || !count($return['records'])) {
				    return false;
				}

				$return = $this->sort_records($return['records']);
				$return["title"] = "";

				// Pagination
				if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
				    $return["paging"] = $this->inject_paginator($return['records']);
				    $return['records'] = $this->cut_paging_list($return['records'], $return["paging"]);
				}

				return $return;
			}
		}
		return false;
	}
}