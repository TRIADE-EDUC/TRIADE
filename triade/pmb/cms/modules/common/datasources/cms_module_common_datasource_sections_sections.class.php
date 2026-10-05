<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_sections_sections.class.php,v 1.11 2022/09/06 07:52:19 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_sections_sections extends cms_module_common_datasource_list{
	
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
			"cms_module_common_selector_sections",
			"cms_module_common_selector_env_var",
			"cms_module_common_selector_global_var",
			"cms_module_common_selector_generic_parent_section",
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
		if ($selector) {
			$tab_values = $selector->get_value();
			if(!is_array($tab_values)){
				$tab_values = array($tab_values);
			}
			if (count($tab_values) > 0) {
				$return = array();
				array_walk($tab_values, 'static::int_caster');
				$list_values = implode(",", $tab_values);
				if($list_values) {
					$query = "select id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date from cms_sections where section_num_parent in (".$list_values.")";
					if ($this->parameters["sort_by"] != "") {
						$query .= " order by ".$this->parameters["sort_by"];
						if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
					}
					$result = pmb_mysql_query($query);
					
					if($result){
						while($row = pmb_mysql_fetch_object($result)){
						    $return["sections"][] = $row->id_section;
						}
					}
				}
				
				$return["sections"] = $this->filter_datas("sections", $return["sections"]);

				// Pagination
				if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
				    $return["paging"] = $this->inject_paginator($return["sections"]);
				    $return["sections"] = $this->cut_paging_list($return["sections"], $return["paging"]);
				}else if ($this->parameters["nb_max_elements"] > 0) {
				    $return["sections"] = array_slice($return["sections"], 0, $this->parameters["nb_max_elements"]);
				}
				return $return;
			}
		}
		return false;
	}
}