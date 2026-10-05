<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_bannette.class.php,v 1.4.6.1 2025/01/17 10:40:42 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_bannette extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
		$this->paging = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_bannette",			
			"cms_module_common_selector_type_article",
			"cms_module_common_selector_type_section",
			"cms_module_common_selector_type_article_generic",
			"cms_module_common_selector_type_section_generic"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
	    if(is_array($this->selectors) && $this->parameters['selector'] != ""){
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
					$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
					break;
				}
			}
			$bannettes = $selector->get_value();
			if(is_array($bannettes) && count($bannettes)){
				foreach ($bannettes as $bannette_id){
					$records = $notices = array();
					notices_bannette($bannette_id, $notices);
					foreach($notices as $id => $niv){
						$records[]=$id;
					}
				}
			}
			$return["records"] = $this->filter_datas("notices", $records);
			
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($return['records']);
			    $return['records'] = $this->cut_paging_list($return['records'], $return["paging"]);
			} else if($this->parameters['nb_max_elements'] > 0){
			    $return["records"] = array_slice($return["records"], 0, $this->parameters['nb_max_elements']);
			}
			
			return $return;
		}
		return false;
	}
}