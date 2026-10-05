<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_all_sections.class.php,v 1.8 2022/10/20 13:18:21 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_all_sections extends cms_module_common_datasource_list{

	protected $all_section_order = [];
	private static $sections = [];
	
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
	    // On regarde si on n'a pas déjà été chercher toutes les rubriques
	    $sections = $this->getAlreadyCalc($this->parameters["sort_by"], $this->parameters["sort_order"]);
	    if($sections !== false){
	        $this->all_section_order = $sections;
	    }else{
	        //Si non, on va les chercher...
    		if($this->parameters["sort_by"] == 'section_order') {
    			$this->get_datas_order(0);
    		} else { 
    			$query = "select id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) as publication_date  from cms_sections";
    			if ($this->parameters["sort_by"] != "") {
    				$query .= " order by ".$this->parameters["sort_by"];
    				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
    			}
    			$result = pmb_mysql_query($query);
    			$this->all_section_order = array();
    			if(pmb_mysql_num_rows($result) > 0){
    				while($row = pmb_mysql_fetch_object($result)){
    					$this->all_section_order[] = $row->id_section;
    				}
    			}
    		}
    		// On les mets de coté pour les prochaines instances...
    		self::$sections[md5($this->parameters["sort_by"]."___".$this->parameters["sort_order"])] = $this->all_section_order;
	    }
	    //On filtre
		$this->all_section_order = $this->filter_datas("sections",$this->all_section_order);
		
		// Pagination
		if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
		    $return["paging"] = $this->inject_paginator($this->all_section_order);
		    $this->all_section_order = $this->cut_paging_list($this->all_section_order, $return["paging"]);
		} else if ($this->parameters["nb_max_elements"] > 0) {
		    $this->all_section_order = array_slice($this->all_section_order, 0, $this->parameters["nb_max_elements"]);
		}
		
		$return["sections"] = $this->all_section_order;
		return $return;
	}
	
	function get_datas_order($section_num) {	
		$query_section = "select id_section from cms_sections where section_num_parent=".$section_num." 
				order by section_order";
		if ($this->parameters["sort_order"] != "") $query_section .= " ".$this->parameters["sort_order"];		
		$result_section = pmb_mysql_query($query_section);
		if(pmb_mysql_num_rows($result_section)){
			while($row_section = pmb_mysql_fetch_object($result_section)){				
				$this->all_section_order[] = $row_section->id_section;					
				// cette section a des enfants section ?
				$this->get_datas_order($row_section->id_section);
			}
		}
	}
	
	private function getAlreadyCalc($sortBy,$sortOrder)
	{
	    if(!empty(self::$sections[md5($sortBy."___".$sortOrder)])){
	        return self::$sections[md5($sortBy."___".$sortOrder)];
	    }
	    return false;
	}
}