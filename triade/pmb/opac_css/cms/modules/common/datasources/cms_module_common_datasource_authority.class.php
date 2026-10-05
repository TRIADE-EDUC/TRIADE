<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_authority.class.php,v 1.4 2018/07/26 15:25:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/authority.class.php');

class cms_module_common_datasource_authority extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
				"cms_module_common_selector_generic_authority"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$authorities_ids = $this->filter_datas("authorities", array($selector->get_value()));
			if($authorities_ids[0]){
				//return new authority($authorities_ids[0]);
				return authorities_collection::get_authority('authority', $authorities_ids[0]);
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){
		return cms_authority::get_format_data_structure();
	}
}