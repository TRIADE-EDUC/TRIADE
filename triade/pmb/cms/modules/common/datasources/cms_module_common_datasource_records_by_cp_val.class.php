<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_by_cp_val.class.php,v 1.4.6.1.2.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_by_cp_val extends cms_module_common_datasource_records_list{

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
			"cms_module_common_selector_record_cp_val"
		);
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $selector_choice;

		$this->parameters= array();
		$this->parameters['selector'] = $selector_choice;
		return parent::save_form();
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
	    if(is_countable($this->selectors) && $this->parameters['selector'] != ""){
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
					$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
					break;
				}
			}
			$values = $selector->get_value();
 			$searcher = new search(false);
 			$current_search = $searcher->serialize_search();
 			$searcher->destroy_global_env();
			global $search;
			$search =array();
			$search[] = "d_".$values['cp'];
			$op = "op_0_d_".$values['cp'];
			$field = "field_0_d_".$values['cp'];
			global ${$op},${$field};
			${$op} = "EQ";
			${$field} = $values['cp_val'];
			$table = $searcher->make_search();
			$query = "select notice_id from ".$table;
			$result = pmb_mysql_query($query);
			$records = array();
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
				    $records[] = intval($row->notice_id);
				}
			}
			$query = "DROP TEMPORARY TABLE ".$table;
			pmb_mysql_query($query);
			$searcher->unserialize_search($current_search);
			$records = $this->filter_datas("notices",$records);
			$return = $this->sort_records($records);

			// Pagination
			if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
			    $return["paging"] = $this->inject_paginator($return['records']);
			    $return['records'] = $this->cut_paging_list($return['records'], $return["paging"]);
			} else if($this->parameters['nb_max_elements'] > 0){
				$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
			}
			return $return;
		}
		return false;
	}
}