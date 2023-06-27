<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_by_concepts.class.php,v 1.6 2016-09-21 15:38:44 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_by_concepts extends cms_module_common_datasource_records_list{
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_generic_authorities_concepts"
		);
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		$return  = parent::get_sort_criterias();
		$return[] = "pert";
		return $return;
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		$selector = $this->get_selected_selector();
		if ($selector && $selector->get_value()) {
			$values = "'".implode("','", $selector->get_authorities_raw_ids())."'";
			$query = 'select distinct num_object from index_concept 
					where type_object = 1 
					and num_concept in ('.$values.')';
			
			// On regarde si on se base sur les concepts d'une notice, auquel cas on ne veut pas de la notice en question
			$excluded_elements = $selector->get_excluded_elements();
			if (isset($excluded_elements['records_ids'])) {
				array_walk($excluded_elements['records_ids'], 'static::int_caster');
				$query.= " and num_object not in ('".implode("','", $excluded_elements['records_ids'])."')";
			}
			
			$result = pmb_mysql_query($query,$dbh);
			$return = array();
			if($result && (pmb_mysql_num_rows($result) > 0)){
				$return["title"] = "Liste de notices";
				while($row = pmb_mysql_fetch_object($result)){
					$return["records"][] = $row->num_object;
				}
			}
			$return['records'] = $this->filter_datas("notices",$return['records']);
			
			if(!count($return['records'])) return false;
			if ($this->parameters["sort_by"] == 'pert') {
				foreach($return['records'] as $key => $record) {
					$return['records'][$key] = $record * 1;
				}
				// on tri par pertinence
				$query = 'select num_object as notice_id from index_concept join notices on notice_id = num_object
						where type_object = 1 and num_object in ("'.implode('","', $return['records']).'") 
						group by num_object order by count(num_concept) '.$this->parameters["sort_order"].', create_date desc limit '.$this->parameters['nb_max_elements'];
				$result = pmb_mysql_query($query,$dbh);
				$return = array();
				if (pmb_mysql_num_rows($result) > 0) {
					$return["title"] = "Liste de notices";
					while($row = pmb_mysql_fetch_object($result)){
						$return["records"][] = $row->notice_id;
					}
				}
			} else {
				$return = $this->sort_records($return["records"]);
			}
			return $return;
		}
		return false;
	}
}