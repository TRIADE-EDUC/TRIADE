<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watch_datasource_watch.class.php,v 1.4 2015-03-10 14:45:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/docwatch_watch.class.php");

class cms_module_watch_datasource_watch extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_watch_selector_watch_generic"
		);
	}
			
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$watch_id = $selector->get_value();
			if ($watch_id) {
				$docwatch_watch = new docwatch_watch($watch_id);
				$docwatch_watch->fetch_items();
				return $docwatch_watch->get_normalized_watch();
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){
		return $this->prefix_var_tree(docwatch_watch::get_format_data_structure(),"watch");
	}
}