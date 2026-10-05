<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_record.class.php,v 1.6.8.1 2025/01/17 10:40:41 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_record extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_record",
			"cms_module_common_selector_record_permalink",
			"cms_module_common_selector_env_var",
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
	    if(is_countable($this->selectors) && $this->parameters['selector'] != ""){
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
					$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
					break;
				}
			}
			//$notice = new notice_info($selector->get_value());
			$notice=$selector->get_value();
			if(is_array($notice)){
				$notice = $this->filter_datas("notices",$notice);
			} else {
				$notice = $this->filter_datas("notices",array($notice));
			}
			if(!empty($notice[0])) {
				$notice = $notice[0];
			} else {
				$notice = 0;
			}
			return $notice;
		}
		return false;
	}
}