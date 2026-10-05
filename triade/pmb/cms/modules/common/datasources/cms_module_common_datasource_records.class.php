<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records.class.php,v 1.19.6.1.2.1 2025/06/03 06:32:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records extends cms_module_common_datasource_list{
	
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
			"cms_module_common_selector_shelve",
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
			$shelves = $selector->get_value();
			$source_infos = array();
			$records = array();
			if(is_array($shelves) && count($shelves)){
				foreach ($shelves as $shelve_id){
				    $shelve_id = intval($shelve_id);
					$query = "select id_tri, name, thumbnail_url from etagere where idetagere = '".$shelve_id."'";
					$result = pmb_mysql_query($query);
					$notices = array();
					if($result && pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						notices_caddie($shelve_id, $notices, '', '', '',0,$row->id_tri);						
						
						foreach($notices as $id => $niv){
							$records[]=$id;
						}
						$etagere_instance = new etagere($shelve_id);
						$source_infos[] = array(
								'type' => 'shelve',
								'id' => $shelve_id,
								'name' => $etagere_instance->get_translated_name(),
								'thumbnail_url' => $row->thumbnail_url,
								'url' => './index.php?lvl=etagere_see&id='.$shelve_id,
						);
					}					
				}
			}
			$records = $this->filter_datas("notices", $records);
			// Pagination
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($records);
			    $records = $this->cut_paging_list($records, $return["paging"]);
			}else if(isset($this->parameters['nb_max_elements']) && $this->parameters['nb_max_elements'] > 0){
				$records = array_slice($records, 0, $this->parameters['nb_max_elements']);
			}
			
			$return_records = array(
					'title'=> 'Liste de Notices',
					'records' => $records,
					'source_infos' => $source_infos
			);
			$return = array_merge(isset($return) ? $return : [], $return_records);
			return $return;
		}
		return false;
	}
}