<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_categories.class.php,v 1.7.6.1.2.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_categories extends cms_module_common_datasource_records_list{

    public function __construct($id=0){
        parent::__construct($id);
        $this->paging = true;
    }

	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_categories"
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		$return  = parent::get_sort_criterias();
		$return[] = "pert";
		return $return;
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			$value['record'] = intval($value['record']);
			if($value['categories'] != ""){
				$query = "select notcateg_notice, count(num_noeud) as pert from notices_categories where num_noeud in (".$value['categories'].") and notcateg_notice != '".$value['record']."' group by notcateg_notice order by pert";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$return["title"] = $this->msg['cms_module_recordslist_datasource_records_categories_title'];
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
					    $records[] = intval($row->notcateg_notice);
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}
			if (!is_countable($return['records']) || !count($return['records'])) {
			    return false;
			}

			if ($this->parameters["sort_by"] == 'pert') {
				// on tri par pertinence
				$query = "select notcateg_notice, count(num_noeud) as pert from notices_categories join notices on notice_id = notcateg_notice
						where num_noeud in (".$value['categories'].") and notcateg_notice != '".$value['record']."' AND notice_id in ('".implode("','", $return['records'])."') group by notcateg_notice
						order by count(*) ".addslashes($this->parameters['sort_order']).", create_date desc
						limit ".((int) $this->parameters['nb_max_elements']);

				$result = pmb_mysql_query($query);
				$return = array();
				if($result && (pmb_mysql_num_rows($result) > 0)){
					while($row = pmb_mysql_fetch_object($result)){
						$return["records"][] = $row->notcateg_notice;
					}
				}
			} else {
			    $return = $this->sort_records($return["records"]);
			}
			$return["title"] = $this->msg['cms_module_recordslist_datasource_records_categories_title'];

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