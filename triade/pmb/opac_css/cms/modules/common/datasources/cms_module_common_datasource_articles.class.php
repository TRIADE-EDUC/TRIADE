<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_articles.class.php,v 1.10.6.2 2025/01/21 15:29:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_articles extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = false;
		$this->paging = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_articles"
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
			$return = array();
			if (is_countable($selector->get_value()) && count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$return[] = $value;
				}
			}
			$return = $this->filter_datas("articles",$return);
			if(count($return)){
				$query = "select id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date from cms_articles where id_article in ('".implode("','",$return)."')";
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".$this->parameters["sort_by"];
					if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
				} 
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$return = array();
					while($row=pmb_mysql_fetch_object($result)){
					    $return['articles'][] = $row->id_article;
					}
				}
			}
			// Pagination
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($return['articles']);
			    $return['articles'] = $this->cut_paging_list($return['articles'], $return["paging"]);
			}else if ($this->parameters["nb_max_elements"] > 0) {
				$return["articles"] = array_slice($return['articles'], 0, $this->parameters["nb_max_elements"]);
			}
			
			return $return;
		}
		return false;
	}
}