<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_searchsections.class.php,v 1.8 2022/09/06 07:52:19 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_searchsections extends cms_module_common_datasource_list{
	
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
			"cms_module_common_selector_search_result"
		);
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"pert",
			"id_section",
			"section_title"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if($selector) {
			if($selector->get_value() != ""){
				$searcher = new cms_editorial_searcher(stripslashes($selector->get_value()),"section");
				$results = $searcher->get_sorted_result($this->parameters["sort_by"],$this->parameters["sort_order"],0);
				$return["sections"] = $this->filter_datas("sections", $results);
				
				// Pagination
				if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
				    $return["paging"] = $this->inject_paginator($return['sections']);
				    $return['sections'] = $this->cut_paging_list($return['sections'], $return["paging"]);
				} else if ($this->parameters["nb_max_elements"] > 0) {
				    $return["sections"] = array_slice($return["sections"], 0, $this->parameters["nb_max_elements"]);
				}
				return $return;
			}
		} 
		return false;	
	}
}