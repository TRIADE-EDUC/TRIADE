<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_sections_by_categories.class.php,v 1.2.8.1 2025/02/10 15:03:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_sections_by_categories extends cms_module_common_datasource_list{
	
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
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"publication_date",
			"id_section",
			"section_title",
			"section_order"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if ($selector && $selector->get_value()) {
		    $num_noeud = intval($selector->get_value());
			$query = "select distinct id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date from cms_sections 
					join cms_sections_descriptors on id_section=num_section and num_noeud = '".$num_noeud."' ";			
			if ($this->parameters["sort_by"] != "") {
				$query .= " order by ".$this->parameters["sort_by"];
				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
			}
			$result = pmb_mysql_query($query);
			$return = array();
			if($result){
				while($row = pmb_mysql_fetch_object($result)){
				    $return["sections"][] = $row->id_section;
				}
			}
			$return["sections"] = $this->filter_datas("sections", $return["sections"]);
			
			// Pagination
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($return['sections']);
			    $return['sections'] = $this->cut_paging_list($return['sections'], $return["paging"]);
			} else if ($this->parameters["nb_max_elements"] > 0) {
			    $return["sections"] = array_slice($return["sections"], 0, $this->parameters["nb_max_elements"]);
			}
			
			return $return;
		}
		return false;
	}
}