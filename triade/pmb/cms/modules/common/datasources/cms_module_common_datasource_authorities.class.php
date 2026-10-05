<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_authorities.class.php,v 1.1.18.1 2025/01/17 10:40:41 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/authority.class.php');

class cms_module_common_datasource_authorities extends cms_module_common_datasource_list{
	
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
				"cms_module_common_selector_generic_authorities"
		);
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"id_authority"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$authorities_ids = array();
			if (is_countable($selector->get_value()) && count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$authorities_ids[] = $value;
				}
			}
			$authorities_ids = $this->filter_datas("authorities", $authorities_ids);
			$authorities = array();
			foreach ($authorities_ids as $authority_id) {
				$authorities[] = new authority($authority_id);
			}
			return $authorities;
		}
		return false;
	}
	
	public function get_format_data_structure(){
		return cms_authority::get_format_data_structure();
	}
}