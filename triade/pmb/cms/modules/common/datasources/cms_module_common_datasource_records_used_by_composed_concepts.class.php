<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_used_by_composed_concepts.class.php,v 1.3 2016-05-20 13:04:53 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/vedette/vedette_composee.class.php');

class cms_module_common_datasource_records_used_by_composed_concepts extends cms_module_common_datasource_records_list{
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_generic_authorities_concepts"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		$selector = $this->get_selected_selector();
		if ($selector && $selector->get_value()) {
			$vedettes = array();
			$return = array('records' => array());
			foreach ($selector->get_authorities_raw_ids() as $concept_id) {
				$vedette_id = vedette_composee::get_vedette_id_from_object($concept_id, TYPE_CONCEPT_PREFLABEL);
				if ($vedette_id) {
					$vedette = new vedette_composee($vedette_id);
					foreach ($vedette->get_elements() as $subdivision) {
						for ($i = 0; $i < count($subdivision); $i++) {
							if (get_class($subdivision[$i]) == 'vedette_records') {
								$record = $subdivision[$i];
								if (!in_array($record->get_id(), $return["records"])) {
									$return["records"][] = $record->get_id();
								}
							}
						}
					}
				}
			}
			$return['records'] = $this->filter_datas("notices",$return['records']);
			if(!count($return['records'])) return false;
			
			return $this->sort_records($return['records']);
		}
		return false;
	}
}