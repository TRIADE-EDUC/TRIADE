<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_authentificated.class.php,v 1.3 2013/09/17 10:26:52 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_authentificated extends cms_module_common_condition{

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_authentificated"
		);
	}
	
	public function check_condition(){
		global $log_ok;
		$selector = $this->get_selected_selector();
		$value = $selector->get_value();
		//si vrai, alors seulement ce qui est authentifié...
		if(!$value || ($value && $log_ok)){
			return true;
		}else{
			return false;
		}
	}
	
	//fonction qui détermine si un cadre utilisant cette condition peut être caché!
	public static function use_cache(){
		return false;
	}
}