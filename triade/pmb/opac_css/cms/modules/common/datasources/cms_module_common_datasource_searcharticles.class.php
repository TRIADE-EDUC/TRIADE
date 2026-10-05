<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_searcharticles.class.php,v 1.12 2022/05/31 08:37:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_searcharticles extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
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
			"id_article",
			"article_title",
		    "rand()"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if($selector) {
			if($selector->get_value() != ""){
				$searcher = new cms_editorial_searcher(stripslashes($selector->get_value()),"article");
				$results = $searcher->get_sorted_result($this->parameters["sort_by"],$this->parameters["sort_order"],0);
				$results = $this->filter_datas("articles", $results);
				if($this->parameters["nb_max_elements"] > 0){
					$results = array_slice($results,0,$this->parameters["nb_max_elements"]);
				}
				return $results;
			}
		} 
		return false;	
	}
}