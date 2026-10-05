<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_articles_by_categories.class.php,v 1.3.8.1 2025/02/10 15:45:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_articles_by_categories extends cms_module_common_datasource_list{
	
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
		if ($selector && $selector->get_value()) {
			
			$query = "select distinct id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date from cms_articles
					 join cms_articles_descriptors on id_article=num_article and num_noeud = '".intval($selector->get_value())."' ";			
			if ($this->parameters["sort_by"] != "") {
				$query .= " order by ".$this->parameters["sort_by"];
				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
			}
			$result = pmb_mysql_query($query);
			$articles = array();
			$return = array();
			if(pmb_mysql_num_rows($result) > 0){
				while($row = pmb_mysql_fetch_object($result)){
				    $articles[] = $row->id_article;
				}
			}
			$return['articles'] = $this->filter_datas("articles",$articles);
			
			// Pagination
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($return['articles']);
			    $return['articles'] = $this->cut_paging_list($return['articles'], $return["paging"]);
			}else if ($this->parameters["nb_max_elements"] > 0) {
			    $return['articles'] = array_slice($return['articles'], 0, $this->parameters["nb_max_elements"]);
			}
			
			return $return;
		}
		return false;
	}
}