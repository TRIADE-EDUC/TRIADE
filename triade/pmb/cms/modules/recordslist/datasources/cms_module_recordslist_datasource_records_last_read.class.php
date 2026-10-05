<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_last_read.class.php,v 1.4 2022/09/06 07:52:20 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_last_read extends cms_module_common_datasource_list{

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
			"cms_module_recordslist_selector_last_read"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			$records = array();
			if (is_array($value) && count($value)) {
				for ($i=0; $i<count($value); $i++) {
					$value[$i] = intval($value[$i]);
					$query = "select notice_id from notices where notice_id='".$value[$i]."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result) > 0){
						$row = pmb_mysql_fetch_object($result);
						$records[] = $row->notice_id;
					}
				}
				$records = array_reverse($records);
				$records['records'] = $this->filter_datas("notices",$records);

				// Pagination
				if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
				    $paging = $this->inject_paginator($records['records']);
				    $records['records'] = $this->cut_paging_list($records['records'], $return["paging"]);
				} else if($this->parameters['nb_max_elements'] > 0){
				    $records['records'] = array_slice($records['records'], 0, $this->parameters['nb_max_elements']);
				}
				
				$return = array(
				    'title'=> 'Liste de Notices',
				    'records' => $records['records'],
				    'paging' => $paging
				);
			}
			return $return;
		}
		return false;
	}
}