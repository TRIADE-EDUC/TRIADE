<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_sections_by_concepts.class.php,v 1.8 2022/09/06 07:52:19 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_sections_by_concepts extends cms_module_common_datasource_list{
	
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
			"cms_module_common_selector_generic_authorities_concepts"
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
			$values = "'".implode("','", $selector->get_authorities_raw_ids())."'";
			$query = "select distinct id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date from cms_sections
					join index_concept on id_section=num_object and type_object = 13 
					where num_concept in (".$values.")";
			
			// On regarde si on se base sur les concepts d'une rubrique, auquel cas on ne veut pas de la rubrique en question
			$excluded_elements = $selector->get_excluded_elements();
			if (isset($excluded_elements['sections_ids'])) {
			    $excluded_elements['sections_ids'] = $this->array_int_caster($excluded_elements['sections_ids']);
				$query.= " and id_section not in ('".implode("','", $excluded_elements['sections_ids'])."')";
			}
			
			if ($this->parameters["sort_by"] != "") {
				$query .= " order by ".$this->parameters["sort_by"];
				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
			}
			$result = pmb_mysql_query($query);
			$return = array();
			if(pmb_mysql_num_rows($result) > 0){
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