<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_articles_sections.class.php,v 1.15.2.1 2025/01/17 10:40:41 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_articles_sections extends cms_module_common_datasource_list{
	
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
			"id_article",
			"article_title",
			"article_order",
		    "rand()"
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
			
			if (is_countable($tab_values) && count($tab_values) > 0) {
			    $tab_values = $this->array_int_caster($tab_values);
				$list_values = "'".implode("','", $tab_values)."'";
				$query = "select id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date  from cms_articles where num_section in (".$list_values.")";	
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".$this->parameters["sort_by"];
					if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
				}
				$result = pmb_mysql_query($query);
				$return = array();
				$articles = array();
				if($result){
					while($row = pmb_mysql_fetch_object($result)){
					    $articles[] = $row->id_article;
					}
				}
				$return["articles"] = $this->filter_datas("articles", $articles);

				if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
				    $return["paging"] = $this->inject_paginator($return['articles']);
				    $return['articles'] = $this->cut_paging_list($return['articles'], $return["paging"]);
				} else if ($this->parameters["nb_max_elements"] > 0) {
				    $return["articles"] = array_slice($return["articles"], 0, $this->parameters["nb_max_elements"]);
				}
				
				return $return;
			}
		}
		return false;
	}
}