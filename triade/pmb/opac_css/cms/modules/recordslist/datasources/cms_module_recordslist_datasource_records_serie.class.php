<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_serie.class.php,v 1.5 2022/09/06 07:52:19 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_serie extends cms_module_common_datasource_records_list {

    public function __construct($id=0){
        parent::__construct($id);
        $this->paging = true;
    }
    
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_serie"
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
			$value['serie'] = intval($value['serie']);
			$value['record'] = intval($value['record']);
			if($value['serie'] != 0){
				$query = "select notice_id from notices where tparent_id = '".$value['serie']."' and notice_id != '".$value['record']."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notice_id;
					}
				}
				$records = $this->filter_datas("notices",$records);
			}
			
			$return = $this->sort_records($records);
			$return["title"] = $this->msg['cms_module_recordslist_datasource_records_serie_title'];
			
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