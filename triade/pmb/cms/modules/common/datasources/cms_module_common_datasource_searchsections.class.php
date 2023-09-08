<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_searchsections.class.php,v 1.5 2016-09-21 13:09:44 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_searchsections extends cms_module_common_datasource_list{
	
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
			"id_section",
			"section_title"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		$selector = $this->get_selected_selector();
		if($selector) {
			$tab_word_query = array();
			$tab_word_query = explode (" ",$selector->get_value());
		
			$query = "";
			if (count($tab_word_query) > 0) {
				$select ="(";
				$where ="("; 
				foreach ($tab_word_query as $i=>$word_query) {
					$select .= "(concat(section_title,' ',section_resume,' ',section_contenu) like '%".addslashes($word_query)."%')+";
					if ($i > 0) $operator = " or ";
					else $operator = "";
					$where .= $operator." concat(section_title,' ',section_resume,' ',section_contenu) like '%".addslashes($word_query)."%'";
				}
				$select .= "(trim(section_title) like '".addslashes($selector->get_value())."%')*0.2";
				$select .= ") as pert";
				$where .= ")";
				$query = "select distinct id_section,".$select." from cms_sections where ".$where;
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".$this->parameters["sort_by"];
					if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
				}
				$result = pmb_mysql_query($query,$dbh);
				$return = array();
				while ($row = pmb_mysql_fetch_object($result)) {
					$return[] = $row->id_section;
				}
				$return = $this->filter_datas("sections",$return);
				if ($this->parameters["nb_max_elements"] > 0) $return = array_slice($return, 0, $this->parameters["nb_max_elements"]);
				return $return;
			}
		} 
		return false;	
	}
}