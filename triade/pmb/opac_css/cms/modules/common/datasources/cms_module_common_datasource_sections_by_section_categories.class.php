<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_sections_by_section_categories.class.php,v 1.3 2019-03-20 10:51:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_sections_by_section_categories extends cms_module_common_datasource_list{
	
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
			"cms_module_common_selector_section",
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
			"section_order",
			"pert"
		);
	}
	
	protected function get_query_base() {
		$selector = $this->get_selected_selector();
		if ($selector) {
			if(!isset($this->parameters['operator_between_authorities'])) $this->parameters['operator_between_authorities'] = 'or';
			switch ($this->parameters["operator_between_authorities"]) {
				case 'and':
					$query = "select distinct cms_sections_descriptors.num_noeud
						from cms_sections_descriptors
					    where cms_sections_descriptors.num_section = '".($selector->get_value()*1)."'";
					$result = pmb_mysql_query($query);
					$descriptors = array();
					if($result && (pmb_mysql_num_rows($result) > 0)){
						while($row = pmb_mysql_fetch_object($result)){
							$descriptors[] = $row->num_noeud;
						}
					}
					if(count($descriptors)) {
						$query = "select distinct id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date, num_noeud
							from cms_sections join cms_sections_descriptors on id_section=num_section
							where cms_sections_descriptors.num_section != '".($selector->get_value()*1)."' and cms_sections_descriptors.num_noeud IN (".implode(',', $descriptors).")
							group by id_section
							having count(id_section) = ".count($descriptors);
						return $query;
					}
					break;
				case 'or':
				default:
					$query = "select distinct id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date, num_noeud
							from cms_sections join cms_sections_descriptors on id_section=num_section
							where num_section != '".($selector->get_value()*1)."' and num_noeud in (select num_noeud from cms_sections_descriptors where num_section = '".($selector->get_value()*1)."')";
					return $query;
			}
		}
		return false;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = $this->get_sorted_datas('id_section', 'num_noeud');
		if($return) {
			$return = $this->filter_datas("sections",$return);
			if ($this->parameters["nb_max_elements"] > 0) $return = array_slice($return, 0, $this->parameters["nb_max_elements"]);
		}
		return $return;
	}
}