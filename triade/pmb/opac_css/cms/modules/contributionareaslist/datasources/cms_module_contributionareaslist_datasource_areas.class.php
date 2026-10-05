<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_contributionareaslist_datasource_areas.class.php,v 1.2 2021/03/23 09:26:18 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/docwatch_item.class.php");

class cms_module_contributionareaslist_datasource_areas extends cms_module_common_datasource_list{
	
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
				"cms_module_contributionareaslist_selector_areas_generic"
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	*/
	protected function get_sort_criterias() {
		return array (
				"id_area",
				"area_title",
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
	    $area_list = array();
	    
	    //on commence par récupérer l'identifiant retourné par le sélecteur...
	    $selector = $this->get_selected_selector();
	    if($selector){
	        $values = $selector->get_value();

	        //On filtre sur les droit d'acces et statuts
	        $values = $this->filter_datas("contribution_areas", $values);
	        //On tri
	        $query = "SELECT id_area FROM contribution_area_areas WHERE id_area in('".implode("','",$values)."')";
	        if ($this->parameters["sort_by"] != "") {
	            $query .= " order by ".addslashes($this->parameters["sort_by"]);
	            if ($this->parameters["sort_order"] != "") $query .= " ".addslashes($this->parameters["sort_order"]);
	        }
	        $result = pmb_mysql_query($query);
	        if ($result){
	            if (pmb_mysql_num_rows($result)) {
	                while($row=pmb_mysql_fetch_object($result)){
        	            $contribution_area = new contribution_area(intval($row->id_area));
        	            $area_list[] = $contribution_area->get_normalized_item();
	                }
	            }
	        }
	        //On limite
	        if ($this->parameters["nb_max_elements"] > 0) $area_list = array_slice($area_list, 0, $this->parameters["nb_max_elements"]);
	    }
	    return array('items' => $area_list);
	}
	
	public function get_format_data_structure(){
		return array();
	}
}